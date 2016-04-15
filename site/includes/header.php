<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="<?php echo CONFIG_AUTHOR?>'s Website">
    <meta name="author" content="<?php echo CONFIG_AUTHOR?>">

    <base href="<?php echo CONFIG_URL_BASE?>">
    <link rel="icon" href="<?php echo DIR_SITE?>graphics/favicon.ico">

    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo DIR_SITE?>css/common.css">

    <?php
    if (is_array($extra_styles)) {
        foreach ($extra_styles as $style) {
            echo '<link rel="stylesheet" type="text/css" href="' . DIR_SITE . 'css/' . $style . '.css">';
        }
    }

    if (isset($page_title)) {
        echo "<title>" . $page_title . "</title>";
    } else {
        echo "<title>" . CONFIG_AUTHOR . "'s website</title>";
    }
    ?>
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
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

<div class="container">
