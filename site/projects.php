<?php
# Copyright 2015-2018 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

if (count(get_included_files()) == 1) { exit("Direct access is not permitted."); }

# This page does not have any subpages.
if (!empty($url_elements)) {
    require DIR_SITE . 'error.php';
    exit();
}

$page_title = CONFIG_TITLE . " - Projects";
$page_meta_description = "Information about various projects made by " . CONFIG_AUTHOR . ", the author of the website.";
require DIR_INCLUDE . "/header.php";

$stats_dir = "projects";
?>

<div class="container">

    <header>
        <h1>Projects</h1>
        <h2>Various projects of mine</h2>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <h2>GitHub</h2>
                <div id="github-projects"><noscript>Enable JavaScript to see listing of <a href="https://github.com/<?php echo CONFIG_GITHUB_USER?>/">my repos.</a></noscript></div>
            </div>
        </div>
    </div>

    <?php require DIR_INCLUDE . "/poweredby.php";?>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo DIR_INCLUDE?>jquery.min.js">\x3C/script>')</script>
<script>
    $("#github-projects").html('<span>Querying <a href="https://github.com/<?php echo CONFIG_GITHUB_USER?>/">my GitHub repositories</a>...</span>');
    $.getJSON("https://api.github.com/users/<?php echo CONFIG_GITHUB_USER?>/repos", function(data) {
        var repos = [];
        for (var i = 0; i < data.length; i++) {
            repos.push('<li><a href="' + data[i].svn_url + '">' + data[i].name + "</a>: " + data[i].description + "</li>");
        };
        $("#github-projects").html($("<ul/>", {"class": "repo-listing", html: repos.join("")}));
    });
</script>

<?php require DIR_INCLUDE . "/htmlend.php";?>
