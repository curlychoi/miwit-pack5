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

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!$is_admin) 
    exit("관리자만 접근 가능합니다.");

if (!trim($bo_table) || !trim($wr_id))
    exit("데이터가 없습니다.");

if (!$token or get_session("ss_delete_token") != $token) 
    exit("토큰 에러로 실행 불가합니다.");

include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");

if ($is_off)
{
    $notice_array = explode($notice_div, trim($board['bo_notice']));

    $bo_notice = '';
    for ($i=0; $i<count($notice_array); $i++)
        if ((int)$wr_id != (int)$notice_array[$i])
            $bo_notice .= $notice_array[$i] . $notice_div;
    $bo_notice = trim($bo_notice);
    sql_query(" update {$g4['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$bo_table}' ");

    $msg = "공지를 내렸습니다.";
}
else
{
    $bo_notice = $wr_id . $notice_div . $board['bo_notice'];
    sql_query(" update {$g4['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$bo_table}' ");
    //sql_query(" update $write_table set ca_name = '공지' where wr_id = '{$wr_id}' ");

    $msg = "공지로 등록하였습니다.";
}

echo $msg;

