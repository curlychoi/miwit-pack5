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

if (!$is_admin) 
    die("접근권한이 없습니다.");

if (!$token or get_session("ss_delete_token") != $token) 
    die("토큰 에러로 실행 불가합니다.");

if (!$bo_table) die("bo_table 이 없습니다.");
if (!$wr_id) die("wr_id 이 없습니다.");

if ($write[wr_view_block]) {
    sql_query(" update $write_table set wr_view_block = '' where wr_id = '$wr_id' ");
    die ("이 게시물 보기차단을 해제했습니다.");
} else {
    sql_query(" update $write_table set wr_view_block = '1' where wr_id = '$wr_id' ");
    die ("이 게시물 보기를 차단 했습니다.");
}

