<?php
# Copyright 2015-2018 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

namespace PostUtils;

require_once DIR_INCLUDE . "/ExtParsedown.php";

function localizedDateFromPath($path, $lang = "en") {

    $date = dateFromPath($path);

    if ($lang == "en") {
        return $date;
    }

    # Date format is: yyyy-mm-dd
    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8, 2);

    if ($lang == "fi") {
        return ltrim($day, '0') . "." . ltrim($month, '0') . "." . $year;
    } else if ($lang == "fr") {
        return $day . "/" . $month . "/" . $year;
    }

    return $date; # Default to yyyy-mm-dd

}

function dateFromPath($path, $prefix_len = 0) {

    $pathelements = explode("/", $path);
    $lastelement = end($pathelements);
    if (empty($lastelement)) {
        # In case path was an URL ending in slash.
        array_pop($pathelements);
    }

    # Date format is: yyyy-mm-dd
    return substr(end($pathelements), $prefix_len, 10);

}

# Given a path, creates a string of tag links based on tags found in the path directory.
function tagsStringFromPath($path, $href_prefix = "") {

    $tags = array_map(function ($tagpath) { return basename($tagpath); }, glob($path . DIR_TAG_PREFIX . "*"));
    return implode(", ", filterLinksFromTags($tags, $href_prefix));

}

# Creates hyperlinks from tags for filtering. Converts underscores to spaces visually.
function filterLinksFromTags($tags, $href_prefix) {

    return array_map(function ($tag) use ($href_prefix) {
            $tag = substr($tag, strlen(DIR_TAG_PREFIX));
            return '<a href="' . $href_prefix . 'tags/' . str_replace(' ', '%20', $tag) . '">' . str_replace('_', ' ', $tag) . "</a>";
        }, $tags);

}

# Given a path, return sorted list of posts from the path directory.
function getPostsByPath($path) {

    $posts = array_reverse(glob(DIR_SITE . $path . DIR_POSTS_GLOB, GLOB_ONLYDIR|GLOB_MARK));

    # If override file exists, post listing order may be modified.
    # Syntax of the file is two columns of directory names per line, nothing else:
    # <post/dir to list> <before this post/dir>
    # <or, this post is newer/more important than> <this post>
    # If the right side is "*", the post is considered pinned, i.e. before all others.
    $override_file = DIR_SITE . $path . CONFIG_SORT_FILE;
    if (!file_exists($override_file)) {
        return $posts;
    }

    $overrides = file($override_file);
    foreach ($overrides as $override) {
        preg_match("/([^ ]+) ([^\n\r ]+)/", $override, $matches);
        if (count($matches) !== 3) {
            error_log(print_r("Error in sort override file.", TRUE));
            continue;
        }

        $overridepost = DIR_SITE . $path . $matches[1] . "/";
        $overrideindex = array_search($overridepost, $posts);

        $originalpost = DIR_SITE . $path . $matches[2] . "/";
        $originalindex = $matches[2] === "*" ? 0 : array_search($originalpost, $posts);

        if (!is_numeric($overrideindex) || !is_numeric($originalindex)) {
            error_log(print_r("Trying to override sort for non-existent post.", TRUE));
            continue;
        }

        unset($posts[$overrideindex]);
        array_splice($posts, $originalindex, 0, $overridepost);
    }

    return(array_values($posts));

}

function getPostIntro($postpath, $blog_url) {

    $mdparser = new \ExtParsedown();

    $postdate = dateFromPath($postpath);
    $posttags = tagsStringFromPath($postpath, $blog_url);

    $contents = file_get_contents($postpath . "intro.md");

    # Remove first path part and last slash for proper href URL to the article.
    $hrefpath = implode("/", array_slice(explode("/", $postpath), 1, -1));

    $preview_ext = "";
    if (file_exists($postpath . "intro.jpg")) {
        $preview_ext = "jpg";
    } elseif (file_exists($postpath . "intro.png")) {
        $preview_ext = "png";
    }

    # Except for the link, the element structure is as Parsedown would do it.
    $preview_image = empty($preview_ext) ? "" :
        ('<p></p><a href="' . $hrefpath . '"><div class="img-container"><img src="' .
        $postpath . '/intro.' . $preview_ext . '" alt="Preview"></div></a><p></p>');

    # Add header links to article and post metadata.
    return preg_replace("/<h1>(.*)<\/h1>(\n<h2>.*<\/h2>)?/",
        '<h1><a href="' . $hrefpath . '">$1</a></h1>$2' .
        '<p class="postmetadata">Posted: ' . $postdate . CONFIG_META_SEPARATOR . "Tags: " . $posttags . "</p>" .
        $preview_image,
        $mdparser->setLocalPath($postpath)->text($contents), 1);

}

?>
