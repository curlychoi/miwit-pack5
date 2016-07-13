<?php
/**
 * Bechu-Basic Skin for Gnuboard4
 *
 * Copyright (c) 2008 Choi Jae-Young <www.miwit.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

$mw_is_view = true;
$mw_is_list = false;
$mw_is_write = false;
$mw_is_comment = false;

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");
include("view_head.skin.php");

if ($write['wr_key_password'] && !get_session($ss_key_name."_".$write['wr_id'])) {
    include("{$board_skin_path}/mw.proc/mw.key.php");
    return;
}

if ($delete_href && !strstr($delete_href, "javascript")) $delete_href = "#;\" onclick=\"del('{$delete_href}')";

if (is_reaction_test())
echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">';
?>
<style> <?php echo $cf_css?> </style>
<?php include_once($board_skin_path."/mw.proc/mw.asset.php")?>

<script> document.title = "<?=strip_tags(addslashes($view[wr_subject]))?>"; </script>

<script src="<?=$board_skin_path?>/mw.js/syntaxhighlighter/scripts/shCore.js"></script>
<script src="<?=$board_skin_path?>/mw.js/syntaxhighlighter/scripts/shBrushPhp.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$board_skin_path?>/mw.js/syntaxhighlighter/styles/shCore.css"/>
<link type="text/css" rel="stylesheet" href="<?=$board_skin_path?>/mw.js/syntaxhighlighter/styles/shThemeDefault.css"/>
<script>
SyntaxHighlighter.config.clipboardSwf = '<?=$board_skin_path?>/mw.js/syntaxhighlighter/scripts/clipboard.swf';
SyntaxHighlighter.all();
</script>

<?php if ($mw_basic[cf_source_copy]) { // 출처 자동 복사 ?>
<?php $copy_url = $shorten ? $shorten : set_http("{$g4[url]}/{$g4[bbs]}/board.php?bo_table={$bo_table}&wr_id={$wr_id}"); ?>
<script src="<?=$board_skin_path?>/mw.js/autosourcing.open.compact.js"></script>
<style>
    div.autosourcing-stub { display:none }
    dIV.autosourcing-stub-extra { position:absolute; opacity:0 }
</style>
<script>
    AutoSourcing.setTemplate("<p style='margin:11px 0 7px 0;padding:0'> <a href='{link}' target='_blank'> [출처] {title} - {link}</a> </p>");
    AutoSourcing.setString(<?=$wr_id?> ,"<?=$config[cf_title];//$view[wr_subject]?>", "<?=$view[wr_name]?>", "<?=$copy_url?>");
    AutoSourcing.init( 'view_%id%' , true);
</script>
<?php } ?>

<?php if ($mw_basic[cf_content_align] && $write[wr_align]) { ?>
<style>
#view_content { text-align:<?=$write[wr_align]?>; }
</style>
<?php } ?>

<!-- 게시글 보기 시작 -->
<table width="<?php echo $bo_table_width?>" align="center" cellpadding="0" cellspacing="0"><tr><td id="mw_basic">

<?php
if (!is_reaction_test())
if ($mw_basic[cf_include_head] && is_mw_file($mw_basic[cf_include_head]) && strstr($mw_basic[cf_include_head_page], '/v/')) {
    include_once($mw_basic[cf_include_head]);
}

if ($mw_basic['cf_bbs_banner']) {
    include_once("$bbs_banner_path/list.skin.php"); // 게시판 배너
}

include_once("$board_skin_path/mw.proc/mw.list.hot.skin.php");
?>

<!-- 분류 셀렉트 박스, 게시물 몇건, 관리자화면 링크 -->
<?php if (!$wr_id) {?>
<table width="100%">
<tr height="25">
    <td>
        <form name="fcategory_view" method="get" style="margin:0;">
        <? if ($is_category && !$mw_basic[cf_category_tab]) { ?>
            <select name=sca onchange="location='<?=$category_location?>'+this.value;">
            <? if (!$mw_basic[cf_default_category]) { ?> <option value=''>전체</option> <? } ?>
            <?=$category_option?>
            </select>
        <? } ?>
        </form>
    </td>
    <td align="right">
        <?php include($board_skin_path."/mw.proc/mw.top.button.php")?>
    </td>
</tr>
<tr><td height=5></td></tr>
</table>

<script>
<?php  if (!$mw_basic[cf_category_tab]) { ?>
if ('<?=$sca?>') document.fcategory_view.sca.value = '<?=urlencode($sca)?>';
<?php } ?>
</script>
<?php } ?>

<?php
include_once("$board_skin_path/mw.proc/mw.notice.top.php");
include_once("$board_skin_path/mw.proc/mw.search.top.php");
include_once("$board_skin_path/mw.proc/mw.cash.membership.skin.php");
?>

<!-- 링크 버튼 -->
<?php
ob_start();

if ($mw_basic['cf_prev_next'])
{
    $tmp_href = $prev_href;
    $tmp_wr_subject = $prev_wr_subject;

    $prev_href = $next_href;
    $prev_wr_subject = $next_wr_subject;

    $next_href = $tmp_href;
    $next_wr_subject = $tmp_wr_subject;

    unset($tmp_href);
    unset($tmp_wr_subject);
}

print '<div>';

if ($prev_href)
    printf('<a class="fa-button" href="%s" title="" accesskey="b"><i class="fa fa-chevron-left"></i> <span>이전글</span></a>&nbsp;', $prev_href, $prev_wr_subject);

if ($next_href) 
    printf('<a class="fa-button" href="%s" title="" accesskey="b"><i class="fa fa-chevron-right"></i> <span>다음글</span></a>&nbsp;', $next_href, $next_wr_subject);

print '</div><div>';

if ($search_href)
    printf('<a class="fa-button" href="%s"><i class="fa fa-search"></i> <span>검색목록</span></a>&nbsp;', $search_href); 

printf('<a class="fa-button" href="%s"><i class="fa fa-list"></i> <span>목록</span></a>&nbsp;', $list_href);

if ($update_href)
    printf('<a class="fa-button" href="%s"><i class="fa fa-cut"></i> <span>수정</span></a>&nbsp;', $update_href);

if ($delete_href)
    printf('<a class="fa-button" href="%s"><i class="fa fa-remove"></i> <span>삭제</span></a>&nbsp;', $delete_href);

if ($reply_href)
    printf('<a class="fa-button" href="%s"><i class="fa fa-reply"></i> <span>답변</span></a>&nbsp;', $reply_href);

if ($write_href)
    printf('<a class="fa-button primary" href="%s"><i class="fa fa-pencil"></i> <span>글쓰기</span></a>', $write_href);

print '</div>';

$link_buttons = ob_get_clean();
?>

<!-- 제목, 글쓴이, 날짜, 조회, 추천, 비추천 -->
<div class="mw_basic_view_subject">
<h1>
<?php 
if ($is_category)
    echo ($category_name ? "[{$view['ca_name']}] " : "");

echo cut_hangul_last(get_text($view['wr_subject'])).$view['icon_secret'];

if ($mw_basic['cf_reward'])
    echo "&nbsp;<img src='{$board_skin_path}/img/btn_reward_{$reward['re_status']}.gif' align='absmiddle'>";

if ($mw_basic['cf_attribute'] == 'qna' && !$view['is_notice']) { 
    echo "&nbsp;&nbsp;<img src=\"{$board_skin_path}/img/icon_qna_{$view['wr_qna_status']}.png\" align=\"absmiddle\"></span>";
}
?>
</h1>
</div>

<div class="mw_basic_view_title">
    <?php echo $mw_admin_button?>
    <? if ($mw_basic[cf_contents_shop]) { // 배추 컨텐츠샵 ?>
    <strong>가격</strong> : 
    <span class="mw_basic_contents_price"><?=$mw_price?></span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <? } ?>
    <? //if ($mw_basic[cf_attribute] != "anonymous") { ?>
    <i class="fa fa-user"></i>
    <span class=mw_basic_view_name><?=$view[name]?>
    <?/* if ($mw_basic[cf_icon_level] && !$view[wr_anonymous] && $mw_basic[cf_attribute] != "anonymous" && $write[mb_id] && $write[mb_id] != $config[cf_admin]) { ?>
    <span class="icon_level<?php echo mw_get_level($write[mb_id])+1?>" style="border:1px solid #ddd;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <? }*/ ?>
    <?php if ($is_ip_view && $ip) { ?>
    <!--<span class="media-ip">&nbsp;(<?=$ip?>)</span>-->
    <?php if ($is_admin) { ?>
        &nbsp;&nbsp;
        <a href="#;" class="tooltip" onclick="btn_ip('<?php echo $view['wr_ip']?>')" title="<?php echo $view['wr_ip']?> 조회">
            <i class="fa fa-info-circle"></i></a>
        &nbsp;
        <a href="#;" class="tooltip" onclick="btn_ip_search('<?php echo $view['wr_ip']?>')" title="<?php echo $view['wr_ip']?> 검색">
            <i class="fa fa-search"></i></a>
    <?php } ?>
    <? //} // mw_basic[cf_attribute] != 'anonymous'?>
    </span>
    <?php } ?>
    <i class="fa fa-clock-o"></i> 
    <span class="mw_basic_view_datetime media-date"><?php echo $view['datetime2']?></span>
    <span class="mw_basic_view_datetime media-date-sns"><?php echo $view['datetime_sns']?></span>
    <i class="fa fa-eye"></i> <span class="mw_basic_view_hit"><?php echo $view['wr_hit']?></span>
    <? /*if ($is_good) { ?>추천 : <span class=mw_basic_view_good><?=$view[wr_good]?></span><?}*/?>
    <? /*if ($is_nogood) { ?>비추천 : <span class=mw_basic_view_nogood><?=$view[wr_nogood]?></span><?}*/?>
    <span class"=mw_basic_view_name">
    <? if ($singo_href) { ?><a href="<?=$singo_href?>" title="신고"><i class="fa fa-warning"></i></a>&nbsp;<?}?>
    <? if ($print_href) { ?><a href="<?=$print_href?>" title="인쇄"><i class="fa fa-print"></i></a><?}?>
    </span>
