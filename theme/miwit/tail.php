<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/tail.php');
    return;
}

mw_script($theme_path.'/js/mw.scroll.top.js');

?>
        <?php if (!$mw['config']['cf_no_content_tail']) echo mw_eval($mw['config']['cf_content_tail_html']); ?>
    </div><!--main-->

    <?php include(G5_THEME_PATH.'/sidebar.php') ?>

    <div class="blank"></div>

</div><!--wrapper-->
</div><!--container-->

<?php if (!$mw['config']['cf_no_tail']) echo mw_eval($mw['config']['cf_tail_html']); ?>

<div class="footer">
    <?php if (!$mw['config']['cf_no_tail_link']) { ?>
    <div class="tail_link">
    <div class="wrapper">
    <ul class="menu">
        <?php mw_eval($mw['config']['cf_tail_link_html']) ?>
    </ul>
    </div><!--wrapper-->
    </div><!--tail_link-->
    <?php } ?>

    <div class="wrapper">
    <?php if (!$mw['config']['cf_no_info']) { ?>
    <div style="margin:10px 0 0 0;">
        <?php mw_eval($mw['config']['cf_info_html']) ?>
    </div>
    <?php } ?>
    <div class="copyright">
        Copyright ⓒ <a href="<?php echo G5_URL?>" class="site"><?php echo G5_URL?></a>.  All rights reserved.
    </div>
    </div><!--wrapper-->
</div><!--footer-->

</div><!-- #mw5 -->

<?php if (is_dir(G5_PLUGIN_PATH."/attendance")) {?>
<link rel="stylesheet" href="<?php echo G5_PLUGIN_URL?>/attendance/suggest.css"> 
<script src="<?php echo G5_PLUGIN_URL?>/attendance/suggest.js.php?t=<?php echo time()?>"></script>
<?php }?>

<?php if (is_dir(G5_PLUGIN_PATH."/memo-up")) {?>
<link rel="stylesheet" href="<?php echo G5_PLUGIN_URL?>/memo-up/suggest.css"> 
<script src="<?php echo G5_PLUGIN_URL?>/memo-up/suggest.js.php?t=<?php echo time()?>"></script> 
<?php }?>

<?php
if (file_exists($g4['path']."/extend/mw.mobile.extend.php") and !G5_USE_MOBILE) {
    //$mobile_link = mw_seo_url($bo_table, $wr_id, '', 2);
    $mobile_link = "$g4[path]/plugin/mobile";
    if ($bo_table) {
        $mobile_link .= "/board.php?bo_table=".$bo_table;
        if ($wr_id)
            $mobile_link .= "&wr_id=".$wr_id;
    }
    echo "<a href='{$mobile_link}' id='device_change'>모바일 웹으로 보기</a>";
}
else if (G5_DEVICE_BUTTON_DISPLAY and G5_USE_MOBILE and !G5_IS_MOBILE) {
    $seq = 0;
    $p = parse_url(G5_URL);
    $href = $p['scheme'].'://'.$p['host'].$_SERVER['PHP_SELF'];
    if($_SERVER['QUERY_STRING']) {
        $sep = '?';
        foreach($_GET as $key=>$val) {
            if($key == 'device')
                continue;

            $href .= $sep.$key.'='.strip_tags($val);
            $sep = '&amp;';
            $seq++;
        }
    }
    if($seq)
        $href .= '&amp;device=mobile';
    else
        $href .= '?device=mobile';

    echo "<a href=\"{$href}\" id=\"device_change\">모바일 버전으로 보기</a>";
}

?>

<!-- } 하단 끝 -->

<script>
$(function() {
    // 폰트 리사이즈 쿠키있으면 실행
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});
</script>

<?php
include_once(G5_THEME_PATH."/tail.sub.php");
