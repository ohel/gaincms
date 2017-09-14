<?php 
# Copyright 2015-2017 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

if (count(get_included_files()) == 1) { exit("Direct access is not permitted."); }

$page_meta_description = "The personal website of " . CONFIG_AUTHOR . ".";
include(DIR_INCLUDE . "/header.php");

$stats_dir = "home";
?>

<div class="container">

    <header>
        <h1><?php echo CONFIG_TITLE?></h1>
        <h2><?php echo CONFIG_AUTHOR?>'s website</h2>
    </header>

    <section>

        <p class="lead">
            Welcome to my website!
        </p>
        <p>
            Check out my tech-oriented <a href="blog">blog</a> and various <a href="projects">projects</a>.
        </p>
        <p>
            Feel free to ask or comment on the articles if you come up with something.
        </p>
        <p class="signature">
            -Author
        </p>

    </section>

    <?php include(DIR_INCLUDE . "/poweredby.php");?>

</div>

<?php include(DIR_INCLUDE . "/htmlend.php");?>
