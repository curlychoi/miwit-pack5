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

if (!$is_member) die("false|회원만 접근 가능합니다.");
if (!$bo_table or !$wr_id) die("false|데이터가 없습니다.");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

$sql = " select count(*) as cnt from $g4[scrap_table]
          where mb_id = '$member[mb_id]'
            and bo_table = '$bo_table'
            and wr_id = '$wr_id' ";
$row = sql_fetch($sql);

if ($row[cnt])
    die("false|이미 스크랩 하신 글 입니다.");

$sql = " insert into $g4[scrap_table] ( mb_id, bo_table, wr_id, ms_datetime )
         values ( '$member[mb_id]', '$bo_table', '$wr_id', '$g4[time_ymdhis]' ) ";
sql_query($sql);

$ms_id = mysql_insert_id();

$ms_subject = addslashes($write[wr_subject]);
sql_query(" update $g4[scrap_table] set ms_subject = '$ms_subject' where ms_id = '$ms_id'", false);

$sql = " select count(*) as cnt from $g4[scrap_table] where bo_table = '$bo_table' and wr_id = '$wr_id' ";
$row = sql_fetch($sql);

echo $row[cnt];
