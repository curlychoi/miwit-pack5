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
include_once("lib/google/apiClient.php");
include_once("lib/google/contrib/apiOauth2Service.php");

$client = new apiClient();
$client->setApplicationName("Miwit.com Social-login Solution");
// Visit https://code.google.com/apis/console?api=plus to generate your
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
$client->setClientId($mw_google_config[client_id]);
$client->setClientSecret($mw_google_config[client_secret]);
$client->setRedirectUri(set_http($mw_google_config[client_domain].$_SERVER[SCRIPT_NAME]));
//$client->setDeveloperKey('insert_your_developer_key');
$oauth2 = new apiOauth2Service($client);

if (isset($_GET['code'])) {
    $client->authenticate();
    $_SESSION['token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
}

if (isset($_REQUEST['logout'])) {
    unset($_SESSION['token']);
    $client->revokeToken();
}

if ($client->getAccessToken())
{
    $_SESSION['token'] = $client->getAccessToken();

    $google_profile = $oauth2->userinfo->get();

    if (strtolower(preg_replace("/-/", "", $g4[charset])) == 'euckr') {
        $google_profile[name] = set_euckr($google_profile[name]);
    }

    $row = sql_fetch("select * from mw_google_login where google_id = '$google_profile[id]'", false);
    if (!$is_member && $row[google_id])
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
    else if (!$row && $google_profile[id])
    {
        mwp_is_social();

        include_once("$g4[path]/plugin/social-login/_upgrade.php");

        $c = 2;
        $mb_nick = $google_profile[name];
        if (!$mb_nick) {
            unset($_SESSION['access_token']);
            alert_close("이름 정보가 없어 소셜로그인 할 수 없습니다.");
        }

        while ($row = sql_fetch("select * from $g4[member_table] where mb_nick = '$mb_nick'")) {
            $mb_nick = $google_profile[name].$c++;
        }

        $mb_email = $google_profile[email];
        $row = sql_fetch(" select * from $g4[member_table] where mb_email = '$mb_email' ");
        if ($row) {
            $mb_email = "$google_profile[id]@google.com";
            $row = sql_fetch(" select * from $g4[member_table] where mb_email = '$mb_email' ");
            if ($row) {
                unset($_SESSION['access_token']);
                alert_close("구글플러스에 등록된 이메일주소가 이미 회원으로 가입되어 있어 소셜로그인이 불가능 합니다.");
            }
        }

        $mb_id = '';

        $sql = " insert into mw_google_login set ";
        $sql.= "   mb_id = '$mb_id' ";
        $sql.= " , google_id = '$google_profile[id]' ";
        $sql.= " , google_name = '$google_profile[name]' ";
        $sql.= " , google_email = '$google_profile[email]' ";
        $sql.= " , google_link = '$google_profile[link]' ";
        $sql.= " , google_gender = '$google_profile[gender]' ";
        $sql.= " , google_locale = '$google_profile[locale]' ";
        $sql.= " , google_datetime = '$g4[time_ymdhis]' ";
        sql_query($sql);

        $id = mysql_insert_id();
        $mb_id = $member[mb_id]?$member[mb_id]:'@gl-1'.sprintf("%09d", $id);

        sql_query("update mw_google_login set mb_id = '$mb_id' where id = '$id'");

        if (!$is_member) {
            $mb_sex = strtolower($google_profile[gender]) == 'male' ? 'M' : 'F';
            //$mb_birth = explode("/", $google_profile[birthday]);
            //$mb_birth = "$mb_birth[2]$mb_birth[0]$mb_birth[1]";

            $sql = " insert into $g4[member_table] set ";
            $sql.= "   mb_id = '$mb_id' ";
            $sql.= " , mb_name = '$google_profile[name]' ";
            $sql.= " , mb_nick = '$mb_nick' ";
            $sql.= " , mb_email = '$mb_email' ";
            $sql.= " , mb_homepage = '$google_profile[link]' ";
            $sql.= " , mb_level = '$config[cf_register_level]' ";
            $sql.= " , mb_sex = '$mb_sex' ";
            //$sql.= " , mb_birth = '$mb_birth' ";
            //$sql.= " , mb_signature = '$google_profile[bio]' ";
            $sql.= " , mb_datetime = '$g4[time_ymdhis]' ";
            $sql.= " , mb_ip = '$_SERVER[REMOTE_ADDR]' ";
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
    echo "<script type='text/javascript'> opener.location.replace('$g4[path]'); self.close(); </script>";
    exit;
 
} else {
    $authUrl = $client->createAuthUrl();
    goto_url($authUrl);
}


