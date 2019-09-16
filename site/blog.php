<?php
# Copyright 2015-2019 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

if (count(get_included_files()) == 1) { exit("Direct access is not permitted."); }

if (!empty($url_elements) && $url_elements[0] == "tags") {
    array_shift($url_elements);
    if (!empty($url_elements)) {
        $filter = $url_elements[0];
    }
    array_shift($url_elements);
}

# Default to page 1 on listings if not given in the URL.
$page = 1;
if (!empty($url_elements) && is_numeric($url_elements[0])) {
    $page = $url_elements[0];
    array_shift($url_elements);
}

if (!empty($url_elements)) {
    require DIR_SITE . 'error.php';
    exit();
}

$blog_url = $page_meta[0];
$blog_dir = $page_meta[1];
$blog_title = $page_meta[2];
$blog_description = $page_meta[3];
$page_title = CONFIG_TITLE . " - " . $blog_title;
$page_meta_description = $blog_title . " - " . $blog_description;

$og_data = array();
$og_data["og:url"] = preg_replace('%/$%', '', $blog_url);
$og_data["og:type"] = "blog";
$og_data["og:title"] = $blog_title;
$og_data["og:description"] = $blog_description;

array_push($extra_styles, "blog");
require DIR_INCLUDE . "/header.php";
require DIR_INCLUDE . "/PostUtils.php";
require DIR_INCLUDE . "/BlogUpdates.php";

$stats_dir = $blog_url;
?>

<div class="container">

    <header>
        <h1><?php echo $blog_title?></h1>
        <h2><?php echo $blog_description?></h2>
    </header>

    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-lg-8 col-lg-offset-2">
            <?php BlogUpdates\listBlogUpdates($blog_dir, 5);?>
        </div>
    </div>

    <div class="row">

        <div class="col-sm-7 col-sm-offset-1 col-lg-6 col-lg-offset-2 postlisting">
            <?php
            $posts = PostUtils\getPostsByPath($blog_dir);
            $postcount = count($posts);

            # Get all tags and filter posts by tag. This method of globbing all tags might be slow if there are hundreds of blog posts and a lot of readers.
            $tags = array();
            for ($i = 0; $i < $postcount; $i++) {
                $tags = array_merge($tags, array_map(function($tagpath) { return basename($tagpath); }, glob($posts[$i] . DIR_TAG_PREFIX . "*", GLOB_NOSORT)));
                if (isset($filter) && !file_exists($posts[$i] . DIR_TAG_PREFIX . $filter)) {
                    unset($posts[$i]);
                }
            }
            asort($tags);
            $tags = PostUtils\filterLinksFromTags(array_unique($tags), $blog_url);

            if (isset($filter)) {
                $posts = array_values($posts);
                echo '<p>Filtering by tag: ' . str_replace('_', ' ', $filter) . "</p>";
            }

            for ($i = (($page - 1) * CONFIG_PAGINATION); $i < (min($page * CONFIG_PAGINATION, count($posts))); $i++) {
                $postpath = $posts[$i];
                # If intro does not exist there is nothing to show. Note: this is a user error.
                if (!file_exists($postpath . "intro.md")) {
                    continue;
                }

                echo "<article>";
                echo PostUtils\getPostIntro($postpath, $blog_url);
                echo BlogUpdates\getPostUpdates($postpath);
                echo "</article>";
            } ?>

            <nav class="navbuttoncontainer">
                <ul class="pagination">
                    <?php
                    if ($page == 1) {
                        echo '<li class="disabled"><span aria-hidden="true">&laquo;</span></li>';
                    } else {
                        # This forces canonical unique URLs so that the first page /1 is never in a link.
                        $nav_url = $blog_url . (isset($filter) ? ("tags/" . $filter . "/") : "");
                        if ($page != 2) {
                            $nav_url .= ($page - 1);
                        }
                        $nav_url = rtrim($nav_url, "/");

                        echo '<li><a href="' . $nav_url . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
                    }

                    $page_count = (int) ceil(count($posts) / CONFIG_PAGINATION);
                    for ($i = 1; $i <= $page_count; $i++) {
                        if ($i == $page) {
                            echo '<li class="active"><a>' . $i . '<span class="sr-only">(current)</span></a></li>';
                        } else {
                            # This forces canonical unique URLs so that the first page /1 is never in a link.
                            $nav_url = $blog_url . (isset($filter) ? ("tags/" . $filter . "/") : "");
                            if ($i != 1) {
                                $nav_url .= $i;
                            }
                            $nav_url = rtrim($nav_url, "/");

                            echo '<li><a href="' . $nav_url . '">' . $i . '</a></li>';
                        }
                    }

                    if ($page == $page_count) {
                        echo '<li class="disabled"><span aria-hidden="true">&raquo;</span></li>';
                    } else {
                        echo '<li><a href="' . $blog_url . (isset($filter) ? ("tags/" . $filter . "/") : "") .
                        ($page + 1) . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
                    } ?>
                </ul>
            </nav>
        </div>

        <div class="col-sm-3 col-lg-2 top-sticky">
            <div class="well">
                <h4>Filter by tag</h4>
                <ul class="list-unstyled">
                    <?php
                    foreach ($tags as $tag) {
                        echo "<li>" . $tag . "</li>";
                    } ?>
                </ul>
            </div>
        </div>

    </div>

    <?php require DIR_INCLUDE . "/poweredby.php";?>

</div>

<?php require DIR_INCLUDE . "/htmlend.php";?>
