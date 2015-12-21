<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/tail.php');
    return;
}

mw_script($theme_path.'/js/mw.scroll.top.js');

?>
    </div><!--main-->

    <?php include(G5_THEME_PATH.'/sidebar.php') ?>

    <div class="blank"></div>

</div><!--wrapper-->
</div><!--container-->

<div class="footer">
    <div class="tail_link">
    <div class="wrapper">
    <ul class="menu">
        <li><a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=company">회사소개</a></li>
        <li><a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=privacy">개인정보취급방침</a></li>
        <li><a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=provision">서비스이용약관</a></li>
        <li><a href="<?php echo G5_URL; ?>/plugin/point-rank/point_policy.php">포인트정책</a></li>
        <li><a href="<?php echo G5_URL; ?>/plugin/point-rank/point_month_ranking.php">포인트 월별랭킹</a></li>
        <li><a href="<?php echo G5_URL; ?>/plugin/point-rank/point_sum_ranking.php">포인트 전체랭킹</a></li>
        <li><a href="<?php echo G5_URL; ?>/plugin/secede/">회원탈퇴</a></li>
        <li><a href="#top" id="ft_totop">상단으로</a></li>
    </ul>
    </div><!--wrapper-->
    </div><!--tail_link-->

    <div class="wrapper">
    <div style="margin:10px 0 0 0;">
        사이트명:<?php echo $config['cf_title']?>,
        연락처:<?php echo mw_nobot_slice($config['cf_admin_email'])?>
    </div>
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
