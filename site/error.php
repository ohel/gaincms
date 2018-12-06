<?php
# Copyright 2015-2018 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

if (count(get_included_files()) == 1) { exit("Direct access is not permitted."); }

header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
require DIR_INCLUDE . "/header.php";
?>

<div class="container">

    <header class="spaced">
        <h1>Error</h1>
        <h2>The resource was not found</h2>
    </header>

    <?php require DIR_INCLUDE . "/poweredby.php";?>

</div>

<?php require DIR_INCLUDE . "/htmlend.php";?>
