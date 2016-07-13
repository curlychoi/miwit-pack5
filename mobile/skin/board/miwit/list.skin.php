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

$mw_is_list = true;
$mw_is_view = false;
$mw_is_write = false;
$mw_is_comment = false;

$list_run_time = get_microtime();

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");
$list_run_time = mw_time_log($list_run_time, "[list] include /mw.lib/mw.skin.basic.lib.php");

if ($board['bo_use_list_view'] && $wr_id) {
    include($board_skin_path.'/mw.proc/mw.seo.php');
    $list_run_time = mw_time_log($list_run_time, "[list] include /mw.proc/mw.seo.php");
}

if (is_g5()) add_stylesheet("<link rel=\"stylesheet\" href=\"{$board_skin_path}/g5.css\"/>");

mw_bomb();
$list_run_time = mw_time_log($list_run_time, "[list] mw_bomb()");

// 실명인증 & 성인인증
if ($mw_basic[cf_kcb_list] && !is_okname()) {
    check_okname();
    $list_run_time = mw_time_log($list_run_time, "[list] check_okname()");
    return;
}

// 컨텐츠샵 멤버쉽
if (function_exists("mw_cash_is_membership")) {
    $is_membership = @mw_cash_is_membership($member[mb_id], $bo_table, "mp_list");
    if ($is_membership == "no")
        ;
    else if ($is_membership != "ok")
        mw_cash_alert_membership($is_membership);
        //alert("$is_membership 회원만 이용 가능합니다.");
    $list_run_time = mw_time_log($list_run_time, "[list] mw_cash_is_membership");
}

// 지업로더로 업로드한 파일
// 하루지난 데이터 삭제 (글작성 완료되지 않은..)
if ($mw_basic[cf_guploader]) {
    $gup_old = date("Y-m-d H:i:s", $g4[server_time] - 86400);
    $sql = "select * from $mw[guploader_table] where bf_datetime <= '$gup_old'";
    $qry = sql_query($sql, false);
    while ($row = sql_fetch_array($qry)) {
        @unlink("$g4[path]/data/guploader/$row[bf_file]");
    }
    sql_query("delete from $mw[guploader_table] where bf_datetime <= '$gup_old'", false);
    $list_run_time = mw_time_log($list_run_time, "[list] guploader delete files..");
}

// 카테고리
$is_category = false;
if ($board[bo_use_category]) 
{
    $is_category = true;
    $category_location = mw_seo_url($bo_table, 0, "&sca=");
    $category_option = mw_get_category_option($bo_table); // SELECT OPTION 태그로 넘겨받음

    if ($mw_basic[cf_default_category] && !$sca) $sca = $mw_basic[cf_default_category];
}

// page 변수 중복 제거
$qstr = preg_replace("/&page=([0-9]+)?/", "", $qstr);
$qstr = preg_replace("/&amp;page=([0-9]+)?/", "", $qstr);
$qstr = preg_replace("/amp;/", "", $qstr);
$qstr = preg_replace("/&amp;/", "&", $qstr);

if (is_g5())  {
    $page_url = mw_seo_url($bo_table, 0, $qstr);
    if (!$qstr)
        $page_url.= '?';
}
else {
    $page_url = mw_seo_url($bo_table, 0, $qstr."&page=");
}

$write_pages = get_paging($config[cf_write_pages], $page, $total_page, $page_url);

// 이전,다음 검색시 페이지 번호 제거
$prev_part_href = preg_replace("/(\&page=.*)/", "", $prev_part_href);
$next_part_href = preg_replace("/(\&page=.*)/", "", $next_part_href);

// 1:1 게시판
if ($mw_basic[cf_attribute] == "1:1" && !$is_admin) {
    require("$board_skin_path/mw.proc/mw.list.1n1.php");
    $list_run_time = mw_time_log($list_run_time, "[list] require /mw.proc/mw.list.1n1.php");
}

// 익명 게시판
if ($mw_basic[cf_attribute] == "anonymous") {
    if (strstr($sfl, "mb_id") || strstr($sfl, "wr_name")) {
        alert("익명게시판에서는 회원아이디 또는 이름으로 검색하실 수 없습니다.");
    }
}

if ($mw_basic[cf_anonymous]) {
    if (strstr($sfl, "mb_id") || strstr($sfl, "wr_name")) {
        alert("익명작성이 가능한 게시판에서는 회원아이디 또는 이름으로 검색하실 수 없습니다.");
    }
}

if ($mw_basic['cf_search_name'] > $member['mb_level']) {
    if (strstr($sfl, "mb_id") || strstr($sfl, "wr_name")) {
        alert("회원아이디 또는 이름으로 검색하실 수 없습니다.");
    }
}

// 글쓰기 버튼에 분류저장
if ($sca && $write_href)
    $write_href .= "&sca=".urlencode($sca);

// 글쓰기 버튼 공지
if ($write_href && $mw_basic[cf_write_notice]) {
    $write_href = "javascript:btn_write_notice('$write_href');";
}

// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 5;
if ($is_category) $colspan++;
if ($is_checkbox) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;
if ($mw_basic[cf_reward]) $colspan+=3;
if ($mw_basic[cf_contents_shop]) $colspan++;
if ($mw_basic[cf_type] == "thumb") $colspan++;
if ($mw_basic[cf_type] == "gall") $colspan = $board[bo_gallery_cols];
else if ($mw_basic[cf_attribute] == "qna") $colspan += 2;

// 목록 셔플
if ($mw_basic[cf_list_shuffle]) { // 공지사항 제외 처리
    $tmp_notice = array();
    $tmp_list = array();
    for ($i=0, $m=sizeof($list); $i<$m; $i++) {
        if ($list[$i][is_notice])
            $tmp_notice[] = $list[$i];
        else
            $tmp_list[] = $list[$i];
    }
    shuffle($tmp_list);
    $list = array_merge($tmp_notice, $tmp_list);
}

$list_count = sizeof($list);

$list_id = array();
for ($i=0; $i<$list_count; $i++) { $list_id[] = $list[$i][wr_id]; }

// 설문 아이콘 표시용
$vote_id = array();
if ($mw_basic[cf_vote] && $list_count) {
    $sql = "select wr_id, vt_id from $mw[vote_table] where bo_table = '$bo_table' and wr_id in (".implode(',', $list_id).")";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $vote_id[] = $row[wr_id];
        // 잘못된 설문 db 보완
        $row2 = sql_fetch("select count(*) as cnt from $mw[vote_item_table] where vt_id = '$row[vt_id]'");
        if (!$row2[cnt])
            sql_query("delete from $mw[vote_table] where vt_id = '$row[vt_id]'");
    }
    $list_run_time = mw_time_log($list_run_time, "[list] vote icon and db check");
}

// 퀴즈 아이콘 표시용
$quiz_id = array();
if ($mw_basic[cf_quiz] && $mw_quiz && $list_count) {
    $sql = "select wr_id, qz_id from $mw_quiz[quiz_table] where bo_table = '$bo_table' and wr_id in (".implode(',', $list_id).")";
    $qry = sql_query($sql, false);
    while ($row = sql_fetch_array($qry)) {
        $quiz_id[] = $row[wr_id];
    }
    $list_run_time = mw_time_log($list_run_time, "[list] quiz icon ");
}

// 자폭 아이콘 표시용
$bomb_id = array();
if ($mw_basic[cf_bomb_level] && $list_count) {
    $sql = "select wr_id from $mw[bomb_table] where bo_table = '$bo_table' and wr_id in (".implode(',', $list_id).")";
    $qry = sql_query($sql, false);
    while ($row = sql_fetch_array($qry)) {
        $bomb_id[] = $row[wr_id];
    }
    $list_run_time = mw_time_log($list_run_time, "[list] bomb icon ");
}

$new_time = date("Y-m-d H:i:s", $g4[server_time] - ($board[bo_new] * 3600));
$row = sql_fetch(" select count(*) as cnt from $write_table where wr_is_comment = 0 and wr_datetime >= '$new_time' ");
$new_count = $row[cnt];
$list_run_time = mw_time_log($list_run_time, "[list] new_count");

