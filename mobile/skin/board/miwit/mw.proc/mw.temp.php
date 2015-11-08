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
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

// 임시 저장

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

if ($w == "save")
{
    $wr_subject = urldecode($wr_subject);
    $wr_content = urldecode($wr_content);

    if (str_replace("-", "", strtolower($g4['charset'])) != 'utf8' ) {
        $wr_subject = set_euckr($wr_subject);
        $wr_content = set_euckr($wr_content);
    }

    if (!trim($wr_content)) die();

    $sql_common = " tp_subject = '$wr_subject' ";
    $sql_common.= ", tp_content = '$wr_content' ";
    $sql_common.= ", tp_datetime = '$g4[time_ymdhis]' ";

    $sql = " select * from $mw[temp_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' ";
    $row = sql_fetch($sql);
    if ($row) {
        $sql = " update $mw[temp_table] set $sql_common where bo_table = '$bo_table' and mb_id = '$member[mb_id]' ";
        sql_query($sql);
    } else {
        $sql = " insert into $mw[temp_table] set $sql_common, bo_table = '$bo_table', mb_id = '$member[mb_id]' ";
        sql_query($sql);
    }
}
else if ($w == "get")
{
    $row = sql_fetch(" select * from $mw[temp_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' ");
    echo "$row[tp_subject]-mw-basic-temp-return-$row[tp_content]";
}


?>
