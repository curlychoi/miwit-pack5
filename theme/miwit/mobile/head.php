<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (defined("_MW_MOBILE_")) return;

include_once(G5_PATH.'/lib/mw.mobile.lib.php');
include_once(G5_THEME_PATH.'/head.sub.php');

$a = mw_mobile_total_alarm();
extract($a);
?>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo G5_URL?>/asset/bootstrap-3.3.5/css/bootstrap.min.css">
<style> #new_sch legend { display:none; } </style>

<!-- Font Awesome -->
<link rel="stylesheet" href="<?php echo G5_URL?>/asset/font-awesome-4.4.0/css/font-awesome.min.css">

<link rel="stylesheet" href="<?php echo G5_THEME_URL?>/mobile/theme.css">
<link rel="stylesheet" href="<?php echo G5_THEME_URL?>/mobile/style.css">

<div class="navbar navbar-default navbar-fixed-top navbar-static-top">
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

