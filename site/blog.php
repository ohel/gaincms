<?php
# Copyright 2015-2020 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

if (count(get_included_files()) == 1) { exit("Direct access is not permitted."); }

if (!empty($url_elements) && !is_numeric($url_elements[0])) {
    $url_part = $url_elements[0];
    array_shift($url_elements);
    if (!empty($url_elements)) {
        # Note: tag filter and search filter are mutually exclusive.
        $url_part == "tags" && $filter = $url_elements[0];
        $url_part == "search" && $search = $url_elements[0];
        array_shift($url_elements);
    }
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
$page_title = $blog_title . " | " . CONFIG_TITLE;
$page_meta_description = $blog_description;

$og_data = array();
$og_data["og:url"] = preg_replace('%/$%', '', $blog_url);
$og_data["og:type"] = "blog";
$og_data["og:title"] = $blog_title;
$og_data["og:description"] = $blog_description;

array_push($extra_styles, "blog");
require DIR_INCLUDE . "/header.php";
require_once DIR_INCLUDE . "/PostUtils.php";
require_once DIR_INCLUDE . "/BlogUpdates.php";
require_once DIR_INCLUDE . "/BlogSearch.php";

$stats_dir = $blog_url;
?>

<div class="container">

    <header>
        <h1><?php echo $blog_title?></h1>
        <h2><?php echo $blog_description?></h2>
    </header>

    <!-- Blog update listing. -->
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-lg-8 col-lg-offset-2">
            <?php BlogUpdates\listBlogUpdates($blog_dir, 5);?>
        </div>
    </div>

    <!-- Blog search tools. -->
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-lg-8 col-lg-offset-2">
            <?php BlogSearch\renderSearchTools();?>
        </div>
    </div>

    <!-- Filtering note. -->
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-lg-8 col-lg-offset-2">
            <div class="search-tools">
                <?php
                if (isset($filter)) {
                    echo '<p><span class="filterinfo">Filtering by tag: </span>'
                        . str_replace('_', ' ', $filter) . "</p>";
                }

                if (isset($search)) {
                    $search = BlogSearch\getUsableSearchString($search);
                    echo '<p><span class="filterinfo">';
                    if ($search) {
                        echo 'Showing articles matching regular expression: </span><span class="monospaced">' . $search . "</span></p>";
                    } else {
                        echo 'Please use a simpler search pattern.';
                        unset($search);
                    }
                }
                ?>
            </div>
        </div>

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

            # Drops values which may have been unset by tag filtering.
            $posts = array_values($posts);

            isset($search) && $posts = BlogSearch\filterPostsBySearch($posts, $search);

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
            }
            ?>

            <!-- Page selection links. -->
            <nav class="navbuttoncontainer">
                <ul class="pagination">
                    <?php
                    $nav_url_base = $blog_url;
                    isset($filter) && $nav_url_base .= "tags/" . $filter . "/";
                    isset($search) && $nav_url_base .= "search/" . $search. "/";

                    # Left arrow link.
                    if ($page == 1) {
                        echo '<li class="disabled"><span aria-hidden="true">&laquo;</span></li>';
                    } else {
                        # This forces canonical unique URLs so that the first page is never in a link (/1).
                        $nav_url = $nav_url_base;
                        $page != 2 && $nav_url .= ($page - 1);
                        $nav_url = rtrim($nav_url, "/");

                        echo '<li><a href="' . $nav_url . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
                    }

                    # Page number links.
                    $page_count = (int) ceil(count($posts) / CONFIG_PAGINATION);
                    for ($i = 1; $i <= $page_count; $i++) {
                        if ($i == $page) {
                            echo '<li class="active"><a>' . $i . '<span class="sr-only">(current)</span></a></li>';
                        } else {
                            # Again, avoid first page link (/1).
                            $nav_url = $nav_url_base;
                            $i != 1 && $nav_url .= $i;
                            $nav_url = rtrim($nav_url, "/");

                            echo '<li><a href="' . $nav_url . '">' . $i . '</a></li>';
                        }
                    }

                    # Right arrow link.
                    if ($page == $page_count || $page_count == 0) {
                        echo '<li class="disabled"><span aria-hidden="true">&raquo;</span></li>';
                    } else {
                        echo '<li><a href="' . $nav_url_base . ($page + 1) .
                            '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>

        <!-- Filter by tag. -->
        <div class="col-sm-3 col-lg-2 top-sticky">
            <div class="well">
                <h4>Filter by tag</h4>
                <ul class="list-unstyled">
                    <?php
                    foreach ($tags as $tag) {
                        echo "<li>" . $tag . "</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

    </div>

    <?php require DIR_INCLUDE . "/poweredby.php";?>

</div>

<?php require DIR_INCLUDE . "/htmlend.php";?>
