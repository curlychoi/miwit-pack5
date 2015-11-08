<?php

/* script */
$script = array();

$script[] = $board_skin_path."/mw.js/mw.g5.adapter.js.php?bo_table=".$bo_table;
$script[] = $board_skin_path."/mw.js/mw_image_window.js";
$script[] = $board_skin_path."/mw.js/mw.star.rate/jquery.mw.star.rate.js";

//if ($wr_id)
    $script[] = $board_skin_path."/mw.js/tooltip.js";

if (is_g5()) 
    $script[] = $board_skin_path."/mw.js/jquery-ui-1.9.2.custom.min.js";
else
    $script[] = $board_skin_path."/mw.js/jquery-ui-1.8.19.custom.min.js";


/* css */
$css = array();

$css[] = $board_skin_path."/style.common.css?".filemtime($board_skin_path."/style.common.css");
$css[] = $board_skin_path."/mw.js/ui-lightness/jquery-ui-1.9.2.custom.min.css";
$css[] = $board_skin_path."/mw.js/mw.star.rate/jquery.mw.star.rate.css";
$css[] = $board_skin_path."/mw.css/font-awesome-4.4.0/css/font-awesome.css";

/* ---- */
foreach ($css as $c) {
    if (defined("_MW_MOBILE_"))
        echo "<link rel=\"stylesheet\" href=\"{$c}\"></script>".PHP_EOL;
    else if (is_g5())
        add_stylesheet("<link rel=\"stylesheet\" href=\"{$c}\"/>");
    else
        echo "<link rel=\"stylesheet\" href=\"{$c}\"></script>".PHP_EOL;
}

foreach ($script as $s) {
    if (defined("_MW_MOBILE_"))
        echo "<script src=\"{$s}\"></script>".PHP_EOL;
    else if (is_g5())
        add_javascript("<script src=\"{$s}\"></script>");
    else
        echo "<script src=\"{$s}\"></script>".PHP_EOL;
}

if ($is_admin == "super") { 
    ?>
    <script>
    function mw_config() {
        var url = "<?php echo $board_skin_path?>/mw.adm/mw.config.php?bo_table=<?php echo $bo_table?>";
        var config = window.open(url, "config", "width=1100, height=700, scrollbars=yes");
        config.focus();
    }
    </script>
    <?php
}


