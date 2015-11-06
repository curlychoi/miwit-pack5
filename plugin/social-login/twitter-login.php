<?php
/**
 * 소셜로그인 (Social-login for Gnuboard4)
 *
 * Copyright (c) 2012 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

include_once("_common.php");
include_once("$g4[path]/plugin/social-login/_lib.php");
include_once("$g4[path]/plugin/social-login/_config.php");
include_once("$g4[path]/plugin/social-login/lib/twitter/twitteroauth.php");

if ($is_member)
    alert_close("이미 로그인 하셨습니다.");

if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
    $_SESSION['oauth_status'] = 'oldtoken';
    session_destroy();
}

$connection = new TwitterOAuth(
    $mw_twitter_config[consumer_key],
    $mw_twitter_config[consumer_secret],
    $_SESSION[oauth_token],
    $_SESSION[oauth_token_secret]);

$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
$_SESSION['access_token'] = $access_token;

unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

if ($connection->http_code == 200) {
    $_SESSION['status'] = 'verified';

    $get_profile = $connection->get('account/verify_credentials');
    $twitter_profile = array(
        'id'            => $get_profile->id,
        'name'          => $get_profile->name,
        'screen_name'   => $get_profile->screen_name,
        'url'           => $get_profile->url,
        'lang'          => $get_profile->lang,
        'description'   => addslashes($get_profile->description)
    );

    if (strtolower(preg_replace("/-/", "", $g4[charset])) == 'euckr') {
        $twitter_profile[name] = set_euckr($twitter_profile[name]);
        $twitter_profile[screen_name] = set_euckr($twitter_profile[screen_name]);
        $twitter_profile[description] = set_euckr($twitter_profile[description]);
    }

    foreach ($twitter_profile as $key => $val) {
        $twitter_profile[$key] = addslashes($val);
    }

    $row = sql_fetch("select * from mw_twitter_login where twitter_id = '$twitter_profile[id]'", false);
    if ($row[twitter_id])
    {
        $mb = get_member($row[mb_id]);

        // 차단된 아이디인가?
        if ($mb[mb_intercept_date] && $mb[mb_intercept_date] <= date("Ymd", $g4[server_time])) {
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1년 \\2월 \\3일", $mb[mb_intercept_date]); 
            unset($_SESSION['access_token']);
            alert_close("회원님의 아이디는 접근이 금지되어 있습니다.\\n\\n처리일 : $date");
        }

        // 탈퇴한 아이디인가?
        if ($mb[mb_leave_date] && $mb[mb_leave_date] <= date("Ymd", $g4[server_time])) {
            $date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1년 \\2월 \\3일", $mb[mb_leave_date]); 
            unset($_SESSION['access_token']);
            alert_close("탈퇴한 아이디이므로 접근하실 수 없습니다.\\n\\n탈퇴일 : $date");
        }

        // 회원아이디 세션 생성
        set_session('ss_mb_id', $mb[mb_id]);
        set_session('ss_mb_key', md5($mb[mb_datetime] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));
    }
    else if (!$row && $twitter_profile[id])
    {
        mwp_is_social();

        include_once("$g4[path]/plugin/social-login/_upgrade.php");

        $mb_id = $member[mb_id]?$member[mb_id]:'@tw-'.$twitter_profile[id];

        $c = 2;
        $mb_nick = $twitter_profile[name];
        if (!$mb_nick) {
            unset($_SESSION['access_token']);
            alert_close("이름 정보가 없어 소셜로그인 할 수 없습니다.");
        }

        while ($row = sql_fetch("select * from $g4[member_table] where mb_nick = '$mb_nick'")) {
            $mb_nick = $twitter_profile[name].$c++;
        }

        $sql = " insert into mw_twitter_login set ";
        $sql.= "   mb_id = '$mb_id' ";
        $sql.= " , twitter_id = '$twitter_profile[id]' ";
        $sql.= " , twitter_name = '$twitter_profile[name]' ";
        $sql.= " , twitter_screen_name = '$twitter_profile[screen_name]' ";
        $sql.= " , twitter_url = '$twitter_profile[url]' ";
        $sql.= " , twitter_lang = '$twitter_profile[lang]' ";
        $sql.= " , twitter_description = '$twitter_profile[description]' ";
        $sql.= " , twitter_datetime = '$g4[time_ymdhis]' ";
        sql_query($sql);

        if (!$is_member) {
            $sql = " insert into $g4[member_table] set ";
            $sql.= "   mb_id = '$mb_id' ";
            $sql.= " , mb_name = '$twitter_profile[name]' ";
            $sql.= " , mb_nick = '$mb_nick' ";
            $sql.= " , mb_homepage = '$twitter_profile[url]' ";
            $sql.= " , mb_level = '$config[cf_register_level]' ";
            $sql.= " , mb_signature = '$twitter_profile[description]' ";
            $sql.= " , mb_datetime = '$g4[time_ymdhis]' ";
            $sql.= " , mb_ip = '$_SERVER[REMOTE_ADDR]' ";
            $sql.= " , mb_email = '$twitter_profile[screen_name]@twitter.com' ";
            $sql.= " , mb_email_certify = '$g4[time_ymdhis]' ";
            $sql.= " , mb_mailling = '1' ";
            $sql.= " , mb_open = '1' ";
            sql_query($sql);

            // 회원아이디 세션 생성
            set_session('ss_mb_id', $mb_id);
            set_session('ss_mb_reg', $mb_id);
            set_session('ss_mb_key', md5($g4[time_ymdhis] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));

            // 회원가입 포인트 부여
            insert_point($mb_id, $config['cf_register_point'], "회원가입 축하", '@member', $mb_id, '회원가입');

            if (function_exists("mw_partner_register")) mw_partner_register($mb_id);
        }

    }
}

echo "<script type='text/javascript'> opener.location.replace('$g4[path]'); self.close(); </script>";

