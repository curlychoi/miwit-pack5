<?php
include_once("_common.php");
@include_once("$g4[path]/lib/mw.builder.lib.php");

$g4[title] = "포인트순위";
include_once("_head.php");

$sql_common = " from $g4[member_table] ";
$sql_where = " where mb_id <> '$config[cf_admin]' and mb_level > 1 and mb_leave_date = '' and mb_intercept_date = '' ";
$sql_order = " order by mb_point desc";

$sql = "select sum(mb_point) as total_point
	$sql_common
	$sql_where";
$row = sql_fetch($sql);
$total_point = number_format($row[total_point]);

$sql = "select count(*) as cnt
	$sql_common
	$sql_where
	and mb_point > '$member[mb_point]'";
$row = sql_fetch($sql);
$my_rank = $row[cnt] + 1;

$sql = "select count(*) as cnt
	$sql_common
	$sql_where";
$row = sql_fetch($sql);
$total_count = $row[cnt];

$page = "";

$rows = 100;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if (!$page) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "select mb_id, mb_name, mb_nick, mb_email, mb_homepage, mb_point 
	$sql_common
	$sql_where
	$sql_order
	limit $from_record, $rows";
$qry = sql_query($sql);

$list = array();
for ($i=0; $row=sql_fetch_array($qry); $i++) {
    $list[$i][rank] = number_format((($page - 1) * $rows) + $i + 1);
    $list[$i][name] = get_sideview($row[mb_id], $row[mb_nick], $row[mb_email]. $row[mb_homepage]); 
    $list[$i][point] = number_format($row[mb_point]);
}

$list_count = sizeof($list);

//$paging = get_paging($rows, $page, $total_page, "?page=");
?>

<script type="text/javascript" src="<?=$g4[path]?>/js/sideview.js"></script>

<style type="text/css">
.info { height:25px; margin:0 0 0 10px; font-size:13px; }
.line { border-top:1px solid #ddd; margin:10px 0 10px 0; }
.point-ranking { }
.point-ranking .head { font-weight:bold; text-align:center; height:30px }
.point-ranking .body { height:30px; padding:0; }
.point-ranking .body .rank { width:50px; text-align:right; }
.point-ranking .body .name { width:150px; text-align:left; padding-left:5px; } 
.point-ranking .body .point { width:100px; text-align:right; }
.paging { clear:both; height:50px; text-align:center; margin:30px 0 0 0; }
</style>

<?
if ($is_member) {
echo "<div class='info'>· 현재 회원님의 포인트는 <strong>".number_format($member[mb_point])."</strong>점 이며, 순위는 <strong>{$my_rank}</strong>등 입니다.</div>";
echo "<div class='info'>· 전체 포인트 : <strong>{$total_point}</strong>점</div>";
}
?>

<div class="line"></div>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr>
    <td width="50%" valign="top">
	<table border=0 cellpadding=0 cellspacing=0 class="point-ranking">
	<? for ($i=0; $i<$rows/2 && $i<$list_count; $i++) { ?>
	<tr>
	    <td class="body"> <div class="rank"><?=$list[$i][rank]?>.</div> </td>
	    <td class="body"> <div class="name"><?=$list[$i][name]?></div> </td>
	    <td class="body"> <div class="point"><?=$list[$i][point]?> 점</div> </td>
	</tr>
	<? } ?>
	</table>
    </td>
    <td width="50%" valign="top">
	<table border=0 cellpadding=0 cellspacing=0 class="point-ranking">
	<? for ($i=$rows/2; $i<$list_count; $i++) { ?>
	<tr>
	    <td class="body"> <div class="rank"><?=$list[$i][rank]?>.</div> </td>
	    <td class="body"> <div class="name"><?=$list[$i][name]?></div> </td>
	    <td class="body"> <div class="point"><?=$list[$i][point]?> 점</div> </td>
	</tr>
	<? } ?>
	</table>
    </td>
</tr>
</table>

<div class="line"></div>

<div class="paging"><?=$paging?></div>

<?
include_once("_tail.php");
?>