</div>

<?php if ($mw['config']['cf_seo_url'] or $mw_basic['cf_shorten']) { ?>
<div class="mw_basic_view_url">
    <i class="fa fa-anchor"></i>
    <input type="text" id="post_url" value="<?php echo $shorten?>" readonly/>
    <!--<img src="<?php echo $board_skin_path?>/img/copy.png" id="post_url_copy" align="absmiddle">-->
</div>
<?php
} 
else if ($mw_basic[cf_umz]) { // 짧은 글주소 사용
    $view['wr_umz'] = str_replace("mwt.so", "umz.kr", $view['wr_umz']);
?>
<div class="mw_basic_view_url">
    글주소 :
    <span id="post_url"><?=$view[wr_umz]?></span>
    <!--<img src="<?=$board_skin_path?>/img/copy.png" id="post_url_copy" align="absmiddle">-->

    <?php if ($is_admin) { ?>
    <span id='btn_get_umz'><a><img src="<?=$board_skin_path?>/img/reumz.png" align="absmiddle"/></a></span>

    <script>
    $(document).ready(function () {
        $("#btn_get_umz a").css("cursor", "pointer");
        $("#btn_get_umz").on("click", function () {
            tmp = $("#btn_get_umz").html();
            $("#btn_get_umz").html("<img src='<?=$board_skin_path?>/img/icon_loading.gif' height='16' align='absmiddle'/>");
            $.get("<?php echo $board_skin_path?>/mw.proc/mw.umz.php", { 'bo_table':'<?=$bo_table?>', 'wr_id':'<?=$wr_id?>' }, function (url) {
                if (url)
                    $("#post_url").text(url);
                $("#btn_get_umz").html(tmp);
            });
        });
    });
    </script>
    <?php } ?>
</div>
<?php } 

if ($mw_basic['cf_include_file_head'] && is_mw_file($mw_basic['cf_include_file_head'])) {
    include_once($mw_basic['cf_include_file_head']);
}

if ($mw_basic['cf_file_head']) {
    echo $mw_basic['cf_file_head'];
}

// 가변 파일
$cnt = 0;
for ($i=0; $i<count($view[file]); $i++) {
    if ($view[file][$i][source] && !$view[file][$i][view] && !$view[file][$i][movie]) {
        $cnt++;

    $view[file][$i][href] = str_replace('./', $g4['bbs_path'].'/', $view[file][$i][href]);
?>
<div class="mw_basic_view_file">
    <a href="javascript:file_download('<?=$view[file][$i][href]?>', '<?=$i?>');" title="<?=$view[file][$i][content]?>">
    <i class="fa fa-save"></i>&nbsp;
    <?=$view[file][$i][source]?></a>
    <span class="mw_basic_view_file_info">
        <i class="fa fa-database"></i> <?php echo $view[file][$i]['size']?>
        <i class="fa fa-download"></i> <?php echo $view[file][$i]['download']?>
        <span title="<?php echo $view[file][$i]['datetime']?>"><i class="fa fa-clock-o"></i> <?php echo mw_basic_sns_date($view[file][$i]['datetime'])?></span>
        <?php if ($good_href) : ?>
        <i class="fa fa-thumbs-o-up" style="cursor:pointer;" onclick="mw_good_act_nocancel('good')"> <?php echo $view['wr_good']?></i>
        
        <?php endif; ?>
        <a href="#c_write"><i class="fa fa-comment-o"></i></a>
    </span>
</div>
<?php
    }
}