// 제목이 두줄로 표시되는 경우 이 코드를 사용해 보세요.
// <nobr style='display:block; overflow:hidden; width:000px;'>제목</nobr>
if (is_reaction_test())
echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">';
?>
<style> <?php echo $cf_css?> </style>
<?php if (!defined("_MW5_")) { ?>
<link href="<?php echo $board_skin_path?>/mw.css/font-awesome-4.3.0/css/font-awesome.css" rel="stylesheet">
<?php } ?>

<? if ($mw_basic[cf_type] == "desc" || $mw_basic[cf_type] == "thumb") { // 요약형, 썸네일형일경우 제목 볼드 ?>
<style type="text/css">
#mw_basic .mw_basic_list_subject a { font-size:13px; font-weight:bold; }
</style>
<? } ?>

<link rel="stylesheet" href="<?=$board_skin_path?>/style.common.css?<?=filemtime("$board_skin_path/style.common.css")?>" type="text/css">
<? if ($mw_basic[cf_social_commerce]) { ?>
    <? if ($mw_basic[cf_type] == 'gall') { ?>
    <link rel="stylesheet" href="<?=$social_commerce_path?>/style-gall.css" type="text/css">
    <? } else { ?>
    <link rel="stylesheet" href="<?=$social_commerce_path?>/style.css" type="text/css">
    <? } ?>
<? } ?>
<? if ($mw_basic[cf_talent_market]) { ?>
    <? if ($mw_basic[cf_type] == 'gall') { ?>
    <link rel="stylesheet" href="<?=$talent_market_path?>/style-gall.css" type="text/css">
    <? } else { ?>
    <link rel="stylesheet" href="<?=$talent_market_path?>/style.css" type="text/css">
    <? } ?>
<? } ?>
<? if ($mw_basic[cf_contents_shop]) { ?>
    <? if ($mw_basic[cf_type] == 'gall') { ?>
    <link rel="stylesheet" href="<?=$mw_cash['path']?>/list-style-gall.css" type="text/css">
    <? } else { ?>
    <link rel="stylesheet" href="<?=$mw_cash['path']?>/list-style.css" type="text/css">
    <? } ?>
<? } ?>
<? if ($mw_basic[cf_reward]) { ?>
    <? if ($mw_basic[cf_type] == 'gall') { ?>
    <link rel="stylesheet" href="<?=$reward_path?>/list-style-gall.css" type="text/css">
    <? } else { ?>
    <link rel="stylesheet" href="<?=$reward_path?>/list-style.css" type="text/css">
    <? } ?>
<? } ?>

<?php include_once($board_skin_path."/mw.proc/mw.asset.php")?>

<!-- 게시판 목록 시작 -->
<table width="<?=$bo_table_width?>" align="center" cellpadding="0" cellspacing="0"><tr><td id=mw_basic>

<?php
if (!is_reaction_test())
if ($mw_basic[cf_include_head] && is_mw_file($mw_basic[cf_include_head]) && strstr($mw_basic[cf_include_head_page], '/l/')) {
    if (!strstr($mw_basic[cf_include_head_page], '/v/') && $wr_id)
        ;
    else {
        include_once($mw_basic[cf_include_head]);
        $list_run_time = mw_time_log($list_run_time, "[list] include mw_basic[cf_include_head]");
    }
}

if ($mw_basic['cf_bbs_banner']) {
    include_once("$bbs_banner_path/list.skin.php"); // 게시판 배너
    $list_run_time = mw_time_log($list_run_time, "[list] bbs_banner_path/list.skin.php");
}

include_once("$board_skin_path/mw.proc/mw.list.hot.skin.php");
$list_run_time = mw_time_log($list_run_time, "[list] /mw.proc/mw.list.hot.skin.php");
?>

<!-- 분류 셀렉트 박스, 게시물 몇건, 관리자화면 링크 -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr height="25">
    <td>
        <form name="fcategory" method="get" style="margin:0;">
        <? if ($is_category && !$mw_basic[cf_category_tab]) { ?>
            <select name=sca onchange="location='<?=$category_location?>'+this.value;">
            <? if (!$mw_basic[cf_default_category]) { ?> <option value=''>전체</option> <? } ?>
            <?=$category_option?>
            </select>
        <? } ?>
        <? if (($mw_basic[cf_type] == "gall" || $mw_basic[cf_social_commerce] || $mw_basic[cf_talent_market]) && $is_checkbox) { ?>
            <input onclick="if (this.checked) all_checked(true); else all_checked(false);" type=checkbox>
        <?}?>


        </form>
    </td>
    <td align="right">
        <?php include($board_skin_path."/mw.proc/mw.top.button.php")?>
    </td>
</tr>
<tr><td height=5></td></tr>
</table>

<?php
include_once("$board_skin_path/mw.proc/mw.notice.top.php");
$list_run_time = mw_time_log($list_run_time, "[list] /mw.proc/mw.notice.top.php");
include_once("$board_skin_path/mw.proc/mw.search.top.php");
$list_run_time = mw_time_log($list_run_time, "[list] /mw.proc/mw.search.top.php");
include_once("$board_skin_path/mw.proc/mw.cash.membership.skin.php");
$list_run_time = mw_time_log($list_run_time, "[list] /mw.proc/mw.cash.membership.skin.php");

if ($is_category && $mw_basic[cf_category_tab]) {
    $category_list = array_map("trim", explode("|", $board[bo_category_list]));
    if ($mw_basic['cf_ca_order']) {
        sort($category_list);
    }
?>
<div class="category_tab">
<ul>
    <?php
    $i = 1;
    $l = 6; // 탭갯수
    $p = 10; // 이 값이 넘어가면 라인이 추가되기 시작
    if (mw_is_mobile_builder() or G5_IS_MOBILE) {
        $l = 3; // 모바일 탭갯수
        $p = 5; // 모바일 이 값이 넘어가면 라인이 추가되기 시작
    }
    $m = sizeof($category_list);
    if (!$mw_basic['cf_default_category']) {
        echo "<li";
        if (!$sca) echo " class='selected'";
        echo "><a href=\"".mw_seo_url($bo_table)."\">전체</a></li>";
        ++$i;
        ++$m;
    }
    //for ($m=sizeof($category_list); $i<$m; ++$i) {
    foreach ($category_list as $cate) {
        echo "<li";
        if (urldecode($sca) == $cate) echo " class='selected'";
        echo "><a href=\"". mw_seo_url($bo_table, 0, "&sca=".urlencode($cate))."\">";
        echo $cate."</a></li>";

        if ($m>=$p && $i++%$l==0 && $i<=$m) echo "</ul><ul>";
    }
    //$rest = $l-(($i-1)%$l);
    $rest = ceil(($i-1)/$l)*$l-($i-1);
    if ($rest > 0 and $m >= $p) {
        for ($z=0; $z<$rest; ++$z) {
            echo "<li class='none'>&nbsp;</li>\n";
        }
    }
    ?>
</ul>
</div>
<? } ?>

