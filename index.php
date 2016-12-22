<?php

# Site configuration and paths.
define("CONFIG_AUTHOR", "Site Author");
define("CONFIG_GITHUB_USER", "user");
define("CONFIG_PAGINATION", 8); # How many posts to show per blog page.
define("CONFIG_TITLE", "Site Title");
define("CONFIG_URL_BASE", "http://10.0.1.2");
define("CONFIG_URL_DISQUS", "mysite.disqus.com");
define("DIR_FILES", "site/files/");
define("DIR_INCLUDE", "site/includes/");
define("DIR_POSTS_GLOB", "[^_]*"); # Used to glob blog posts. Begin article directory with underscore to skip it.
define("DIR_SITE", "site/");
define("DIR_STATS_BASE", "site/stats/");
define("DIR_TAG_PREFIX", "tag_");

$url_elements = explode('/', ltrim($_SERVER["REQUEST_URI"], "/"));

# Remove trailing slashes from url elements.
$lastelem = end($url_elements);
while (empty($lastelem)) {
    array_pop($url_elements);
    $lastelem = empty($url_elements) ? "last" : end($url_elements);
}

# Routing.
if (empty($url_elements)) {

    $page = "home";

} else switch(array_shift($url_elements)) {

    case "blog":
        $page = "blog";
        $page_meta = array("blog/", "posts/", "Blog", "A tech-oriented blog");
        $navbarhighlight = "blogs";
        break;
    case "posts":
        $page = "post";
        $page_meta = array("blog/", "posts/");
        $navbarhighlight = "blogs";
        break;
    case "blog2":
        $page = "blog";
        $page_meta = array("blog2/", "posts2/", "Blog 2", "Another blog");
        $navbarhighlight = "blogs";
        break;
    case "posts2":
        $page = "post";
        $page_meta = array("blog2/", "posts2/");
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

# Visitor statistics logging.
if (file_exists(DIR_STATS_BASE) && isset($stats_dir) && isset($_SERVER["REMOTE_ADDR"])) {
    if (!file_exists(DIR_STATS_BASE . $stats_dir)) {
        mkdir(DIR_STATS_BASE . $stats_dir, 0755, true);
    }
    $stats_file = fopen(DIR_STATS_BASE . $stats_dir . "/" . $_SERVER["REMOTE_ADDR"], "a");
    fwrite($stats_file,
        date("Y-m-d H:i:s") .
        (isset($_SERVER["HTTP_USER_AGENT"]) ? " \"" . $_SERVER["HTTP_USER_AGENT"] . "\"": "Unknown") .
        (isset($_SERVER["HTTP_REFERER"]) ? " " . $_SERVER["HTTP_REFERER"] : "") .
        "\n");
    fclose($stats_file);
}

?>
