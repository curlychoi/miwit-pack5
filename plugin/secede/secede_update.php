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

if (!$is_member)
    alert("로그인 해주세요.");

if (defined('G5_PATH')) {
    include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
    if (!chk_captcha()) {
        alert('자동등록방지 숫자가 틀렸습니다.');
    }
}
else {
    $key = get_session("captcha_keystring");
    if (!($key && $key == $_POST[wr_key])) {
        $_SESSION['captcha_keystring'] = '';
        unset($_SESSION['captcha_keystring']);
        alert('자동등록방지 숫자가 틀렸습니다.');
    }
}

if (substr($member['mb_id'], 0, 1) != '@' and sql_password($mb_password) != $member[mb_password])
    alert("패스워드가 틀립니다.\\n\\n패스워드는 대소문자를 구분합니다.");

if ($is_admin)
    alert("관리자는 탈퇴할 수 없습니다.");

$mb_leave_date = date("Ymd", $g4[server_time]);
$mb_memo = $member[mb_memo] . "\n---------------------------\n회원탈퇴사유\n---------------------------\n$mb_memo";

if ($member[mb_recommend]) {
    $row = sql_fetch(" select count(*) as cnt from $g4[member_table] where mb_id = '".addslashes($member[mb_recommend])."' ");
    $msg = "{$member[mb_id]}님의 회원자료 삭제로 인한 추천인 포인트 반환";
    if ($row[cnt])
	insert_point($member[mb_recommend], $config[cf_recommend_point] * (-1), $msg, '@member', $member[mb_recommend], "{$member[mb_id]} 추천인 삭제");
}

$sql = "update $g4[member_table] set mb_leave_date = '$mb_leave_date', mb_level = '1', mb_memo = '$mb_memo' where mb_id = '$member[mb_id]'";
$qry = sql_query($sql);
 
alert("탈퇴가 완료되었습니다.\\n\\n그동안 이용해주셔서 감사합니다.", "$g4[bbs_path]/logout.php");

