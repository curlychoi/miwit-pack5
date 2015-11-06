<?
/**
 * 월별 포인트 통계 for Gnuboard4
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

include_once("_common.php");
@include_once("$g4[path]/lib/mw.builder.lib.php");

$g4[title] = "월별 포인트순위";
include_once("_head.php");

// 시작월
$start_month = "2008-05";

$months = array();
$sql = "select left(po_datetime, 7) as month from $g4[point_table] where left(po_datetime, 7) >= '$start_month' group by month";
$qry = sql_query($sql);
for ($i=0; $row=sql_fetch_array($qry); $i++) {
    $months[$i] = $row[month];
}
$months_count = count($months);

if (!$month)
    $month = $months[$months_count-1];

if (!in_array($month, $months))
    alert("월 선택이 잘못되었습니다.");

$rows = 100;

$sql = "select mb_id, sum(po_point) as point, left(po_datetime, 7) as month 
	  from $g4[point_table] 
	 where left(po_datetime, 7) = '$month' 
         group by mb_id, month 
         order by point desc";
$qry = sql_query($sql);

$i = 0;
$list = array();
while ($row = sql_fetch_array($qry)) {
    $mb = get_member($row[mb_id]);
    if ($row[mb_id] == $config[cf_admin]) continue; 
    if ($mb[mb_level] < 2) continue;
    if ($mb[mb_leave_datel] != "") continue;
    if ($mb[mb_intercept_datel] != "") continue;
    $list[$i][rank] = $i+1;
    $list[$i][name] = get_sideview($mb[mb_id], $mb[mb_nick], $mb[mb_email]. $mb[mb_homepage]); 
    $list[$i][point] = number_format($row[point]);
    if (++$i >= $rows) break;
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


<strong><?=$g4[title]?></strong> 
&nbsp;&nbsp;&nbsp;
<select id="month" name="month" onchange="location.href='<?=$_SERVER[PHP_SELF]?>?month='+this.value">;
<? for ($i=$months_count-1; $i>=0; $i--) { ?>
<option value="<?=$months[$i]?>"><?=$months[$i]?></option>
<? } ?>
</select>
<script type="text/javascript"> document.getElementById("month").value = "<?=$month?>"; </script> 

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
