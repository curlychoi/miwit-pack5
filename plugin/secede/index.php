<?php
/**
 * 탈퇴 플러그인 (Secede for Gnuboard4)
 *
 * Copyright (c) 2011 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

include_once("_common.php");

$is_builder = $g4['path'].'/lib/mw.builder.lib.php';
if (is_file($is_builder))
    include_once($is_builder);

$g4[title] = "회원탈퇴";
include_once("_head.php");

if (defined('G5_PATH')) {
    include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
    $captcha_html = captcha_html();
    $captcha_js   = chk_captcha_js();
}
?>

<style type="text/css">
.secede { margin:0 0 10px 0; text-align:left; }
/*.subject { font-weight:bold; font-size:14px; padding:10px; background-color:#efefef; }*/
.subject { border-bottom:1px solid #ddd; }
.content { margin:30px 0 20px 0; line-height:25px; }
.content .item { margin:10px 0 30px 0; }
.content .input-label { font-weight:bold; margin:0 0 7px 0; }
.content .input-box { margin:0 0 0 10px; }
.content textarea { width:100%; height:50px;  border:1px solid #9A9A9A; border-right:1px solid #D8D8D8; border-bottom:1px solid #D8D8D8; padding:3px 2px 0 2px; }
.action { }
.btn1 { background-color:#efefef; cursor:pointer; }
</style>

<div class="secede">
<form name="fwrite" method="post" action="secede_update.php" onsubmit="return fcheck(this)">
<!--<div class="subject"> 정말 탈퇴하시겠습니까? </div>-->
<div class="subject"><img src="img/title.gif"></div>
<div class="content">
    <?php if (substr($member['mb_id'], 0, 1) != '@') { ?>
    <div class="item"> 
	<div class="input-label">- 비밀번호를 입력해주세요. </div>
	<div class="input-box"> <input type="password" name="mb_password" size="20" class="ed" required itemname="비밀번호"> </div>
    </div>
    <?php } ?>
    <div class="item">
	<div class="input-label">- 탈퇴사유를 작성해주세요.</div>
	<div class="input-box"> <textarea name="mb_memo" class="ed" required itemname="탈퇴사유"></textarea> </div>
    </div>

    <div class="item">
	<div class="input-label">- 아래의 자동방지 글자를 입력하세요.</div>
	<div class="input-box">
            <?php if (defined('G5_PATH')) { echo $captcha_html; } else { ?>
	    <img id='kcaptcha_image'> <br/>
	    <input type=input class=ed size=20 name=wr_key itemname="자동등록방지" required>
            <?php } ?>
	</div>
    </div>

</div>
<div class="action"> <input type="submit" value="탈퇴합니다" class="btn1"> </div>
</div>


<script src="<?="$g4[path]/js/jquery.kcaptcha.js"?>"></script>
<script>
function fcheck(f) {

    <?php if (defined('G5_PATH')) { echo $captcha_js; } else { ?>
    if (!check_kcaptcha(f.wr_key)) {
        return false;
    }
    <?php } ?>

    if (confirm("정말 탈퇴하시겠습니까?")) {
	return true;
    }
    return false;
}
</script>
<?php
include_once("_tail.php");
