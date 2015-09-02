<?php
if (count(get_included_files()) == 1) { exit("Direct access not permitted."); }

# This page does not have any subpages.
if (count($url_elements) > 1) {
    require DIR_SITE . 'error.php';
    exit();
}
include(DIR_INCLUDE . "/header.php");
include(DIR_INCLUDE . "/ExtParsedown.php");
include(DIR_INCLUDE . "/PostUtils.php");

echo '<link rel="stylesheet" href="' . DIR_SITE . 'css/post.css">';
?>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
            <?php
            $comments_id = $url_elements[0];
            $postpath = DIR_SITE . "posts/" . $url_elements[0] . "/";
            $postdate = PostUtils\dateFromPath($postpath);
            $posttags = PostUtils\tagsFromPath($postpath);
            $contents = file_get_contents($postpath . "article.md");

            echo '<p class="postmetadata">Posted: ' . $postdate . " / Tags: " . $posttags . "</p>";

            $Parsedown = new ExtParsedown();
            echo $Parsedown->setLocalPath($postpath)->text($contents);
            ?>
        </div>
    </div>
</div>

<nav>
    <ul class="pager">
        <?php
        $posts = glob(DIR_SITE . "posts/*", GLOB_ONLYDIR|GLOB_MARK);
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
include(DIR_INCLUDE . "/comments.php");
?>

<div class="to-top-button-container"><a onclick="scrollToTop()" title="To top">▲</a></div>

<div class="footnote"><a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/80x15.png" /></a>&nbsp;This article</span> by <span xmlns:cc="http://creativecommons.org/ns#" property="cc:attributionName"><?php echo CONFIG_AUTHOR?></span> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 International License</a></div>

<?php
$activepage = "blog";
include(DIR_INCLUDE . "/footer.php");
?>

