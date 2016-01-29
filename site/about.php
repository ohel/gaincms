<?php
if (count(get_included_files()) == 1) { exit("Direct access not permitted."); }

# This page does not have any subpages.
if (!empty($url_elements)) {
    require DIR_SITE . 'error.php';
    exit();
}

$page_title = CONFIG_AUTHOR . "'s website - About";
include(DIR_INCLUDE . "/header.php")
?>

<header>
    <h1>About</h1>
</header>

<div class="container">
    <div class="row">

        <div class="col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">

            <h2>About me</h2>
            <p>Coming soon...</p>

            <h2>My social network sites</h2>
            <p>Coming soon...</p>

            <h2>About this website</h2>
            <p>Coming soon...</p>

        </div> <!-- col -->

    </div> <!-- row -->
</div> <!-- container -->

<?php include(DIR_INCLUDE . "/footer.php")?>

