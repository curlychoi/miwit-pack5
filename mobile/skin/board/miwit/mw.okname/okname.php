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

if (!trim($bo_table)) die();

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

header("Content-Type: text/html; charset=$g4[charset]");

$name           = $_POST[nam]; // *** 성명
$ssn            = $_POST[ssn]; // *** 주민번호 (숫자만)
$memid          = $mw_basic[cf_kcb_id]; // *** 회원사코드
$qryBrcCd       = "x"; 
$qryBrcNm       = "x"; 
$qryId          = "u1234"; // 쿼리ID, 고정값 
$qryKndCd       = "1"; // 요청구분  내국인,주민등록번호 : 1, 외국인,외국인등록번호 : 2 
$qryRsnCd       = "04"; // 조회사유  회원가입 : 01, 회원정보수정 : 02, 회원탈회 : 03, 성인인증 : 04, 기타 : 05
$qryIP          = $_SERVER[SERVER_ADDR]; // *** 회원사 IP,   $_SERVER["SERVER_ADDR"] 사용가능.
$qryDomain      = $_SERVER[SERVER_NAME]; // *** 회원사 도메인, $_SERVER["SERVER_NAME"] 사용가능.
$qryDt          = date("Ymd", $g4[server_time]); // 현재일자 20101101 과 같이 숫자8자리 입력되어야함.
//$EndPointURL    = "http://www.allcredit.co.kr/KcbWebService/OkNameService"; 
$EndPointURL    = "http://www.ok-name.co.kr/KcbWebService/OkNameService"; 
$Option         = "D"; // utf-8인경우는 U추가, D: debug mode, L: log 기록.

// 19금
if ($mw_basic[cf_kcb_type] == '19ban')
{
    $b = null;
    $s = substr($ssn, 6, 1);

    if ($s == '1' || $s == '2')
        $b = '19' . substr($ssn, 0, 6);
    else
        $b = '20' . substr($ssn, 0, 6);

    $n = date("Ymd", $g4[server_time]);
    $y = floor(($n - $b) / 10000);

    if ($y < 19) die("19ban");
}

if (strtolower(str_replace($g4[charset], '-', '')) == 'utf8') {
    $Option .= "U";
} else {
    $name = set_euckr($name);
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

$cmd = "{$exe} \"{$name}\" \"{$ssn}\" $memid $qryBrcCd $qryBrcNm $qryId $qryKndCd ";
$cmd.= " $qryRsnCd $qryIP $qryDomain $qryDt $EndPointURL $Option";

exec($cmd, $out, $ret);

if ($ret <= 200)
    $result = sprintf("B%03d", $ret);
else
    $result = sprintf("S%03d", $ret);

echo $result;

if ($result == 'B000' && !get_session("ss_okname"))
{
    set_session("ss_okname", $name);

    if (!$member[mb_id]) exit;

    $row = sql_fetch("select * from $mw[okname_table] where mb_id = '$member[mb_id]'", false);
    if ($row) exit;

    $sql = " insert into $mw[okname_table] set mb_id = '$member[mb_id]' ";
    $sql.= ", ok_ip = '$_SERVER[REMOTE_ADDR]' ";
    $sql.= ", ok_datetime = '$g4[time_ymdhis]' ";
    sql_query($sql, false);
}
exit;

