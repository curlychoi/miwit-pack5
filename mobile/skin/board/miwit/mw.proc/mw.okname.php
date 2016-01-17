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
?>

<script src="http://www.ok-name.co.kr/member/js/okname.js" type="text/javascript" language="javascript1.5" ></script>
<script type="text/javascript" src="<?=$board_skin_path?>/mw.okname/okname.js"></script>
<script type="text/javascript">
function ipin_win() {
    ipin_window = window.open("<?=$board_skin_path?>/mw.okname/ipin1.php?bo_table=<?=$bo_table?>", "kcbPop", "left=200, top=100, status=0, width=450, height=550" );
}
board_skin_path = "<?=$board_skin_path?>";
okname_path = "<?=$board_skin_path?>/mw.okname/okname.php";
</script>

<div id="okname">
<form name="fok" method="post" onsubmit="return check_okname();">
<fieldset><legend> ipin 인증 </legend>
<div class="ibox">
    <div class="title_img"><img src="<?=$board_skin_path?>/img/okname.png"></div>
    <div class="fbox">
        <!--
        <div>
            <label> 이름(실명) </label> :
            <input type="text" name="nam" size="15" maxlength="15" class="ed" required hangul itemname="이름(실명)">
        </div>
        <div>
            <label> 주민등록번호 </label> :
            <input type="password" name="ssn" size="15" maxlength="13" class="ed" required jumin itemname="주민등록번호">
            <span>(숫자만 입력해주세요)</span>
        </div>
        <div id="err-msg">&nbsp;</div>
        <div id="btn-send"><input type="submit" value="실명인증" class="btn"></div>
        -->
        <div style="text-align:center;">
            <input type="button" class="btn" value="아이핀 인증받기" onclick="ipin_win()">
        </div>
    </div>
</div>
</fieldset>
</form>
<div>

