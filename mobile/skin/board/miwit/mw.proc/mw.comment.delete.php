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

if (!$bo_table)
    die("bo_table 이 없습니다.");

if (!$is_admin)
    die("관리자만 실행할 수 있습니다.");

if (!$token or get_session("ss_delete_token") != $token) 
    die("토큰 에러로 실행 불가합니다.");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

$chk_comment_id = explode(",", $comment_id);

for ($i=0, $m=count($chk_comment_id); $i<$m; $i++) 
{
    $write = sql_fetch(" select * from $write_table where wr_id = '{$chk_comment_id[$i]}' ");
    if ($write[wr_id])
        mw_delete_row($board, $write, 'no');
}

echo "ok";
exit;
//goto_url("$g4[bbs_path]/board.php?bo_table=$bo_table&wr_id=$wr_id&sfl=$sfl&stx=$stx&page=$page");

