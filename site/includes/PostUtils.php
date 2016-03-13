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

function tagsStringFromPath($path, $href_prefix = "") {

    return implode(", ", tagsFromPath($path, $href_prefix));

}

function tagsFromPath($path, $href_prefix) {

    return array_map(function ($p) use ($href_prefix) {
            $tag = substr(basename($p), 4); # tag_
            return '<a href="' . $href_prefix . 'tags/' . $tag . '">' . str_replace('_', ' ', $tag) . "</a>";
        }, glob($path . "tag_*"));

}

?>
