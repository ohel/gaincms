<?php
# Copyright 2015-2017 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html
?>

<div class="contactnote">Send me <a href="mailto:user@gaincms.com" title="Email" onclick="window.open('mailto:user@gaincms.com?subject=' + encodeURIComponent(document.title)); return false;">email</a> or comment below:
</br>
<span class="disclaimer">(Please note: comments with direct links to commercial sites might not be published.)</span>
</div>

<div id="disqus_thread"></div>
<script type="text/javascript">
    var disqus_identifier = "<?php echo $comments_id?>";
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = "//<?php echo CONFIG_URL_DISQUS?>/embed.js";
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>

