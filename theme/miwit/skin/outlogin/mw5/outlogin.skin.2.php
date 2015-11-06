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

global $g5;

// 회원가입후 몇일째인지? + 1 은 당일을 포함한다는 뜻
$sql = " select (TO_DAYS('".G5_TIME_YMDHIS."') - TO_DAYS('{$member['mb_datetime']}') + 1) as days ";
$row = sql_fetch($sql);
$mb_reg_after = $row['days'];

$sql = " select count(*) as cnt from {$g5['scrap_table']} where mb_id = '{$member['mb_id']}' ";
$row = sql_fetch($sql);
$scrap_count = $row['cnt'];

$comment_image_path = G5_PATH."/data/mw.basic.comment.image/".$member['mb_id'];
$comment_image_url = G5_URL."/data/mw.basic.comment.image/".$member['mb_id'];
?>

<div class="outlogin2">

    <div class="profile">
        <?php if (is_file($comment_image_path)) { ?>
        <img class="img" src="<?php echo $comment_image_url?>">
        <?php } else { ?>
        <div class="img"><i class="fa fa-user"></i></div>
        <?php } ?>

        <div class="name">
            <?php if ($is_admin == "super" || $is_auth) { ?>
            <a href="<?php echo G5_ADMIN_URL?>/"><strong><?php echo $nick?></strong>님</a>
            <?php } else { ?>
            <strong><?php echo $nick?></strong>님
            <?php } ?>
        </div>
    </div>

    <div class="func">
        <a class="item" href="<?php echo G5_BBS_URL?>/member_confirm.php?url=register_form.php">
            <i class="fa fa-user-plus"></i>
            정보수정
        </a>
        <a class="item win_memo" href="<?php echo G5_BBS_URL?>/memo.php">
            <i class="fa fa-comment"></i>
            쪽지 <span id="outlogin_memo"><?php echo '0';//$memo_not_read?></span> 건
        </a>
        <div class="item">
            <i class="fa fa-graduation-cap"></i>
            <span id="outlogin_level"><?php echo '0';//$member['mb_level']?></span> 레벨
        </div>

        <a class="item win_point" href="<?php echo G5_BBS_URL?>/point.php">
            <i class="fa fa-diamond"></i>
            포인트 <span id="outlogin_point"><?php echo '0';//$point?></span> 점
        </a>
        <div class="item" title="<?php echo substr($member['mb_datetime'], 0, 10)?>">
            <i class="fa fa-calendar"></i>
            가입 <span id="outlogin_reg"><?php echo '0';//number_format($mb_reg_after)?></span> 일
        </div>
        <a class="item last win_scrap" href="<?php echo G5_BBS_URL?>/scrap.php">
            <i class="fa fa-paperclip"></i>
            스크랩 <span id="outlogin_scrap"><?php echo '0';//$scrap_count?></span> 개
        </a>
    </div>

    <button type="button" class="logout" onclick="location.href='<?php echo G5_BBS_URL?>/logout.php?url=<?php echo $urlencode?>'">
        <i class="fa fa-sign-out"></i> 로그아웃
    </button>

</div>

<script>
$(document).ready(function () {
    $("button.slide").click(function () {
        var max = $(".func")[0].scrollWidth - $(".func")[0].clientWidth;
        $(".func").animate({ scrollLeft:'50px' }, 'fast');
    });

    setTimeout(function () {
        var d = 1000;
        $({memo:0}).animate({memo:<?php echo $memo_not_read?>}, { duration:d, step:function () {
            $("#outlogin_memo").text(number_format(String(Math.round(this.memo))));
        }, complete:function () {
            $("#outlogin_memo").text("<?php echo number_format($memo_not_read)?>");
        }});

        $({point:0}).animate({point:<?php echo str_replace(",", "", $point)?>}, { duration:d, step:function () {
            $("#outlogin_point").text(number_format(String(Math.round(this.point))));
        }, complete:function () {
            $("#outlogin_point").text("<?php echo $point?>");
        }});

        $({reg:0}).animate({reg:<?php echo $mb_reg_after?>}, { duration:d, step:function () {
            $("#outlogin_reg").text(number_format(String(Math.round(this.reg))));
        }, complete:function () {
            $("#outlogin_reg").text("<?php echo number_format($mb_reg_after)?>");
        }});

        $({scrap:0}).animate({scrap:<?php echo $scrap_count?>}, { duration:d, step:function () {
            $("#outlogin_scrap").text(number_format(String(Math.round(this.scrap))));
        }, complete:function () {
            $("#outlogin_scrap").text("<?php echo number_format($scrap_count)?>");
        }});

        $({level:0}).animate({level:<?php echo $member['mb_level']?>}, { duration:d, step:function () {
            $("#outlogin_level").text(number_format(String(Math.round(this.level))));
        }, complete:function () {
            $("#outlogin_level").text("<?php echo number_format($member['mb_level'])?>");
        }});
    }, 1000);
});
</script>

