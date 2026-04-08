<?php
# Copyright 2015-2017, 2020, 2026 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] == 1) ||
    isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https") {
    $protocol = "https://";
}
else {
    $protocol = "http://";
}
$url = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
?>

<script>
function share(platform) {
    const url = encodeURIComponent(document.URL);
    const title = encodeURIComponent(document.title);
    let shareUrl = '';

    switch (platform) {

        case 'facebook':
            shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + url;
            break;

        case 'x':
            shareUrl = 'https://twitter.com/intent/tweet?text=' + title + '&url=' + url;
            break;

        case 'linkedin':
            shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + url;
            break;

        case 'email':
            window.location.href =
                'mailto:?subject=' + title + '&body=' + url;
            return false;

        case 'native':
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: document.URL
                });
                return false;
            }
            return true;
    }

    if (shareUrl !== '') {
        window.open(
            shareUrl,
            '_blank',
            'noopener,noreferrer,width=600,height=600'
        );
        return false;
    }

    return true;
}

</script>

<div class="share-buttons">
    <ul class="share-buttons">
        <li>
            <button type="button"
                    id="share-native"
                    title="Share"
                    onclick="share('native');">
                <img alt="Share"
                     src="<?php echo DIR_SITE?>graphics/icon_share.svg">
            </button>
        </li>

        <li>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>"
               target="_blank"
               rel="noopener noreferrer"
               title="Share on Facebook"
               aria-label="Share post on Facebook"
               onclick="return share('facebook');">
                <img alt="Facebook" src="<?php echo DIR_SITE?>graphics/icon_facebook.svg">
            </a>
        </li>

        <li>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($url); ?>"
               target="_blank"
               rel="noopener noreferrer"
               title="Share on X"
               aria-label="Share post on X"
               onclick="return share('x');">
                <img alt="X" src="<?php echo DIR_SITE?>graphics/icon_x.svg">
            </a>
        </li>

        <li>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($url); ?>"
               target="_blank"
               rel="noopener noreferrer"
               title="Share on LinkedIn"
               aria-label="Share post on LinkedIn"
               onclick="return share('linkedin');">
                <img alt="LinkedIn" src="<?php echo DIR_SITE?>graphics/icon_linkedin.svg">
            </a>
        </li>

        <li>
            <a href="mailto:?subject=<?php echo rawurlencode($page_title ?? CONFIG_TITLE); ?>&amp;body=<?php echo urlencode($url); ?>"
               title="Share link via email"
               aria-label="Share link to post via email"
               onclick="return share('email');">
                <img alt="Email" src="<?php echo DIR_SITE?>graphics/icon_email.svg">
            </a>
        </li>
    </ul>
</div>

<script>
if (!navigator.share) {
    const btn = document.getElementById('share-native');
    if (btn) btn.style.display = 'none';
}
</script>
