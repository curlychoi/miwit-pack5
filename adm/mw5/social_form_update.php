<?php
$sub_menu = "110100";
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super')
    alert('최고관리자만 접근 가능합니다.');

check_token();

$sql_common = "
    cf_facebook_use_login = '{$_POST['cf_facebook_use_login']}' 
    ,cf_facebook_appid = '{$_POST['cf_facebook_appid']}' 
    ,cf_facebook_secret = '{$_POST['cf_facebook_secret']}' 
    ,cf_twitter_use_login = '{$_POST['cf_twitter_use_login']}' 
    ,cf_twitter_consumer_key = '{$_POST['cf_twitter_consumer_key']}' 
    ,cf_twitter_consumer_secret = '{$_POST['cf_twitter_consumer_secret']}' 
    ,cf_google_use_login = '{$_POST['cf_google_use_login']}' 
    ,cf_google_client_id = '{$_POST['cf_google_client_id']}' 
    ,cf_google_client_secret = '{$_POST['cf_google_client_secret']}' 
    ,cf_google_client_domain = '{$_POST['cf_google_client_domain']}' 
    ,cf_naver_use_login = '{$_POST['cf_naver_use_login']}' 
    ,cf_naver_client_id = '{$_POST['cf_naver_client_id']}' 
    ,cf_naver_client_secret = '{$_POST['cf_naver_client_secret']}' 
    ,cf_kakao_use_login = '{$_POST['cf_kakao_use_login']}' 
    ,cf_kakao_client_id = '{$_POST['cf_kakao_client_id']}' 
";

$row = sql_fetch("select * from {$mw5['config_table']}", false);
if ($row)
    $sql = " update {$mw5['config_table']} set {$sql_common} ";
else
    $sql = " insert into {$mw5['config_table']} set {$sql_common}";

sql_query($sql);

goto_url('./social_form.php', false);