<!-- 제목 -->
<form name="fboardlist" id="fboardlist" method="post">
<input type='hidden' name='bo_table' value='<?=$bo_table?>'>
<input type='hidden' name='sfl'  value='<?=$sfl?>'>
<input type='hidden' name='stx'  value='<?=$stx?>'>
<input type='hidden' name='spt'  value='<?=$spt?>'>
<input type='hidden' name='sca'  value='<?=$sca?>'>
<input type='hidden' name='page' value='<?=$page?>'>
<input type='hidden' name='sw' id='sw'  value=''>
<input type='hidden' name='btn_submit' id='btn_submit'  value=''>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td colspan=<?=$colspan?> height=1 class=mw_basic_line_color></td></tr>
<? if ($mw_basic[cf_type] != "gall" && !$mw_basic[cf_social_commerce] && !$mw_basic[cf_talent_market]) { ?>
<tr class=mw_basic_list_title>
    <? if ($is_checkbox) { ?><td width=40><input onclick="if (this.checked) all_checked(true); else all_checked(false);" type=checkbox></td><?}?>
    <? if (!$mw_basic[cf_post_num]) { ?><td width=60 class="media-no-text">번호</td><? } ?>
    <? if (!$mw_basic['cf_list_cate'] && $is_category) {?> <td width="80" class="media-no-text">분류</td> <? }?> 
    <? if (!$mw_basic[cf_post_name] && $mw_basic['cf_name_location']) { ?> <? if ($mw_basic[cf_attribute] != "anonymous") { ?> <td width=95 class="media-no-text">글쓴이</td> <?}?> <?}?>
    <? if ($mw_basic[cf_type] == "thumb") { ?><td width="<?=$mw_basic[cf_thumb_width]+20?>" class="thumb_td"> 이미지 </td><?}?>
    <td>제목</td>
    <?php if ($mw_basic['cf_type'] != 'desc') { ?>
    <? if ($mw_basic[cf_reward]) { ?> <td width=70 class="media-no-text">충전</td> <?}?>
    <? if ($mw_basic[cf_reward]) { ?> <td width=50 class="media-no-text">마감</td> <?}?>
    <? if ($mw_basic[cf_reward]) { ?> <td width=50 class="media-no-text">상태</td> <?}?>
    <? if ($mw_basic[cf_contents_shop]) { ?> <td width=80 class="media-no-text"><?=$mw_cash[cf_cash_name]?></td> <?}?>
    <? if (!$mw_basic[cf_post_name] && !$mw_basic['cf_name_location']) { ?> <? if ($mw_basic[cf_attribute] != "anonymous") { ?> <td width=95 class="media-no-text">글쓴이</td> <?}?> <?}?>
    <? if ($mw_basic[cf_attribute] == "qna") { ?> <td width=50 class="media-no-text">상태</td> <?}?>
    <? if ($mw_basic[cf_attribute] == "qna" && $mw_basic[cf_qna_point_use]) { ?> <td width=40 class="media-no-text">포인트</td> <?}?>
    <? if (!$mw_basic[cf_post_date]) { ?> <td width=70 class="media-no-text">날짜</td> <?}?>
    <? if (!$mw_basic[cf_list_good] && $is_good) { ?><td width=40 class="media-no-text"><?=subject_sort_link('wr_good', $qstr2, 1)?>추천</a></td><?}?>
    <? if (!$mw_basic[cf_list_nogood] && $is_nogood) { ?><td width=40 class="media-no-text"><?=subject_sort_link('wr_nogood', $qstr2, 1)?>비추천</a></td><?}?>
    <? if (!$mw_basic[cf_post_hit]) { ?> <td width=70 class="media-no-text"><?=subject_sort_link('wr_hit', $qstr2, 1)?>조회</a></td> <?}?>
    <?php } // $mw_basic['cf_type'] != 'desc' ?>
</tr>
<tr><td colspan=<?=$colspan?> height=1 class=mw_basic_line_color></td></tr>
<tr><td colspan=<?=$colspan?> height=3 style="background-color:#efefef;"></td></tr>
<tr><td colspan=<?=$colspan?> height=3 style=""></td></tr>
<? } ?>
<? if ($mw_basic[cf_type] == "gall") { ?> <tr><td colspan=<?=$colspan?> height=10></td></tr> <? } ?>

<!-- 목록 -->
<? $mw_membership = array(); ?>
<? $mw_membership_icon = array(); ?>

