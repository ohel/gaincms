<?php
# Copyright 2020 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

namespace BlogSearch;

require_once DIR_INCLUDE . "/PostUtils.php";

function base64urlDecode($b64urlstring, $strict = false)
{
    $b64string = strtr($b64urlstring, "-_", "+/");
    $pad = strlen($b64string) % 4;
    if ($pad) {
        $b64string = str_pad($b64string, 4 - $pad, "=");
    }

    return base64_decode($b64string, $strict);
}

# Given post paths and base64url-encoded search text, search intro
# texts and articles leaving only those posts that match the search.
function filterPostsBySearch($posts, $search_b64url) {
    $search = base64urlDecode($search_b64url);
    $postcount = count($posts);

    for ($i = 0; $i < $postcount; $i++) {
        $intro = file_get_contents($posts[$i] . "intro.md");
        $article = file_get_contents($posts[$i] . "article.md");
        if (!preg_match("/" . str_replace("/", "\/", $search) . "/i",
            $intro . "\n" . $article)) {
            unset($posts[$i]);
        }
    }
    return(array_values($posts));
}

function renderSearchTools() {

    ?>
    <noscript><div style="display:none"></noscript>
    <div class="search-tools">
        <p>Regex search:</p>
        <input id="searchbox" type="text"/>
        <button title="Search" onclick="return doSearch()">🔍</button>
    </div>
    <script>
        function base64urlEncode(str) {
            return btoa(str).replace(/\+/g, "-").replace(/\//g, "_").replace(/=/g, "");
        }

        function doSearch() {
            const searchbox = document.getElementById("searchbox");
            if (!!searchbox.value) {
                const blog_path = window.location.pathname.split("/")[1]; // <host>/<blog>
                location = blog_path + "/search/" + base64urlEncode(searchbox.value);
            }
        }

        const searchbox = document.getElementById("searchbox");
        searchbox.addEventListener("keypress", function(event) {
            (event.keyCode == 13) && doSearch();
        });
    </script>
    <noscript></div></noscript>
    <?php
}

?>
