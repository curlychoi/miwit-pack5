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

if ($is_member)
    alert_close("이미 로그인 하셨습니다.");

if ($_SESSION['access_token']) {
    $url = "https://apis.naver.com/nidlogin/nid/getUserProfile.xml";

    $ch = curl_init();  
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt ($ch, CURLOPT_SSLVERSION, 1); 
    curl_setopt ($ch, CURLOPT_HEADER, 0); 
    curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$_SESSION['access_token'])); 
    curl_setopt ($ch, CURLOPT_POST, 0);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 30); 
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);       
    curl_close($ch);

    $xml = simplexml_load_string($result); 

    $naver_id = (String)$xml->response->enc_id;
    $naver_email = (String)$xml->response->email;
    $naver_nickname = (String)$xml->response->nickname;
    $naver_age = (String)$xml->response->age;
    $naver_birthday = (String)$xml->response->birthday;
    $naver_gender = (String)$xml->response->gender;

    if (strtolower(preg_replace("/-/", "", $g4['charset'])) == 'euckr') {
        $naver_nickname = set_euckr($naver_nickname);
    }

    $row = sql_fetch("select * from mw_naver_login where naver_id = '$naver_id'", false);
    if ($row['naver_id'])
    {
        $mb = get_member($row['mb_id']);
        if ($mb) {
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

            set_session('ss_mb_id', $mb['mb_id']);
            set_session('ss_mb_key', md5($mb['mb_datetime'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));
        }
    }
    else {
        mwp_is_social();

        include_once("$g4[path]/plugin/social-login/_upgrade.php");

        $c = 2; 
        $mb_nick = $naver_nickname;
        if (!$mb_nick) {
            unset($_SESSION['access_token']);
            alert_close("이름 정보가 없어 소셜로그인 할 수 없습니다.");
        }

        $row = sql_fetch(" select * from {$g4['member_table']} where mb_email = '{$naver_email}' ");
        if ($row) {
            unset($_SESSION['access_token']);
            alert_close("이메일주소가 이미 회원으로 가입되어 있어 소셜로그인이 불가능 합니다.");
        }

        while ($row = sql_fetch("select * from {$g4['member_table']} where mb_nick = '{$mb_nick}'")) {
            $mb_nick = $naver_nickname.$c++;
        }

        $mb_id = $member['mb_id']?$member['mb_id']:'@nv-'.substr($naver_email, 0, strpos($naver_email, "@"));

        $sql = " insert into mw_naver_login set ";
        $sql.= "   mb_id = '{$mb_id}' ";
        $sql.= " , naver_id = '{$naver_id}' ";
        $sql.= " , naver_nickname = '{$naver_nickname}' ";
        $sql.= " , naver_email = '{$naver_email}' ";
        $sql.= " , naver_birthday = '{$naver_birthday}' ";
        $sql.= " , naver_gender = '{$naver_gender}' ";
        $sql.= " , naver_age = '{$naver_age}' ";
        $sql.= " , naver_datetime = '{$g4['time_ymdhis']}' ";
        sql_query($sql);

        $sql = " insert into $g4[member_table] set ";
        $sql.= "   mb_id = '{$mb_id}' ";
        $sql.= " , mb_name = '{$naver_nickname}' ";
        $sql.= " , mb_nick = '{$mb_nick}' ";
        $sql.= " , mb_email = '{$naver_email}' ";
        $sql.= " , mb_level = '{$config['cf_register_level']}' ";
        $sql.= " , mb_sex = '{$naver_gender}' ";
        $sql.= " , mb_datetime = '{$g4['time_ymdhis']}' ";
        $sql.= " , mb_ip = '{$_SERVER['REMOTE_ADDR']}' ";
        $sql.= " , mb_email_certify = '{$g4['time_ymdhis']}' ";
        $sql.= " , mb_mailling = '1' ";
        $sql.= " , mb_open = '1' ";
        sql_query($sql);

        // 회원아이디 세션 생성
        set_session('ss_mb_id', $mb_id);
        set_session('ss_mb_reg', $mb_id);
        set_session('ss_mb_key', md5($g4['time_ymdhis'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));

        // 회원가입 포인트 부여
        insert_point($mb_id, $config['cf_register_point'], "회원가입 축하", '@member', $mb_id, '회원가입');

        if (function_exists("mw_partner_register")) mw_partner_register($mb_id);
    }
    echo "<script>opener.location.reload(); self.close();</script>";
    exit;
}

if ($_GET['code']) {
    $url = "https://nid.naver.com/oauth2.0/token";
    $url.= sprintf("?client_id=%s&client_secret=%s&grant_type=authorization_code&state=%s&code=%s",
        $mw_naver_config['client_id'], $mw_naver_config['client_secret'], $state, $_GET['code']);
    
    $ch = curl_init();  
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt ($ch, CURLOPT_SSLVERSION,1); 
    curl_setopt ($ch, CURLOPT_HEADER, 0); 
    curl_setopt ($ch, CURLOPT_POST, 0);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 30); 
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);       
    curl_close($ch);

    $json = json_decode($result, true);

    if ($json['access_token']) {
        set_session("access_token", $json['access_token']);

        echo "<script>location.reload()</script>";
        exit;
    }
    else
        alert_close("로그인 실패");

    exit;
}

if ($_GET['state']) {
    if ($_GET['state'] == $_SESSION['state']) {
        return RESPONSE_SUCCESS;
    }
    else {
        return RESPONSE_UNAUTHORIZED;
    }
    exit;
}

if (!$_SESSION['access_token'] ) {
    $state = mw_naver_generate_state();
    set_session("state", $state);

    $url = "https://nid.naver.com/oauth2.0/authorize";
    $url.= sprintf("?client_id=%s&response_type=code&redirect_uri=%s&state=%s",
        $mw_naver_config['client_id'], urlencode($mw_naver_config['callback']), $state);

    header("location: ".$url);
    exit;
}