<?php
$line_number = 0;
for ($i=0,$list_size=count($list); $i<$list_size; ++$i) {

$list[$i] = mw_list_icon($list[$i]);

$html = 0;
if (strstr($list[$i]['wr_option'], "html1"))
    $html = 1;
else if (strstr($list[$i]['wr_option'], "html2"))
    $html = 2;

if ($mw_basic[cf_include_list_main] && is_mw_file($mw_basic[cf_include_list_main])) {
    include($mw_basic[cf_include_list_main]);
}

mw_basic_move_cate($bo_table, $list[$i][wr_id]);
$list_run_time = mw_time_log($list_run_time, "[list] mw_basic_move_cate()");

$reply = $list[$i]['wr_reply'];

$list[$i]['reply'] = "";
if (strlen($reply) > 0) {
    for ($k=0; $k<strlen($reply); $k++)
        $list[$i]['reply'] .= ' &nbsp;&nbsp; ';
}

$ca_color = '';
if ($sca && $mw_category) {
    $ca_color = $mw_category['ca_color'];
}
else {
    if (!$mw_category_list[$list[$i]['ca_name']])
        $mw_category_list[$list[$i]['ca_name']] = mw_category_info($list[$i]['ca_name']);

    $ca_color = $mw_category_list[$list[$i]['ca_name']]['ca_color'];
}

$ca_color_style = '';
if ($ca_color)
    $ca_color_style = " style='color:#{$ca_color}' ";

// 댓글감춤
if ($list[$i][wr_comment_hide])
    $list[$i][comment_cnt] = 0;

// 호칭
$list[$i][name] = get_name_title($list[$i][name], $list[$i][wr_name]);
$list[$i][name] = mw_sideview($list[$i][name]);

$list[$i]['subject'] = $list[$i]['wr_subject'];
$list[$i]['subject'] = mw_reg_str($list[$i]['subject']);
$list[$i]['subject'] = bc_code($list[$i]['subject'], 0);
$list[$i]['subject'] = conv_subject($list[$i]['subject'], $board['bo_subject_len'], "…");
if (strstr($sfl, 'subject')) {
    $list[$i]['subject'] = search_font($stx, $list[$i]['subject']);
}

// 멤버쉽 아이콘
if (function_exists("mw_cash_membership_icon") && $list[$i][mb_id] != $config[cf_admin])
{
    if (!in_array($list[$i][mb_id], $mw_membership)) {
        $mw_membership[] = $list[$i][mb_id];
        $mw_membership_icon[$list[$i][mb_id]] = mw_cash_membership_icon($list[$i][mb_id]);
        $list[$i][name] = $mw_membership_icon[$list[$i][mb_id]].$list[$i][name];
    }
    else {
        $list[$i][name] = $mw_membership_icon[$list[$i][mb_id]].$list[$i][name];
    }
    $list_run_time = mw_time_log($list_run_time, "[list] mw_cash_membership_icon()");
}

// 익명
if ($list[$i][wr_anonymous] or $mw_basic[cf_attribute] == "anonymous") {
    $list[$i][name] = "익명";
    $list[$i][wr_name] = $list[$i][name];
}

// 공지사항 상단
if ($mw_basic[cf_notice_top] && $mw_basic[cf_type] != 'gall') {
    if ($list[$i][is_notice]) continue;
    if (in_array($list[$i][wr_id], $notice_list) && !$stx) continue;
}

// 리워드
if ($mw_basic[cf_reward]) {
    $reward = sql_fetch("select * from $mw[reward_table] where bo_table = '$bo_table' and wr_id = '{$list[$i][wr_id]}'");
    if ($reward[re_edate] != "0000-00-00" && $reward[re_edate] < $g4[time_ymd]) { // 날짜 지나면 종료
        sql_query("update $mw[reward_table] set re_status = '' where bo_table = '$bo_table' and wr_id = '{$list[$i][wr_id]}'");
        $reward[re_status] = '';
    }
    if ($reward[re_edate] == "0000-00-00")
        $reward[edate] = "&nbsp;";
    else
        $reward[edate] = substr($reward[re_edate], 5, 5);
    $list_run_time = mw_time_log($list_run_time, "[list] reward");
}

// 컨텐츠샵
$mw_price = "";
if ($mw_basic[cf_contents_shop]) {
    if ($mw_basic['cf_contents_shop_category']) { // 분류별 결제
        $sql = sprintf(" select * from %s where bo_table = '%s' and ca_name = '%s'", $mw['category_table'], $bo_table, $list[$i]['ca_name']);
        $ro2 = sql_fetch($sql);

        if ($ro2['ca_cash']) {
            $list[$i]['wr_contents_price'] = $ro2['ca_cash'];
        }
    }

    if ($list[$i][is_notice])
        $mw_price = '&nbsp;';
    elseif (!$list[$i][wr_contents_price])
	$mw_price = "무료";
    else
	$mw_price = number_format($list[$i][wr_contents_price]).$mw_cash[cf_cash_unit];
}

$list[$i] = mw_list_link($list[$i]);

// sns식 날짜표시
if ($mw_basic[cf_sns_datetime]) {
    $list[$i][datetime2] = "<span style='font-size:11px;'>".mw_basic_sns_date($list[$i][wr_datetime])."</span>";
}

if ($mw_basic['cf_time_list'])
    $list[$i]['datetime2'] = mw_get_date($list[$i]['wr_datetime'], $mw_basic['cf_time_list']);

// 공지사항 출력 항목
if ($mw_basic[cf_post_name]) $list[$i][name] = "";
if ($mw_basic[cf_post_date]) $list[$i][datetime2] = "";
if ($mw_basic[cf_post_hit]) $list[$i][wr_hit] = "";

if ($list[$i][is_notice]) {
    if ($mw_basic[cf_notice_name]) $list[$i][name] = "";
    if ($mw_basic[cf_notice_date]) $list[$i][datetime2] = "";
    if ($mw_basic[cf_notice_hit]) $list[$i][wr_hit] = "";
    if ($mw_basic[cf_notice_good]) $list[$i][wr_good] = "";
    if ($mw_basic[cf_notice_good]) $list[$i][wr_nogood] = "";
}

// 조회수, 추천수, 글번호에 세자리마다 컴마, 사용
if ($mw_basic[cf_comma]) {
    $list[$i][num] = @number_format($list[$i][num]);
    $list[$i][wr_hit] = @number_format($list[$i][wr_hit]);
    $list[$i][wr_good] = @number_format($list[$i][wr_good]);
    $list[$i][wr_nogood] = @number_format($list[$i][wr_nogood]);
}

// 신고된 게시물
$is_singo = false;
if ($list[$i][wr_singo] && $list[$i][wr_singo] >= $mw_basic[cf_singo_number] && $mw_basic[cf_singo_write_block]) {
    $list[$i][subject] = "신고가 접수된 게시물입니다.";
    $is_singo = true;
}

// 보기차단 게시물
if ($list[$i]['wr_view_block']) {
    $list[$i][subject] = "보기가 차단된 게시물입니다.";
}

// 게시물 아이콘
$write_icon = mw_write_icon($list[$i]);

// 썸네일
$thumb_file = mw_thumb_jpg($thumb_path.'/'.$list[$i]['wr_id']);
$thumb2_file = mw_thumb_jpg($thumb2_path.'/'.$list[$i]['wr_id']);
$thumb3_file = mw_thumb_jpg($thumb3_path.'/'.$list[$i]['wr_id']);
$thumb4_file = mw_thumb_jpg($thumb4_path.'/'.$list[$i]['wr_id']);
$thumb5_file = mw_thumb_jpg($thumb5_path.'/'.$list[$i]['wr_id']);

$set_width = $mw_basic[cf_thumb_width];
$set_height = $mw_basic[cf_thumb_height];

if (!$wr_id && !is_mw_file($thumb_file))
{
    $is_thumb = mw_make_thumbnail_row($bo_table, $list[$i]['wr_id'], $list[$i]['wr_content']);
    $list_run_time = mw_time_log($list_run_time, "[list] mw_make_thumbnail_row");

    if (!is_mw_file($thumb_file)) {
        /*
        if (preg_match("/youtu/i", $list[$i]['link'][1])) mw_get_youtube_thumb($list[$i]['wr_id'], $list[$i]['link'][1]);
        else if (preg_match("/youtu/i", $list[$i]['link'][2])) mw_get_youtube_thumb($list[$i]['wr_id'], $list[$i]['link'][2]);
        else if (preg_match("/vimeo/i", $list[$i]['link'][1])) mw_get_vimeo_thumb($list[$i]['wr_id'], $list[$i]['link'][1]);
        else if (preg_match("/vimeo/i", $list[$i]['link'][2])) mw_get_vimeo_thumb($list[$i]['wr_id'], $list[$i]['link'][2]);
        else {
            $pt = mw_youtube_pattern($list[$i]['wr_content']);
            if ($pt) {
                preg_match($pt, $list[$i]['wr_content'], $mat);
                mw_get_youtube_thumb($list[$i]['wr_id'], $mat[1]);
            }
            else {
                $pt = mw_vimeo_pattern($list[$i]['wr_content']);
                if ($pt) {
                    preg_match($pt, $list[$i]['wr_content'], $mat);
                    mw_get_vimeo_thumb($list[$i]['wr_id'], $mat[1]);
                }
            }
        }
        $list_run_time = mw_time_log($list_run_time, "[list] youtube or vimeo");
        */
    }
}
else {
    $thumb_size = @getimagesize($thumb_file);

    $set_width = $mw_basic[cf_thumb_width];
    $set_height = $mw_basic[cf_thumb_height];

    if ($mw_basic[cf_thumb_keep]) {
        //$size = @getimagesize($thumb_file);
        $size = mw_thumbnail_keep($thumb_size, $set_width, $set_height);
        $set_width = $size[0];
        $set_height = $size[1];
    }

    if ($thumb_size[0]) {
        if ($thumb_size[0] != $set_width || $thumb_size[1] != $set_height) {
            thumb_log($thumb_file, 'list-resize');
            mw_make_thumbnail($mw_basic[cf_thumb_width], $mw_basic[cf_thumb_height],
                $thumb_file, $thumb_file, $mw_basic[cf_thumb_keep], $list[$i]['wr_datetime']);
            $list_run_time = mw_time_log($list_run_time, "[list] resize thumbnail");
        }
    }
}

if ($mw_basic[cf_social_commerce])
{
    $a = include("$social_commerce_path/list.skin.php");    
    if (!$a) continue;
    $list_run_time = mw_time_log($list_run_time, "[list] include /social_commerce/list.skin.php");
}
else if ($mw_basic[cf_talent_market])
{
    $a = include("$talent_market_path/list.skin.php");    
    if (!$a) continue;
    $list_run_time = mw_time_log($list_run_time, "[list] include /talent_marekt/list.skin.php");
}
else if ($mw_basic[cf_type] == "gall" && $mw_basic[cf_contents_shop])
{
    $a = include("{$mw_cash['path']}/list.skin.php");    
    if (!$a) continue;
    $list_run_time = mw_time_log($list_run_time, "[list] include /cybercash/list.skin.php");
}
else if ($mw_basic[cf_type] == "gall" && $mw_basic[cf_reward])
{
    $a = include("{$reward_path}/list.skin.php");    
    if (!$a) continue;
    $list_run_time = mw_time_log($list_run_time, "[list] include /reward/list.skin.php");
}
else if ($mw_basic[cf_type] == "gall")
{
    if (!is_g5() and $list[$i][is_notice]) continue;
    if (is_g5()) {
        include_once(G5_LIB_PATH.'/thumbnail.lib.php');
        $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);
    }

    $is_lock_thumb = false;
    if (!is_mw_file($thumb_file)) {
        if ($list[$i]['file'][0]['view'])
            $thumb_file = $list[$i]['file'][0]['path'].'/'.$list[$i]['file'][0]['file'];
        if (!is_mw_file($thumb_file))
            $thumb_file = mw_get_noimage();
        $thumb_width = "width='$mw_basic[cf_thumb_width]'";
        $thumb_height = "height='$mw_basic[cf_thumb_height]'";
    }
    else if ($list[$i][icon_secret] || $list[$i][is_secret] || $list[$i][wr_view_block] || $list[$i][wr_key_password]) {
        $thumb_file = $board_skin_path.'/img/lock.png';
        $thumb_width = "width='$mw_basic[cf_thumb_width]'";
        $thumb_height = "height='$mw_basic[cf_thumb_height]'";
        $is_lock_thumb = true;
    }
    else {
        $thumb_width = "";
        $thumb_height = "";
    }

    $style = "";
    $class = "";
    if ($list[$i][is_notice]) $style = " class=mw_basic_list_notice";

    if ($wr_id == $list[$i][wr_id]) { // 현재위치
        $style = " class=mw_basic_list_num_select";
        $class = " select";
    }

    $td_width = (int)(100 / $board[bo_gallery_cols]);

    // 제목스타일
    if ($mw_basic[cf_subject_style]) {
        $style .= " style='font-family:{$list[$i][wr_subject_font]}; ";
        if ($list[$i][wr_subject_color] && $wr_id != $list[$i]['wr_id'])
            $style .= " color:{$list[$i][wr_subject_color]}";

        if ($list[$i][wr_subject_bold]) {
            $style .= "; font-weight:bold; ";
        }
        $style .= " '";
    }

    $list[$i][subject] = "<span{$style}>{$list[$i][subject]}</span></a>";

    $is_notice_thumb = '';
    if (is_notice($list[$i]['wr_id']) && $thumb_file == mw_get_noimage()) {
        $thumb_file = $board_skin_path.'/img/notice.png';
        $is_notice_thumb = true;
    }

    //if (($line_number+1)%$colspan==1) echo "<tr>";
    if (!$line_number) echo "<tr><td>";
?>
    <!--<td width="<?=$td_width?>%" class="mw_basic_list_gall <?=$class?>">-->

        <div class="mw_basic_list_gall">

        <?php if ($is_checkbox) { ?>
        <div class="gall_check"><input type=checkbox name=chk_wr_id[] value="<?=$list[$i][wr_id]?>"></div>
        <?php } ?>

        <div class="box" style="width:<?=$mw_basic[cf_thumb_width]+35?>px">
        <table border="0" cellspacing="0" cellpadding="0" class="gall_list">
        <tr> 
            <td>
                <?php if (0) {//$list[$i]['ca_name']) { ?>
                <div class="gall_info" style="float:left;">
                    <i class="fa fa-cube"></i> <?php echo $list[$i]['ca_name']?>
                </div>
                <?php } ?>

 
                <?php if (0) { //$list[$i]['datetime2']) { ?>
                <div class="gall_info" style="float:right;">
                    <i class="fa fa-calendar"></i> <?php echo $list[$i]['datetime2']?>
                </div>
                <?php } ?>

                <div class="gall_image">
                    <?php if ($list[$i][icon_new]) { echo "<div class='icon_gall_new'><img src='{$board_skin_path}/img/icon_gall_new.png'></div>"; } ?>
                    <?php if ($is_notice_thumb or $is_lock_thumb) { ?>
                        <div><a href="<?=$list[$i][href]?>" style="background:url(<?php echo $thumb_file?>) no-repeat center center; display:block; border:1px solid #ddd; <?php echo "width:{$set_width}px; height:{$set_height}px; text-align:left;"?>" class="thumb"></a></div>
                    <?php } else { ?>
                        <div><a href="<?=$list[$i][href]?>"><!--
                        --><img src="<?=$thumb_file?>" class="thumb"
                        <?php if (!$mw_basic[cf_thumb_keep])
                            echo "style='width:{$set_width}px; height:{$set_height}px; text-align:left;'"; ?>></a></div>
                    <?php } ?>
                </div>

                <?php if (0) { //$list[$i]['wr_hit']) { ?>
                <div class="gall_info" style="float:right;">
                    <i class="fa fa-eye"></i> <?php echo $list[$i]['wr_hit']?>
                </div>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td valign="top">

                <div class="gall_subject" style="width:<?=$mw_basic[cf_thumb_width]?>px;">
                    <div><a href="<?=$list[$i][href]?>"><?=$list[$i][subject]?></a></div>
                </div>

                <div class="gall_member">
                    <?php if ($mw_basic['cf_attribute'] != "anonymous" && !$list[$i]['wr_anonymous']) echo $list[$i][name]?>&nbsp;

                    <?php if ($list[$i]['wr_comment']) { ?>
                    <span class="gall_info">
                        <i class="fa fa-comments"></i> <?php echo $list[$i]['wr_comment']?>
                    </span>
                    <?php } ?>
                </div>

                <?php if ($mw_basic['cf_rate_level']) { ?>
                <div class="gall_star">
                <?php
                $rate = mw_rate($bo_table, $list[$i]['wr_id']);
                ?>
                    <div class="gall_star_inner" id="list_rate_<?php echo $list[$i]['wr_id']?>"></div>
                    <script>
                    $(document).ready(function () {
                        $("#list_rate_<?php echo $list[$i]['wr_id']?>").mw_star_rate({
                            path : "<?php echo $board_skin_path?>/mw.js/mw.star.rate/",
                            default_value : <?php echo round($rate['rate'], 1)?>,
                            readonly : true,
                            readonly_msg : '',
                            grade: false
                        });
                    });
                    </script>
                </div>
                <?php } ?>

            </td>
        </tr>
        </table></div>

    </div>

    <!--</td>-->
    <?php if ((($line_number+1)%2==0) ) echo '<div class="mobile_list_gall_br"></div>'; ?>
    <?php if ((($line_number+1)%$colspan==0) ) echo '<div class="list_gall_br"></div>'; ?>
    <?php if ($line_number >= $list_count) echo "</td></tr>"; ?>
    <?php //if ($list_count == $i-1) echo "</tr>"; ?>

<?php } else { // $mw_basic[cf_type] == "gall" ?>

<tr align=center <? if ($list[$i][is_notice]) echo "bgcolor='#f8f8f9'"; ?>>

    <? if ($is_checkbox) { ?>
    <!-- 관리자용 체크박스 -->
    <td><input type=checkbox name=chk_wr_id[] value="<?=$list[$i][wr_id]?>"></td>
    <? } ?>

    <!-- 글번호 -->
    <? if (!$mw_basic[cf_post_num]) { ?>
    <td class="media-no-text">
        <?php
	if ($list[$i]['is_notice'] && $mw_basic['cf_notice_hit']) $list[$i]['wr_hit'] = "";

        if ($list[$i]['is_notice']) // 공지사항
            echo "<img src=\"{$board_skin_path}/img/icon_notice.gif\" width=30 height=16>";
        else if ($wr_id == $list[$i]['wr_id']) // 현재위치
            echo "<span class=mw_basic_list_num_select><i class='fa fa-folder-open'></i></span>";
        else if ($list[$i]['icon_new'])
            echo "<span class=mw_basic_list_num_new>{$list[$i]['num']}</span>";
        else // 일반
            echo "<span class=mw_basic_list_num>{$list[$i]['num']}</span>";
        ?>
    </td>
    <? } ?>

    <?php
    if (!$mw_basic['cf_list_cate'] && $is_category) {
        echo "<td class='media-no-text'><a href=\"{$list[$i][ca_name_href]}\" class=mw_basic_list_category {$ca_color_style}>{$list[$i][ca_name]}</a></td>";
    }
    ?>
    <? if (!$mw_basic[cf_post_name] && $mw_basic['cf_name_location']) { ?>
    <? if ($mw_basic[cf_attribute] != "anonymous") { ?>
    <td class="mw_basic_list_name" class="media-no-text"><?=$list[$i][name]?></td><?php }}?>

    <?php
    if ($mw_basic[cf_type] == "thumb") {
        if (!is_mw_file($thumb_file) && $list[$i]['file'][0]['view'])
            $thumb_file = $list[$i]['file'][0]['path'].'/'.$list[$i]['file'][0]['file'];
        if (!is_mw_file($thumb_file))
            $thumb_file = mw_get_noimage();
        if ($list[$i][icon_secret] || $list[$i][is_secret] || $list[$i][wr_view_block] || $list[$i][wr_key_password])
            $thumb_file = $board_skin_path.'/img/lock.png';
        if (is_notice($list[$i]['wr_id']) && $thumb_file == mw_get_noimage())
            $thumb_file = $board_skin_path.'/img/notice.png';
    ?>

    <!-- 썸네일 -->
    <td class=mw_basic_list_thumb><!-- 여백제거
        --><? if ($list[$i][icon_new]) { echo "<div class='icon_gall_new'><img src='{$board_skin_path}/img/icon_gall_new.png'></div>"; } ?><div><a href="<?=$list[$i][href]?>"><img src="<?=$thumb_file?>" width=<?=$mw_basic[cf_thumb_width]?> height=<?=$mw_basic[cf_thumb_height]?> align="absmiddle" class="list_thumb_img"></a></div><!--
    --></td>
    <?php } ?>

    <!-- 글제목 -->
    <td class="mw_basic_list_subject <?php echo $mw_basic['cf_type'] == 'desc' ? 'desc' : '';?>">

        <?php/* if ($is_checkbox) { ?>
        <span class="media-on-text"><input type=checkbox name=chk_wr_id[] value="<?=$list[$i][wr_id]?>"></span>
        <?php }*/ ?>
        <?
        if ($mw_basic[cf_type] == "desc" && is_mw_file($thumb_file)) {
            if ($list[$i][icon_secret] || $list[$i][is_secret] || $list[$i][wr_view_block] || $list[$i][wr_key_password])
                $thumb_file = $board_skin_path.'/img/lock.png'; 

            if (is_notice($list[$i]['wr_id']) && $thumb_file == mw_get_noimage())
                $thumb_file = $board_skin_path.'/img/notice.png';

            echo "<div class='mw_basic_list_thumb thumb_td'>";
            if ($list[$i][icon_new])
                echo "<div class='icon_gall_new'><img src='{$board_skin_path}/img/icon_gall_new.png'></div>";
            echo "<div><a href=\"{$list[$i][href]}\"><img src=\"{$thumb_file}\" width={$mw_basic[cf_thumb_width]} height={$mw_basic[cf_thumb_height]} align='absmiddle' class='list_thumb_img'></a></div>";
            echo "</div>\n";
        }
        if ($mw_basic[cf_type] == "desc") {
            echo "<div class=mw_basic_list_subject_desc>\n";
        }
        echo $list[$i][reply];
        echo $list[$i][icon_reply];
        if ($is_category && $list[$i][ca_name]) {
            //echo "<a href=\"{$list[$i][ca_name_href]}\" class=mw_basic_list_category {$ca_color_style}>[{$list[$i][ca_name]}]</a>&nbsp;";
        }

        if ($mw_basic[cf_read_level] && $list[$i][wr_read_level])
            echo "<span class=mw_basic_list_level>[{$list[$i][wr_read_level]}레벨]</span>&nbsp;";

        $style = "";
        if ($list[$i][is_notice]) $style = " class=mw_basic_list_notice";

        if ($wr_id == $list[$i][wr_id]) // 현재위치
            $style = " class=mw_basic_list_num_select";

        //if ($mw_basic[cf_type] == "list") {
        echo $write_icon;
        //}
        if (!$mw_basic[cf_subject_link] || $board[bo_read_level] <= $member[mb_level]) {
            if (!$mw_basic[cf_board_member] || ($mw_basic[cf_board_member] && $mw_basic[cf_board_member_view]) || $mw_is_board_member || $is_admin) {
                echo "<a href=\"{$list[$i][href]}\">";
            }
        }

        // 제목스타일
        if ($mw_basic[cf_subject_style]) {
            $style .= " style='font-family:{$list[$i][wr_subject_font]}; ";
            if ($list[$i][wr_subject_color] && $wr_id != $list[$i]['wr_id'])
                $style .= " color:{$list[$i][wr_subject_color]}";

            if ($list[$i][wr_subject_bold])
                $style .= "; font-weight:bold; ";
            $style .= " '";
        }

        if ($wr_id == $list[$i]['wr_id'])
            $list[$i][subject] = "<span class='subject_current'>{$list[$i]['subject']}</span>";

        echo "<span class='media-list-subject'{$style}>{$list[$i][subject]}</span></a>\n";

        if ($list[$i][comment_cnt])
            //echo " <span class=mw_basic_list_comment_count>{$list[$i][comment_cnt]}</span>";
            //echo " <a href=\"{$list[$i][comment_href]}\" class=mw_basic_list_comment_count>{$list[$i][comment_cnt]}</a>";
            echo " <a href=\"{$list[$i][comment_href]}\" class=mw_basic_list_comment_count>{$list[$i][wr_comment]}</a>";

        echo " " . $list[$i][icon_update];
        echo " " . $list[$i][icon_new];
        echo " " . $list[$i][icon_file];
        echo " <a target='_blank' href='{$list[$i][link_href][1]}'>" . $list[$i][icon_link] ."</a>";
        echo " " . $list[$i][icon_hot];
        echo " " . $list[$i][icon_secret];

        echo "</div>\n";
        //if ($mw_basic['cf_type'] == 'desc') {

            echo "<div class='list_desc_info media-on-text'>";

            if ($mw_basic['cf_reward']) {
                echo number_format($reward['re_point']).'P';
                echo $reward['edate'];
                echo "<img src='{$board_skin_path}/img/btn_reward_{$reward['re_status']}.gif' align='absmiddle'>";
            }
            if ($mw_basic['cf_contents_shop']) {
                echo "<span class='item'>{$mw_price}</span>";
            }
            if (!$mw_basic['cf_post_name'] && !$mw_basic['cf_name_location']) { 
                if ($mw_basic['cf_attribute'] != "anonymous") {
                    echo "<span class='item'><i class='fa fa-user'></i> ".$list[$i]['name']."</span>";
                }
            }
            if ($mw_basic[cf_attribute] == 'qna') { 
                    if ($list[$i]['reply']) {
                        echo "&nbsp;";
                    }
                    else if ($list[$i]['wr_qna_status'] == 2) {
                        echo "<span class='item'><i class='fa fa-battery-0'></i> 보류</span>";
                    }
                    else if ($list[$i]['wr_qna_status'] == 1) {
                        echo "<span class='item'><i class='fa fa-battery-4'></i> 해결</span>";
                    }
                    else {
                        echo "<span class='item'><i class='fa fa-battery-1'></i> 미해결</span>";
                    }
            }
            if ($mw_basic[cf_attribute] == 'qna' && $mw_basic['cf_qna_point_use']) {
                echo "<span class='item'><i class='fa fa-gift'></i> ".$list[$i]['wr_qna_point']."</span>";
            }
            if (!$mw_basic['cf_post_date']) {
                echo "<span class='item'><i class='fa fa-clock-o'></i> ".$list[$i]['datetime2']."</span>";
            }
            if (!$mw_basic['cf_list_good'] && $is_good) {
                echo "<span class='item media-no-text'><i class='fa fa-thumbs-up'></i> ".$list[$i]['wr_good']."</span>";
            }
            if (!$mw_basic['cf_list_nogood'] && $is_nogood) {
                echo "<span class='item media-no-text'><i class='fa fa-thumbs-down'></i> ".$list[$i]['wr_nogood']."</span>";
            }
            if (!$mw_basic['cf_post_hit']) {
                echo "<span class='item'><i class='fa fa-eye'></i> ".$list[$i]['wr_hit']."</span>";
            }
            echo "</div>";

            if ($mw_basic['cf_type'] == 'desc') {
                $desc = strip_tags($list[$i]['wr_content']);
                if ($list[$i]['wr_contents_preview'])
                    $desc = conv_content($list[$i]['wr_contents_preview'], $html);
                $desc = preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/i", "", $desc);
                $desc = mw_reg_str($desc);
                $desc = cut_str($desc, $mw_basic['cf_desc_len']);
                echo "<div class='mw_basic_list_desc media-no-text'>{$desc}</div>\n";
            }
        //}
        ?>
    </td>
    <?php if ($mw_basic['cf_type'] != 'desc') { ?>
    <? if ($mw_basic[cf_reward]) { ?>
    <td class="mw_basic_list_reward_point media-no-text"><?=number_format($reward[re_point])?> P</td>
    <td class="mw_basic_list_reward_edate media-no-text"><?=$reward[edate]?></td>
    <td class="mw_basic_list_reward_status media-no-text"><img src="<?=$board_skin_path?>/img/btn_reward_<?=$reward[re_status]?>.gif" align="absmiddle"></td>
    <? } ?>
    <? if ($mw_basic[cf_contents_shop]) { ?>
        <td class="mw_basic_list_contents_price media-no-text"><span><?=$mw_price?></span></td><?}?>
    <? if (!$mw_basic[cf_post_name] && !$mw_basic['cf_name_location']) { ?>
    <? if ($mw_basic[cf_attribute] != "anonymous") { ?>
    <td class="mw_basic_list_name media-no-text"><?php echo $list[$i]['name']?></td><?php }}?>
    <? if ($mw_basic[cf_attribute] == 'qna') { ?>
        <td class="mw_basic_list_qna_status media-no-text">
            <?/* if ($list[$i]['reply'] ) { ?>
                &nbsp;
            <? } else { ?>
            <div><img src="<?=$board_skin_path?>/img/icon_qna_<?=$list[$i][wr_qna_status]?>.png"></div>
            <? } */?>
            <?php
            if ($list[$i]['reply']) {
                echo "&nbsp;";
            }
            else if ($list[$i]['wr_qna_status'] == 2) {
                echo "<div class='fa-button center gray' style='width:40px;'> 보류</div>";
            }
            else if ($list[$i]['wr_qna_status'] == 1) {
                echo "<div class='fa-button center gray' style='width:40px;'> 해결</div>";
            }
            else {
                echo "<div class='fa-button center' style='width:40px;'> 미해결</div>";
            }
            ?>
        </td>
    <? } ?>
    <? if ($mw_basic[cf_attribute] == 'qna' && $mw_basic[cf_qna_point_use]) { ?> <td class="mw_basic_list_point media-no-text"><?=$list[$i][wr_qna_point]?></span></td> <?}?>
    <? if (!$mw_basic[cf_post_date]) { ?> <td class="mw_basic_list_datetime media-no-text"><?=$list[$i][datetime2]?></td> <?}?>
    <? if (!$mw_basic[cf_list_good] && $is_good) { ?><td class="mw_basic_list_good media-no-text"><?=$list[$i][wr_good]?></td><? } ?>
    <? if (!$mw_basic[cf_list_nogood] && $is_nogood) { ?><td class="mw_basic_list_nogood media-no-text"><?=$list[$i][wr_nogood]?></td><? } ?>
    <? if (!$mw_basic[cf_post_hit]) { ?> <td class="mw_basic_list_hit media-no-text"><?=$list[$i][wr_hit]?></td> <?}?>
    <?php } // $mw_basic['cf_type'] != 'desc' ?>
</tr>
<? if ($i<$list_size-1) { // 마지막 라인 출력 안함 ?>
<!--<tr><td colspan=<?=$colspan?> height=1 bgcolor=#E7E7E7></td></tr>-->
<tr><td colspan=<?=$colspan?> height=1 class="mw_basic_list_line"></td></tr>
<?}?>
<?}?>
<?  $line_number++; ?>
<?} //$mw_basic[cf_type] == "gall" else?>

