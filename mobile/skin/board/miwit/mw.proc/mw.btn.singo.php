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

if (!$is_member)
    alert_close("회원만 이용하실 수 있습니다.");

if ($write[mb_id] == $config[cf_admin])
    alert_close("최고관리자의 글은 신고하실 수 없습니다.");

if ($write[mb_id] == $member[mb_id])
    alert_close("본인의 글은 신고할 수 없습니다.");

if ($member[mb_level] < $mw_basic[cf_singo_level])
    alert_close("죄송합니다.\\n\\n신고 권한이 없습니다.");

$sql = "select * from $mw[singo_log_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and (mb_id = '$member[mb_id]' or si_ip = '$_SERVER[REMOTE_ADDR]')";
$row = sql_fetch($sql);
if ($row && !$is_admin)
    alert_close("이미 신고하셨습니다.");

set_session("ss_singo_token", $token = uniqid(time()));

$meta = "<meta name=\"viewport\" content=\"width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0\">";
ob_start();
include_once("$g4[path]/head.sub.php");
$head = ob_get_clean();
$head = str_replace("<head>", "<head>\n{$meta}", $head);
echo $head;
?>

<link rel="stylesheet" href="<?=$board_skin_path?>/style.common.css" type="text/css">

<style type="text/css">
.title { font-size:15px; font-weight:bold; background-color:#efefef; padding:10px; }
table { border:1px solid #ccc; }
td.tdh { background-color:#efefef; font-weight:bold; text-align:center; }
td.tdb { padding:5px; }
.btn { background-color:#efefef; cursor:pointer; }
</style>

<div class="title"> ▶ 게시물 신고 </div>

<form method="post" action="mw.btn.singo.update.php" style="margin:10px;">
<input type="hidden" name="bo_table" value="<?=$bo_table?>"/>
<input type="hidden" name="wr_id" value="<?=$wr_id?>"/>
<input type="hidden" name="parent_id" value="<?=$parent_id?>"/>
<input type="hidden" name="token" value="<?=$token?>"/>

<table class="tout" border="0" cellpadding="0" cellspacing="2" width="95%">
<col width="60"/>
<col width=""/>
<tr>
    <td class="tdh"> 분류 </td>
    <td class="tdb">
        <select name="category" itemname="분류" required>
        <option value="">선택하십시오.</option>
        <option value="무의미한 도배글">무의미한 도배글</option>
        <option value="광고,홍보,방문유도 게시글">광고,홍보,방문유도 게시글</option>
        <option value="성희롱,욕설,비방,반사회적 게시글">성희롱,욕설,비방,반사회적 게시글</option>
        <option value="저작권법 위반 게시글">저작권법 위반 게시글</option>
        <option value="게시판 성격과 맞지 않음">게시판 성격과 맞지 않음</option>
        </select>
    </td>
</tr>
<tr>
    <td class="tdh"> 신고<br/>내용 </td>
    <td class="tdb">
        <textarea name="content" itemname="신고내용" style="width:90%" rows="5" class="mw_basic_textarea"></textarea>
    </td>
</tr>
</table>

<p align="center">
    <input type="submit" value="신  고" class="btn">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" value="창닫기" onclick="self.close()" class="btn">
</p>

</form>


<?
include_once("$g4[path]/tail.sub.php");
