<?php
$sub_menu = "110400";
include_once('./_common.php');

check_demo();

if ($is_admin != 'super')
    alert('최고관리자만 접근 가능합니다.');

check_token();

$sql = "create table if not exists {$mw5['menu_table']} (
     me_code varchar(4) not null default ''
    ,me_icon varchar(50) not null default ''
    ,me_level tinyint not null default 0
    ,me_perm tinyint not null default 0
    ,me_no_side varchar(1) not null default ''
    ,primary key (me_code)
) ENGINE=MyISAM default charset=utf8 ";
sql_query($sql);

sql_query("alter table {$mw5['menu_table']} add me_perm tinyint not null default 0 ", false);

$_POST = array_map_deep('trim', $_POST);

$count = count($_POST['me_code']);
for ($i=0; $i<$count; $i++)
{
    $me_code = $_POST['me_code'][$i];
    $me_icon = $_POST['me_icon'][$i];
    $me_level = $_POST['me_level'][$i];
    $me_perm = $_POST['me_perm'][$i];
    $me_no_side = $_POST['me_no_side'][$me_code];

    // 메뉴 등록
    $sql = " replace into {$mw5['menu_table']}
                set me_code = '{$me_code}'
                    ,me_icon = '{$me_icon}'
                    ,me_level = '{$me_level}'
                    ,me_perm = '{$me_perm}'
                    ,me_no_side = '{$me_no_side}'";
    sql_query($sql);
}

goto_url('./menu_list.php');

