<?php
# Copyright 2015-2017 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $('#navbar a[href="' + "<?php if (isset($navbarhighlight)) echo $navbarhighlight?>" + '"]').parent().addClass("active");
    function scrollToTop() {
        $("html, body").animate({ scrollTop: 0 }, "fast");
        return false;
    }
</script>

</body>
</html>
