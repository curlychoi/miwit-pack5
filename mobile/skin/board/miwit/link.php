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

if (defined("G5_PATH"))
    include_once($board_skin_path."/mw.proc/mw.g5.adapter.extend.php");

$html_title = "$group[gr_subject] > $board[bo_subject] > " . conv_subject($write[wr_subject], 255) . " > 링크";

if (!($bo_table && $wr_id && $no)) 
    alert_close("값이 제대로 넘어오지 않았습니다.");

// SQL Injection 예방
$row = sql_fetch(" select count(*) as cnt from {$g4[write_prefix]}{$bo_table} ", FALSE);
if (!$row[cnt])
    alert_close("존재하는 게시판이 아닙니다.");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if (!$write["wr_link{$no}"])
    alert_close("링크가 없습니다.");

if (!$is_admin and $mw_basic['cf_link_level'] > $member['mb_level'])
    alert_close("권한이 없습니다.");

$ss_name = "ss_link_{$bo_table}_{$wr_id}_{$no}";
if (empty($_SESSION[$ss_name])) 
{
    if (!$is_admin && $mw_basic[cf_link_point]) {
        $sql = "select * from $g4[point_table] ";
        $sql.= " where mb_id = '$member[mb_id]' ";
        $sql.= "   and po_rel_table = '$bo_table' ";
        $sql.= "   and po_rel_id = '$wr_id' ";
        $sql.= "   and po_rel_action = '링크'";
        $tmp = sql_fetch($sql);
        if (!$tmp && $member[mb_point] + $mw_basic[cf_link_point] < 0) {
            $str = "보유하신 포인트(".number_format($member[mb_point]).")가 없거나 모자라서 ";
            $str.= "링크이동(".number_format($mw_basic[cf_link_point]).")이 불가합니다.";
            $str.= "\\n\\n포인트를 모으신 후 다시 링크를 클릭해 주십시오.";
            alert_close($str);
        }
        if (!$tmp) {
            insert_point($member[mb_id], $mw_basic[cf_link_point],
                "$board[bo_subject] $wr_id 링크클릭", $bo_table, $wr_id, '링크');
        }
    }

    $sql = " update {$g4[write_prefix]}{$bo_table} set wr_link{$no}_hit = wr_link{$no}_hit + 1 where wr_id = '$wr_id' ";
    sql_query($sql);

    set_session($ss_name, true);
}

if ($mw_basic[cf_link_log]) { // 링크 기록
    $ll_name = $board[bo_use_name] ? $member[mb_name] : $member[mb_nick];
    $sql = "insert into $mw[link_log_table]
               set bo_table = '$bo_table'
                   , wr_id = '$wr_id'
                   , ll_no = '$no'
                   , mb_id = '$member[mb_id]'
                   , ll_name = '$ll_name'
                   , ll_ip = '$_SERVER[REMOTE_ADDR]'
                   , ll_datetime = '$g4[time_ymdhis]'";
    $qry = sql_query($sql);
}

$url = set_http($write["wr_link{$no}"]);

if ($mw_basic['cf_hidden_link'] && $write["wr_hidden_link{$no}"]) {
    $url = set_http($write["wr_hidden_link{$no}"]);
}

goto_url($url);

