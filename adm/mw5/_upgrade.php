<?php
if (!defined("_GNUBOARD_")) exit;

$sql = "create table if not exists {$mw5['config_table']} ( 
cf_theme varchar(255) not null default 'mw5'
,cf_theme_color varchar(255) not null default ''
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
sql_query($sql, false);

sql_query("alter table {$mw5['config_table']} add cf_content_width int not null default 728 ", false);

sql_query("alter table {$mw5['config_table']} add cf_nav_no_scroll varchar(1) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_new varchar(5) not null default 'count' ", false);
sql_query("alter table {$mw5['config_table']} add cf_title varchar(255) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_author varchar(255) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_desc varchar(255) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_facebook varchar(255) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_twitter varchar(255) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_google varchar(255) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_instagram varchar(255) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_youtube varchar(255) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_github varchar(255) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_follow varchar(1) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_naver_webmaster varchar(255) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_google_webmaster varchar(255) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_bing_webmaster varchar(255) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_naver_analytics text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_google_analytics text not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_no_popular varchar(1) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_search_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_search varchar(1) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_quick_link_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_quick_link varchar(1) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_tail_link_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_tail_link varchar(1) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_index_image_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_index_image varchar(1) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_index_latest_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_index_latest varchar(1) not null default '1' ", false);

sql_query("alter table {$mw5['config_table']} add cf_head_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_head varchar(1) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_tail_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_tail varchar(1) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_content_head_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_content_head varchar(1) not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_content_tail_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_content_tail varchar(1) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_sidebar_head_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_sidebar_head varchar(1) not null default '1' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_tail_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_sidebar_tail varchar(1) not null default '1' ", false);

sql_query("alter table {$mw5['config_table']} add cf_sidebar_outlogin varchar(1) not null default '1' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_social varchar(1) not null default '1' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_menu varchar(1) not null default '1' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_cash varchar(1) not null default '1' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_notice varchar(1) not null default '1' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_latest_write varchar(1) not null default '1' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_latest_comment varchar(1) not null default '1' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_visit varchar(1) not null default '1' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_poll varchar(1) not null default '1' ", false);

sql_query("alter table {$mw5['config_table']} add cf_info_html text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_info varchar(1) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_css text not null default '' ", false);
sql_query("alter table {$mw5['config_table']} add cf_no_css varchar(1) not null default '' ", false);

sql_query("alter table {$mw5['config_table']} add cf_sidebar_notice_table varchar(50) not null default 'notice' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_latest_write_limit tinyint not null default '5' ", false);
sql_query("alter table {$mw5['config_table']} add cf_sidebar_latest_comment_limit tinyint not null default '5' ", false);
