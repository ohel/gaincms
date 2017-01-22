<?php
# Copyright 2015-2017 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

# Share buttons generated using https://simplesharingbuttons.com/

if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] == 1) ||
    isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https") {
    $protocol = "https://";
}
else {
    $protocol = "http://";
}
$url = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
?>

<div class="share-buttons">
    <ul class="share-buttons">
        <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url?>&amp;t=" title="Share on Facebook" target="_blank"><img alt="Facebook" src="<?php echo DIR_SITE?>graphics/icon_facebook.svg" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(document.URL) + '&t=' + encodeURIComponent(document.URL)); return false;"></a></li>
        <li><a href="https://twitter.com/intent/tweet?source=<?php echo $url?>&amp;text=:%20<?php echo $url?>" target="_blank" title="Tweet"><img alt="Twitter" src="<?php echo DIR_SITE?>graphics/icon_twitter.svg" onclick="window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(document.title) + ':%20' + encodeURIComponent(document.URL)); return false;"></a></li>
        <li><a href="http://wordpress.com/press-this.php?u=<?php echo $url?>&amp;t=&amp;s=" target="_blank" title="Publish on WordPress"><img alt="WordPress" src="<?php echo DIR_SITE?>graphics/icon_wordpress.svg" onclick="window.open('http://wordpress.com/press-this.php?u=' + encodeURIComponent(document.URL) + '&t=' + encodeURIComponent(document.title)); return false;"></a></li>
        <li><a href="mailto:?subject=&amp;body=<?php echo $url?>" target="_blank" title="Email"><img alt="Email" src="<?php echo DIR_SITE?>graphics/icon_email.svg" onclick="window.open('mailto:?subject=' + encodeURIComponent(document.title) + '&body=' +  encodeURIComponent(document.URL)); return false;"></a></li>
    </ul>
</div>

