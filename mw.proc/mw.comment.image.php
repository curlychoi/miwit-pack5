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

if (!$bo_table)
    alert_close("bo_table 이 없습니다.");

include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");
include_once($board_skin_path."/mw.proc/mw.comment.image.config.php");

if (!$is_member)
    alert_close("회원만 이용 가능합니다.");

if (!$is_admin && $member['mb_id'] != $mb_id)
    alert_close("자신의 사진만 변경 가능합니다.");

if (!mb_id_check($mb_id))
    alert_close("존재하지 않는 회원입니다.");

$viewport = "<meta name=\"viewport\" content=\"width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0\">";
ob_start();
include_once($g4['path']."/head.sub.php");
$head = ob_get_clean();
$head = str_replace("<head>", "<head>\n{$viewport}", $head);
echo $head;
?>
<style>
.ci_title { font-size:15px; font-weight:bold; background-color:#efefef; padding:10px; }
.ci_info { margin:20px 0 0 20px; line-height:20px; font-size:13px; }
.ci_file {  }
.ci_button { background-color:#efefef; cursor:pointer; }
.ci_buttons { text-align:center; margin:50px 0 0 0; }
</style>

<div class="ci_title"> ▶ 회원사진등록 </div>

<div class="ci_info">
댓글에 출력되는 사진을 업로드하실 수 있습니다.<br/>
가로 <?php echo $cf_x?>px, 세로 <?php echo $cf_y?>px 사이즈, <?php echo get_filesize($cf_size)?> byte 용량이하의<br/> jpg, gif, png 이미지만 등록해주세요.<br/><br/>

<form method="post" action="mw.comment.image.update.php" enctype="multipart/form-data">
<input type="hidden" name="bo_table" value="<?php echo $bo_table?>">
<input type="hidden" name="mb_id" value="<?php echo $mb_id?>">
<input type="file" size="30" class="ci_file" name="comment_image"><br/>

<?php if (file_exists($comment_image_path.'/'.$mb_id)) { ?>
<img src="<?php echo $comment_image_path.'/'.$mb_id?>" style="border:1px solid #ddd; margin:10px;" width="58" height="58"><br/>
<input type="checkbox" name="image_del"> 기존 이미지를 삭제합니다.
<?php } ?>

<div class="ci_buttons">
    <input type="submit" class="ci_button" value="등     록"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" class="ci_button" value="닫     기" onclick="self.close()">
</div>
</form>

</div>

<?php

include_once($g4['path']."/tail.sub.php");