// 링크
$cnt = 0;
for ($i=1; $i<=$g4[link_count]; $i++) {
    if ($view[link][$i]) {
        $cnt++;
        if (mw_is_mobile_builder() or G5_IS_MOBILE)
            $link = cut_str($view[link][$i], 40);
        else
            $link = cut_str($view[link][$i], 70);
?>
<div class="mw_basic_view_link">
    <?php if ($is_admin && (strstr($link, "youtu") or strstr($link, "vimeo"))) { ?>
    <script>
    function mw_youtube_rethumb() {
        if (!confirm("유투브/비메오 썸네일을 갱신하시겠습니까?")) return false;

        $("#youtube_thumb").removeClass("fa-youtube");
        $("#youtube_thumb").addClass("fa-spinner fa-pulse");

        $.get("<?php echo $board_skin_path?>/mw.proc/mw.youtube.thumb.php", {
            "bo_table":"<?php echo $bo_table?>",
            "wr_id":"<?php echo $wr_id?>",
            "num":"<?php echo $i?>"
        }, function (str) {
            alert(str);
            $("#youtube_thumb").removeClass("fa-spinner fa-pulse");
            $("#youtube_thumb").addClass("fa-youtube");
        });
    }
    </script>
    <a href="#;" onclick="mw_youtube_rethumb()">
    <?php } ?>

    <?php if (strstr($link, "youtu")) { ?>
    <i class="fa fa-youtube" id="youtube_thumb"></i></a>
    <?php } else if (strstr($link, "vimeo")) { ?>
    <i class="fa fa-vimeo-square"></i></a>
    <?php } else { ?>
    <i class="fa fa-external-link"></i>
    <?php } ?>
    <a href="<?=$view[link_href][$i]?>" target="<?=$view[link_target][$i]?>"><?=$link?></a>
    <span class=mw_basic_view_link_info>(<?=$view[link_hit][$i]?>)</span>
    <span class="qr_code" value="<?=$view[link][$i]?>"><i class="fa fa-qrcode"></i></span>
</div>
<?php
    }
}
?>

<?php ob_start(); if ($mw_basic['cf_contents_shop'] == "1" and !$is_buy) {  // 배추컨텐츠샵-다운로드 결제 ?>
<div class="mw_basic_contents_shop_pay_button">
    <i class="fa fa-shopping-cart" aria-hidden="true"></i> 구매하기
</div>
<?php } $buy_button = ob_get_contents(); ob_end_flush(); ?>

<script>
$(document).ready(function () {
    $("#post_url").on('focus, mouseup, click', function () {
        $(this)[0].setSelectionRange(0, 9999);
    });

    $("#mw_basic").append("<div id='qr_code_layer'>QR CODE</div>");
    $(".qr_code").css("cursor", "pointer");
    $(".qr_code").toggle(function () {
        var url = $(this).attr("value");
        var x = $(this).position().top;
        var y = $(this).position().left;

        //$(".qr_code").append("<div");
        $("#qr_code_layer").hide("fast");

        $("#qr_code_layer").css("position", "absolute");
        $("#qr_code_layer").css("top", x + 20);
        $("#qr_code_layer").css("left", y);
        $("#qr_code_layer").html("<div class='qr_code_google'><img src='https://chart.googleapis.com/chart?cht=qr&chld=H|2&chs=100&chl="+url+"'></div>");
        $("#qr_code_layer").html($("#qr_code_layer").html() + "<div class='qr_code_info'>모바일로 QR코드를 스캔하면 웹사이트 또는 모바일사이트에 바로 접속할 수 있습니다.</div>");
        $("#qr_code_layer").show("fast");
    }, function () {
        $("#qr_code_layer").hide("fast");
    });
});
</script>

<?php
if ($mw_basic['cf_file_tail']) {
    echo $mw_basic['cf_file_tail'];
}

if ($mw_basic['cf_include_file_tail'] && is_mw_file($mw_basic['cf_include_file_tail'])) {
    include_once($mw_basic['cf_include_file_tail']);
} 
?>

<div class="view_buttons">
<?php echo $link_buttons?>
</div>

<?php
if ($mw_basic['cf_social_commerce']) include($social_commerce_path."/view.skin.php");
if ($mw_basic['cf_talent_market']) include($talent_market_path."/view.skin.php");
?>

<?php
$bomb = sql_fetch(" select * from $mw[bomb_table] where bo_table = '$bo_table' and wr_id = '$wr_id' ");
if ($bomb) {
?>
    <div class="mw_basic_view_bomb">
        <img src="<?=$board_skin_path?>/img/icon_bomb.gif" align="absmiddle">&nbsp;
        이 게시물이 자동 폭파되기까지 <span id="bomb_end_timer"></span> 남았습니다.
    </div>

    <script>
    var bomb_end_time = <?=(strtotime($bomb[bm_datetime])-$g4[server_time])?>;
    function bomb_run_timer()
    {
        var timer = document.getElementById("bomb_end_timer");

        dd = Math.floor(bomb_end_time/(60*60*24));
        hh = Math.floor((bomb_end_time%(60*60*24))/(60*60));
        mm = Math.floor(((bomb_end_time%(60*60*24))%(60*60))/60);
        ii = Math.floor((((bomb_end_time%(60*60*24))%(60*60))%60));

        var str = "";

        if (dd > 0) str += dd + "일 ";
        if (hh > 0) str += hh + "시간 ";
        if (mm > 0) str += mm + "분 ";
        str += ii + "초 ";

        //timer.style.color = "#FF6C00";
        timer.style.color = "#FF0000";
        timer.style.fontWeight = "bold";
        timer.innerHTML = str;

        bomb_end_time--;

        if (bomb_end_time <= 0)  {
            clearInterval(bomb_tid);
            location.href = "<?=$g4[bbs_path]?>/board.php?bo_table=<?=$bo_table?>";
        }
    }
    bomb_run_timer();
    bomb_tid = setInterval('bomb_run_timer()', 1000); 
    </script>
<?php } ?>

