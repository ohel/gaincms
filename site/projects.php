<?php
# Copyright 2015-2018, 2020, 2026 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

if (count(get_included_files()) == 1) { exit("Direct access is not permitted."); }

# This page does not have any subpages.
if (!empty($url_elements)) {
    require DIR_SITE . 'error.php';
    exit();
}

$page_title = "Projects | " . CONFIG_TITLE;
$page_meta_description = "Information about various projects made by " . CONFIG_AUTHOR . ", the author of the website.";
require DIR_INCLUDE . "/header.php";

$stats_dir = "projects";
?>

<main class="container">

    <header>
        <h1>Projects</h1>
        <h2>Various projects of mine</h2>
    </header>

    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <h2>GitHub</h2>
            <div id="github-projects"><noscript>Enable JavaScript to see listing of <a href="https://github.com/<?php echo CONFIG_GITHUB_USER?>/">my repos.</a></noscript></div>
        </div>
    </div>

    <?php require DIR_INCLUDE . "/poweredby.php";?>

</main>

<script>
    const e = document.getElementById("github-projects");
    e.innerHTML = '<span>Querying <a href="https://github.com/<?php echo CONFIG_GITHUB_USER?>/">my GitHub repositories</a>...</span>';

    fetch("https://api.github.com/users/<?php echo CONFIG_GITHUB_USER?>/repos")
        .then(response => response.json())
        .then(data => {
            const repos = data.map(repo => `<li><a href="${repo.svn_url}">${repo.name}</a>: ${repo.description || ""}</li>`);
            e.innerHTML = `<ul class="custom-padding">${repos.join("")}</ul>`;
        })
        .catch(error => {
            e.innerHTML = `<span>Error loading GitHub repositories.</span>`;
            console.error("GitHub API error:", error);
        });
</script>

<?php require DIR_INCLUDE . "/htmlend.php";?>
