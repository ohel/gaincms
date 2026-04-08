<?php
# Copyright 2015-2017, 2020, 2026 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html
?>

<div class="contactnote">Send me <a href="mailto:<?php echo CONFIG_EMAIL?>" title="Email author" aria-label="Send email to the author" onclick="window.open('mailto:<?php echo CONFIG_EMAIL?>?subject=' + encodeURIComponent(document.title)); return false;">email</a> or comment the article below.
</br>
<span class="disclaimer">Please note: comments with direct links to commercial sites might not be published.</span>
</div>

<div id="disqus-container">
    <a role="button" id="disqus-load-btn" onclick="() => { enableDisqus(); }">
        <div class="disqus-button" >
            Click here to enable Disqus comments.<br>
            <span class="disclaimer">Ads may show up without an ad blocker.</span>
        </div>
    </a>
    <div id="disqus_thread" style="display: none;"></div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const btn = document.getElementById("disqus-load-btn");
    const thread = document.getElementById("disqus_thread");

    btn.addEventListener("click", function(e) {
        e.preventDefault();
        btn.style.display = "none";
        thread.style.display = "block";
        const disqus_identifier = "<?php echo $comments_id?>";

        if (location.hostname.match(/^(10)|(192\.168)\..*/)) {
            thread.innerHTML = "Disqus comments disabled for localhost.";
            thread.classList.add("contactnote");
        } else {
            var dsq = document.createElement('script');
            dsq.type = 'text/javascript';
            dsq.async = true;
            dsq.src = "//<?php echo CONFIG_URL_DISQUS?>/embed.js";
            (document.head || document.body).appendChild(dsq);
        }
        return false;
    });
});
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>

