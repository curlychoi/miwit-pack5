<?php
$g4_path = "../../../.."; // common.php 의 상대 경로
if (is_file("$g4_path/common.php"))
    include_once("$g4_path/common.php");
else {
    $g4_path = "../../../../..";
    include_once("$g4_path/common.php");
}

if (!defined('G5_PATH')) return;

header("Content-Type: text/html; charset=utf-8");

include_once($board_skin_path."/mw.proc/mw.g5.adapter.extend.php");

header('Content-Type: application/javascript');
?>

var g4_path      = "<?php echo $g4['path']?>";
var g4_bbs       = "<?php echo $g4['bbs']?>";
var g4_bbs_img   = "<?php echo $g4['bbs_img']?>";
var g4_url       = g5_url;
var g4_is_member = g5_is_member;
var g4_is_admin  = g5_is_admin;
var g4_bo_table  = g5_bo_table;
var g4_sca       = g5_sca;
var g4_charset   = "utf-8";
var g4_cookie_domain = g5_cookie_domain;
var g4_is_gecko  = navigator.userAgent.toLowerCase().indexOf("gecko") != -1;
var g4_is_ie     = navigator.userAgent.toLowerCase().indexOf("msie") != -1;

function win_open(url, name, option)
{
    var popup = window.open(url, name, option);
    popup.focus();
}