<div class="mw_basic_view_content">
    <div id=view_<?=$wr_id?>>
    <?php
    if ($mw_basic['cf_include_view_head'] && is_mw_file($mw_basic['cf_include_view_head'])) {
        include_once($mw_basic['cf_include_view_head']);
    }

    echo bc_code($mw_basic[cf_content_head], 1, 1);
    ?>

    <div id="view_content">

    <?php if ($mw_basic[cf_reward] && $reward[url]) { // 리워드 ?>
    <style>
    .reward_button { background:url(<?=$board_skin_path?>/img/btn_reward_click.jpg) no-repeat; width:140px; height:60px; cursor:pointer; margin:0 0 10px 0; }
    .reward_click { margin:10px 0 10px 0; font-weight:bold; }
    .reward_info { margin:0 0 30px 0; }
    </style>
    <div class="reward_button" onclick="<?=$reward[script]?>"></div>
    <div class="reward_click">↑ 위 배너를 클릭하시면 됩니다 </div>
    <div class="reward_info">
        <div class="point">적립 : <?=number_format($reward[re_point])?> P</div>
        <div class="edate">마감 : <?=$reward[re_edate]?></div>
    </div>
    <? } ?>

    <?  if ($mw_basic['cf_lightbox'] && $mw_basic['cf_lightbox'] <= $mb['mb_level'] && $view['wr_lightbox']) { ?>
    <script> board_skin_path = "<?=$board_skin_path?>"; </script>
    <script src="<?=$board_skin_path?>/mw.js/lightbox/js/jquery-1.7.2.min.js"></script>
    <script src="<?=$board_skin_path?>/mw.js/lightbox/js/lightbox.js"></script>
    <link href="<?=$board_skin_path?>/mw.js/lightbox/css/lightbox.css" rel="stylesheet" />

    <div class="lightbox_container">
    <?php
    mw_make_lightbox();
    for ($i=$file_start; $i<=$view['file']['count']; $i++) {
        $file = $view['file'][$i];
        if (!$file['view']) continue;
        if ($cf_img_1_noview) {
            $cf_img_1_noview = false;
            continue;
        }
        $lightbox_file = "{$file['path']}/{$file['file']}";
        $lightbox_thumb = "{$lightbox_path}/{$wr_id}-{$i}";

        echo "\n<a href=\"{$lightbox_file}\" rel=\"lightbox[roadtrip]\"><img src=\"{$lightbox_thumb}\"></a>";
    }
    ?>
    </div><!--lightbox_container-->
    <?php } ?>

    <?echo $view[rich_content]; // {이미지:0} 과 같은 코드를 사용할 경우?>
    <div style="clear:both; line-height:0; font-size:0;"></div>

    <?php
    echo bc_code($mw_basic[cf_content_add], 1, 1);

    if ($mw_basic[cf_include_view] && is_mw_file($mw_basic[cf_include_view])) {
        include_once($mw_basic[cf_include_view]);
    }
    ?>
    </div><!--view_content-->

    <?php if ($mw_basic[cf_zzal] && $file_viewer) { ?>
    <div class="mw_basic_view_zzal">
        <input type="button" id="zzbtn" value="<?=$view[wr_zzal]?> 보기" onclick="zzalview()" class="mw_basic_view_zzal_button">

        <script>
        mw_zzal_flag = false;
        function zzalview()
        {
            var zzb = document.getElementById("zzb");
            var btn = document.getElementById("zzbtn");

            if (zzb.style.display == "none")
            {
                if (!mw_zzal_flag) {
                    $("#zzb").load("<?="{$board_skin_path}/mw.proc/mw.zzal.php?bo_table={$bo_table}&wr_id={$wr_id}"?>");
                    mw_zzal_flag = true;
                }
                zzb.style.display = "block";
                btn.value = "<?=$view[wr_zzal]?> 가리기";
                //resizeBoardImage(650);
            }
            else
            {
                zzb.style.display = "none";
                btn.value = "<?=$view[wr_zzal]?> 보기";
            }
        }
        </script>

        <div id=zzb style="display:none; margin-top:20px;"></div>
    </div><!--mw_basic_view_zzal-->
    <?php } ?>

    <?php
    if (!$ob_exam_flag) echo $ob_exam;
    if (!$ob_marketdb_flag) echo $ob_marketdb;
    ?>

    <!-- 테러 태그 방지용 --></xml></xmp><a href=""></a><a href=''></a>

    <?php if ($mw_basic['cf_ccl'] && $view['wr_ccl']['by']) { ?>
    <div class="mw_basic_ccl">
        <a rel="license" href="<?php echo $view['wr_ccl']['link']?>" title="<?php echo $view['wr_ccl']['msg']?>" target="_blank">
        <img src="<?php echo $board_skin_path?>/mw.ccl/ccls_by.gif">
        <?php if ($view['wr_ccl']['nc'] == "nc") { ?><img src="<?php echo $board_skin_path?>/mw.ccl/ccls_nc.gif"><? } ?>
        <?php if ($view['wr_ccl']['nd'] == "nd") { ?><img src="<?php echo $board_skin_path?>/mw.ccl/ccls_nd.gif"><? } ?>
        <?php if ($view['wr_ccl']['nd'] == "sa") { ?><img src="<?php echo $board_skin_path?>/mw.ccl/ccls_sa.gif"><? } ?>
        </a>
    </div>
    <?php } ?>

    <?php if ($board[bo_use_good] || $board[bo_use_nogood]) { // 추천, 비추천?>
        <div id="mw_good"></div>

        <script>
        function mw_good_load() {
            if (!Date.now) {
                Date.now = function() { return new Date().getTime(); };
            }
            var t = Date.now() ;

            $.get("<?=$board_skin_path?>/mw.proc/mw.good.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&t="+t, function (data) {
                $("#mw_good").html(data);
            });
        }
        function mw_good_act(good) {
            if (good == "nogood") {
                flag = false;
                $.ajax({
                    url: "<?=$board_skin_path?>/mw.proc/mw.good.confirm.php",
                    type: "post",
                    async: false,
                    data: { 'bo_table':'<?=$bo_table?>', 'wr_id':'<?=$wr_id?>' },
                    success: function (str) {
                        if (str == 'true') {
                            flag = true;
                        }
                    }
                });

                if (!flag && !confirm("정말 비추천하시겠습니까?")) return;
            } 

            $.get("<?=$board_skin_path?>/mw.proc/mw.good.act.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&good="+good, function (data) {
                alert(data);
                mw_good_load();
            });
        }
        function mw_good_act_nocancel(good) {
            $.get("<?=$board_skin_path?>/mw.proc/mw.good.act.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&good="+good+"&no_cancel=1", function (data) {
                alert(data);
                mw_good_load();
            });
        }

        mw_good_load();
        </script>
    <?php } ?>

    <?php
    echo bc_code($mw_basic[cf_content_tail], 1, 1);

    if ($mw_basic[cf_include_view_tail] && is_mw_file($mw_basic[cf_include_view_tail])) {
        include_once($mw_basic[cf_include_view_tail]);
    }
    ?>

    </div><!--view_$wr_id-->
</div><!--mw_basic_view_content-->

<?php if ($mw_basic[cf_talent_market]) echo $talent_market_content; ?>
<?php if ($mw_basic[cf_google_map] && trim($write[wr_google_map]) && !$google_map_is_view && $google_map_code)
    echo $google_map_code; ?>

<?php if ($mw_basic['cf_rate_level'] && $write['wr_rate'] > 0) { ?>

    <div id="view_rate_box"> 
        <div><strong>종합평점</strong> (참여 <?php echo $rate_count?>명)</div>
        <div id="view_rate"></div>
    </div> 

    <script>
    //$(document).ready(function () {
        $("#view_rate").mw_star_rate({
            path : "<?php echo $board_skin_path?>/mw.js/mw.star.rate/",
            default_value : <?php echo round($write['wr_rate'], 1)?>,
            readonly : true,
            readonly_msg : '',
        });
    //});
    </script>
<?php } ?>

<?php echo $buy_button; ?>

