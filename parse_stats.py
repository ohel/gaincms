#!/usr/bin/python3

# Copyright 2016-2018, 2025 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

# Parse GainCMS site statistics.

from ipaddress import ip_address # Requires Python 3.3 or newer.
from os import path, walk
from re import search
from sys import argv, exit, stderr

FILE_IP2ID = "GeoLite2-Country-Blocks-IPv4.csv"
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

    def __init__(self, page, ipv4, ipv6, timestamp, ua, ref):

        self.country = ""
        self.geoid = 0
        self.ipv4 = ipv4
        self.ipv6 = ipv6
        self.page = page
        self.ref = ref
        self.timestamp = timestamp
        self.useragent = ua

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

        ipv4 = ipv6 = None
        if (str(ip).find(":") < 0):
            ipv4 = ip
        else:
            ipv6 = ip
        visits.append(Visit(page, ipv4, ipv6, ts, ua, ref))

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
            check_ip = ""

            # Non-directory file names are IP addresses.
            for _, _, ips in walk(page_dir):

                try:
                    for ip in ips:
                        check_ip = ip
                        ip_address(ip) # IPv4 or IPv6 are both valid here. Throws error if invalid.
                    visitors_ips.extend(ips)
                except:
                    print("Not a valid statistics file: %s" % check_ip, file=stderr)
                finally:
                    break

            for visitor_ip in visitors_ips:

                if visitor_ip in ignore_ips:
                    if not ignore_ips[visitor_ip]:
                        print("Ignoring IP: %s" % visitor_ip)
                        ignore_ips[visitor_ip] = True
                    continue
                file = open(path.join(page_dir, visitor_ip))
                for visit_data in file: addPageVisit(page, visitor_ip, visit_data, file.name)
                file.close()

def parseGeoData(visits):

    # GeoIP parsing requires *sorted* GeoLite2 data, which is available on MaxMind's website.
    geopath = '.'
    if not (path.isfile(FILE_IP2ID) and path.isfile(FILE_ID2COUNTRY)):
        geopath = argv[1]
        if not (path.isfile(path.join(geopath, FILE_IP2ID)) and path.isfile(path.join(geopath, FILE_ID2COUNTRY))):
            print("GeoLite2 data not found, not parsing country data.", file=stderr)
            return

    print("Parsing GeoIP country data...")

    def ip2value(b1, b2, b3, b4):

        # pow(2,24) = 16777216
        # pow(2,16) = 65536
        # pow(2,8) = 256
        return 16777216 * b1 + 65536 * b2 + 256 * b3 + b4

    class GeoIP:

        def __init__(self, geolite2_csv_line):

            split_data = geolite2_csv_line.split(",")
            ipv4_blocks = split_data[0].split(".")
            self.ipv4_block_1 = int(ipv4_blocks[0])
            self.ipv4_block_2 = int(ipv4_blocks[1])
            self.ipv4_block_3 = int(ipv4_blocks[2])
            self.ipv4_block_4 = int(ipv4_blocks[3].split("/")[0])
            self.netmask = int(split_data[0].split("/")[1])
            self.geoid = split_data[1]

            netmask_str = self.netmask * '1' + (32 - self.netmask) * '0'
            nm1 = int(netmask_str[0:8], 2)
            nm2 = int(netmask_str[8:16], 2)
            nm3 = int(netmask_str[16:24], 2)
            nm4 = int(netmask_str[24:32], 2)
            min1 = nm1 & self.ipv4_block_1
            min2 = nm2 & self.ipv4_block_2
            min3 = nm3 & self.ipv4_block_3
            min4 = nm4 & self.ipv4_block_4
            max1 = min1 | ~nm1 + 256
            max2 = min2 | ~nm2 + 256
            max3 = min3 | ~nm3 + 256
            max4 = min4 | ~nm4 + 256

            self.ipv4_min_val = ip2value(min1, min2, min3, min4)
            self.ipv4_max_val = ip2value(max1, max2, max3, max4)

    class GeoCountry:

        def __init__(self, geolite2_csv_line):

            split_data = geolite2_csv_line.split(",")
            self.geoid = split_data[0]
            self.country = split_data[5]
            # There might be IPs that don't belong to any specific country. In that case, use the continent.
            if (len(self.country) == 0):
                self.country = "(No country data) " + split_data[3]

    geoips = []
    with open(path.join(geopath, FILE_IP2ID)) as f:
        firstline = True
        for line in f:
            # Skip the header line.
            if not firstline: geoips.append(GeoIP(line))
            firstline = False

    with open(path.join(geopath, FILE_ID2COUNTRY)) as f: data_id2country = f.readlines()
    data_id2country.pop(0) # Pop the header line.
    countries = list(map(GeoCountry, data_id2country))
    known_geoips = {}

    def applyCountry(visit):

        country_candidate = list(filter(lambda c: c.geoid == visit.geoid, countries))
        if len(country_candidate) == 0:
            # print("Applying GeoIP country failed for IP: %s, GeoID: %s" % (visit.ipv4, visit.geoid), file=stderr)
            visit.country = "(No GeoIP data) Unknown"
        else:
            visit.country = country_candidate[0].country.replace("\n", "").replace("\"", "")

    geo_main_ip_blocks = {}

    def getPreviousMainIPBlock(b):

        for i in range(b - 1, 0, -1):
            if (i not in geo_main_ip_blocks):
                geo_main_ip_blocks[i] = list(filter(lambda gip: gip.ipv4_block_1 == i, geoips))
            if len(geo_main_ip_blocks[i]) > 0:
                return geo_main_ip_blocks[i][-1]

    def applyGeoDataIPv4(visit):

        if (visit.ipv4 in known_geoips):
            [visit.geoid, visit.country] = known_geoips[visit.ipv4]
            return

        try:
            [vb1, vb2, vb3, vb4] = map(lambda vb: int(vb), visit.ipv4.split("."))
        except:
            print("Applying GeoData failed for IP: %s" % visit.ipv4, file=stderr)
            visit.country = "(Invalid IPv4)"
            known_geoips[visit.ipv4] = [visit.geoid, visit.country]
            return

        if (vb1 not in geo_main_ip_blocks):
            geo_main_ip_blocks[vb1] = list(filter(lambda gip: gip.ipv4_block_1 == vb1, geoips))

        candidates = []
        first_candidate = next((gip for gip in reversed(geo_main_ip_blocks[vb1]) if gip.ipv4_block_2 < vb2), None)
        # If visitor IP might be in the first part of a matching main block, it might be in a previous main block also.
        if (not first_candidate or (len(geo_main_ip_blocks[vb1]) > 0 and first_candidate == geo_main_ip_blocks[vb1][0])):
            pb = getPreviousMainIPBlock(vb1) # Will be None if vb1 == 1
            if pb: candidates.append(pb)
        if (first_candidate):
            candidates.append(first_candidate)
        candidates.extend(list(filter(lambda gip: gip.ipv4_block_2 == vb2, geo_main_ip_blocks[vb1])))

        if len(candidates) == 0:
            #print("No GeoIP candidate found for IP: %s" % visit.ipv4)
            visit.country = "(No GeoIP data) Unknown"
            known_geoips[visit.ipv4] = [visit.geoid, visit.country]
            return

        visit_ip_val = ip2value(vb1, vb2, vb3, vb4)
        match = list(filter(lambda c: c.ipv4_min_val <= visit_ip_val and c.ipv4_max_val >= visit_ip_val, candidates))
        if len(match) > 1:
            print("Multiple GeoIP matches for IP %s, something is wrong." % visit.ipv4, file=stderr)
            visit.country = "(No country data) Error"
        elif len(match) == 0:
            #print("No GeoIP match found for IP: %s" % visit.ipv4)
            visit.country = "(No GeoIP data) Unknown"
        else:
            visit.geoid = match[0].geoid
            applyCountry(visit)

        known_geoips[visit.ipv4] = [visit.geoid, visit.country]

    def applyGeoData(visit):

        if (visit.ipv4):
            applyGeoDataIPv4(visit)
        else:
            visit.geoid = 0
            visit.country = "(IPv6 GeoIP not implemented)"

    for visit in visits:
        if visit.useragent != UA_BOT: applyGeoData(visit)

