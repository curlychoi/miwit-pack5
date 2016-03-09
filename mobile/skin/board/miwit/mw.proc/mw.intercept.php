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

$viewport = "<meta name=\"viewport\" content=\"width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0\">";
ob_start();
include_once("$g4[path]/head.sub.php");
$head = ob_get_clean();
$head = str_replace("<head>", "<head>\n{$viewport}", $head);
echo $head;

if (!mw_singo_admin($member[mb_id]))
    alert_close("접근 권한이 없습니다.");

if (preg_match("/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/", $mb_id)) {
    $mb_name = $mb_id;
}
else {
    $mb = get_member($mb_id);
    if (!$mb)
        alert_close("존재하지 않는 회원ID 입니다.");

    $mb_name = get_sideview($mb[mb_id], $mb[mb_nick], $mb[mb_homepage], $mb[mb_email]);
    $mb_name.= " ($mb[mb_id]) ";
}

if ($config[cf_admin] == $mb_id)
    alert_close("최고관리자는 접근 차단할 수 없습니다.");

$token = md5(session_id().$member[mb_today_login].$member[mb_login_ip]);
set_session("ss_token", $token);
?>

<style type="text/css">
.container { padding:10px; text-align:center; } 
.title { font-weight:bold; font-size:13px; text-align:center; margin:20px 0 20px 0; }
.content { margin:0 0 20px 0; text-align:left; }
.text {
    border-top:1px solid #9a9a9a;
    border-left:1px solid #9a9a9a;
    border-right:1px solid #d8d8d8;
    border-bottom:1px solid #d8d8d8;
    font-size:12px;
    padding:2px;
    width:95%;
    height:50px;
}
.input { 
    border-top:1px solid #9a9a9a;
    border-left:1px solid #9a9a9a;
    border-right:1px solid #d8d8d8;
    border-bottom:1px solid #d8d8d8;
    font-size:12px;
    padding:2px;
    width:95%;
    height:25px;
}
.options { text-align:center; margin:0 0 10px 0; }
.buttons { text-align:center; }
.btn1 { background-color:#efefef; cursor:pointer; }
</style>
<script src="<?=$g4[path]?>/js/sideview.js"></script>
<script type="text/javascript">
function form_check() {
    if (!confirm("정말 접근차단 하시겠습니까?")) return false;
    return true;
}
function is_all_del() {
    if (!confirm("정말 접근차단하고 전체 게시물을 삭제 하시겠습니까?")) return false;

    fwrite.is_all_delete.value = 1;
    fwrite.submit();
}
function is_all_moving() {
    if (!confirm("정말 접근차단하고 전체 게시물을 이동 하시겠습니까?")) return false;

    var sub_win = window.open("<?=$board_skin_path?>/mw.proc/mw.intercept.table.php?bo_table=<?=$bo_table?>", "move", "left=50, top=50, width=500, height=550, scrollbars=1");
    //fwrite.is_all_move.value = 1;
    //fwrite.submit();
}
</script>

<form name="fwrite" method="post" action="mw.intercept.update.php" onsubmit="return form_check()">
<input type="hidden" name="mb_id" value="<?=$mb_id?>">
<input type="hidden" name="bo_table" value="<?=$bo_table?>">
<input type="hidden" name="form_token" value="<?=$token?>">
<input type="hidden" name="is_all_delete" value="0">
<input type="hidden" name="is_all_move" value="0">
<input type="hidden" name="move_table" value="">

<div class="container">
    <div class="title"> <?=$mb_name?> 회원을 접근차단하시겠습니까? </div>

    <div class="content">
        <div> 관리자 메모 </div>
        <textarea name="mb_memo" class="text"><?=$mb[mb_memo]?></textarea>
    </div>

    <div class="content">
        <div> 단어필터링 </div>
        <input type="text" name="cf_filter" class="input">
    </div>

    <div class="options">
        <input type="checkbox" name="intercept_ip" value="1"> IP 차단
    </div>

    <div class="buttons">
    <input type="submit" value="확     인" class="btn1">
    &nbsp;&nbsp;
    <input type="button" value="게시물 전체 삭제" class="btn1" onclick="is_all_del()">
    &nbsp;&nbsp;
    <input type="button" value="게시물 전체 이동" class="btn1" onclick="is_all_moving()">
    &nbsp;&nbsp;
    <input type="button" value="취     소" class="btn1" onclick="self.close()">
    </div>
</div>

</form>

<?
include_once("$g4[path]/tail.sub.php");
