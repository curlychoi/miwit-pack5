<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_PATH.'/lib/mw.mobile.lib.php');
include_once(G5_THEME_PATH.'/head.sub.php');

if ($mw['config']['cf_seo_url']) {
    $pc_url = mw_seo_url($bo_table, $wr_id, '&mobile=1&device=pc', false);
}
else {
    $pc_url = set_http(preg_replace('/^m\./i', '', $_SERVER[HTTP_HOST]));
    $pc_url.= str_replace("/plugin/mobile", "", $_SERVER[SCRIPT_NAME]);
    if ($bo_table) {
        $pc_url .= "/$g4[bbs]/board.php?bo_table=$bo_table";
        if ($wr_id) {
            $pc_url .= "&wr_id=$wr_id";
        }   
        $pc_url .= "&mobile=1";
    }
    else {
        $pc_url .= "?mobile=1";
    }
    $pc_url .= "&device=pc";
}
$a = mw_mobile_total_alarm();
extract($a);

$navbar_fixed_top = "";
if ($mw_mobile['use_head_fixed']) {
    $navbar_fixed_top = "navbar-fixed-top";

    if (defined("MW_MOBILE_INDEX") && $mwus['path'])
        echo "<style>#mw_mobile { margin-top:100px; }</style>";
    else
        echo "<style>#mw_mobile { margin-top:50px; }</style>";
}
?>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo G5_URL?>/asset/bootstrap-3.3.5/css/bootstrap.min.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="<?php echo G5_URL?>/asset/font-awesome-4.4.0/css/font-awesome.min.css">

<link rel="stylesheet" href="<?php echo G5_THEME_URL?>/mobile/theme.css">
<link rel="stylesheet" href="<?php echo G5_THEME_URL?>/mobile/style.css">

<div class="navbar navbar-default <?php echo $navbar_fixed_top?> navbar-static-top">
    <button type="button" class="navbar-toggle navbar-toggle-always" id="mw_toggle_button">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <?php if ($total_alarm) { ?><div class="mw_total_alarm"><?php echo $total_alarm?></div><?php } ?>
    </button>
    <a href="<?php echo G5_URL?>/"><div class="fa fa-home mw_home"></div></a>
    <div class="mw_title"><!-- class="navbar-brand title">-->
        <a href="<?php echo G5_URL?>/"><?php echo $config['cf_title'];?></a>
        <?php if ($bo_table) { ?>
            <a href="<?php echo mw_seo_url($bo_table)?>">- <?php echo $board['bo_subject'];?></a>
        <?php } ?>
    </div>
    <?php if (defined("MW_MOBILE_INDEX") && $mwus['path']) { ?>
    <div class="mw_index_search">
        <form method="get" action="<?php echo $mwus['path']?>">
        <input type="hidden" name="is_mobile" value="1">
        <div class="search_icon"><button type="submit"><i class="fa fa-search"></i></button></div>
        <div class="search_box">
            <input type="text" name="stx" value=""/>
        </div>
        </form>
    </div>
    <?php } ?>
</div>

<div id="mw_mobile">


