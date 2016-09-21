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

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

if (!$bo_table or !$wr_id) die("데이터가 없습니다.");

include_once("{$board_skin_path}/mw.lib/mw.skin.basic.lib.php");

if (!$is_admin and !($mw_basic['cf_jump_level'] && $mw_basic['cf_jump_level'] <= $member['mb_level'])) {
    die("권한이 없습니다.");
}

if (!$is_admin and $write['mb_id'] != $member['mb_id']) {
    die("권한이 없습니다.");
}

if ($mw_basic['cf_jump_days'] or $mw_basic['cf_jump_count']) {
    $jump_days = $mw_basic['cf_jump_days'] - 1;
    $old = date("Y-m-d 00:00:00", strtotime("-{$jump_days} day", $g4['server_time']));

    if (!$mw_basic['cf_jump_days']) $old = "";

    $sql = " select count(*) as cnt from {$mw['jump_log_table']} ";
    $sql.= "  where mb_id = '{$member['mb_id']}' ";
    $sql.= "    and jp_datetime > '$old' ";
    $row = sql_fetch($sql);

    $count = $row['cnt'];
    $count++;

    if ($mw_basic['cf_jump_count'] and $count > $mw_basic['cf_jump_count'] and !$is_admin) {
        die("횟수를 초과했습니다. ({$mw_basic['cf_jump_days']}일에 {$mw_basic['cf_jump_count']}번)");
    }
}

if ($mw_basic['cf_jump_point']) {
    if ($mw_basic['cf_jump_point'] > $member['mb_point']) {
        die("포인트가 부족합니다.");
    }
    insert_point($member['mb_id'], -1*$mw_basic['cf_jump_point'], "새글 점프", $bo_table, $wr_id, $g4['time_ymd'].'-'.$count);
}

/*
$wr_num = get_next_num($write_table);

$sql = " update {$write_table} ";
$sql.= "    set wr_num = '{$wr_num}' ";
$sql.= "      , wr_datetime = '{$g4['time_ymdhis']}' ";
$sql.= "  where wr_id = '{$write['wr_id']}' ";

$qry = sql_query($sql);

if ($qry) {
    $sql = " insert into {$mw['jump_log_table']} set ";
    $sql.= " bo_table = '{$bo_table}' ";
    $sql.= " , wr_id = '{$wr_id}' ";
    $sql.= " , mb_id = '{$member['mb_id']}' ";
    $sql.= " , jp_datetime = '{$g4['time_ymdhis']}' ";
    sql_query($sql);

    $sql = " update {$g4['board_new_table']} set bn_datetime = '{$g4['time_ymdhis']}' ";
    $sql.= " where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' ";
    sql_query($sql);
}
*/
mw_jump($bo_table, $wr_id);

echo "ok";

