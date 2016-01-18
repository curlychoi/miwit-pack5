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

include_once("_common.php");
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if (!($mw_basic[cf_post_history] && $member[mb_level] >= $mw_basic[cf_post_history_level])) {
    alert_close("로그를 열람할 권한이 없습니다.");
}

$sql = "select * from $mw[post_history_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and ph_id = '$ph_id'";
$view = sql_fetch($sql);

if (strstr($view[ph_option], "html1"))
    $html = 1;
else if (strstr($view[ph_option], "html2"))
    $html = 2;

$view[content] = conv_content($view[ph_content], $html);

$colspan = 2;

$g4[title] = "변경기록";
include_once("$g4[path]/head.sub.php");
?>

<script language="javascript" src="<?=$g4[path]?>/js/sideview.js"></script>

<link rel="stylesheet" href="<?=$board_skin_path?>/style.common.css" type="text/css">

<style type="text/css">
body { font-size:12px; color:#555; }
a { color:#555; text-decoration:none; }
td { font-size:12px; color:#555; }
td.head { text-align:center; font-weight:bold; height:30px; background-color:#f8f8f8; }
td.body { padding-left:10px; }
td a { color:#555; text-decoration:none; }
table caption { font-size:13px; color:#555; font-weight:bold; text-decoration:none; text-align:left; }
</style>

<table border=0 cellpadding=0 cellspacing=1 style="margin:10px; border:1px solid #ddd;">
<caption> 변경기록 : <?=cut_str($write[wr_subject], 50)?> </caption>
<tr>
    <td width=100 class=head> 변경한이 </td>
    <td width=450 class=body> <?=get_sideview($view[mb_id], $view[ph_name])?> </td>
</tr>
<tr><td colspan=<?=$colspan?> bgcolor="#f8f8f8"></td></tr>
<tr>
    <td class=head> 변경한IP </td>
    <td class=body> <?=$view[ph_ip]?> </td>
</tr>
<tr><td colspan=<?=$colspan?> bgcolor="#f8f8f8"></td></tr>
<tr>
    <td class=head> 변경일시 </td>
    <td class=body> <?=$view[ph_datetime]?> </td>
</tr>
<tr><td colspan=<?=$colspan?> bgcolor="#f8f8f8"></td></tr>
<tr>
    <td class=head> 변경전 제목 </td>
    <td class=body> <?=$view[ph_subject]?> </td>
</tr>
<tr><td colspan=<?=$colspan?> bgcolor="#f8f8f8"></td></tr>
<tr>
    <td colspan=<?=$colspan?> class=body style="height:100px; padding:10px;"> <?=$view[content]?> </td>
</tr>
<tr><td colspan=<?=$colspan?> bgcolor="#f8f8f8"></td></tr>
</table>

<p align=center>
<input type="button" value="복     원" onclick="restore_contents()">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="목     록" onclick="location.href='mw.history.list.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&<?=$qstr?>';">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="닫     기" onclick="self.close();">
</p>
<br/>
<br/>

<script>
function restore_contents() {
    ans = confirm("정말 복원하시겠습니까?");
    if (!ans) return;


    location.href = "mw.history.restore.php?bo_table=<?php echo $bo_table?>&wr_id=<?php echo $wr_id?>&ph_id=<?php echo $ph_id?>";
    opener.location.reload();

    /*$.get("mw.history.restore.php", {
        "bo_table": "<?php echo $bo_table?>",
        "wr_id": "<?php echo $wr_id?>",
        "ph_id": "<?php echo $ph_id?>"
    }, function (str) {
        alert(str);
        opener.location.reload();
    });*/
}
</script>

<?php

include_once("$g4[path]/tail.sub.php");
