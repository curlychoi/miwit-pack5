<?php
include_once("_common.php");

$g4[title] = "회원가입";
include_once("_head.php");
include_once("../_lib.php");

$g4_path = $g4[path];

if (!check_string($member['mb_nick'], _G4_HANGUL_ + _G4_ALPHABETIC_ + _G4_NUMERIC_)) {
    $config[cf_nick_modify] = 0;
}

$is_sns = is_social_login();
if ($is_sns) {
    $member['mb_password'] = '';
}
else {
    goto_url($g4['bbs_path'].'/member_confirm.php?url=register_form.php');
}

ob_start();
readfile($g4_path."/bbs/register_form.php");
$skin = ob_get_contents();
ob_end_clean();
$skin = str_replace("<"."?php", "", $skin);
$skin = str_replace("<"."?", "", $skin);
$skin = str_replace("?".">", "", $skin);
$skin = str_replace("include_once(\"\$member_skin_path/register_form.skin.php\");", "", $skin);
$skin = str_replace("include_once(\"./_head.php\");", "", $skin);
$skin = str_replace("include_once(\"./_tail.php\");", "", $skin);

//g5
$_POST['mb_id'] = $member['mb_id'];
$skin = str_replace("include_once(\$member_skin_path.'/register_form.skin.php');", "", $skin); 
$skin = str_replace("include_once('./_head.php');", "", $skin);
$skin = str_replace("include_once('./_tail.php');", "", $skin);

ob_start();
eval($skin);
$skin = ob_get_clean();

ob_start();
include_once("$member_skin_path/register_form.skin.php");
$skin = ob_get_clean();

if (!check_string($member['mb_name'], _G4_HANGUL_)) {
    $skin = preg_replace("/<input(.*)name=[\"\']?mb_name[\"\']?(.*)readonly(.*)>/i", "<input$1name=\"mb_name\"$2$3>", $skin);
}

$skin = preg_replace("/<input.*name=[\"\']?mb_password[\"\']?[^>]+>/i",
        "<span style='color:#999;'>{$is_sns} 연동 계정은  패스워드가 없습니다.</span>", $skin);

$skin = preg_replace("/<\/form>/i", "<input type='hidden' name='mb_password' value=''><input type='hidden' name='mb_password_re' value=''></form>", $skin);

if ($g4['https_url'])
    $skin = str_replace("register_form_update.php", "../plugin/social-login/bbs/register_form_update.php", $skin);

if (defined('G5_BBS_URL'))
    $skin = str_replace(G5_BBS_URL."/register_form_update.php", "./register_form_update.php", $skin); 

echo $skin;

include_once("_tail.php");

