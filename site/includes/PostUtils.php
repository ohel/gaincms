<?php
namespace PostUtils;

function dateFromPath($postpath) {

    $pathelements = explode("/", $postpath);
    $lastelement = end($pathelements);
    if (empty($lastelement)) {
        array_pop($pathelements);
    }

    return substr(end($pathelements), 0, 10); # For example: 2015-01-30

}

function tagsStringFromPath($path) {

    return implode(", ", tagsFromPath($path));

}

function tagsFromPath($path) {

    return array_map(function ($p) {
            $tag = substr(basename($p), 4); # tag_
            return '<a href="blog/tags/' . $tag . '">' . str_replace('_', ' ', $tag) . "</a>";
        }, glob($path . "tag_*"));

}

?>
