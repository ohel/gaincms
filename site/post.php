<?php
# Copyright 2015-2017 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

if (count(get_included_files()) == 1) { exit("Direct access not permitted."); }

$blog_url = $page_meta[0];
$blog_dir = $page_meta[1];

# This page requires a valid subpage.
if (count($url_elements) != 1 || !file_exists(DIR_SITE . $blog_dir . $url_elements[0])) {
    require DIR_SITE . 'error.php';
    exit();
}

$post_dir = $url_elements[0];
$postpath = DIR_SITE . $blog_dir . $post_dir . "/";

include(DIR_INCLUDE . "/ExtParsedown.php");
$Parsedown = new ExtParsedown();
$intro_contents = file_get_contents($postpath . "intro.md");
$parsed_intro = $Parsedown->setLocalPath($postpath)->text($intro_contents);

# Use headers as title, and rest of the intro as description.
$description_start = strpos($parsed_intro, "<p>");
$pre_description = substr($parsed_intro, 0, $description_start);
preg_match("/<h1>(?P<main>.*)<\/h1>[^<]*(<h2>(?P<sub>.*)<\/h2>)?/", $pre_description, $titles);
$og_data = array();
$og_data["og:url"] = CONFIG_URL_BASE . "/" . $blog_dir . $post_dir;
$og_data["og:type"] = "article";
$og_data["og:title"] = $titles["main"] . (isset($titles["sub"]) ? (": " . $titles["sub"]) : "");
$og_data["og:description"] = strip_tags(substr($parsed_intro, $description_start));
if (file_exists($postpath . "og_image.jpg")) {
    $og_data["og:image"] = $postpath . "og_image.jpg";
}
$page_title = CONFIG_TITLE . " - " . $og_data["og:title"];

array_push($extra_styles, "post");
include(DIR_INCLUDE . "/header.php");
include(DIR_INCLUDE . "/PostUtils.php");

$stats_dir = $blog_url . $post_dir;
?>

<div class="container">

    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <?php
                $postdate = PostUtils\dateFromPath($postpath);
                $posttags = PostUtils\tagsStringFromPath($postpath, $blog_url);
                $contents = file_get_contents($postpath . "article.md");
                echo '<p class="postmetadata">Posted: ' . $postdate . " / Tags: " . $posttags . "</p>";
                echo "<article>" . $Parsedown->setLocalPath($postpath)->text($contents) . "</article>";
                ?>
            </div>
        </div>
    </div>

    <nav>
        <ul class="pager">
            <?php
            $posts = glob(DIR_SITE . $blog_dir . DIR_POSTS_GLOB, GLOB_ONLYDIR|GLOB_MARK);
            $i = array_search($postpath, $posts);

            echo ($i > 0) ?
                '<li><a href="' . substr($posts[$i - 1], strlen(DIR_SITE)) . '">Previous</a></li>' :
                '<li class="disabled"><a>Previous</a></li>';
            echo ($i < (count($posts) - 1)) ?
                '<li><a href="' . substr($posts[$i + 1], strlen(DIR_SITE)) . '">Next</a></li>' :
                '<li class="disabled"><a>Next</a></li>';
            ?>
        </ul>
    </nav>

    <div class="to-top-button-container"><a onclick="scrollToTop()" title="To top">▲</a></div>

    <?php
    include(DIR_INCLUDE . "/someshare.php");
    $comments_id = $post_dir;
    include(DIR_INCLUDE . "/comments.php");
    ?>

    <div class="to-top-button-container"><a onclick="scrollToTop()" title="To top">▲</a></div>

    <div class="license"><a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/80x15.png" /></a>&nbsp;This article by <?php echo CONFIG_AUTHOR?> is licensed under the <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 International License</a><footer>

</div>

<?php include(DIR_INCLUDE . "/htmlend.php");?>
