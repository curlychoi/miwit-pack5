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
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

header("Content-Type: text/html; charset=$g4[charset]");
 
if (!($is_admin or ($write[mb_id] && $member[mb_id] && $write[mb_id] == $member[mb_id]))) {
    die("권한이 없습니다.");
}

$vote = sql_fetch("select * from $mw[vote_table] where bo_table = '$bo_table' and wr_id = '$wr_id'");

if (!$vote)
    die("설문이 존재하지 않습니다.");

$sql = "select * from $mw[vote_log_table] where vt_id = '$vote[vt_id]' ";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry)) {
    delete_point($row[mb_id], $bo_table, $wr_id, "설문");
}

sql_query("delete from $mw[vote_log_table] where vt_id = '$vote[vt_id]'");
sql_query("update $mw[vote_item_table] set vt_hit = 0 where vt_id = '$vote[vt_id]'");
sql_query("update $mw[vote_table] set vt_total = 0 where vt_id = '$vote[vt_id]'");

