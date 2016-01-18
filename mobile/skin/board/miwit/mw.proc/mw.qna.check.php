<?
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

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

// 게시판 관리자 이상 복사, 이동 가능
if ($is_admin != 'board' && $is_admin != 'group' && $is_admin != 'super') 
    exit("게시판 관리자 이상 접근이 가능합니다.");

if ($sw != "0" && $sw != "1" && $sw != "2")
    alert("sw 값이 제대로 넘어오지 않았습니다.");

for ($i=0, $m=count($_POST['chk_wr_id']); $i<$m; $i++) 
{
    $wr_id = $_POST['chk_wr_id'][$i];
    sql_query("update $write_table set wr_qna_status = '$sw' where wr_id = '$wr_id'");
}

switch ($sw) {
    case '0': $m = '미해결'; break;
    case '1': $m = '해결'; break;
    case '2': $m = '보류'; break;
}

echo "질문을 $m 처리 했습니다.";
exit;

?>
