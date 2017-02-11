<?php
# Copyright 2015-2017 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

if (count(get_included_files()) == 1) { exit("Direct access not permitted."); }

# This page does not have any subpages.
if (!empty($url_elements)) {
    require DIR_SITE . 'error.php';
    exit();
}

$page_title = CONFIG_AUTHOR . "'s website - About";
include(DIR_INCLUDE . "/header.php");

$stats_dir = "about";
?>

<div class="container">

    <header>
        <h1>About</h1>
    </header>

    <div class="container">
        <div class="row">

            <div class="col-sm-8 col-sm-offset-2">

                <h2>About me</h2>
                <p>Coming soon...</p>

                <h2>My social network sites</h2>
                <p>Coming soon...</p>

                <h2>About this website</h2>
                <p>Coming soon...</p>

            </div>

        </div>
    </div>

    <?php include(DIR_INCLUDE . "/poweredby.php");?>

</div>

<?php include(DIR_INCLUDE . "/htmlend.php");?>
