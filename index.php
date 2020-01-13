<?php
# Copyright 2015-2018 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

# Site configuration and paths.
define("CONFIG_AUTHOR", "Site Author"); # Your name.
define("CONFIG_DEFAULT_CHANGELOG", "New article."); # Description to show for new posts/articles in blog updates listing.
define("CONFIG_EMAIL", "info@gainit.fi"); # Email for article comments.
define("CONFIG_GITHUB_USER", "user"); # Your GitHub user name, for retrieving projects.
define("CONFIG_META_SEPARATOR", " | "); # Used to separate displayed post metadata, for example date and tags.
define("CONFIG_PAGINATION", 8); # How many posts to show per blog page.
define("CONFIG_SORT_FILE", "_sort_override"); # Name of the per-blog file used to force post sort order.
define("CONFIG_STATS_IP_IGNORE_FILE", "stats_ip_ignore.txt"); # Skip the IPv4 addresses and IPv6 prefixes found in this file from visitor statistics.
define("CONFIG_TITLE", "Site Title"); # Name of your website.
define("CONFIG_URL_BASE", "http://10.0.1.2"); # One could prefix URLs with a slash, but this saves the trouble and is needed for Open Graph data anyway.
define("CONFIG_URL_DISQUS", "mysite.disqus.com"); # URL for Disqus comments.
define("DIR_FILES", "site/files/"); # Location of miscellaneous files (e.g. downloadable documents). Optional, use where it feels handy.
define("DIR_INCLUDE", "site/includes/"); # Location of includable code files.
define("DIR_POSTS_GLOB", "[^_]*"); # Used to glob blog posts. Begin article directory with underscore to skip it.
define("DIR_SITE", "site/"); # Location of site data. You may use a subdirectory to keep the root more clean.
define("DIR_STATS_BASE", "site_stats/"); # If collecting visitor statistics, the location to store them.
define("DIR_TAG_PREFIX", "tag_"); # Prefix used to identify blog article tags.
define("DIR_UPDATE_PREFIX", "update_"); # Prefix used to identify blog article updates.

# We don't expect any query parameters so strip them.
# Query parameters are not an error either, because for example social media sites sometimes add some parameters to links.
$query_stripped_url = explode('?', $_SERVER["REQUEST_URI"])[0];
$url_elements = explode('/', ltrim($query_stripped_url, "/"));

# Remove trailing slashes from url elements.
$lastelem = end($url_elements);
while (empty($lastelem)) {
    array_pop($url_elements);
    $lastelem = empty($url_elements) ? "last" : end($url_elements);
}

# Routing.
# $page: Which PHP page to load.
# $page_meta: Page-specific metadata.
# $navbarhighlight: The navigation bar link with corresponding href will be highlighted.
if (empty($url_elements)) {

    $page = "home";

} else switch(array_shift($url_elements)) {

    case "blog":
        $page = "blog";
    case "posts":
        isset($page) || $page = "post";
        $page_meta = array("blog/", "posts/", "Blog", "A tech-oriented blog");
        $navbarhighlight = "blogs";
        break;
    case "blog2":
        $page = "blog";
    case "posts2":
        isset($page) || $page = "post";
        $page_meta = array("blog2/", "posts2/", "Blog 2", "Another blog");
        $navbarhighlight = "blogs";
        break;
    case "projects":
        $page = "projects";
        $navbarhighlight = $page;
        break;
    case "about":
        $page = "about";
        $navbarhighlight = $page;
        break;
    default:
        $page = "error";
}

$extra_styles = array();
require DIR_SITE . $page . ".php";

include DIR_INCLUDE . "/visitorstats.php";

?>
