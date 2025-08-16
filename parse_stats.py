#!/usr/bin/python3

# Copyright 2016-2018, 2025 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

# Parse GainCMS site statistics.

from ipaddress import ip_address, ip_network # Requires Python 3.3 or newer.
from os import path, walk
from re import search
from sys import argv, exit, stderr
import bisect

FILE_IPV4_2ID = "GeoLite2-Country-Blocks-IPv4.csv"
FILE_IPV6_2ID = "GeoLite2-Country-Blocks-IPv6.csv"
FILE_ID2COUNTRY = "GeoLite2-Country-Locations-en.csv"
UA_IGNORE_LIST = [
    "bot",
    "Bot"
]
UA_BOT = "BOT"

if (len(argv) < 2):
    print("Give the site statistics directory as a parameter.")
    exit()

print("For studying statistics interactively, run the script using: python3 -i %s <site statistics directory> [<ignore IPs file>]" % path.basename(argv[0]))

class Visit:

    def __init__(self, page, ip, timestamp, ua, ref):

        self.country = ""
        self.geoid = 0
        self.ip = ip
        self.page = page
        self.ref = ref
        self.timestamp = timestamp
        self.useragent = ua

class GeoCountry:

    def __init__(self, geolite2_csv_line):

        split_data = geolite2_csv_line.split(",")
        self.geoid = split_data[0]
        self.country = split_data[5]
        # There might be IPs that don't belong to any specific country. In that case, use the continent.
        if (len(self.country) == 0):
            self.country = "(No country data) " + split_data[3]

def sortGeoIPs(geoips):
    networks = sorted(geoips, key = lambda x: int(x[0].network_address))
    net_addrs = [int(n[0].network_address) for n in networks]
    return networks, net_addrs

def ipBinarySearch(ip, networks, net_addrs):
    ip_int = int(ip)
    pos = bisect.bisect_right(net_addrs, ip_int) - 1
    if pos >= 0:
        net, geoname_id = networks[pos]
        if ip in net:
            return geoname_id
    return None

def parseVisitData(visits):

    print("Parsing visit data...")

    def addPageVisit(page, ip, visit_data, filename):

        split_data = search(r"(?P<timestamp>\S+ \S+) (?P<ua>\".*\")( (?P<ref>\S+))?", visit_data)
        if (not split_data or not split_data.group("timestamp") or not split_data.group("ua")):
            print("Regex error in file %s: %s" % (filename, visit_data), file=stderr)
            return

        ua = split_data.group("ua")
        if (any(map(lambda x: x in ua, UA_IGNORE_LIST))): ua = UA_BOT

        ts = split_data.group("timestamp")
        ref = split_data.group("ref") if split_data.group("ref") else ""

        visits.append(Visit(page, ip, ts, ua, ref))

    ignore_ips = {}
    ignore_ip_file = argv[2] if len(argv) > 2 else "stats_ip_ignore.txt"
    if (path.isfile(ignore_ip_file)):

        file = open(ignore_ip_file)
        for ip_to_ignore in file: ignore_ips[ip_to_ignore.rstrip()] = False
        file.close()

    for root_dir, directories, _ in walk(argv[1] if len(argv) > 1 else '.'):

        for page in directories:

            visitors_ips = []
            page_dir = path.join(root_dir, page)

            # Non-directory file names are IP addresses.
            for _, _, ips in walk(page_dir):

                try:
                    # IPv4 or IPv6 are both valid here. Throws error if invalid.
                    ip_addresses = map(ip_address, ips)
                    visitors_ips.extend(ip_addresses)
                except:
                    print("Not a valid IP on page: %s" % page, file=stderr)
                finally:
                    break

            for visitor_ip in visitors_ips:

                if str(visitor_ip) in ignore_ips:
                    if not ignore_ips[visitor_ip]:
                        print("Ignoring IP: %s" % str(visitor_ip))
                        ignore_ips[visitor_ip] = True
                    continue
                file = open(path.join(page_dir, str(visitor_ip)))
                for visit_data in file: addPageVisit(page, visitor_ip, visit_data, file.name)
                file.close()

