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

if ($is_admin != 'super')
    alert_close("접근 권한이 없습니다.");

$g4[title] = "권한별 설정";
include_once("$g4[path]/head.sub.php");

$colspan = 3;
?>
<style type="text/css">
body { padding:5px; }
.f { height:30px; font:normal 11px 'dotum'; }
.fp { font:normal 11px 'dotum'; margin:5px 0 0 0; float:left; }
.fp a { font:bold 11px 'dotum'; }
.fb { float:right; }
.t { border-top:2px solid #818181; border-bottom:2px solid #818181; }
.t .tt { background-color:#f2f2f2; font:normal 11px 'dotum'; text-align:center; height:30px; color:#3f4ea1; border-bottom:1px solid #d5d5d5; }
.t .tl { border-bottom:1px solid #e8e8e8; height:50px; text-align:center; font:normal 11px 'dotum'; }
.t .tl a { font:normal 11px 'dotum'; }
.t .tn { background-color:#fff; padding:100px 0 100px 0; text-align:center; color:#bbb; border-bottom:1px solid #d5d5d5; }
.b { background-color:#efefef; cursor:pointer; font:normal 11px 'dotum'; }
.ed.r { text-align:right; }
</style>

<? if ($is_admin == "super") { ?>
<div class="f">
    <div class="fp">
        <a href="<?php echo $_SERVER['SCRIPT_NAME']?>"><?=$g4['title']?></a>
        <input type="checkbox" onclick="$('.use').attr('checked', this.checked)">
    </div>
    <div class="fb">
        <input type="button" class="b" value="닫 기" onclick="self.close()">
    </div>
</div>
<? } ?>

<form name="fwrite" method="post" action="mw.level.update.php">
<input type="hidden" name="bo_table" value="<?php echo $bo_table?>">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="t">
<tr>
    <td class="tt" width="60"> 레벨 </td>
    <td class="tt" width="50"> 사용 </td>
    <td class="tt" width=""> 글작성 제한 </td>
    <td class="tt" width=""> 질문 미해결 </td>
</tr>
<?
for ($i=2; $i<=10; $i++) {
    $sql = " select * from {$mw['level_table']} where bo_table = '{$bo_table}' and mb_level = '{$i}' ";
    $row = sql_fetch($sql);
?>
<tr>
    <td class="tl"> lv.<?php echo $i?> </td>
    <td class="tl">
        <input type="checkbox" class="use" name="cf_use[<?php echo $i?>]" value="1"
            <?php if ($row['cf_use']) echo "checked" ?>>사용
    </td>
    <td class="tl">
        <input type=text size=10 name="cf_write_day[<?php echo $i?>]" class="ed r" value="<?php echo $row['cf_write_day']?>">
        일에
        <input type=text size=10 name="cf_write_day_count[<?php echo $i?>]" class="ed r" value="<?php echo $row['cf_write_day_count']?>">
        번 이하 
    </td>
    <td class="tl">
        <input type=text size=10 name="cf_qna_count[<?php echo $i?>]" class="ed r" value="<?php echo $row['cf_qna_count']?>">
    </td>
</tr>
<? } ?>
</table>

<p align="center">
    <input type="submit" class="b" value="확     인">
</p>
</form>

<?php

include_once("$g4[path]/tail.sub.php");
