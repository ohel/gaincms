<?php
# Copyright 2015-2017, 2020, 2026 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html
?>
<!DOCTYPE html>
<!-- This site was created using GainCMS, a free software released under the
terms of the GPL-3.0 license. See more at https://github.com/ohel/gaincms -->
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    if (!isset($page_meta_description)) {
        $page_meta_description = CONFIG_AUTHOR . "'s website";
    } ?>
    <meta name="description" content="<?php echo $page_meta_description?>">
    <meta name="author" content="<?php echo CONFIG_AUTHOR?>">

    <?php
    if (!isset($page_title)) {
        $page_title = CONFIG_TITLE;
    }
    echo "<title>" . $page_title . "</title>\n";

    # Open Graph metadata.
    $og_defaults = [
        "og:url" => "/",
        "og:type" => "website",
        "og:title" => $page_title,
        "og:description" => $page_meta_description,
    ];
    $og_array = [];
    if (is_array($og_data)) {
        $og_array = $og_data;
    }
    foreach ($og_defaults as $key => $value) {
        if (!isset($og_array[$key])) {
            $og_array[$key] = $value;
        }
    }
    if (!isset($og_array["og:image"])) {
        if (file_exists(DIR_SITE . "graphics/og_image.jpg")) {
            $og_array["og:image"] = DIR_SITE . "graphics/og_image.jpg";
        } elseif (file_exists(DIR_SITE . "graphics/og_image.png")) {
            $og_array["og:image"] = DIR_SITE . "graphics/og_image.png";
        }
    }

    foreach ($og_array as $key => $value) {
        # Note: assumes relative URLs for images.
        if ($key === "og:url" || $key === "og:image") {
            $value = CONFIG_URL_BASE . "/" . ltrim($value, "/");
        }

        echo '<meta property="' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') .
            '" content="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . "\" />\n";
    }
    echo '<meta property="og:site_name" content="' . htmlspecialchars(CONFIG_TITLE, ENT_QUOTES, 'UTF-8') . "\" />\n";
    ?>

    <base href="<?php echo CONFIG_URL_BASE?>">
    <link rel="icon" href="<?php echo DIR_SITE?>graphics/favicon.ico">

    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo DIR_SITE?>css/common.css">
    <link rel="stylesheet" type="text/css" href="<?php echo DIR_SITE?>css/nav.css">

    <?php if (is_array($extra_styles)) {
        foreach ($extra_styles as $style) {
            echo '<link rel="stylesheet" type="text/css" href="' . DIR_SITE . 'css/' . $style . '.css">';
        }
    } ?>
</head>
<body>

<nav aria-label="main-nav" class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href=""><?php echo CONFIG_TITLE?></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="blogs" onclick="$('#blogs-submenu').toggleClass('navbar-submenu-invisible'); return false;">Blogs <span class="caret"></span></a>
                    <ul id="blogs-submenu" class="nav navbar-nav navbar-submenu navbar-submenu-invisible">
                        <li><a href="blog">Blog</a></li>
                        <li><a href="blog2">Another blog</a></li>
                    </ul>
                </li>
                <li><a href="projects">Projects</a></li>
                <li><a href="about">About</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="background background-glow"></div>
