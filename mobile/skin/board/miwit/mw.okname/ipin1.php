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

//KCB 운영서버를 호출할 경우
$idpUrl    = "https://ipin.ok-name.co.kr/tis/ti/POTI90B_SendCertInfo.jsp";
$returnUrl = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/ipin2.php"; // 본인인증 완료후 리턴될 URL (도메인 포함 full path)

$idpCode   = "V";       		// 고정값. KCB기관코드
$cpCode    = $mw_basic[cf_kcb_id];	// 회원사 코드 (회원사 아이디)

$keypath = "./key/ipin.key";		// 키파일이 생성될 위치. 웹서버에 해당파일을 생성할 권한 필요.
$reserved1 = "0";			//reserved1
$reserved2 = "0";			//reserved2
$EndPointURL = "http://www.ok-name.co.kr/KcbWebService/OkNameService";    // 운영 서버
$logpath = "./log";		        // 로그파일을 남기는 경우 로그파일이 생성될 경로
$option = "CL";

if ($is_test) {
    $idpUrl = "https://tipin.ok-name.co.kr:8443/tis/ti/POTI90B_SendCertInfo.jsp";
    //$EndPointURL = "http://tallcredit.kcb4u.com:9088/KcbWebService/OkNameService";
    $EndPointURL = "http://twww.ok-name.co.kr:8888/KcbWebService/OkNameService";
    $cpCode = "P00000000000";
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
$cmd = "$exe $keypath $cpCode \"{$reserved1}\" \"{$reserved2}\" $EndPointURL $logpath $option";

// 실행
exec($cmd, $out, $ret);


$pubkey = "";
$sig = "";
$curtime = "";

$pubkey=$out[0];
$sig=$out[1];
$curtime=$out[2];

set_session("ss_ipin_bo_table", $bo_table);

include_once("$g4[path]/head.sub.php");
?>
<script type="text/javascript">
$(document).ready(function () {

    //KCB 운영서버를 호출할 경우
    document.kcbInForm.action = "https://ipin.ok-name.co.kr/tis/ti/POTI01A_LoginRP.jsp";

    <? if ($is_test) { ?>
    //KCB 테스트서버를 호출할 경우
    document.kcbInForm.action = "https://tipin.ok-name.co.kr:8443/tis/ti/POTI01A_LoginRP.jsp";
    <? } ?>

    document.kcbInForm.submit();
});
</script>


<form name="kcbInForm" method="post" >
<input type="hidden" name="IDPCODE" value="<?=$idpCode?>" />
<input type="hidden" name="IDPURL" value="<?=$idpUrl?>" />
<input type="hidden" name="CPCODE" value="<?=$cpCode?>" />
<input type="hidden" name="CPREQUESTNUM" value="<?=$curtime?>" />
<input type="hidden" name="RETURNURL" value="<?=$returnUrl?>" />
<input type="hidden" name="WEBPUBKEY" value="<?=$pubkey?>" />
<input type="hidden" name="WEBSIGNATURE" value="<?=$sig?>" />
</form>
<form name="kcbOutForm" method="post">
<input type="hidden" name="encPsnlInfo" />
<input type="hidden" name="virtualno" />
<input type="hidden" name="dupinfo" />
<input type="hidden" name="realname" />
<input type="hidden" name="cprequestnumber" />
<input type="hidden" name="age" />
<input type="hidden" name="sex" />
<input type="hidden" name="nationalinfo" />
<input type="hidden" name="birthdate" />
<input type="hidden" name="coinfo1" />
<input type="hidden" name="coinfo2" />
<input type="hidden" name="ciupdate" />
<input type="hidden" name="cpcode" />
<input type="hidden" name="authinfo" />
</form>

<?
include_once("$g4[path]/tail.sub.php");

