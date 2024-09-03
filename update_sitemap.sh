#!/bin/bash

# Copyright 2018, 2020, 2024 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

# Updates sitemap to match existing posts and their update dates.
# Assumes the blog posts reside in their default URLs (/blog[n]) and directories (site/posts[n]).
# Only the <url> elements are affected, so the <urlset> must already exist in the sitemap file.
# Add possible alternate language links manually afterwards.

base_url=http://10.0.1.2
sitemap=sitemap.xml
date_regex="[0-9]\{4\}-[0-9]\{2\}-[0-9]\{2\}"

[ "$(echo "${BASH_VERSION}" | cut -f 1 -d '.')" -gt 3 ] && checkblogs=1
if [ "$checkblogs" ]
then
    declare -A blog_updates
    for blog_dir in $(ls -d site/posts* | cut -f 2 -d '/')
    do
        blog_updates[$blog_dir]=""
    done
fi

fixes_done=0
for post in $(ls -1 site/posts* | grep "^[0-9]" | sort | uniq)
do
    url=$(ls -d site/posts*/$post | head -n 1 | cut -f 2- -d '/')
    blog_dir=$(echo $url | cut -f 1 -d '/')
    post_date=$(ls -d site/$url site/$url/update_* 2>/dev/null | grep -o "[^/]*$" | grep -o "$date_regex" | sort | tail -n 1)

    [ "$checkblogs" ] && blog_updates[$blog_dir]="${blog_updates[${blog_dir}]} $post_date"

    smap_date=$(grep -A 1 "<loc>.*$post" $sitemap | tail -n 1 | tr -d -c "[:digit:]-")
    smap_date_line=$(grep -n -A 1 "<loc>.*$post" $sitemap | tail -n 1 | cut -f 1 -d '-')
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
        let fixes_done+=1;
    elif [ "$post_date" != "$smap_date" ]
    then
        echo "Date mismatch for $post:"
        echo "   Post: $post_date"
        echo "   Smap: $smap_date"
        sed -i "$smap_date_line s/$date_regex/$post_date/" $sitemap
        echo "   Fixed entry to sitemap."
        let fixes_done+=1;
    fi
done

[ ! "$checkblogs" ] && exit
for blog_dir in ${!blog_updates[@]}; do
    blog_date=$(echo ${blog_updates[${blog_dir}]} | tr " " "\n" | sort -g | tail -n 1)
    blog_number=$(echo $blog_dir | grep -o "[0-9]$")
    blog_url="$base_url/blog$blog_number"
    smap_date=$(grep -A 1 "<loc>$blog_url</loc>" $sitemap | tail -n 1 | tr -d -c "[:digit:]-")
    smap_date_line=$(grep -n -A 1 "<loc>$blog_url</loc>" $sitemap | tail -n 1 | cut -f 1 -d '-')
    if [ ! "$smap_date" ]
    then
        echo "Missing from sitemap: $blog_url"
    elif [ "$blog_date" != "$smap_date" ]
    then
        echo "Date mismatch for $blog_url:"
        echo "   Blog: $blog_date"
        echo "   Smap: $smap_date"
        sed -i "$smap_date_line s/$date_regex/$blog_date/" $sitemap
        echo "   Fixed entry to sitemap."
        let fixes_done+=1;
    fi
done

echo Done. Did $fixes_done fixes.
