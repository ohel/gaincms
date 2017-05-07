<?php
# Copyright 2015-2017 Olli Helin
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

?>
