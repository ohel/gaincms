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

$page_title = "About | " . CONFIG_TITLE;
$page_meta_description = "Information about " . CONFIG_AUTHOR . ", the author of the website, and the website itself.";
require DIR_INCLUDE . "/header.php";

$stats_dir = "about";
?>

<div class="container">

    <header>
        <h1>About</h1>
    </header>

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

    <?php require DIR_INCLUDE . "/poweredby.php";?>

</div>

<?php require DIR_INCLUDE . "/htmlend.php";?>
