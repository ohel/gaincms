<?php # Share buttons generated using https://simplesharingbuttons.com/ ?>

<div class="share-buttons">
    <ul class="share-buttons">
        <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo CONFIG_URL_SOME?>&amp;t=" title="Share on Facebook" target="_blank"><img alt="Facebook" src="<?php echo DIR_SITE?>graphics/icon_facebook.svg" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(document.URL) + '&t=' + encodeURIComponent(document.URL)); return false;"></a></li>
        <li><a href="https://twitter.com/intent/tweet?source=<?php echo CONFIG_URL_SOME?>&amp;text=:%20<?php echo CONFIG_URL_SOME?>" target="_blank" title="Tweet"><img alt="Twitter" src="<?php echo DIR_SITE?>graphics/icon_twitter.svg" onclick="window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(document.title) + ':%20' + encodeURIComponent(document.URL)); return false;"></a></li>
        <li><a href="http://wordpress.com/press-this.php?u=<?php echo CONFIG_URL_SOME?>&amp;t=&amp;s=" target="_blank" title="Publish on WordPress"><img alt="WordPress" src="<?php echo DIR_SITE?>graphics/icon_wordpress.svg" onclick="window.open('http://wordpress.com/press-this.php?u=' + encodeURIComponent(document.URL) + '&t=' + encodeURIComponent(document.title)); return false;"></a></li>
        <li><a href="mailto:?subject=&amp;body=<?php echo CONFIG_URL_SOME?>" target="_blank" title="Email"><img alt="Email" src="<?php echo DIR_SITE?>graphics/icon_email.svg" onclick="window.open('mailto:?subject=' + encodeURIComponent(document.title) + '&body=' +  encodeURIComponent(document.URL)); return false;"></a></li>
    </ul>
</div>

