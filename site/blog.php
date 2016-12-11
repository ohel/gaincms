<?php
if (count(get_included_files()) == 1) { exit("Direct access not permitted."); }

if (!empty($url_elements) && $url_elements[0] == "tags") {
    array_shift($url_elements);
    if (!empty($url_elements)) {
        $filter = $url_elements[0];
    }
    array_shift($url_elements);
}

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

$og_data = array();
$og_data["og:url"] = CONFIG_URL_BASE . "/" . preg_replace('%/$%', '', $blog_url);
$og_data["og:type"] = "blog";
$og_data["og:title"] = $blog_title;
$og_data["og:description"] = $blog_description;

array_push($extra_styles, "blog");
include(DIR_INCLUDE . "/header.php");
include(DIR_INCLUDE . "/ExtParsedown.php");
include(DIR_INCLUDE . "/PostUtils.php");
?>

<header>
    <h1><?php echo $blog_title?></h1>
    <h2><?php echo $blog_description?></h2>
</header>

<div class="container">
    <div class="row">

        <div class="col-sm-7 col-sm-offset-1 col-lg-6 col-lg-offset-2 postlisting">

            <?php

            $Parsedown = new ExtParsedown();
            $posts = array_reverse(glob(DIR_SITE . $blog_dir . DIR_POSTS_GLOB, GLOB_ONLYDIR|GLOB_MARK));
            $postcount = count($posts);

            # TODO: optimize tag globbing. This method might be slow if there are hundreds of blog posts and a lot of readers.
            $tags = array();
            for ($i = 0; $i < $postcount; $i++) {
                # Create an array of all the tags there exists.
                $tags = array_merge($tags, array_map(function($tagpath) { return basename($tagpath); }, glob($posts[$i] . DIR_TAG_PREFIX . "*", GLOB_NOSORT)));

                # Filter if post doesn't have matching tag.
                if (isset($filter) && !file_exists($posts[$i] . DIR_TAG_PREFIX . $filter)) {
                    unset($posts[$i]);
                }
            }

            $tags = array_unique($tags);
            asort($tags);

            if (isset($filter)) {
                $posts = array_values($posts);
                echo '<p>Filtering by tag: ' . str_replace('_', ' ', $filter) . "</p>";
            }

            for ($i = (($page - 1) * CONFIG_PAGINATION); $i < (min($page * CONFIG_PAGINATION, count($posts))); $i++) {
                $postpath = $posts[$i];
                $postdate = PostUtils\dateFromPath($postpath);
                $posttags = PostUtils\tagsStringFromPath($postpath, $blog_url);
                if (!file_exists($postpath . "intro.md"))
                    continue; # Intro does not exist so nothing to show. Note: this is a user error.

                $contents = file_get_contents($postpath . "intro.md");
                # Remove first path part and last slash.
                $hrefpath = implode("/", array_slice(explode("/", $postpath), 1, -1)); 
                # Add link to article and post metadata by regexp replacing.
                echo "<article>" .
                    preg_replace("/<h1>(.*)<\/h1>(\n<h2>.*<\/h2>)?/", '<h1><a href="' . $hrefpath . '">$1</a></h1>$2' .
                    '<p class="postmetadata">Posted: ' . $postdate . " / Tags: " . $posttags . "</p>",
                    $Parsedown->setLocalPath($postpath)->text($contents), 1) .
                    "</article>";
            }
            ?>

            <nav class="navbuttoncontainer">
                <ul class="pagination">
                    <?php
                        if ($page == 1) {
                            echo '<li class="disabled"><span aria-hidden="true">&laquo;</span></li>';
                        } else {
                            echo '<li><a href="' . $blog_url . (isset($filter) ? ("tags/" . $filter . "/") : "") .
                            ($page - 1) . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
                        }

                        $page_count = (int) ceil(count($posts) / CONFIG_PAGINATION);
                        for ($i = 1; $i <= $page_count; $i++) {
                            if ($i == $page) {
                                echo '<li class="active"><a>' . $i . '<span class="sr-only">(current)</span></a></li>';
                            } else {
                                echo '<li><a href="' . $blog_url . (isset($filter) ? ("tags/" . $filter . "/") : "") . $i . '">' . $i . '</a></li>';
                            }
                        }

                        if ($page == $page_count) {
                            echo '<li class="disabled"><span aria-hidden="true">&raquo;</span></li>';
                        } else {
                            echo '<li><a href="' . $blog_url . (isset($filter) ? ("tags/" . $filter . "/") : "") .
                            ($page + 1) . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
                        }
                    ?>
                </ul>
            </nav>

        </div> <!-- col -->

        <div class="col-sm-4 col-md-3">

            <div class="well">
                <h4>Filter by tag</h4>
                <ul class="list-unstyled">
                    <?php
                    $tags = PostUtils\filterLinksFromTags($tags, $blog_url);
                    foreach ($tags as $tag) {
                        echo "<li>" . $tag . "</li>";
                    }
                    ?>
                </ul>
            </div>

        </div> <!-- col -->

    </div> <!-- row -->
</div> <!-- container -->

<?php include(DIR_INCLUDE . "/footer.php")?>

