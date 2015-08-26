<?php

# Site configuration and paths.
define("CONFIG_URL_BASE", "http://10.0.1.2");
define("CONFIG_AUTHOR", "Site Author");
define("CONFIG_TITLE", "Site Title");
define("CONFIG_URL_DISQUS", "mysite.disqus.com");
define("CONFIG_GITHUB_USER", "user");
define("CONFIG_URL_SOME", "http%3A%2F%2Fwww.mysite.com");
define("DIR_SITE", "site/");
define("DIR_INCLUDE", "site/includes/");
define("DIR_FILES", "site/files/");

$path = ltrim($_SERVER["REQUEST_URI"], "/");
$url_elements = explode('/', $path);

# Remove trailing slashes from url elements.
$lastelem = end($url_elements);
while (empty($lastelem)) {
    array_pop($url_elements);
    $lastelem = empty($url_elements) ? "last" : end($url_elements);
}

# Routing.
if (empty($url_elements)) {

    $page = "home";

} else switch(array_shift($url_elements)) {

    case "blog":
        $page = "blog";
        break;
    case "posts":
        $page = "post";
        break;
    case "projects":
        $page = "projects";
        break;
    case "about":
        $page = "about";
        break;
    default:
        $page = "error";
}

require DIR_SITE . $page . ".php";

?>
