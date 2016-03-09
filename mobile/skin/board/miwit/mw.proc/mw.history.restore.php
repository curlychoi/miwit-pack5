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

if (!isset($bo_table) or !isset($wr_id) or !isset($ph_id)) {
    alert("데이터가 없습니다.");
}

include_once("{$board_skin_path}/mw.lib/mw.skin.basic.lib.php");

if (!($mw_basic['cf_post_history'] && $member['mb_level'] >= $mw_basic['cf_post_history_level'])) {
    alert("권한이 없습니다.");
}

$sql = " select * from {$mw['post_history_table']} ";
$sql.= " where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and ph_id = '{$ph_id}' ";
$row = sql_fetch($sql);

if (!$row)
    alert("데이터가 없습니다.");

$sql = "insert into {$mw['post_history_table']}
           set bo_table = '{$board['bo_table']}'
               ,wr_id = '{$write['wr_id']}'
               ,wr_parent = '{$write['wr_parent']}'
               ,mb_id = '{$write['mb_id']}'
               ,ph_name = '{$write['wr_name']}'
               ,ph_option = '{$write['wr_option']}'
               ,ph_subject = '".addslashes($write['wr_subject'])."'
               ,ph_content = '".addslashes($write['wr_content'])."'
               ,ph_ip = '{$_SERVER['REMOTE_ADDR']}'
               ,ph_datetime = '{$g4['time_ymdhis']}'";
sql_query($sql);

$sql = " update {$write_table}
            set wr_subject = '".addslashes($row['ph_subject'])."'
                ,wr_content = '".addslashes($row['ph_content'])."'
          where wr_id = '{$wr_id}'";
sql_query($sql);

alert("복원했습니다.");

