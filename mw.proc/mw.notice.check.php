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

if ($is_admin != 'board' && $is_admin != 'group' && $is_admin != 'super') 
    exit("게시판 관리자 이상 접근이 가능합니다.");

if ($sw != "up" && $sw != "down")
    alert("sw 값이 제대로 넘어오지 않았습니다.");

include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");

if ($sw == 'down') {
    $bo_notice = explode($notice_div, trim($board['bo_notice']));
    $bo_notice = implode($notice_div, array_diff($bo_notice, $chk_wr_id));

    sql_query(" update {$g4['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$bo_table}' ");

    $msg = "공지를 내렸습니다.";
}
else
{
    $bo_notice = explode($notice_div, trim($board['bo_notice']));
    $bo_notice = implode($notice_div, array_unique(array_merge($bo_notice, $chk_wr_id)));

    sql_query(" update {$g4['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$bo_table}' ");

    /*$tmp = explode("\n", trim($bo_notice));
    for ($i=0, $m=count($tmp); $i<$m; $i++)
        sql_query(" update $write_table set ca_name = '공지' where wr_id = '{$tmp[$i]}' ");*/

    $msg = "공지로 등록하였습니다.";
}

echo $msg;

