<?php
# Copyright 2020 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

namespace BlogSearch;

require_once DIR_INCLUDE . "/PostUtils.php";

function base64urlDecodeUnicode($b64urlstring, $strict = false)
{
    $b64string = strtr($b64urlstring, "-_", "+/");
    $pad = strlen($b64string) % 4;
    if ($pad) {
        $b64string = str_pad($b64string, strlen($b64string) + 4 - $pad, "=");
    }
    return htmlspecialchars_decode(base64_decode($b64string, $strict));
}

# Given a base64url-encoded unicode string, decode it and check if it is "reasonable":
# strip away the regex delimiter and check the number of capturing groups etc.
# Returns false if search string is not "reasonable enough".
function getUsableSearchString($b64urlstring)
{
    $s = base64urlDecodeUnicode($b64urlstring);
    $s = str_replace(CONFIG_REGEX_DELIMITER, "", $s);
    return (substr_count($s, "*") < 4 &&
        min(substr_count($s, "{"), substr_count($s, "}")) < 4 &&
        min(substr_count($s, "("), substr_count($s, ")")) < 6) ? $s : false;
}

# Given post paths and search string, search intro
# texts and articles leaving only those posts that match the search.
function filterPostsBySearch($posts, $search) {
    $postcount = count($posts);

    for ($i = 0; $i < $postcount; $i++) {
        $intro = file_get_contents($posts[$i] . "intro.md");
        $article = file_get_contents($posts[$i] . "article.md");
        if (!preg_match(CONFIG_REGEX_DELIMITER . $search . CONFIG_REGEX_DELIMITER . "i",
            $intro . "\n" . $article)) {
            unset($posts[$i]);
        }
    }
    return array_values($posts);
}

function renderSearchTools() {

    ?>
    <noscript><div style="display:none"></noscript>
    <div class="search-tools">
        <p id="searchlabel">Regex search:</p>
        <input id="searchbox" type="text" class="monospaced" aria-labelledby="searchlabel"/>
        <button title="Search" onclick="return doSearch()">🔍</button>
    </div>
    <script>
        function base64urlEncodeUnicode(str) {
            // Need to do some encoding, otherwise decoding in PHP won't work properly for all characters.
            return btoa(encodeURIComponent(str)
                .replace(/%([0-9A-F]{2})/g, (match, hex) => { return String.fromCharCode('0x' + hex); }))
                .replace(/\+/g, "-").replace(/\//g, "_").replace(/=/g, "");
        }

        function doSearch() {
            const searchbox = document.getElementById("searchbox");
            if (!!searchbox.value) {
                const blog_path = window.location.pathname.split("/")[1]; // <host>/<blog>
                location = blog_path + "/search/" + base64urlEncodeUnicode(searchbox.value);
            }
        }

        const searchbox = document.getElementById("searchbox");
        searchbox.addEventListener("keypress", function(event) {
            // Disable regex delimiter input as it'll be stripped out later.
            (event.keyCode === "<?php echo CONFIG_REGEX_DELIMITER?>".charCodeAt(0)) && event.preventDefault();
            (event.keyCode === 13) && doSearch();
        });
    </script>
    <noscript></div></noscript>
    <?php
}

?>
