<?php
if (count(get_included_files()) == 1) { exit("Direct access not permitted."); }

# This page does not have any subpages.
if (count($url_elements) > 1) {
    require DIR_SITE . 'error.php';
    exit();
}
include(DIR_INCLUDE . "/header.php")
?>

<div class="mainheader">
    <h1>About</h1><span></span>
</div>

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

<?php
$activepage = "about";
include(DIR_INCLUDE . "/footer.php");
?>
