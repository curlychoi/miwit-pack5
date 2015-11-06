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

// 게시판 관리자 이상 복사, 이동 가능
if ($is_admin != "board" && $is_admin != "group" && $is_admin != "super")
    die("게시판 관리자 이상 접근이 가능합니다.");

if (!$token or get_session("ss_delete_token") != $token) 
    die("토큰 에러로 실행 불가합니다.");

if (!$bo_table) die("bo_table 이 없습니다.");
if (!$wr_id) die("wr_id 이 없습니다.");

sql_query("update $write_table set wr_datetime='$g4[time_ymdhis]' where wr_id='$wr_id'");
sql_query("update {$g4['board_new_table']} set bn_datetime='{$g4['time_ymdhis']}' where bo_table = '{$bo_table}' and wr_id='{$wr_id}'");

// 시간순 정렬
if ($renum == "1") {
    $data = array();

    $sql = "select wr_id, wr_num from {$write_table} where wr_is_comment = 0 and wr_reply = '' order by wr_datetime";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $data[] = $row;
    }

    sql_query("update {$write_table} set wr_num = wr_num * -1");
    $wr_num = 0;
    foreach ($data as $row) {
        $wr_num--;
        $row[wr_num] *= -1;

        $sql = "update {$write_table} set wr_num = '{$wr_num}' where wr_num = '{$row[wr_num]}'";
        sql_query($sql);
    }
}

