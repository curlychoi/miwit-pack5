<?php
/**
 * 소셜로그인 (Social-login for Gnuboard)
 *
 * Copyright (c) 2012 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if (defined('G5_PATH') && @is_file(G5_EXTEND_PATH."/mw.g5.adapter.extend.php"))
    include_once(G5_EXTEND_PATH."/mw.g5.adapter.extend.php");

include_once($g4['path'].'/plugin/social-login/_lib.php');

if (is_social_login() && strstr($_SERVER['REQUEST_URI'], "member_confirm.php?url=register_form.php")) {
    goto_url($g4['path']."/plugin/social-login/bbs/register_form.php?w=u&mb_id=".$member['mb_id']);
}

if (strstr($_SERVER['PHP_SELF'], $g4['bbs'].'/login_check.php'))
{
    $is_social_login = is_social_login($_POST['mb_id']);
    if ($is_social_login)
        alert("{$is_social_login} 아이콘을 클릭해 {$is_social_login} 연동 계정으로 로그인 해주세요.");
}

if (strstr($_SERVER['PHP_SELF'], $g4['bbs'].'/password_lost2.php'))
{
    $email = trim($_POST['mb_email']);
    if (!$email) 
        alert_close("메일주소 오류입니다.");

    $mb = sql_fetch(" select mb_id from $g4[member_table] where mb_email = '{$email}' ");
    $is_social_login = is_social_login($mb['mb_id']);
    if ($is_social_login) 
        alert_close("{$is_social_login} 아이콘을 클릭해 {$is_social_login} 연동 계정으로 로그인 해주세요.");
}