<?php
/* if ($mw_basic['cf_type'] == 'gall') {
    $rest = $colspan-($line_number%$colspan);
    if ($rest > 0) {
        for ($z=0; $z<$rest; ++$z) {
            echo "<td>&nbsp;</td>\n";
        }
        echo "</tr>\n";
    }
}*/
?>
<?php if ($line_number == 0) { echo "<tr><td colspan={$colspan} class=mw_basic_nolist>게시물이 없습니다.</td></tr>"; } ?>
<tr><td colspan=<?=$colspan?> class=mw_basic_line_color height=1></td></tr>
</table>

</form>

<!-- 링크 버튼, 검색 -->
<table width=100%>
<tr>
    <td height="40">
        <form name="fsearch" method="get">

        <?php if ($is_checkbox) { ?>
        <script>
        $(document).ready(function () {
            $(".mw_manage_list_title").mouseenter(function () {
                $manage_button = $(this);
                $(".mw_manage_list").css("top", $manage_button.position().top);
                $(".mw_manage_list").css("left", $manage_button.position().left);
                $(".mw_manage_list").css("display", "block");
                $(".mw_manage_list .item").mouseenter(function () {
                    $(this).css("background-color", "#ddd");
                });
                $(".mw_manage_list .item").mouseleave(function () {
                    $(this).css("background-color", "#fff");
                });
            });
            $(".mw_manage_list").mouseleave(function () {
                $(this).css("display", "none");
            });
        });
        </script>

        <button class="mw_manage_list_title fa-button"><i class="fa fa-gear"></i> 관리</button>
        <div class="mw_manage_list">
            <div class="item" onclick="select_delete()"><i class="fa fa-remove"></i> 선택 삭제</div>
            <div class="item" onclick="select_copy('copy')"><i class="fa fa-copy"></i> 선택 복사</div>
            <div class="item" onclick="select_copy('move')"><i class="fa fa-arrow-right"></i> 선택 이동</div>
            <div class="item" onclick="mw_move_cate()"><i class="fa fa-tag"></i> 선택 분류이동</div>
            <div class="item" onclick="mw_notice('up')"><i class="fa fa-bell-o"></i> 선택 공지올림</div>
            <div class="item" onclick="mw_notice('down')"><i class="fa fa-bell-slash-o"></i> 선택 공지내림</div>
            <div class="item" onclick="mw_qna(0)"><i class="fa fa-question"></i> 선택 질문 미해결</div>
            <div class="item" onclick="mw_qna(1)"><i class="fa fa-mortar-board"></i> 선택 질문 해결</div>
            <div class="item" onclick="mw_qna(2)"><i class="fa fa-inbox"></i> 선택 질문 보류</div>
            <div class="item" onclick="select_secret()"><i class="fa fa-inbox"></i> 선택 비밀글</div>
            <div class="item" onclick="select_secret_open()"><i class="fa fa-inbox"></i> 선택 비밀글 해제</div>
        </div><!--mw_manage-->
        <?php } // is_checkbox ?>

        <?php if ($is_admin or $mw_basic['cf_search_level_view'] or ($mw_basic['cf_search_level'] && $mw_basic['cf_search_level'] <= $member['mb_level'])) { ?>
        <span class="media-list-search">
        <input type=hidden name=bo_table value="<?=$bo_table?>">
        <input type=hidden name=sca value="<?=$sca?>">
        <select name=sfl>
            <option value='wr_subject'>제목</option>
            <option value='wr_content'>내용</option>
            <option value='wr_subject||wr_content'>제목+내용</option>
            <? if ($mw_basic[cf_attribute] != "anonymous" && !$mw_basic[cf_anonymous] && $mw_basic['cf_search_name'] <= $member['mb_level']) { ?>
            <option value='mb_id,1'>회원아이디</option>
            <option value='mb_id,0'>회원아이디(코)</option>
            <option value='wr_name,1'>이름</option>
            <option value='wr_name,0'>이름(코)</option>
            <? } ?>
        </select>
        <input name=stx maxlength=15 size=10 itemname="검색어" required value='<?=stripslashes($stx)?>'>
        <select name=sop>
            <option value=and>and</option>
            <option value=or>or</option>
        </select>
        <button type="submit" class="fa-button"><i class="fa fa-search"></i> 검색</button>
        </span>
        <span class="media-on-text">
            <button type="button" class="fa-button" name="search-modal-button" data-target="#search-modal"><i class="fa fa-search"></i> 검색</button>
        </span>
        <? } ?>
        </form>

    </td>
    <td align="right">

        <?php if ($list_href) { ?>
            <!--<a href="<?=$list_href?>"><img src="<?=$board_skin_path?>/img/btn_list.gif" border="0" align="absmiddle"></a>-->
            <a class="fa-button" href="<?php echo $list_href?>"><i class="fa fa-list"></i> 목록</a>
        <?php } ?>
        <?php if ($write_href) { ?>
            <!--<a href="<?=$write_href?>"><img src="<?=$board_skin_path?>/img/btn_write.gif" border="0" align="absmiddle"></a>-->
            <a class="fa-button primary" href="<?php echo $write_href?>"><i class="fa fa-pencil"></i> 글쓰기</a>
        <?php } ?>
    </td>
