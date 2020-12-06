<?php
# Copyright 2018-2020 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

namespace BlogUpdates;

require_once DIR_INCLUDE . "/PostUtils.php";

function getPostUpdates($postpath, $bare = False) {

    $returnstring = "<div class=\"blog-updates\">";
    $updates = array_reverse(glob($postpath . DIR_UPDATE_PREFIX . "*"));
    if ((count($updates) > 0)) {
        $returnstring .= $bare ? "Updates:" : "<h2>Updates</h2>";
    }
    $returnstring .= "<table class=\"custom-padding\">";

    foreach ($updates as $update) {
        $returnstring .= "<tr><td>";
        $returnstring .= \PostUtils\dateFromPath($update, strlen(DIR_UPDATE_PREFIX));
        $returnstring .= '</td><td class="post-update-description">';
        $returnstring .= file_get_contents($update);
        $returnstring .= "</td></tr>";
    }

    $returnstring .= "</table></div>";
    return $returnstring;

}

class BlogUpdate
{
    private $_date;
    private $_title;
    private $_path;
    private $_changelog;
    private $_last_update;
    private $_order;

    function __construct($postpath, $order) {

        $this->_path = $postpath;
        $this->_order = $order;
        $updates = glob($postpath . DIR_UPDATE_PREFIX . "*");
        $this->_last_update = end($updates);
        $this->_date = $this->_last_update === False ?
            \PostUtils\dateFromPath($postpath) :
            \PostUtils\dateFromPath($this->_last_update, strlen(DIR_UPDATE_PREFIX));
    }

    function readInfo() {
        if ($this->_last_update === False) {
            $this->_changelog = CONFIG_DEFAULT_CHANGELOG;
        } else {
            $this->_changelog = file_get_contents($this->_last_update);
        }
        if (file_exists($this->_path . "intro.md")) {
            # Trim out Markdown header.
            $this->_title = trim(fgets(fopen($this->_path . "intro.md", 'r')), " #\n\r");
        }
    }

    function listInfo() {
        $url = rtrim(substr($this->_path, strlen(DIR_SITE)), "/");
        return "<tr><td>" . $this->_date .
            "</td><td><a href=\"" . $url . "\">" . $this->_title .
            "</a>: " . $this->_changelog . "</td>";
    }

    static function sortBlogUpdate($a, $b) {
        if ($a->_date < $b->_date) {
            return 1;
        }
        if ($b->_date < $a->_date) {
            return -1;
        }
        return ($a->_order > $b->_order) ? 1 : -1;
    }

}

# List blog updates. Give one or more blog post directories (in priority order) and the maximum number of updates to fetch.
function listBlogUpdates($blog_paths, $max_updates) {

    $updates = array();
    $order_solver = 0;
    if (!is_array($blog_paths)) {
        $blog_paths = array($blog_paths);
    }
    foreach ($blog_paths as $path) {
        $order_solver += $max_updates;
        $posts = \PostUtils\getPostsByPath($path);
        for ($i = 0; $i < count($posts); $i++) {
            $updates[] = new BlogUpdate($posts[$i], $i + $order_solver);
        }
    }
    usort($updates, array("BlogUpdates\BlogUpdate", "sortBlogUpdate"));
    $updates = array_slice($updates, 0, $max_updates);?>

    <div class="blog-updates">
        <hr>
        <h1>Latest blog updates</h1>
        <table class="custom-padding">
        <?php
        foreach ($updates as $update) {
            $update->readInfo();
            echo $update->listInfo();
        }?>
        </table>
        <hr>
    </div>
    <?php
}

?>
