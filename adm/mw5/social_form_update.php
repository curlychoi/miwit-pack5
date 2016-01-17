<?php
$sub_menu = "110100";
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super')
    alert('최고관리자만 접근 가능합니다.');

check_token();

$cf_theme_time = $mw['config']['cf_theme_time'];
if ($mw['config']['cf_theme'] != $_POST['cf_theme'] or $mw['config']['cf_theme_color'] != $_POST['cf_theme_color']) {
    $cf_theme_time = G5_SERVER_TIME;
}

$sql_common = "
    cf_theme = '{$_POST['cf_theme']}'
    ,cf_theme_color = '{$_POST['cf_theme_color']}'
    ,cf_theme_time = '{$cf_theme_time}'
    ,cf_width = '{$_POST['cf_width']}'
    ,cf_side_width = '{$_POST['cf_side_width']}'
    ,cf_side_position = '{$_POST['cf_side_position']}'
    ,cf_www = '{$_POST['cf_www']}' 
    ,cf_seo_url = '{$_POST['cf_seo_url']}' 
    ,cf_seo_except = '{$_POST['cf_seo_except']}' 
    ,cf_facebook_use_login = '{$_POST['cf_facebook_use_login']}' 
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

//sql_query("alter table {$mw5['config_table']} add cf_theme_color varchar(255) not null default '' after cf_theme");
//sql_query("alter table {$mw5['config_table']} add cf_theme_time int not null default '0' after cf_theme_color");

$sql = " update {$mw5['config_table']} set {$sql_common} ";
$qry = sql_query($sql, false);
if (!$qry) {
    $sql = "create table if not exists {$mw5['config_table']} ( 
                cf_theme varchar(255) not null default 'mw5'
                ,cf_theme_color varchar(255) not null default ''
                ,cf_theme_time int not null default '0' 
                ,cf_width int not null default '1078'
                ,cf_side_width int not null default '300'
                ,cf_side_position varchar(5) not null default 'right'
                ,cf_www varchar(1) not null default ''
                ,cf_seo_url varchar(1) not null default ''
                ,cf_seo_except text not null default ''
                ,cf_facebook_use_login varchar(1) not null default ''
                ,cf_facebook_appid varchar(255) not null default ''
                ,cf_facebook_secret varchar(255) not null default ''
                ,cf_twitter_use_login varchar(1) not null default ''
                ,cf_twitter_consumer_key varchar(255) not null default ''
                ,cf_twitter_consumer_secret varchar(255) not null default ''
                ,cf_google_use_login varchar(1) not null default ''
                ,cf_google_client_id varchar(255) not null default ''
                ,cf_google_client_secret varchar(255) not null default ''
                ,cf_google_client_domain varchar(255) not null default ''
                ,cf_naver_use_login varchar(1) not null default ''
                ,cf_naver_client_id varchar(255) not null default ''
                ,cf_naver_client_secret varchar(255) not null default ''
                ,cf_kakao_use_login varchar(1) not null default ''
                ,cf_kakao_client_id varchar(255) not null default ''
) default charset=utf8 ";
    sql_query($sql);
    sql_query("insert into {$mw5['config_table']} set {$sql_common}");
}

goto_url('./config_form.php', false);

