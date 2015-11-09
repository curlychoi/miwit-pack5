<?
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

include_once("_common.php");
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if (!($mw_basic[cf_post_history] && $member[mb_level] >= $mw_basic[cf_post_history_level])) {
    alert_close("로그를 열람할 권한이 없습니다.");
}

$sql_common = " from $mw[post_history_table] ";
$sql_order = " order by ph_id desc ";
$sql_search = " where bo_table = '$bo_table' and wr_id = '$wr_id' ";

$sql = "select count(*) as cnt
        $sql_common
        $sql_search";
$row = sql_fetch($sql);
$total_count = $row[cnt];

$rows = $config[cf_page_rows];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "select *
        $sql_common
        $sql_search
        $sql_order
        limit $from_record, $rows ";
$qry = sql_query($sql);

$list = array();
for ($i=0; $row = sql_fetch_array($qry); ++$i) {
    $list[$i] = $row;
    $list[$i][num] = $total_count - ($page - 1) * $rows - $i;
    $list[$i][href] = "mw.history.view.php?bo_table={$row[bo_table]}&wr_id={$row[wr_id]}&ph_id={$row[ph_id]}{$qstr}";
}

$write_pages = get_paging($config[cf_write_pages], $page, $total_page, "./mw.history.list.php?bo_table={$bo_table}&wr_id={$wr_id}{$qstr}&page=");

$colspan = 3;

$g4[title] = "변경기록";
include_once("$g4[path]/head.sub.php");
?>

<script language="javascript" src="<?=$g4[path]?>/js/sideview.js"></script>

<link rel="stylesheet" href="<?=$board_skin_path?>/style.common.css" type="text/css">

<style type="text/css">
body { font-size:12px; color:#555; }
a { color:#555; text-decoration:none; }
td { font-size:12px; color:#555; }
td a { color:#555; text-decoration:none; }
table caption { font-size:13px; color:#555; font-weight:bold; text-decoration:none; text-align:left; }
</style>

<table border=0 cellpadding=0 cellspacing=1 style="width:95%; margin:10px; border:1px solid #ddd;">
<caption> 변경기록 : <?=cut_str($write[wr_subject], 50)?> </caption>
<tr style="text-align:center; font-weight:bold; height:30px; background-color:#f8f8f8;">
    <td width=10%> 번호 </td>
    <td width=40%> 변경한이 </td>
    <td width=50%> 변경일시 </td>
</tr>
<tr><td colspan=<?=$colspan?> bgcolor="#dddddd"></td></tr>
<? for ($i=0; $i<count($list); $i++) { ?>
<tr style="text-align:center; height:25px;">
    <td> <?=$list[$i][num]?> </td>
    <td> <?=get_sideview($list[$i][mb_id], $list[$i][ph_name])?> </td>
    <td> <a href="<?=$list[$i][href]?>"><?=$list[$i][ph_datetime]?></a> </td>
</tr>
<tr><td colspan=<?=$colspan?> bgcolor="#f8f8f8"></td></tr>
<? } ?>
<? if ($i == 0) { ?>
<tr><td colspan=<?=$colspan?> height=100 align=center> 변경 기록이 없습니다. </td></tr>
<? } ?>
</table>

<p align=center>
<?=$write_pages?>
</p>
<p align=center>
<br/>
<br/>
<!--
<input type="button" value="목     록" onclick="location.href='mw.history.list.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&<?=$qstr?>';">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
-->
<input type="button" value="닫     기" onclick="self.close();">
</p>
<br/>
<br/>

<?
include_once("$g4[path]/tail.sub.php");
?>