<?php
//if ($is_signature && $signature && !$view[wr_anonymous] && $mw_basic[cf_attribute] != "anonymous") // 서명출력
if ($is_signature && !$view[wr_anonymous] && $mw_basic[cf_attribute] != "anonymous") // 서명출력
{ 
    $tmpsize = array(0, 0);
    $is_comment_image = false;
    $comment_image = mw_get_noimage();
    $comment_class = 'noimage';
    if ($mw_basic[cf_attribute] != "anonymous" && !$view[wr_anonymous] && $view[mb_id] && file_exists("$comment_image_path/{$view[mb_id]}")) {
        $comment_image = "$comment_image_path/{$view[mb_id]}";
        $is_comment_image = true;
        $tmpsize = @getimagesize($comment_image);
        $comment_image.= '?'.filemtime($comment_image);
        $comment_class = '';
    }

    $signature = preg_replace("/<a[\s]+href=[\'\"](http:[^\'\"]+)[\'\"][^>]+>(.*)<\/a>/i", "[$1 $2]", $signature);
    $signature = nl2br(strip_tags($signature));
    $signature = preg_replace("/\[([^\s]+) ([^\]]+)\]/i", "<a href='$1'>$2</a>", $signature);
    //$signature = htmlspecialchars($signature);
?>
<div class="mw_basic_view_signature">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="70">
            <div class="line">

            <div class="comment_image <?php echo $comment_class?>">
            <img src="<?=$comment_image?>"
                <?php
                if ($is_comment_image) { echo "onclick='mw_image_window(this, {$tmpsize[0]}, {$tmpsize[1]});'"; }
                else if (($is_member && $view[mb_id] == $member[mb_id] && !$view[wr_anonymous]) || $is_admin) { echo "onclick='mw_member_photo(\"{$view['mb_id']}\");'"; }?>>

            <? if (($is_member && $view[mb_id] == $member[mb_id] && !$view[wr_anonymous]) || $is_admin) { ?>
            <div style="margin:0 0 0 10px;"><a href="javascript:mw_member_photo('<?=$view[mb_id]?>')"
                style="font:normal 11px; color:#888; text-decoration:none;"><? echo $is_comment_image ? "사진변경" : "사진등록"; ?></a></div>
            <? } ?>
            <script>
            function mw_member_photo(mb_id) {
                window.open('<?=$board_skin_path?>/mw.proc/mw.comment.image.php?bo_table=<?=$bo_table?>&mb_id='+mb_id,'comment_image','width=500,height=350');
            }
            </script>
            <?
            if ($mw_basic[cf_icon_level] && !$view[wr_anonymous] && $mw_basic[cf_attribute] != "anonymous" && $write[mb_id] && $write[mb_id] != $config[cf_admin]) { 
                $level = mw_get_level($view[mb_id]);
                echo "<div class=\"icon_level".($level+1)."\">&nbsp;</div>";
                $exp = $icon_level_mb_point[$view[mb_id]] - $level*$mw_basic[cf_icon_level_point];
                $per = round($exp/$mw_basic[cf_icon_level_point]*100);
                if ($per > 100) $per = 100;
                echo "<div style=\"background:url($board_skin_path/img/level_exp_bg.gif); width:61px; height:3px; font-size:1px; line-height:1px; margin:5px 0 0 3px;\">";
                echo "<div style=\"background:url($board_skin_path/img/level_exp_dot.gif); width:$per%; height:3px;\">&nbsp;</div>";
                echo "</div>";
            }
            ?>
            </div><!--line-->
        </td>
        <td class="content">
            <div id="signature"><table border="0" cellpadding="0" cellspacing="0"><tr><td>
            <?=$signature?>
            </td></tr></table></div>
        </td>
    </tr>
    </table>
</div><!--mw_basic_view_signature-->
<?php } ?>


<?php if ($mw_basic[cf_quiz] && is_file($quiz_path."/view.php")) { // 퀴즈 ?>
<div class="mw_basic_view_quiz">
    <div id="mw_quiz"></div>

    <script>
    function mw_quiz_load() {
        $.get("<?=$quiz_path?>/view.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>", function (data) {
            $("#mw_quiz").html(data);
        });
    }
    mw_quiz_load();
    </script>
</div><!--mw_basic_view_quiz-->
<?php } ?>

<?php if ($mw_basic[cf_vote]) { // 설문 ?>
<div class="mw_basic_view_vote">
    <div id="mw_vote"></div>

    <script>
    function mw_vote_load() {
        $.get("<?=$board_skin_path?>/mw.proc/mw.vote.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>", function (data) {
            $("#mw_vote").html(data);
        });
    }
    function mw_vote_result() {
        $.get("<?=$board_skin_path?>/mw.proc/mw.vote.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&result_view=1", function (data) {
            $("#mw_vote").html(data);
        });
    }
    function mw_vote_join() {
        var is_check = false;
        var vt_num = $("input[name='vt_num']");
        var choose = '';
        for (i=0; i<vt_num.length; i++)  {
            if (vt_num[i].checked) {
                is_check = true;
                choose += i + ',';
            }
        }
        if (!is_check) {
            alert("설문항목을 선택해주세요.");
            return;
        }
        $.get("<?=$board_skin_path?>/mw.proc/mw.vote.join.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&vt_num="+choose, function (data) {
            alert(data);
            mw_vote_load();
        });
    }
    <? if ($is_admin or ($write[mb_id] && $member[mb_id] && $write[mb_id] == $member[mb_id])) { ?>
    function mw_vote_init() {
        if (!confirm("초기화한 데이터는 복구할 방법이 없습니다.\n\n정말 설문을 초기화 하시겠습니까?")) return;
        $.get("<?=$board_skin_path?>/mw.proc/mw.vote.init.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>", function (str) {
            if (str) {
                alert(str);
                return;
            }
            alert("설문을 초기화 했습니다.");
            location.reload();
        });
    }
    <? } ?>

    mw_vote_load();
    </script>
    <div class='division'></div>
</div><!--mw_basic_view_vote-->
<?php } ?>
<div class='division'></div>

<?php
if ($mw_basic[cf_attribute] == 'qna' && !$view[is_notice]) {
    $qna_save_point = round($write[wr_qna_point]*round($mw_basic[cf_qna_save]/100,2));
    $qna_total_point = $qna_save_point + $mw_basic[cf_qna_point_add];
    $uname = $board[bo_use_name] ? $member[mb_name] : $member[mb_nick];
    ?>
    <div class="mw_basic_qna_info">
        <? if ($is_member) { ?>
        <div><span class="mb_id"><?=$uname?></span>님의 지식을 나누어 주세요!</div> <? } ?>
        <div class="info2">
            <?php if ($write[wr_qna_point]) { ?>
            질문자가 자신의 포인트 <span class="num"><b><?=$write[wr_qna_point]?></b></span> 점을 걸었습니다.<br/> <? } ?>
            답변하시면 포인트 <span class="num"><b><?=$board[bo_comment_point]?></b>점</span>을<?
            if ($qna_total_point) { ?>, 답변이 채택되면
            포인트 <span class="num"><b><?=$qna_total_point?></b>점 <? } ?>
            <? if ($mw_basic[cf_qna_point_add]) { ?>
                (채택 <b><?=$qna_save_point?></b> + 추가 <b><?=$mw_basic[cf_qna_point_add]?></b>) <? } ?></span>을 드립니다.
        </div>
    </div><!--mw_basic_qna_info-->
<?php } ?>

<?php
echo "<div class='division'></div>".PHP_EOL;

