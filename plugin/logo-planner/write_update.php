<?php
/**
 * 로고 플래너 (Logo Planner for Gnuboard4)
 *
 * Copyright (c) 2011 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

include_once("_common.php");
include_once("_config.php");
include_once("_lib.php");

if ($is_admin != 'super')
    alert("접근 권한이 없습니다.");

if ($w != "d" && $ls_url) {
    $ls_url = set_http($ls_url);
    $tmp = parse_url($ls_url);
    if (!$tmp[host]) alert("URL 이 잘못되었습니다.");
}

$path = $mw_logo_planner[logo_path];

@mkdir($path, 0707);
@chmod($path, 0707);

$indexfile = $path."/index.php";
$f = @fopen($indexfile, "w");
@fwrite($f, "");
@fclose($f);
@chmod($indexfile, 0606);

$file_source = "";
$file_name = "";
$size = array();

if (($w == "u" || $w == "d") && $ls_id) {
    $row = sql_fetch("select * from $mw_logo_planner[logo_table] where ls_id = '$ls_id'");
    if (!$row)
        alert("데이터가 존재하지 않습니다.");

    $file_name = $row[ls_logo_file];

    if (($ls_file_del || $w == "d") && $file_name) {
        $del_file = "$path/$file_name";
        if (file_exists($del_file)) @unlink($del_file);
    }
}

$file = $_FILES[ls_logo_file];
$size = @getImageSize($file[tmp_name]);
$mime = array('image/png', 'image/jpeg', 'image/gif', 'application/x-shockwave-flash');

if (is_uploaded_file($file[tmp_name]))
{
    if (!in_array($size['mime'], $mime))
        alert("PNG, GIF, JPG, SWF 형식의 이미지 파일만 업로드 가능합니다.");

    if (!preg_match("/\.(jpg|png|gif|swf)$/i", $file[name]))
        alert("PNG, GIF, JPG, SWF 형식의 이미지 파일만 업로드 가능합니다.");

    if (!is_dir($path))
        alert("$path 디렉토리가 존재하지 않습니다.");

    if (!is_writable($path))
        alert("$path 디렉토리의 퍼미션을 707로 변경해주세요.");

    $file_name = abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr(md5(uniqid($g4[server_time])),0,8).
                    '_'.str_replace('%', '', urlencode($file[name]));

    $dest_file = $path . '/' . $file_name;
    $count = 0;

    move_uploaded_file($file[tmp_name], $dest_file);
    chmod($dest_file, 0606);
}

$sql_common = "   ls_use = '$ls_use' ";
$sql_common.= " , ls_lieu = '$ls_lieu' ";
$sql_common.= " , ls_repeat = '$ls_repeat' ";
$sql_common.= " , ls_order = '$ls_order' ";
$sql_common.= " , ls_title = '$ls_title' ";
$sql_common.= " , ls_url = '$ls_url' ";
$sql_common.= " , ls_target = '$ls_target' ";
$sql_common.= " , ls_sdate = '$ls_sdate' ";
$sql_common.= " , ls_edate = '$ls_edate' ";
$sql_common.= " , ls_week = '$ls_week' ";
$sql_common.= " , ls_lunar = '$ls_lunar' ";
$sql_common.= " , ls_logo_file = '$file_name' ";
$sql_common.= " , ls_memo = '$ls_memo' ";

$act = '';
$url = '';

if ($w == "u" && $ls_id)
{
    if ($file[size]) {
        $del_file = "$path/$row[ls_logo_file]";
        if (file_exists($del_file)) @unlink($del_file);
    }

    sql_query(" update $mw_logo_planner[logo_table] set $sql_common where ls_id = '$ls_id' ");

    $act = '수정';
    $url = "view.php?ls_id=$ls_id";
}
else if ($w == "d" && $ls_id)
{
    sql_query(" delete from $mw_logo_planner[logo_table] where ls_id = '$ls_id' ");

    $act = '삭제';
    $url = "list.php";
}
else
{
    sql_query(" insert into $mw_logo_planner[logo_table] set $sql_common, ls_datetime = '$g4[time_ymdhis]' ");
    $ls_id = sql_insert_id();

    $act = '등록';
    $url = "view.php?ls_id=$ls_id";
}

alert("{$act} 완료되었습니다.", $url);
