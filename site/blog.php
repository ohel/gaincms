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

$page_title = CONFIG_AUTHOR . "'s website - Blog";
include(DIR_INCLUDE . "/header.php");
include(DIR_INCLUDE . "/ExtParsedown.php");
include(DIR_INCLUDE . "/PostUtils.php");

echo '<link rel="stylesheet" property="stylesheet" type="text/css" href="' . DIR_SITE . 'css/blog.css">';

$blog_url = $page_meta[0];
$blog_dir = $page_meta[1];
$blog_title = $page_meta[2];
$blog_description = $page_meta[3];
?>

<header>
    <h1><?php echo $blog_title?></h1>
    <h2><?php echo $blog_description?></h2>
</header>

<div class="container">
    <div class="row">

        <div class="col-sm-6 col-sm-offset-2 col-md-6 col-md-offset-2 col-lg-6 col-lg-offset-2 postlisting">

            <?php

            $Parsedown = new ExtParsedown();
            $posts = array_reverse(glob(DIR_SITE . $blog_dir . DIR_POSTS_GLOB, GLOB_ONLYDIR|GLOB_MARK));
            if (isset($filter)) {
                $postcount = count($posts);
                for ($i = 0; $i < $postcount; $i++) {
                    if (!file_exists($posts[$i] . "tag_" . $filter)) {
                        unset($posts[$i]);
                    }
                }
                $posts = array_values($posts);
                echo '<p>Filtering by tag: ' . str_replace('_', ' ', $filter) . "</p>";
            }

            for ($i = (($page - 1) * CONFIG_PAGINATION); $i < (min($page * CONFIG_PAGINATION, count($posts))); $i++) {
                $postpath = $posts[$i];
                $postdate = PostUtils\dateFromPath($postpath);
                $posttags = PostUtils\tagsStringFromPath($postpath, $blog_url);
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

        <div class="col-sm-4 col-md-3 col-lg-3">

            <div class="well">
                <h4>Filter by tag</h4>
                <ul class="list-unstyled">
                    <?php
                    $tags = PostUtils\tagsFromPath(DIR_SITE . "tags_" . $blog_dir, $blog_url);
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