if ($mw_basic[cf_sns]
or (
    ($board[bo_use_good] or $board[bo_use_nogood])
    and $mw_basic[cf_view_good]
    and $member[mb_level] >= $mw_basic[cf_view_good])
or $scrap_href) { 

    echo "<div class='scrap_jump'>".PHP_EOL;

    if ($scrap_href) {
        $sql = " select count(*) as cnt from $g4[scrap_table] where bo_table = '$bo_table' and wr_id = '$wr_id' ";
        $row = sql_fetch($sql);
        $scrap_count = $row[cnt];
        ?>
        <div class="scrap_button">
            <button class="fa-button" id="scrap_button" onclick="scrap_ajax()">
                <i class="fa fa-paperclip"></i>
                <span class="media-no-text">스크랩</span>
                +<span id="scrap_count"><?php echo $scrap_count?></span>
            </button>
        </div>

        <script>
        function scrap_ajax() {
            $.get("<?php echo $board_skin_path?>/mw.proc/mw.scrap.php", {
                'bo_table' : '<?php echo $bo_table?>',
                'wr_id' : '<?php echo $wr_id?>',
                'token' : '<?php echo $token?>' // 토큰 새로만들어야 하는데 이것까지 토큰 쓰기에는 세션이 너무;
            }, function (str) {
                tmp = str.split('|');
                if (tmp[0] == 'false') {
                    alert(tmp[1]);
                    return;
                }
                $("#scrap_count").text(tmp[0]);
                $("#scrap_button").effect("highlight", {}, 3000);
            });
        }
        </script>
    <?php
    }

    if (($board[bo_use_good] or $board[bo_use_nogood])
        and $mw_basic[cf_view_good]
        and $member[mb_level] >= $mw_basic[cf_view_good]) { ?>
            <div class="view_good">
                <button type="button" class="fa-button" onclick="window.open(
                    '<?=$board_skin_path?>/mw.proc/mw.good.list.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>',
                    'good_list',
                        'width=600,height=500,scrollbars=1');"/>
                    <i class="fa fa-thumbs-o-up"></i>
                    <span class="media-no-text">추천목록</span>
                </button>
            </div>
    <?php
    }

    echo '<div class="jump">';
    require($board_skin_path."/mw.proc/mw.jump.php");
    echo '</div>';

    echo "</div>".PHP_EOL;

    if ($mw_basic['cf_sns']) { 
        echo "<div class='sns'>{$view_sns}</div>";
    }
    else { 
        //echo "<style>.jump { margin:10px 0 0 5px }</style>";
    }

}

echo "<div class='division'></div>".PHP_EOL;

// 관련글 출력
if ($mw_basic['cf_related'] && $view['wr_related']) { 
    $related_skin = '';
    ob_start();
    ?>
    <div class="mw_basic_view_related_title">
        <h3>{{board_subject}} 관련글<a href="{{board_url}}" class="more">[더보기]</a></h3>
    </div>
    <div class="mw_basic_view_related">
        <ul>
        {{for}}
        <li><a href="{{href}}">[{{date}}] {{subject}} {{comment}}</a></li>
        {{/for}}
        </ul>
    </div>
    <?php
    $related_skin = ob_get_clean();

    if (!$mw_basic['cf_related_table'] or ($mw_basic['cf_related_table'] && $mw_basic['cf_related_table_div']))
        echo mw_related2($bo_table, $view['wr_related'], $related_skin); 

    if ($mw_basic['cf_related_table']) {
        $tables = array_map('trim', explode(",", $mw_basic['cf_related_table']));
        foreach ($tables as $table) {
            echo mw_related2($table, $view['wr_related'], $related_skin); 
        }
    }
}

if ($mw_basic[cf_latest]) { 
    $latest = mw_view_latest();
    if (count($latest)) {
        $bo_subject = $board[bo_subject];
        if ($mw_basic[cf_latest_table]) {
            $tmp = sql_fetch("select bo_subject from $g4[board_table] where bo_table = '$mw_basic[cf_latest_table]'");
            $bo_subject = $tmp[bo_subject];
        }

?>
<div class="mw_basic_view_latest_title">
    <h3>
        <?php echo $view['name']?> 님의 <?php echo $bo_subject?> 최신글
        <a href="<?php echo mw_seo_url($bo_table, 0, "&sfl=mb_id,1&stx=$write[mb_id]")?>" class="more">[더보기]</a>
    </h3>
</div>
<div class="mw_basic_view_latest">
    <ul>
    <?php for ($i=0; $i<count($latest); $i++) { ?>
    <li>
        <a href="<?php echo $latest[$i]['href']?>">[<?php echo substr($latest[$i]['wr_datetime'], 0, 10)?>]
        <?php echo $latest[$i]['subject']?>
        <span class="comment"><?php echo $latest[$i]['wr_comment']?$latest[$i]['wr_comment']:''?></span></a>
    </li>
    <?php } ?>
    </ul>
</div>
<?php
    }
} 

if ($mw_basic['cf_include_comment_head'] && is_mw_file($mw_basic['cf_include_coment_head']) )
    include_once($mw_basic['cf_include_comment_head']);

if (!$view[wr_comment_hide] && ($mw_basic[cf_comment_level] <= $member[mb_level])) {
    include_once("./view_comment.php"); // 코멘트 입출력 
}

printf('<div class="view_buttons2">%s</div>', $link_buttons);

if ($mw_basic[cf_include_tail] && is_mw_file($mw_basic[cf_include_tail]) && strstr($mw_basic[cf_include_tail_page], '/v/')) {
    include_once($mw_basic[cf_include_tail]);
}
?>

</td></tr></table><!-- 게시글 보기 끝 -->

<?php
if ($mw_basic['cf_exif']) {
?>
<script>
$(document).ready(function () {
    $("img[name=exif]").click(function (e) {
        var x = e.pageX;
        var y = e.pageY;

        var bf_no = $(this).attr("bf_no");

        var param = {
            'bo_table' : '<?php echo $bo_table?>',
            'wr_id' : '<?php echo $wr_id?>',
            'bf_no' : bf_no
        }

        $.post("<?php echo $board_skin_path?>/mw.proc/mw.exif.show.php", param, function (req) {
            $("body").append("<div id='exif-info' title='클릭하면 창이 닫힙니다.'></div>");
            $("#exif-info").css({ 'background': 'url(<?php echo $board_skin_path?>/img/exif.png) no-repeat' });
            $("#exif-info").css("position", "absolute");
            $("#exif-info").css("left", x);
            $("#exif-info").css("top", y);
            $("#exif-info").html(req);
            $("#exif-info").show();
            $("#exif-info").click(function () {
                $(this).remove();
            });
        });
    });
}); 
</script>
<?php } ?>

<?php if ($download_log_href) { ?>
<script>
function btn_download_log() {
    window.open("<?=$board_skin_path?>/mw.proc/mw.download.log.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>", "mw_download_log", "width=500, height=300, scrollbars=yes");
}
</script>
<? } ?>

<? if ($link_log_href) { ?>
<script>
function btn_link_log() {
    window.open("<?=$board_skin_path?>/mw.proc/mw.link.log.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>", "mw_link_log", "width=500, height=300, scrollbars=yes");
}
</script>
<? } ?>

<? if ($history_href) { ?>
<script>
function btn_history(wr_id) {
    window.open("<?=$board_skin_path?>/mw.proc/mw.history.list.php?bo_table=<?=$bo_table?>&wr_id=" + wr_id, "mw_history", "width=500, height=300, scrollbars=yes");
}
</script>
<? } ?>

<? if ($singo_href) { ?>
<script>
function btn_singo(wr_id, parent_id) {
    //if (confirm("이 게시물을 정말 신고하시겠습니까?")) {
    //hiddenframe.location.href = "<?=$board_skin_path?>/mw.proc/mw.btn.singo.php?bo_table=<?=$bo_table?>&wr_id=" + wr_id + "&parent_id=" + parent_id;
    window.open("<?=$board_skin_path?>/mw.proc/mw.btn.singo.php?bo_table=<?=$bo_table?>&wr_id=" + wr_id + "&parent_id=" + parent_id, "win_singo", "width=500,height=300,scrollbars=yes");
    //}
}
function btn_singo_view(wr_id) {
    var id = "singo_block_" + wr_id;

    if (document.getElementById(id).style.display == 'block')
        document.getElementById(id).style.display = 'none';
    else
        document.getElementById(id).style.display = 'block';
}

function btn_singo_clear(wr_id) {
    if (confirm("정말 초기화 하시겠습니까?")) {
        $.get("<?=$board_skin_path?>/mw.proc/mw.btn.singo.clear.php?bo_table=<?=$bo_table?>&token=<?=$token?>&wr_id="+wr_id, function(msg) {
            alert(msg);
        });
    }
}
</script>
<? } ?>