def parseGeoData():

    # GeoIP parsing requires *sorted* GeoLite2 data, which is available on MaxMind's website.
    if not (path.isfile(FILE_IPV4_2ID) and path.isfile(FILE_ID2COUNTRY)):
        print("GeoLite2 data not found, not parsing country data.", file=stderr)
        print("Required files: %s, %s, %s" % (FILE_ID2COUNTRY, FILE_IPV4_2ID, FILE_IPV6_2ID), file=stderr)
        return None, None

    print("Parsing GeoIP country data...")

    geo_networks = []
    with open(FILE_IPV4_2ID) as f:
        headerline = True
        for line in f:
            if (headerline):
                headerline = False
                continue
            split_data = line.split(",")
            geonetwork = ip_network(split_data[0])
            geoid = split_data[1]
            geo_networks.append((geonetwork, geoid))

    with open(FILE_IPV6_2ID) as f:
        headerline = True
        for line in f:
            if (headerline):
                headerline = False
                continue
            split_data = line.split(",")
            geonetwork = ip_network(split_data[0])
            geoid = split_data[1]
            geo_networks.append((geonetwork, geoid))

    with open(FILE_ID2COUNTRY) as f: data_id2country = f.readlines()
    data_id2country.pop(0) # Pop the header line.
    countries = list(map(GeoCountry, data_id2country))

    return geo_networks, countries

def applyGeoData(visits, geoips, countries):

    print("Applying GeoIP data...")

    networks, net_addrs = sortGeoIPs(geoips)
    country_map = {c.geoid: c.country.strip().replace('"', '') for c in countries}
    known_geoips = {} # For optimization.

    for visit in visits:
        if (visit.useragent == UA_BOT): continue
        if (visit.ip in known_geoips):
            [visit.geoid, visit.country] = known_geoips[visit.ip]
            continue

        geoname_id = ipBinarySearch(visit.ip, networks, net_addrs)
        if geoname_id:
            visit.geoid = geoname_id
            visit.country = country_map.get(geoname_id, "(No GeoIP data) Unknown")
        else:
            visit.geoid = 0
            visit.country = "(No GeoIP data) Unknown"

        known_geoips[visit.ip] = (visit.geoid, visit.country)

def pageStatsFromVisits(visits, page):

    page_visits = [visit for visit in visits if visit.page == page]
    print("\nStatistics for page: %s\n" % page)
    print("   %30s %15s %20s %s" % ("Visitor IP".ljust(30), "Country".ljust(15), "Timestamp".ljust(20), "Referrer\n"))
    for visit in page_visits:
        print("   %30s %15s %20s %s" % (str(visit.ip).ljust(30), visit.country.ljust(15), visit.timestamp.ljust(20), visit.ref))

def printStatsFromVisits(visits):

    ignored_count = 0
    visits[:] = [visit for visit in visits if not (visit.useragent == UA_BOT and (ignored_count := ignored_count + 1))]
    if (ignored_count > 0): print("Ignored (bot) visitor count: %s" % ignored_count)

    print("Total site visits (non-unique): %s" % len(visits))

    unique_ips = set([str(visit.ip) for visit in visits])
    print("Total unique site visitors: %s" % len(unique_ips))

    print("\nUnique visitors per country:\n")
    unique_visitors = list(filter(lambda v: str(v.ip) in unique_ips and not unique_ips.remove(str(v.ip)), visits))
    countries = list(set([visit.country for visit in visits]))
    countries.sort()
    for country in countries:
        country_visits = list(filter(lambda v: v.country == country, unique_visitors))
        print("   %50s %s" % ((country + " ").ljust(49, "."), len(country_visits)))

    print("\nVisits per page (non-unique): \n")
    pages = list(set([visit.page for visit in visits]))
    pages.sort()
    for page in pages:
        page_visits = list(filter(lambda v: v.page == page, visits))
        print("   %50s %s" % ((page + " ").ljust(49, "."), len(page_visits)))

visits = [];
geoips, countries = parseGeoData()
parseVisitData(visits)
applyGeoData(visits, geoips, countries)
def pageStats(page): pageStatsFromVisits(visits, page)
def printStats(): printStatsFromVisits(visits)
print("\nTo show detailed visitor data for a single page: pageStats(visits, \"page name\")")
print("To print stats again: printStats()\n")
printStats()
