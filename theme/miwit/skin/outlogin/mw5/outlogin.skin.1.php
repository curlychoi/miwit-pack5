<?php
/**
 * MW5-Outlogin Skin for Gnuboard5
 *
 * Copyright (c) 2015 Choi Jae-Young <www.miwit.com>
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

add_stylesheet(get_skin_stylesheet($outlogin_skin_path));
add_javascript(get_skin_javascript($outlogin_skin_path));
?>
<div class="outlogin">
    <form name="foutlogin" action="<?php echo $outlogin_action_url?>" method="post" autocomplete="off">
    <input type="hidden" name="url" value="<?php echo $outlogin_url?>"/>
    <div class="func">
        <a href="<?php echo G5_BBS_URL?>/register.php"><strong><i class="fa fa-user"></i> 회원가입</strong></a>
        <a href="<?php echo G5_BBS_URL ?>/password_lost.php" class="win_password_lost"><i class="fa fa-search"></i>회원정보찾기</a>
    </div>

    <div class="item">
        <span class="icon"><i class="fa fa-user"></i></span>
        <input type="text" name="mb_id" class="mb_id" placeholder="아이디" maxlength="50">
    </div>

    <div class="item">
        <span class="icon"><i class="fa fa-lock"></i></span>
        <input type="password" name="mb_password" maxlength="50" placeholder="패스워드">
    </div>

    <!--<input type="checkbox" name="auto_login" id="auto_login">-->
    <input type="hidden" name="auto_login" id="auto_login" value="">
    <div class="auto"><i class="fa fa-gear"></i> 자동로그인</div>

    <button type="submit" class="login"><i class="fa fa-lock"></i> 로그인</button>
    </form>
</div>