</tr>
</table>

<div id="search-modal">
    <form name="fsearch" method="get">
        <input type=hidden name=bo_table value="<?=$bo_table?>">
        <input type=hidden name=sca value="<?=$sca?>">

        <select name=sfl>
            <option value='wr_subject'>제목</option>
            <option value='wr_content'>내용</option>
            <option value='wr_subject||wr_content'>제목+내용</option>
            <? if ($mw_basic[cf_attribute] != "anonymous" && !$mw_basic[cf_anonymous] && $mw_basic['cf_search_name'] <= $member['mb_level']) { ?>
            <option value='mb_id,1'>회원아이디</option>
            <option value='mb_id,0'>회원아이디(코)</option>
            <option value='wr_name,1'>이름</option>
            <option value='wr_name,0'>이름(코)</option>
            <? } ?>
        </select>

        <select name=sop>
            <option value=and>and</option>
            <option value=or>or</option>
        </select>

        <input name=stx maxlength=15 size=10 itemname="검색어" required value='<?=stripslashes($stx)?>'>

        <button type="submit" class="fa-button"><i class="fa fa-search"></i> 검색</button>
    </form>
</div><!-- /.modal -->

<script>
$("button[name=search-modal-button]").click(function () {
    $("#search-modal").slideToggle('');
});
</script>

