<?php
# Copyright 2015-2018 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

namespace PostUtils;

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

function dateFromPath($path) {

    $pathelements = explode("/", $path);
    $lastelement = end($pathelements);
    if (empty($lastelement)) {
        array_pop($pathelements);
    }

    # Date format is: yyyy-mm-dd
    return substr(end($pathelements), 0, 10);

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

?>
