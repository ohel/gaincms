<?php
# Copyright 2015-2017, 2020 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html
?>

<div class="contactnote">Send me <a href="mailto:<?php echo CONFIG_EMAIL?>" title="Email author" aria-label="Send email to the author" onclick="window.open('mailto:<?php echo CONFIG_EMAIL?>?subject=' + encodeURIComponent(document.title)); return false;">email</a> or comment below:
</br>
<span class="disclaimer">(Please note: comments with direct links to commercial sites might not be published.)</span>
</div>

<div id="disqus_thread"></div>
<script type="text/javascript">
    var disqus_identifier = "<?php echo $comments_id?>";
    if (!location.hostname.match(/^(10)|(192\.168)\..*/)) {
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = "//<?php echo CONFIG_URL_DISQUS?>/embed.js";
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    } else {
        const e = document.getElementById("disqus_thread");
        e.innerHTML = "Disqus comments disabled for localhost.";
        e.classList.add("contactnote");
    }
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>

