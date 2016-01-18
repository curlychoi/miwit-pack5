<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (!$mw['config']['cf_search_html']) {
    $mw['config']['cf_search_html'] = '
        <form name="fmainsearch" action="<?php echo G5_BBS_URL?>/search.php">
            <input type="hidden" name="sfl" value="wr_subject||wr_content">
            <input type="hidden" name="sop" value="and">
            <span class="search-text"><input type=text name=stx></span>
            <input type="submit" value="검색" class="search-button">
        </form>
    ';
}

if (!$mw['config']['cf_quick_link_html']) {
    $mw['config']['cf_quick_link_html'] = '
        <div class="quick-link">
        <ul>
            <li><a href="http://www.miwit.com/b/mw_builder" target="_blank">빌더다운</a></li>
            <li><a href="http://www.miwit.com/b/g4_skin" target="_blank">스킨다운</a></li>
            <li><a href="http://www.miwit.com/plugin/project/" target="_blank">프로젝트</a></li>
            <li><a href="http://www.miwit.com/plugin/family/" target="_blank">통합팩</a></li>
            <li><a href="http://www.miwit.com/b/mw_tip" target="_blank">매뉴얼,팁</a></li>
            <li><a href="http://www.miwit.com/b/g4_site" target="_blank">사용후기</a></li>
            <li><a href="http://www.miwit.com/b/g4_qna" target="_blank">질문게시판</a></li>
            <li><a href="http://www.miwit.com/b/mw_board" target="_blank">커뮤니티</a></li>
        </ul>
        </div>
    ';
}

if (!$mw['config']['cf_head_html']) {
    $mw['config']['cf_head_html'] = '';
}

if (!$mw['config']['cf_content_head_html']) {
    $mw['config']['cf_content_head_html'] = '';
}

if (!$mw['config']['cf_index_image_html']) {
    $mw['config']['cf_index_image_html'] = '
<?php
mw_script($theme_path."/js/mw.slider.js");

$slide = array();
$slide[] = array("url"=>"http://www.miwit.com", "target"=>"_blank", "img"=>G5_THEME_URL."/img/01.jpeg");
$slide[] = array("url"=>"http://www.miwit.com", "target"=>"_blank", "img"=>G5_THEME_URL."/img/02.jpeg");
$slide[] = array("url"=>"http://www.miwit.com", "target"=>"_blank", "img"=>G5_THEME_URL."/img/03.jpeg");
$slide[] = array("url"=>"http://www.miwit.com", "target"=>"_blank", "img"=>G5_THEME_URL."/img/04.jpeg");
$slide[] = array("url"=>"http://www.miwit.com", "target"=>"_blank", "img"=>G5_THEME_URL."/img/05.jpeg");
$slide[] = array("url"=>"http://www.miwit.com", "target"=>"_blank", "img"=>G5_THEME_URL."/img/06.jpeg");
$slide[] = array("url"=>"http://www.miwit.com", "target"=>"_blank", "img"=>G5_THEME_URL."/img/07.jpeg");

shuffle($slide);
?>
<style>
.banner { width:728px; height:360px; position:relative; overflow:hidden; margin:0 0 10px 0; }
.banner ul { position:absolute; margin:0; padding:0; list-style:none; font-size:0; }
.banner ul li { margin:0; padding:0; list-style:none; }
</style>

<div class="banner">
<ul>
    <?php
    foreach ($slide as $item) {
        printf(\'<li><a href="%s" target="%s"><img src="%s"></a></li>\'.PHP_EOL, $item[\'url\'], $item[\'target\'], $item[\'img\']);
    } 
    ?>
</ul>
</div>

<script>
$(document).ready(function() {
    $(".banner").mw_slider({
        way:"left",
        delay:3000,
    });
});
</script>
';

}

if (!$mw['config']['cf_content_tail_html']) {
    $mw['config']['cf_content_tail_html'] = '';
}

if (!$mw['config']['cf_sidebar_head_html']) {
    $mw['config']['cf_sidebar_head_html'] = '
    <div class="block">내용1</div>
    <div class="block">내용2</div>
';
}

if (!$mw['config']['cf_sidebar_tail_html']) {
    $mw['config']['cf_sidebar_tail_html'] = '
    <div class="block">내용3</div>
    <div class="block">내용4</div>
';
}

if (!$mw['config']['cf_tail_link_html']) {
    $mw['config']['cf_tail_link_html'] = '
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
';
}

if (!$mw['config']['cf_info_html']) {
    $mw['config']['cf_info_html'] = '
        사이트명:<?php echo $config[\'cf_title\']; ?>,
        연락처:<?php echo mw_nobot_slice($config[\'cf_admin_email\']); ?>
';
}