<? if ($print_href) { ?>
<script>
function btn_print() {
    window.open("<?=$board_skin_path?>/mw.proc/mw.print.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>", "print", "width=800,height=600,scrollbars=yes");
}
</script>
<? } ?>

<? if ($secret_href || $nosecret_href) { ?>
<script>
function btn_secret() {
    if (confirm("이 게시물을 비밀글로 설정하시겠습니까?")) {
        $.get("<?=$board_skin_path?>/mw.proc/mw.btn.secret.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&token=<?=$token?>", function (str) {
            alert(str);
        });;
    }
}
function btn_nosecret() {
    if (confirm("이 게시물의 비밀글 설정을 해제하시겠습니까?")) {
        $.get("<?=$board_skin_path?>/mw.proc/mw.btn.secret.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&token=<?=$token?>&flag=no", function (str) {
            alert(str);
        });
    }
}

</script>
<? } ?>

<? if ($is_singo_admin) { ?>
<script>
function btn_intercept(mb_id, wr_ip) {
    if (mb_id == undefined || mb_id == '') {
        mb_id = wr_ip;
    }
    window.open("<?=$board_skin_path?>/mw.proc/mw.intercept.php?bo_table=<?=$bo_table?>&mb_id=" + mb_id, "intercept", "width=500,height=300,scrollbars=yes");
}
</script>
<? } ?>

<? if ($is_admin) { ?>
<script>
function btn_now() {
    var renum = 0;
    if (confirm("이 게시물의 작성시간을 현재로 변경하시겠습니까?")) {
        if (confirm("날짜순으로 정렬 하시겠습니까?")) renum = 1;

        $.get("<?=$board_skin_path?>/mw.proc/mw.time.now.php", { 
            "bo_table":"<?=$bo_table?>", 
            "wr_id":"<?=$wr_id?>", 
            "token":"<?=$token?>", 
            "renum":renum 
            } , function (ret) {
                if (ret)
                    alert(ret);
                else
                    location.reload();
            });
    }
}

function btn_view_block() {
    <? if ($write[wr_view_block]) { ?>
    if (!confirm("이 게시물 보기차단을 해제 하시겠습니까?")) return;
    <? } else { ?>
    if (!confirm("이 게시물 보기를 차단하시겠습니까?")) return;
    <? } ?>
    $.post("<?=$board_skin_path?>/mw.proc/mw.view.block.php", {
        "bo_table":"<?=$bo_table?>",
        "wr_id":"<?=$wr_id?>",
        "token":"<?=$token?>"
    }, function (str) {
        if (str)
            alert(str);
    });
}
function btn_ip(ip) {
    window.open("<?=$board_skin_path?>/mw.proc/mw.whois.php?ip=" + ip, "whois", "width=700,height=600,scrollbars=yes");
}
function btn_ip_search(ip) {
    window.open("<?=$g4[admin_path]?>/member_list.php?sfl=mb_ip&stx=" + ip);
}
function btn_notice() {
    var is_off = 0;
    <? if ($view[is_notice]) { ?>
    if (!confirm("이 공지를 내리시겠습니까?")) return;
    is_off = 1; 
    <? } else { ?>
    if (!confirm("이 글을 공지로 등록하시겠습니까?")) return;
    <? } ?>
    $.get("<?=$board_skin_path?>/mw.proc/mw.notice.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&token=<?=$token?>&is_off="+is_off, function(data) {
        alert(data);
    });
}
function btn_popup() {
    var is_off = 0;
    <? if ($is_popup) { ?>
    if (!confirm("이 팝업공지를 내리시겠습니까?")) return;
    is_off = 1; 
    <? } else { ?>
    if (!confirm("이 글을 팝업공지로 등록하시겠습니까?")) return;
    <? } ?>
    $.get("<?=$board_skin_path?>/mw.proc/mw.popup.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&token=<?=$token?>", function(data) {
        alert(data);
    });
}
function btn_comment_hide() {
    var is_off = 0;
    <? if (!$view[wr_comment_hide]) { ?>
    if (!confirm("이 글의 댓글을 감추시겠습니까?")) return;
    is_off = 1; 
    <? } else { ?>
    if (!confirm("이 글의 댓글을 보이시겠습니까?")) return;
    <? } ?>
    $.get("<?=$board_skin_path?>/mw.proc/mw.comment.hide.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&token=<?=$token?>&is_off="+is_off, function(data) {
        alert(data);
        location.reload();
    });
}
</script>
<? } ?>

<?php if ($mw_basic[cf_contents_shop]) { // 배추컨텐츠샵 ?>
<script src="<?=$mw_cash[path]?>/cybercash.js"></script>
<script>
var mw_cash_path = "<?=$mw_cash[path]?>";
</script>
<!--<span><img src="<?=$board_skin_path?>/img/icon_cash2.gif" style="cursor:pointer;" onclick="buy_contents('<?=$bo_table?>', '<?=$wr_id?>')" align="absmiddle"></span>-->
<? } ?>


<script>
function file_download(link, no) {
    <?
    if ($member[mb_level] < $board[bo_download_level]) {
        $alert_msg = "다운로드 권한이 없습니다.";
        if ($member[mb_id]) { 
            echo "alert('$alert_msg'); return;\n";
        } else {
            echo "alert('$alert_msg\\n\\n회원이시라면 로그인 후 이용해 보십시오.');\n";
            echo "location.href = './login.php?url=".urlencode("$g4[bbs_path]/board.php?bo_table=$bo_table&wr_id=$wr_id")."';\n";
            echo "return;";
        }
    }
    ?>

    <? if ($board[bo_download_point] < 0) { ?>if (confirm("파일을 다운로드 하시면 포인트가 차감(<?=number_format($board[bo_download_point])?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?"))<?}?>

    <?php if ($mw_basic[cf_contents_shop] == "1" and !$is_per) { // 배추컨텐츠샵 다운로드 결제 ?>
        alert("<?=$is_per_msg?>");
        <?php if (!$ca_cash_use) { ?>
            return;
        <?php } ?>
        buy_contents('<?=$bo_table?>', '<?=$wr_id?>', no);
        return;
    <? } ?>

    if (<?=$mw_basic[cf_download_popup]?>)
        window.open("<?=$board_skin_path?>/mw.proc/download.popup.skin.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&no="+no, "download_popup", "width=<?=$mw_basic[cf_download_popup_w]?>,height=<?=$mw_basic[cf_download_popup_h]?>,scrollbars=yes");
    else {
        if (typeof comment_rate_run == 'function') {
            comment_rate_run();
        }
        document.location.href=link;
    }
}
</script>

<?php if (is_file($g4['path']."/js/board.js")) { ?>
<script src="<?php echo $g4['path']."/js/board.js"?>"></script>
<?php } ?>
<script src="<?php echo $board_skin_path."/mw.js/mw_image_window.js"?>"></script>

