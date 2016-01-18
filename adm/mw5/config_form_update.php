<?php
$sub_menu = "110100";
include_once('_common.php');
include_once('_upgrade.php');

check_demo();

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super')
    alert('최고관리자만 접근 가능합니다.');

check_token();

    //,cf_width = '{$_POST['cf_width']}'
$sql_common = "
    cf_theme = '{$_POST['cf_theme']}'
    ,cf_theme_color = '{$_POST['cf_theme_color']}'
    ,cf_content_width = '{$_POST['cf_content_width']}'
    ,cf_side_width = '{$_POST['cf_side_width']}'
    ,cf_side_position = '{$_POST['cf_side_position']}'
    ,cf_nav_no_scroll = '{$_POST['cf_nav_no_scroll']}'
    ,cf_new = '{$_POST['cf_new']}'
    ,cf_no_popular = '{$_POST['cf_no_popular']}'

    ,cf_css = '{$_POST['cf_css']}'
    ,cf_no_css = '{$_POST['cf_no_css']}'

    ,cf_search_html = '{$_POST['cf_search_html']}'
    ,cf_no_search = '{$_POST['cf_no_search']}'

    ,cf_quick_link_html = '{$_POST['cf_quick_link_html']}'
    ,cf_no_quick_link = '{$_POST['cf_no_quick_link']}'

    ,cf_head_html = '{$_POST['cf_head_html']}'
    ,cf_no_head = '{$_POST['cf_no_head']}'

    ,cf_content_head_html = '{$_POST['cf_content_head_html']}'
    ,cf_no_content_head = '{$_POST['cf_no_content_head']}'
    ,cf_index_image_html = '{$_POST['cf_index_image_html']}'
    ,cf_no_index_image = '{$_POST['cf_no_index_image']}'
    ,cf_content_tail_html = '{$_POST['cf_content_tail_html']}'
    ,cf_no_content_tail = '{$_POST['cf_no_content_tail']}'

    ,cf_sidebar_head_html = '{$_POST['cf_sidebar_head_html']}'
    ,cf_no_sidebar_head = '{$_POST['cf_no_sidebar_head']}'
    ,cf_sidebar_tail_html = '{$_POST['cf_sidebar_tail_html']}'
    ,cf_no_sidebar_tail = '{$_POST['cf_no_sidebar_tail']}'

    ,cf_sidebar_outlogin = '{$_POST['cf_sidebar_outlogin']}'
    ,cf_sidebar_social = '{$_POST['cf_sidebar_social']}'
    ,cf_sidebar_cash = '{$_POST['cf_sidebar_cash']}'
    ,cf_sidebar_menu = '{$_POST['cf_sidebar_menu']}'
    ,cf_sidebar_notice = '{$_POST['cf_sidebar_notice']}'
    ,cf_sidebar_visit = '{$_POST['cf_sidebar_visit']}'
    ,cf_sidebar_poll = '{$_POST['cf_sidebar_poll']}'
    ,cf_sidebar_latest_write = '{$_POST['cf_sidebar_latest_write']}'
    ,cf_sidebar_latest_comment = '{$_POST['cf_sidebar_latest_comment']}'

    ,cf_sidebar_notice_table = '{$_POST['cf_sidebar_notice_table']}'
    ,cf_sidebar_latest_write_limit = '{$_POST['cf_sidebar_latest_write_limit']}'
    ,cf_sidebar_latest_comment_limit = '{$_POST['cf_sidebar_latest_comment_limit']}'

    ,cf_tail_html = '{$_POST['cf_tail_html']}'
    ,cf_no_tail = '{$_POST['cf_no_tail']}'

    ,cf_tail_link_html = '{$_POST['cf_tail_link_html']}'
    ,cf_no_tail_link = '{$_POST['cf_no_tail_link']}'
    ,cf_info_html = '{$_POST['cf_info_html']}'
    ,cf_no_info = '{$_POST['cf_no_info']}'
";

$row = sql_fetch("select * from {$mw5['config_table']}", false);
if ($row)
    sql_query(" update {$mw5['config_table']} set {$sql_common} ");
else
    sql_query(" insert into {$mw5['config_table']} set {$sql_common}");

goto_url('./config_form.php', false);

