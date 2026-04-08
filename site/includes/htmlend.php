<?php
# Copyright 2015-2017, 2026 Olli Helin
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
    document.addEventListener("DOMContentLoaded", function() {
        const firstInvisible = document.querySelector(".invisible");
        if (firstInvisible && firstInvisible.offsetParent !== null) {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.type = "text/css";
            link.href = "<?php echo DIR_INCLUDE?>bootstrap.min.css";

            const firstLink = document.head.querySelector("link");
            document.head.insertBefore(link, firstLink);
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const navbarHighlight = "<?php if (isset($navbarhighlight)) echo $navbarhighlight?>";
        const link = document.querySelector('#navbar a[href="' + navbarHighlight + '"]');
        if (link && link.parentElement) {
            link.parentElement.classList.add("active");
        }

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: "smooth" });
            return false;
        }
    });
</script>

</body>
</html>
