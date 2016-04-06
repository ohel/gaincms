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

    $tags = array_map(function ($tagpath) { return basename($tagpath); }, glob($path . DIR_TAG_PREFIX . "*"));
    return implode(", ", filterLinksFromTags($tags, $href_prefix));

}

# Creates hyperlinks from tags for filtering. Converts underscores to spaces visually.
function filterLinksFromTags($tags, $href_prefix) {

    return array_map(function ($tag) use ($href_prefix) {
            $tag = substr($tag, strlen(DIR_TAG_PREFIX));
            return '<a href="' . $href_prefix . 'tags/' . $tag . '">' . str_replace('_', ' ', $tag) . "</a>";
        }, $tags);

}

?>
