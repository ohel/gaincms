<?php
# Copyright 2015-2017 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html
?>

<div class="invisible"></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<!-- CDN fail fallbacks. -->
<script>
    window.jQuery || document.write('<script src="<?php echo DIR_INCLUDE?>jquery.min.js">\x3C/script>')
</script>
<script>
    $.fn.modal || document.write('<script src="<?php echo DIR_INCLUDE?>bootstrap.min.js">\x3C/script>')
</script>
<script>
    $(function () {
        if ($(".invisible:first").is(":visible") === true) {
            var link = document.createElement("link");
            link.rel = "stylesheet";
            link.type = "text/css";
            link.href = "<?php echo DIR_INCLUDE?>bootstrap.min.css";
            document.head.insertBefore(link, $("head").children("link").first()[0]);
        }
    });
</script>

<script>
    $('#navbar a[href="' + "<?php if (isset($navbarhighlight)) echo $navbarhighlight?>" + '"]').parent().addClass("active");
    function scrollToTop() {
        $("html, body").animate({ scrollTop: 0 }, "fast");
        return false;
    }
</script>

</body>
</html>
