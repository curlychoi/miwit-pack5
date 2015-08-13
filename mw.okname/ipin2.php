<?php
/**
 * Bechu-Basic Skin for Gnuboard4
 *
 * Copyright (c) 2008 Choi Jae-Young <www.miwit.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

include_once("_common.php");

$bo_table = get_session("ss_ipin_bo_table");
set_session("ss_ipin_bo_table", "");

$bo_table = preg_match("/^[a-zA-Z0-9_]+$/", $bo_table) ? $bo_table : "";
if (isset($bo_table)) {
    $board = sql_fetch(" select * from {$g4['board_table']} where bo_table = '$bo_table' ");
    if ($board['bo_table']) {
        $gr_id = $board['gr_id'];
        $write_table = $g4['write_prefix'] . $bo_table; // 게시판 테이블 전체이름
        //$comment_table = $g4['write_prefix'] . $bo_table . $g4['comment_suffix']; // 코멘트 테이블 전체이름
        if ($wr_id)
            $write = sql_fetch(" select * from $write_table where wr_id = '$wr_id' ");
    }
    $board_skin_path = "{$g4['path']}/skin/board/{$board['bo_skin']}"; // 게시판 스킨 경로
}

if (!trim($bo_table)) die();
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

//아이핀팝업에서 조회한 PERSONALINFO이다.
$encPsnlInfo = $_POST["encPsnlInfo"];

//KCB서버 공개키
$WEBPUBKEY = trim($_POST["WEBPUBKEY"]);

//KCB서버 서명값
$WEBSIGNATURE = trim($_POST["WEBSIGNATURE"]);

//아이핀 서버와 통신을 위한 키파일 생성
// 파라미터 정의
$keypath = "./key/ipin.key";
$cpCode = $mw_basic[cf_kcb_id]; // 회원사 코드 (회원사 아이디)
//$EndPointURL = "http://www.allcredit.co.kr/KcbWebService/OkNameService"; // 운영 서버
$EndPointURL = "http://www.ok-name.co.kr/KcbWebService/OkNameService";
$cpubkey = $WEBPUBKEY;       //server publickey
$csig = $WEBSIGNATURE;    //server signature
$encdata = $encPsnlInfo;     //PERSONALINFO
$logpath = "./log";
$option = "SL";

if ($is_test) {
    $cpCode = "P00000000000";
    //$EndPointURL = "http://tallcredit.kcb4u.com:9088/KcbWebService/OkNameService";
    $EndPointURL = "http://twww.ok-name.co.kr:8888/KcbWebService/OkNameService";
}

if (preg_match("/utf/i", $g4[charset])) {
    $option .= "U";
}

if (isset($_ENV["OS"]) && eregi("win", $_ENV["OS"])) // 윈도우
{
    $exe = dirname(__FILE__)."\\okname.exe";
}
else
{
    exec("getconf LONG_BIT", $r);
    if ($r[0] == '32') {
        $exe = dirname(__FILE__)."/okname";  // linux 32bit
    } else {
        $exe = dirname(__FILE__)."/okname64"; // linux 64bit
    }
}

// 명령어
$cmd = "$exe $keypath $cpCode $EndPointURL $cpubkey $csig $encdata $logpath $option";

// 실행
exec($cmd, $out, $ret);

if (strlen($out[0]) != 64)
    alert_close("아이핀 인증 실패");

$DupInfo = $out[0];
$realname = $out[6];
$sex = $out[9];
$birthDate = $out[11];

/*
$row = sql_fetch("select * from mw_kcb_info where ok_DupInfo = '$DupInfo' ", false);
if ($row)
    alert_close("이미 가입하셨습니다.");

set_session("ss_okname", $DupInfo);
*/

// 19금
if ($mw_basic[cf_kcb_type] == '19ban')
{
    $b = $birthDate;
    $n = date("Ymd", $g4[server_time]);
    $y = floor(($n - $b) / 10000);

    if ($y < 19)
        alert_close("19세 이상만 이용이 가능합니다.");
}

if (!get_session("ss_okname"))
{
    set_session("ss_okname", $DupInfo);

    $sql = " insert into $mw[okname_table] set mb_id = '$member[mb_id]' ";
    $sql.= ", ok_ip = '$_SERVER[REMOTE_ADDR]' ";
    $sql.= ", ok_datetime = '$g4[time_ymdhis]' ";
    sql_query($sql, false);
}

// 결과라인에서 값을 추출
/*
foreach($out as $a => $b) {
    if($a < 13) {
        $field[$a] = $b;
    }
}
*/
/*
   $field_name_IPIN_DEC = array(
   "dupInfo        ",	// 0
   "coinfo1        ",	// 1
   "coinfo2        ",	// 2
   "ciupdate       ",	// 3
   "virtualNo      ",	// 4
   "cpCode         ",	// 5
   "realName       ",	// 6
   "cpRequestNumber",	// 7
   "age            ",	// 8
   "sex            ",	// 9
   "nationalInfo   ",	// 10
   "birthDate      ",	// 11
   "authInfo       ",	// 12
   );

   echo "encPsnlInfo=$encPsnlInfo<br>";	
// 추출된 값 프린트
foreach($field as $a => $b) {
echo $field_name_IPIN_DEC[$a].": ".$field[$a]."<br>";
}
 */

include_once("$g4[path]/head.sub.php");
?>

<script type="text/javascript">
$(document).ready(function () {
    opener.location.reload();
    self.close();
});
</script>

<?
include_once("$g4[path]/tail.sub.php");