<script>
// 서명 링크를 새창으로
if (document.getElementById('signature')) {
    var target = '_blank';
    var link = document.getElementById('signature').getElementsByTagName("a");
    for(i=0;i<link.length;i++) {
        link[i].target = target;
    }
}

function move_link(obj, point, href, target)
{
    obj.target = '';

    if (!point) return;

    $.ajax({
        url: "<?php echo $board_skin_path?>/mw.proc/mw.link.point.php",
        type: "POST",
        data: { 'bo_table':'<?php echo $bo_table?>', 'wr_id':'<?php echo $wr_id?>' },
        async: false,
        cache: false,
        success: function(ret) {
            if (ret) point = 0;
        },
        error:function(request,status,error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });

    if (point > 0) {
        alert(point + "포인트 적립되었습니다.");
    }

    if (point < 0) {
        var a = confirm(point + "포인트 차감됩니다. 이동하시겠습니까?");
        if (!a) return;
    }

    if (target == "_self")
        location.href = href;
    else if (target == "_top")
        top.location.href = href;
    else
        window.open(href);

    return false;
}
</script>

<? if ($mw_basic[cf_write_notice]) { ?>
<script>
// 글쓰기버튼 공지
function btn_write_notice(url) {
    var msg = "<?=$mw_basic[cf_write_notice]?>";
    if (confirm(msg))
	location.href = url;
}
</script>
<? } ?>

<? if ($mw_basic[cf_link_blank]) { // 본문 링크를 새창으로 ?>
<script>
if (document.getElementById('view_content')) {
    var target = '_blank';
    var link = document.getElementById('view_content').getElementsByTagName("a");
    for(i=0;i<link.length;i++) {
        link[i].target = target;
    }
}
</script>
<? } ?>

<? if ($mw_basic[cf_source_copy]) { // 출처 자동 복사 ?>
<script>
function mw_copy()
{
    if (window.event)
    {
        window.event.returnValue = true;
        window.setTimeout('mw_add_source()', 10);
    }
}
function mw_add_source()
{
    if (window.clipboardData) {
        txt = window.clipboardData.getData('Text');
        txt = txt + "\r\n[출처 : <?=$g4[url]?>]\r\n";
        window.clipboardData.setData('Text', txt);
    }
}
//document.getElementById("view_content").oncopy = mw_copy;

</script>
<? } ?>

<? if ($is_admin == "super") { ?>
<script>
function mw_member_email() {
    if (!confirm("이 글을 회원메일로 등록하시겠습니까?")) return false;
    $.get("<?=$board_skin_path?>/mw.proc/mw.member.email.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&token=<?=$token?>", function (data) {
        if (confirm(data)) location.href = "<?=$g4[admin_path]?>/mail_list.php";
    });
}
</script>
<? } ?>

<? if ($is_admin) { ?>
<script>
function btn_copy_new() {
    if (!confirm("이 글을 새글로 등록하시겠습니까?")) return false;
    $.get("<?=$board_skin_path?>/mw.proc/mw.copy.new.php?token=<?=$token?>&bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>", function (data) {
        tmp = data.split("|");
        if (tmp[0] == 'true') {
            location.href = "<?=$g4[bbs_path]?>/board.php?bo_table=<?=$bo_table?>&wr_id="+tmp[1];
        } else {
            alert(tmp[1]);
        }
    });
}
</script>
<? } ?>

<? if ($is_category) { ?>
<script>
// 선택한 게시물 분류 변경
function mw_move_cate_one() {
    var sub_win = window.open("<?=$board_skin_path?>/mw.proc/mw.move.cate.php?bo_table=<?=$bo_table?>&chk_wr_id[0]=<?=$wr_id?>",
        "move", "left=50, top=50, width=500, height=550, scrollbars=1");
}
</script>
<? } ?>

<?php if (!is_g5()) { ?>
<script> $(document).ready (function() { resizeBoardImage(<?=$board[bo_image_width]?>); }); </script>
<?php } else { ?>
<script>
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });
</script>
<?php } ?>
<script>
$(".mw_basic_contents_shop_pay_button").click(function () {
    <?php if (!$ca_cash_use) printf('alert("%s"); return;', $is_per_msg); ?>
    buy_contents('<?php echo $bo_table?>', '<?php echo $wr_id?>');
});
</script>

<style>
/* 본문 img */
#mw_basic .mw_basic_view_content img {
    max-width:<?php echo $board['bo_image_width']?>px;
    height:auto; 
}

#mw_basic .mw_basic_comment_content img {
    max-width:<?php echo $board['bo_image_width']-200?>px;
    height:auto; 
}

@media screen and (max-width:<?php echo $board['bo_image_width']?>px) {
    #mw_basic .mw_basic_view_content img {
        max-width: 100% !important;
        max-height: 100%;
    }

    #mw_basic .mw_basic_comment_content img {
        max-width: 100% !important;
        max-height: 100%;
    }
    .videoWrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        padding-top: 25px;
        height: 0;
    }
    .videoWrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
}

<?php echo $cf_css?>
</style>
<link rel="stylesheet" href="<?php echo $board_skin_path?>/sideview.css"/>

<?php
// 팝업공지
$sql = "select * from $mw[popup_notice_table] where bo_table = '$bo_table' order by wr_id desc";
$qry = sql_query($sql, false);
while ($row = sql_fetch_array($qry)) {
    $row2 = sql_fetch("select * from $write_table where wr_id = '$row[wr_id]'");
    if (!$row2) {
        sql_query("delete from $mw[popup_notice_table] where bo_table = '$bo_table' and wr_id = '$row[wr_id]'");
        continue;
    }
    $view = get_view($row2, $board, $board_skin_path, 255);
    mw_board_popup($view, $html);
}

// RSS 수집기
if ($mw_basic[cf_collect] == 'rss' && $rss_collect_path && file_exists("$rss_collect_path/_config.php")) {
    include_once("$rss_collect_path/_config.php");
    if ($mw_rss_collect_config[cf_license]) {
        ?>
        <script>
        $(document).ready(function () {
            $.get("<?=$rss_collect_path?>/ajax.php?bo_table=<?=$bo_table?>");
        });
        </script>
        <?
    }
}

// Youtube 수집기
if ($mw_basic[cf_collect] == 'youtube' && $youtube_collect_path && file_exists("$youtube_collect_path/_config.php")) {
    include_once("$youtube_collect_path/_config.php");
    if ($mw_youtube_collect_config[cf_license]) {
        ?>
        <script>
        $(document).ready(function () {
            $.get("<?=$youtube_collect_path?>/ajax.php?bo_table=<?=$bo_table?>");
        });
        </script>
        <?
    }
}

// kakao 수집기
if ($mw_basic[cf_collect] == 'kakao' && $kakao_collect_path && is_mw_file("$kakao_collect_path/_config.php")) {
    include_once("$kakao_collect_path/_config.php");
    if ($mw_kakao_collect_config['cf_license']) {
        ?>
        <script>
        $(document).ready(function () {
            $.get("<?=$kakao_collect_path?>/ajax.php?bo_table=<?=$bo_table?>");
        });
        </script>
        <?
    }
}

