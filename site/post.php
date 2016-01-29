<?php
if (count(get_included_files()) == 1) { exit("Direct access not permitted."); }

# This page requires a valid subpage.
if (count($url_elements) != 1 || !file_exists(DIR_SITE . $page_meta . $url_elements[0])) {
    require DIR_SITE . 'error.php';
    exit();
}

$postpath = DIR_SITE . $page_meta . $url_elements[0] . "/";
$contents = file_get_contents($postpath . "article.md");

# Make the first line of the article the title of the page, but strip Markdown header marks first.
$page_title = CONFIG_AUTHOR . "'s blog - " . ltrim(strtok($contents, "\n"), " #");
include(DIR_INCLUDE . "/header.php");
include(DIR_INCLUDE . "/ExtParsedown.php");
include(DIR_INCLUDE . "/PostUtils.php");

echo '<link rel="stylesheet" property="stylesheet" href="' . DIR_SITE . 'css/post.css">';
?>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
            <?php
            $postdate = PostUtils\dateFromPath($postpath);
            $posttags = PostUtils\tagsStringFromPath($postpath);
            echo '<p class="postmetadata">Posted: ' . $postdate . " / Tags: " . $posttags . "</p>";

            $Parsedown = new ExtParsedown();
            echo "<article>" . $Parsedown->setLocalPath($postpath)->text($contents) . "</article>";
            ?>
        </div>
    </div>
</div>

<nav>
    <ul class="pager">
        <?php
        $posts = glob(DIR_SITE . $page_meta . DIR_POSTS_GLOB, GLOB_ONLYDIR|GLOB_MARK);
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
$comments_id = $url_elements[0];
include(DIR_INCLUDE . "/comments.php");
?>

<div class="to-top-button-container"><a onclick="scrollToTop()" title="To top">▲</a></div>

<footer><a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/80x15.png" /></a>&nbsp;This article by <?php echo CONFIG_AUTHOR?> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 International License</a><footer>

<?php include(DIR_INCLUDE . "/footer.php")?>

