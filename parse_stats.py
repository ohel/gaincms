#!/usr/bin/python3
import os
from ipaddress import ip_address # Requires Python 3.3 or newer.
from sys import argv

FILE_IP2ID = "GeoLite2-Country-Blocks-IPv4.csv"
FILE_ID2COUNTRY = "GeoLite2-Country-Locations-en.csv"

visits = [];

def parseVisitData(visits):

    print("Parsing visit data...")

    class Visit:
        def __init__(self, page, ipv4, timestamp, ref):
            self.page = page;
            self.ipv4 = ipv4;
            self.timestamp = timestamp;
            self.ref = ref;
            self.geoid = 0;
            self.country = "";

    def addPageVisit(page, ip, visit_data):
        split_data = visit_data.split()
        ref = split_data[2] if len(split_data) > 2 else ""
        visits.append(Visit(page, ip, "%s %s" % (split_data[0], split_data[1]), ref))

    for root_dir, directories, ordinary_files in os.walk(argv[1] if len(argv) > 1 else '.'):
        for page in directories:

            visitors = []
            page_dir = os.path.join(root_dir, page)

            for page_root_dir, page_directories, ips in os.walk(page_dir):
                check_ip = ""
                try:
                    for ip in ips:
                        check_ip = ip
                        ip_address(ip)
                    visitors.extend(ips)
                except:
                    print("Not a valid statistics file: %s" % check_ip)
                finally:
                    break

            for visitor in visitors:
                file = open(os.path.join(page_dir, visitor))
                for visit_data in file:
                    addPageVisit(page, visitor, visit_data)
                file.close()

parseVisitData(visits)

def parseGeoData(visits):

    # GeoIP parsing requires sorted GeoLite2 data, which is available on MaxMind's website.
    geopath = '.'
    if not (os.path.isfile(FILE_IP2ID) and os.path.isfile(FILE_ID2COUNTRY)):
        if len(argv) > 1:
            geopath = argv[1]
        if not (os.path.isfile(os.path.join(geopath, FILE_IP2ID)) and os.path.isfile(os.path.join(geopath, FILE_ID2COUNTRY))):
            print("GeoLite2 data not found, not parsing country data.")
            return

    print("Parsing GeoIP country data...")

    class GeoIP:
        def __init__(self, geolite2_csv_line):
            split_data = geolite2_csv_line.split(",")
            ipv4_blocks = split_data[0].split(".")
            self.ipv4_block_1 = ipv4_blocks[0]
            self.ipv4_block_2 = ipv4_blocks[1]
            self.ipv4_block_3 = ipv4_blocks[2]
            self.ipv4_block_4 = ipv4_blocks[3].split("/")[0]
            self.netmask = split_data[0].split("/")[1]
            self.geoid = split_data[1]
            self.ipv4_min_val = 0
            self.ipv4_max_val = 0

    class GeoCountry:
        def __init__(self, geolite2_csv_line):
            split_data = geolite2_csv_line.split(",")
            self.geoid = split_data[0]
            self.country = split_data[5]

    geoips = []
    with open(os.path.join(geopath, FILE_IP2ID)) as f:
        firstline = True
        for line in f:
            if not firstline: # Skip the header line.
                geoips.append(GeoIP(line))
            firstline = False

    with open(os.path.join(geopath, FILE_ID2COUNTRY)) as f:
        data_id2country = f.readlines()
    data_id2country.pop(0) # Pop the header line.
    countries = list(map(GeoCountry, data_id2country))

    def ip2value(b1, b2, b3, b4):
        return pow(2,24) * int(b1) + pow(2,16) * int(b2) + pow(2,8) * int(b3) + int(b4)

    def applyValues(gip):
        gip.ipv4_min_val = ip2value(gip.ipv4_block_1, gip.ipv4_block_2, gip.ipv4_block_3, gip.ipv4_block_4)
        gip.ipv4_max_val = int(gip.ipv4_min_val) + pow(2, (32 - int(gip.netmask)) - 1)

    def applyCountry(visit):
        visit.country = list(filter(lambda c: c.geoid == visit.geoid, countries))[0].country.replace("\n", "").replace("\"", "")

    def applyGeoData(visit):
        [vb1, vb2, vb3, vb4] = visit.ipv4.split(".")
        main_block_matches = list(filter(lambda gip: gip.ipv4_block_1 == vb1, geoips))
        candidates = lambda c: [c] if c else []
        candidates = candidates(next((gip for gip in reversed(main_block_matches) if gip.ipv4_block_2 < vb2), None))
        candidates.extend(list(filter(lambda gip: gip.ipv4_block_2 == vb2, main_block_matches)))

        if len(candidates) == 0:
            print("No candidate found for IP: %s" % visit.ipv4)
            return
        if all(map(lambda gip: gip.geoid == candidates[0].geoid, candidates)):
            visit.geoid = candidates[0].geoid
            applyCountry(visit)
            return

        uninitialized = list(filter(lambda c: c.ipv4_min_val == 0, candidates))
        for u in uninitialized:
            applyValues(u)

        visit_ip_val = ip2value(vb1, vb2, vb3, vb4)
        match = list(filter(lambda c: c.ipv4_min_val <= visit_ip_val and c.ipv4_max_val >= visit_ip_val, candidates))
        if len(match) > 1:
            print("Multiple GeoIP matches for IP %s, something is wrong." % visit.ipv4)
        elif len(match) == 0:
            print("No match found for IP: %s" % visit.ipv4)
        else:
            visit.geoid = match[0].geoid
            applyCountry(visit)

    for visit in visits:
        applyGeoData(visit)

parseGeoData(visits)

def printStats(visits):

    print("\nTotal site visits: %s" % len(visits))

    unique_ips = set([visit.ipv4 for visit in visits])
    print("\nTotal site visitors: %s" % len(unique_ips))

    print("\nVisits by page:\n")
    pages = list(set([visit.page for visit in visits]))
    pages.sort()
    for page in pages:
        page_visits = list(filter(lambda v: v.page == page, visits))
        print("   %50s %s" % ((page + " ").ljust(49, "."), len(page_visits)))

    print("\nVisitors by country:\n")
    unique_visitors = list(filter(lambda v: v.ipv4 in unique_ips and not unique_ips.remove(v.ipv4), visits))
    countries = list(set([visit.country for visit in visits]))
    countries.sort()
    for country in countries:
        country_visits = list(filter(lambda v: v.country == country, unique_visitors))
        print("   %50s %s" % ((country + " ").ljust(49, "."), len(country_visits)))

printStats(visits)

def pageStats(page):
    page_visits = [visit for visit in visits if visit.page == page]
    print("\nStatistics for page: %s\n" % page)
    print("   %15s %15s %20s %s" % ("Visitor".ljust(15), "Country".ljust(15), "Timestamp".ljust(20), "Referrer\n"))
    for visit in page_visits:
        print("   %15s %15s %20s %s" % (visit.ipv4.ljust(15), visit.country.ljust(15), visit.timestamp.ljust(20), visit.ref))

def showAll():
    pages = list(set([visit.page for visit in visits]))
    pages.sort()
    for page in pages:
        pageStats(page)

print("\nType pageStats(\"page name\") to show detailed statistics for a page.")
print("Type showAll() to show detailed statistics for every page.")

