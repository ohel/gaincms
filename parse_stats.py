#!/usr/bin/python3
import os
from sys import exit

class Visit:
    def __init__(self, page, ipv4, timestamp, ref):
        self.page = page;
        self.ipv4 = ipv4;
        self.timestamp = timestamp;
        self.ref = ref;
        self.geoid = 0;
        self.country = "";

visits = [];

def parseVisitData():

    print("Parsing visit data...")
    for root_dir, directories, ordinary_files in os.walk('.'):
        for page in directories:
            def addPageVisit(ip, visit_data):
                split_data = visit_data.split()
                ref = split_data[2] if len(split_data) > 2 else ""
                visits.append(Visit(page, ip, "%s %s" % (split_data[0], split_data[1]), ref))

            visitors = []
            page_dir = os.path.join(root_dir, page)

            for page_root_dir, page_directories, ips in os.walk(page_dir):
                visitors.extend(ips)
                break

            for visitor in visitors:
                file = open(os.path.join(page_dir, visitor))
                for visit_data in file:
                    addPageVisit(visitor, visit_data)
                file.close()

    print("%s visits parsed." % (len(visits)))

parseVisitData()

def parseGeoData():

    # GeoIP parsing requires sorted GeoLite2 data, which is available on MaxMind's website.
    file_ip2id = "GeoLite2-Country-Blocks-IPv4.csv"
    file_id2country = "GeoLite2-Country-Locations-en.csv"
    if not (os.path.isfile(file_ip2id) and os.path.isfile(file_id2country)):
        exit()

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

    file = open(file_ip2id)
    data_ip2id = file.readlines()
    file.close()
    data_ip2id.pop(0) # Pop the header line.
    geoips = map(GeoIP, data_ip2id)

    file = open(file_id2country)
    data_id2country = file.readlines()
    file.close()
    data_id2country.pop(0) # Pop the header line.
    countries = map(GeoCountry, data_id2country)

    def ip2value(b1, b2, b3, b4):
        return pow(2,24) * int(b1) + pow(2,16) * int(b2) + pow(2,8) * int(b3) + int(b4)
    def applyValues(gip):
        gip.ipv4_min_val = ip2value(gip.ipv4_block_1, gip.ipv4_block_2, gip.ipv4_block_3, gip.ipv4_block_4)
        gip.ipv4_max_val = int(gip.ipv4_min_val) + pow(2, (32 - int(gip.netmask)) - 1)

    def applyCountry(v):
        v.country = filter(lambda c: c.geoid == v.geoid, countries)[0].country.replace("\n", "")

    def applyGeoData(visit):
        [vb1, vb2, vb3, vb4] = visit.ipv4.split(".")
        main_block_matches = filter(lambda gip: gip.ipv4_block_1 == vb1, geoips)
        candidates = lambda c: [c] if c else []
        candidates = candidates(next((gip for gip in reversed(main_block_matches) if gip.ipv4_block_2 < vb2), None))
        candidates.extend(filter(lambda gip: gip.ipv4_block_2 == vb2, main_block_matches))

        if len(candidates) == 0:
            return
        if all(map(lambda gip: gip.geoid == candidates[0].geoid, candidates)):
            visit.geoid = candidates[0].geoid
            applyCountry(visit)
            return

        uninitialized = filter(lambda c: c.ipv4_min_val == 0, candidates)
        map(applyValues, uninitialized)

        visit_ip_val = ip2value(vb1, vb2, vb3, vb4)
        match = filter(lambda c: c.ipv4_min_val <= visit_ip_val and c.ipv4_max_val >= visit_ip_val, candidates)
        if len(match) > 1:
            print("Multiple GeoIP matches for IP %s, something is wrong." % visit.ipv4)
        elif len(match) == 0:
            print("No match found for IP: %s" % visit.ipv4)
        else:
            visit.geoid = match[0].geoid
            applyCountry(visit)

    map(applyGeoData, visits)
    print("GeoIP country data applied to visit data.")

parseGeoData()
