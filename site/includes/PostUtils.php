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

function tagsFromPath($postpath) {

    return implode(", ",
        array_map(function ($p) {
            $tag = substr(basename($p), 4);
            return '<a href="blog/tags/' . $tag . '">' . $tag . "</a>";
        },
        glob($postpath . "tag_*")));

}

?>
