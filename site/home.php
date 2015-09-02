<?php 
if (count(get_included_files()) == 1) { exit("Direct access not permitted."); }

include(DIR_INCLUDE . "/header.php")
?>

<header>
    <h1><?php echo CONFIG_TITLE?></h1>
    <h2><?php echo CONFIG_AUTHOR?>'s website</h2>
<header>

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

<section>

<?php include(DIR_INCLUDE . "/footer.php")?>

