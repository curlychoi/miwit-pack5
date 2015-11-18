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
$viewport = "<meta name=\"viewport\" content=\"width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0\">";
ob_start();
include_once("$g4[path]/head.sub.php");
$head = ob_get_clean();
$head = str_replace("<head>", "<head>\n{$viewport}", $head);
echo $head;

if ($is_admin != "super") 
    alert_close("최고관리자만 접근 가능합니다.");

if (!$ip)
    alert_close("IP 주소가 없습니다.");

$query = $ip;

$server = "whois.nic.or.kr";
$server_name = "KRNIC";

$fp = fsockopen($server, 43);
if(!$fp)
    alert_close("WHOIS 서버에 접속할 수 없습니다.");

fputs($fp,"$ip\n");

while (!feof($fp)) {
    $row = fgets($fp, 80);
    if (!preg_match("/^utf/i", $g4[charset]))
        $row = iconv("utf-8", "cp949", $row)."<br>";
    
    $res .= $row;
}
fclose($fp);
?>
<div style="padding:10px">

    <h3> <?=$ip?> 에 대한 <?=$server_name?>  WHOIS 검색결과 입니다.</h3>
    <hr>
    <?
        echo nl2br($res);
    ?>
    <hr>
    <div style="text-align:center; height:50px;"><input type="button" value="닫     기" onclick="self.close();"></div>
</div>
<?
include_once("$g4[path]/tail.sub.php");