<!-- 페이지 -->
<div class=mw_basic_page>
    <?php if ($prev_part_href) { echo "<a href='$prev_part_href'>이전검색</a>"; } ?>
    <?php echo $write_pages; ?>
    <?php if ($next_part_href) { echo "<a href='$next_part_href'>다음검색</a>"; } ?>
</div>

<?php
if ($mw_basic[cf_include_tail] && is_mw_file($mw_basic[cf_include_tail]) && strstr($mw_basic[cf_include_tail_page], '/l/')) {
    if (!strstr($mw_basic[cf_include_tail_page], '/v/') && $wr_id)
        ;
    else {
        include_once($mw_basic[cf_include_tail]);
        $list_run_time = mw_time_log($list_run_time, "[list] include mw_basic[cf_include_tail]");
    }
}
?>

</td></tr></table>


<script>
<?  if (!$mw_basic[cf_category_tab]) { ?>
if ('<?=$sca?>') document.fcategory.sca.value = '<?=urlencode($sca)?>';
<? } ?>
if ('<?=$stx?>') {
    $("form[name=fsearch]").find("select[name=sfl]").val("<?php echo $sfl?>");
    $("form[name=fsearch]").find("select[name=sop]").val("<?php echo $sop?>");
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


<? if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function check_confirm(str) {
    var f = document.fboardlist;
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(str + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }
    return true;
}

// 선택한 게시물 삭제
function select_delete() {
    var f = document.fboardlist;

    $("#admin_action").val('');

    str = "삭제";
    if (!check_confirm(str))
        return;

    if (!confirm("선택한 게시물을 정말 "+str+" 하시겠습니까?\n\n한번 "+str+"한 자료는 복구할 수 없습니다"))
        return;

    f.btn_submit.value = "선택삭제";
    <?php if (is_g5()) { ?>
    f.action = "<?php echo $g4['bbs_path']?>/board_list_update.php";
    <?php } else { ?>
    f.action = "<?php echo $g4['bbs_path']?>/delete_all.php";
    <?php } ?>
    f.submit();
}

// 선택한 게시물 비밀글
function select_secret() {
    var f = document.fboardlist;

    $("#admin_action").val('');

    if (!check_confirm("비밀글로 변경"))
        return;

    if (!confirm("선택한 게시물을 정말 비밀글로 변경 하시겠습니까?"))
        return;

    f.action = "<?php echo $board_skin_path?>/secret_all.php?opt=secret";
    f.submit();
}

// 선택한 게시물 해제 비밀글
function select_secret_open() {
    var f = document.fboardlist;

    $("#admin_action").val('');

    if (!check_confirm("비밀글에서 공개글로 변경"))
        return;

    if (!confirm("선택한 게시물을 정말 비밀글에서 공개글로 변경 하시겠습니까?"))
        return;

    f.action = "<?php echo $board_skin_path?>/secret_all.php";
    f.submit();
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    $("#admin_action").val('');

    if (sw == "copy")
        str = "복사";
    else
        str = "이동";

    if (!check_confirm(str))
        return;

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = "<?=$board_skin_path?>/move.php";
    f.submit();
}

// 선택한 게시물 분류 변경
function mw_move_cate() {
    var f = document.fboardlist;

    $("#admin_action").val('');

    if (!check_confirm("분류이동"))
        return;

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.target = "move";
    f.action = "<?=$board_skin_path?>/mw.proc/mw.move.cate.php";
    f.submit();
}

function mw_notice(sw) {
    $("#admin_action").val('');

    var str = '';

    if (sw == 'up') {
        str = '공지로 등록';
        if (!confirm("공지로 등록하시겠습니까?")) {
            return false;
        }
    } else {
        str = '공지 내림';
        if (!confirm("공지를 내리시겠습니까?")) {
            return false;
        }
    }

    if (!check_confirm(str)) 
        return;

    $("#sw").val(sw);

    $.post("<?=$board_skin_path?>/mw.proc/mw.notice.check.php", $("#fboardlist").serialize(), function(data) {
        alert(data);
        location.reload();
    });
}

function mw_qna(sw) {
    $("#admin_action").val('');

    var m = '';

    switch (sw) {
        case 0: m = '미해결'; break;
        case 1: m = '해결'; break;
        case 2: m = '보류'; break;
    }
    if (!confirm("질문을 " + m + " 처리 하시겠습니까?")) { return false; }

    $("#sw").val(sw);

    $.post("<?=$board_skin_path?>/mw.proc/mw.qna.check.php", $("#fboardlist").serialize(), function(data) {
        alert(data);
        location.reload();
    });
}
</script>
<? } ?>

