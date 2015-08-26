</div> <?php # Close <div class="container"> from header. ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $('a[href="' + "<?php if (isset($activepage)) echo $activepage?>" + '"]').parent().addClass("active");
</script>

<?php
if (!isset($skipclosingtags) || $skipclosingtags == false) {
    echo "</body></html>";
}
?>
