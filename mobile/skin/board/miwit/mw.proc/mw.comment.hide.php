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

header("Content-Type: text/html; charset=$g4[charset]");

if (!$is_admin) 
    exit("관리자만 접근 가능합니다.");

if (!trim($bo_table) || !trim($wr_id))
    exit("데이터가 없습니다.");

if (!$token or get_session("ss_delete_token") != $token) 
    die("토큰 에러로 실행 불가합니다.");

if ($is_off)
{
    sql_query(" update $write_table set wr_comment_hide = '1' where wr_id = '$wr_id' ");
    $msg = "댓글을 감추었습니다.";
}
else
{
    sql_query(" update $write_table set wr_comment_hide = '' where wr_id = '$wr_id' ");
    $msg = "댓글을 오픈하였습니다. ";
}

echo $msg;

?>