<script>
$(window).load(function () {
    $(".icon_gall_new").each(function () {
        var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
        <?php if ($mw_basic['cf_type'] == 'gall') { ?>
        var wt = $(this).closest('td').width();
        //var wi = $(this).next().find("img").width();
        var wi = $(this).next().width();
        var ma = (wt-wi)/2-17;
        if (!g4_is_ie) {
            $(this).css('margin-left', ma+'px');
            $(this).css('margin-top', '-10px');
        }
        if (navigator.appVersion.indexOf("MSIE 10") !== -1) {
            $(this).css('margin-left', ma+'px');
        }
        <?php } else if ($mw_basic['cf_type'] == 'thumb') { ?>
        if (is_chrome)
            ;//$(this).css('margin-left', '-10px');
        <?php } ?>
        $(this).css('display', 'block');
    });
});
</script>

<style>
<?php if ($mw_basic['cf_type'] == 'desc') echo "#mw_basic .list_desc_info { display:block; }".PHP_EOF; ?>
@media screen and (max-width:500px) {
    #mw_basic .mw_basic_list_gall a.thumb {
        width:100% !important;
        height:<?php echo round((130/$mw_basic['cf_thumb_height'])*$mw_basic['cf_thumb_height'])?>px !important;
    }
}
<?php echo $cf_css?>
</style>
<link rel="stylesheet" href="<?php echo $board_skin_path?>/sideview.css"/>
<!-- 게시판 목록 끝 -->

<?
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
    $list_run_time = mw_time_log($list_run_time, "[list] mw_board_popup");
}

// RSS 수집기
if ($mw_basic[cf_collect] == 'rss' && $rss_collect_path && is_mw_file("$rss_collect_path/_config.php")) {
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
    $list_run_time = mw_time_log($list_run_time, "[list] rss-collect");
}

// Youtube 수집기
if ($mw_basic[cf_collect] == 'youtube' && $youtube_collect_path && is_mw_file("$youtube_collect_path/_config.php")) {
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
    $list_run_time = mw_time_log($list_run_time, "[list] youtube-collect");
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
    $list_run_time = mw_time_log($list_run_time, "[list] kakao-collect");
}

