#!/bin/bash

# Copyright 2016-2018 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

# Updates sitemap to match existing posts and their update dates.

base_url=https://localhost
sitemap=sitemap.xml
date_regex="[0-9]\{4\}-[0-9]\{2\}-[0-9]\{2\}"

for post in $(ls -1 site/posts* | grep "^[0-9]" | sort | uniq)
do
    url=$(ls -d site/posts*/$post | head -n 1 | cut -f 2- -d '/')
    post_date=$(ls -d site/$url site/$url/update_* 2>/dev/null | grep -o "[^/]*$" | grep -o "$date_regex" | sort | tail -n 1)
    smap_date=$(grep -A 1 $post $sitemap | tail -n 1 | tr -d -c "[:digit:]-")
    smap_date_line=$(grep -n -A 1 $post $sitemap | tail -n 1 | cut -f 1 -d '-')
    if [ ! "$smap_date" ]
    then
        echo "Missing from sitemap: $post"
        sed -i "/<\/urlset>/d" $sitemap
        echo "<url>" >> $sitemap
        echo "  <loc>$base_url/$url</loc>" >> $sitemap
        echo "  <lastmod>$post_date</lastmod>" >> $sitemap
        echo "  <changefreq>yearly</changefreq>" >> $sitemap
        echo "</url>" >> $sitemap
        echo "</urlset>" >> $sitemap
        echo "   Added missing entry to sitemap."
    elif [ "$post_date" != "$smap_date" ]
    then
        echo "Date mismatch for $post:"
        echo "   Post: $post_date"
        echo "   Smap: $smap_date"
        sed -i "$smap_date_line s/$date_regex/$post_date/" $sitemap
        echo "   Fixed entry to sitemap."
    fi
done
