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
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

/*function alert_only($msg) 
{
    global $g4;
    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=$g4[charset]\">";
    echo "<script language='javascript'> alert('$msg'); </script>";
    exit;
}*/

if (!$is_admin)
    die("접근권한이 없습니다.");

if (!$token or get_session("ss_delete_token") != $token) 
    die("토큰 에러로 실행 불가합니다.");

if ($flag == 'no') 
{
    if (!strstr($write[wr_option], "secret"))
        die("비밀글이 아닙니다.");

    $wr_option = str_replace("secret", "", $write[wr_option]);

    $msg = "비밀글 설정을 해제하였습니다.";
} 
else 
{
    if (strstr($write[wr_option], "secret"))
        die("이미 잠겨져 있는 게시물입니다.");

    if ($write[wr_option]) {
        $wr_option = "$write[wr_option],secret";
    } else {
        $wr_option = "secret";
    }

    $msg = "게시물을 비밀글로 잠궜습니다.";
}

$sql = "update $write_table set wr_option = '$wr_option' where wr_id = '$wr_id'";
sql_query($sql);

die($msg);
