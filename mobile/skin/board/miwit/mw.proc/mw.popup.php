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
header("Content-Type: text/html; charset=$g4[charset]");

if (!$is_admin) 
    die("접근 권한이 없습니다.");

if (!$token or get_session("ss_delete_token") != $token) {
    if (get_session("ss_popup_token") != $token) {
        die("토큰 에러로 실행 불가합니다.");
    }
}

$sql = "create table if not exists $mw[popup_notice_table] (
bo_table varchar(20) not null,
wr_id int not null,
primary key (bo_table, wr_id)
) $default_charset ";
sql_query($sql);

$sql = "select * from $mw[popup_notice_table] where bo_table = '$bo_table' and wr_id = '$wr_id' ";
$row = sql_fetch($sql);
if (!$row) {
    sql_query("insert into $mw[popup_notice_table] set bo_table = '$bo_table', wr_id = '$wr_id' ");
    die("팝업공지로 등록되었습니다. ");
} else {
    sql_query("delete from $mw[popup_notice_table] where bo_table = '$bo_table' and wr_id = '$wr_id' ");
    die("팝업공지를 내렸습니다. ");
}