def printStats(visits):

    ignored_count = 0
    visits[:] = [visit for visit in visits if not (visit.useragent == UA_BOT and (ignored_count := ignored_count + 1))]
    if (ignored_count > 0): print("\nIgnored (bot) visitor count: %s" % ignored_count)

    print("\nTotal site visits (non-unique): %s" % len(visits))

    unique_ips = set([(visit.ipv4 or visit.ipv6) for visit in visits])
    print("\nTotal unique site visitors: %s" % len(unique_ips))

    print("\nVisits by page (non-unique): \n")
    pages = list(set([visit.page for visit in visits]))
    pages.sort()
    for page in pages:
        page_visits = list(filter(lambda v: v.page == page, visits))
        print("   %50s %s" % ((page + " ").ljust(49, "."), len(page_visits)))

    print("\nUnique visitors by country:\n")
    unique_visitors = list(filter(lambda v: (v.ipv4 or v.ipv6) in unique_ips and not unique_ips.remove(v.ipv4 or v.ipv6), visits))
    countries = list(set([visit.country for visit in visits]))
    countries.sort()
    for country in countries:
        country_visits = list(filter(lambda v: v.country == country, unique_visitors))
        print("   %50s %s" % ((country + " ").ljust(49, "."), len(country_visits)))

visits = [];

def pageStats(page):

    page_visits = [visit for visit in visits if visit.page == page]
    print("\nStatistics for page: %s\n" % page)
    print("   %15s %15s %20s %s" % ("Visitor".ljust(15), "Country".ljust(15), "Timestamp".ljust(20), "Referrer\n"))
    for visit in page_visits:
        print("   %15s %15s %20s %s" % ((visit.ipv4 or visit.ipv6).ljust(15), visit.country.ljust(15), visit.timestamp.ljust(20), visit.ref))

def showAll():

    pages = list(set([visit.page for visit in visits]))
    pages.sort()
    for page in pages:
        pageStats(page)

def parseStats():

    parseVisitData(visits)
    parseGeoData(visits)
    printStats(visits)

parseStats()

print("\nAvailable statistics parsing functions:")
print("pageStats(\"page name\") - show detailed statistics for a page")
print("showAll() - show detailed statistics for every page")
print("parseStats() - parse statistics again (e.g. if UA_IGNORE_LIST has been modified)")
