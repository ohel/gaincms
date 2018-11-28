<?php
# Copyright 2018 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

# Visitor statistics logging. Only log if not a LAN or ignored IP.
$write_stats = file_exists(DIR_STATS_BASE) &&
    isset($stats_dir) &&
    isset($_SERVER["REMOTE_ADDR"]) &&
    strpos($_SERVER["REMOTE_ADDR"], "10.") !== 0 &&
    strpos($_SERVER["REMOTE_ADDR"], "192.") !== 0 &&
    (!file_exists(CONFIG_STATS_IP_IGNORE_FILE) ||
    strpos(file_get_contents(CONFIG_STATS_IP_IGNORE_FILE), $_SERVER["REMOTE_ADDR"]) === false);

if ($write_stats) {

    if (!file_exists(DIR_STATS_BASE . $stats_dir)) {
        mkdir(DIR_STATS_BASE . $stats_dir, 0755, true);
    }

    $stats_file = fopen(DIR_STATS_BASE . $stats_dir . "/" . $_SERVER["REMOTE_ADDR"], "a");
    # Write only a maximum of 200 characters for the user agent.
    fwrite($stats_file,
        date("Y-m-d H:i:s") .
        " \"" . (isset($_SERVER["HTTP_USER_AGENT"]) ? substr($_SERVER["HTTP_USER_AGENT"], 0, 200) : "Unknown") . "\"" .
        (isset($_SERVER["HTTP_REFERER"]) ? " " . $_SERVER["HTTP_REFERER"] : "") . "\n");
    fclose($stats_file);

}

?>
