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
