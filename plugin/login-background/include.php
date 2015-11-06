<?php
if (!defined("_GNUBOARD_")) exit;

add_javascript("<script src='".G5_URL."/plugin/login-background/jquery.backstretch.min.js'></script>");
?>
<script>
function shuffle(o){ //v1.0
    for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
};
var images = [
    <?php for ($i=1; $i<=16; ++$i) { ?>
    "<?php echo G5_URL?>/plugin/login-background/<?php echo $i?>.jpg",
    <?php } ?>
];
shuffle(images);

$.backstretch(images, {
    fade: 1000,
    duration: 3000
});
</script>


