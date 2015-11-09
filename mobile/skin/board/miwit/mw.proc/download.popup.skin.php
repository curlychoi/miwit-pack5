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

//if (!$is_member) alert_close("회원만 이용 가능합니다.");

include_once("$g4[path]/head.sub.php");
?>
<style type="text/css">
.ci_title { font-size:15px; font-weight:bold; background-color:#efefef; padding:10px; }
.ci_info { margin:20px 0 0 20px; line-height:20px; font-size:13px; }
.ci_file { border-top:1px solid #9a9a9a; border-left:1px solid #9a9a9a; border-right:1px solid #d8d8d8; border-bottom:1px solid #d8d8d8; height:20px; font-size:12px; padding:2px; }
.ci_button { background-color:#efefef; cursor:pointer; }
.ci_buttons { text-align:center; margin:20px 0 0 0; height:50px; }
</style>

<div class="ci_title"> ▶ 파일다운로드 </div>

<div class="ci_info">
    <?=$mw_basic[cf_download_popup_msg]?>

    <div class="ci_buttons">
        <input type="button" class="ci_button" value="파일 다운로드"
            onclick="location.href='<?=$g4[bbs_path]?>/download.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&no=<?=$no?>'">
    </div>

</div>

<?
include_once("$g4[path]/tail.sub.php");
?>
