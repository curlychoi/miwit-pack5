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

if (!$bo_table)
    alert("데이터가 없습니다.");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if ($is_admin != 'super')
    alert("접근 권한이 없습니다.");

for ($i=2; $i<=10; $i++)
{
    $sql_common = "  bo_table = '{$bo_table}' ";
    $sql_common.= ", mb_level = '{$i}' ";
    $sql_common.= ", cf_use = '{$cf_use[$i]}' ";
    $sql_common.= ", cf_write_day = '{$cf_write_day[$i]}' ";
    $sql_common.= ", cf_write_day_count = '{$cf_write_day_count[$i]}' ";
    $sql_common.= ", cf_qna_count = '{$cf_qna_count[$i]}' ";

    $sql = " replace into {$mw['level_table']} set {$sql_common}  ";
    $qry = sql_query($sql);
    /*$sql = " update {$mw['level_table']} set {$sql_common} where mb_level = '{$i}' and bo_table = '{$bo_table}' ";
    $qry = sql_query($sql);
    if (!mysql_affected_rows()) {
        $sql = " insert into {$mw['level_table']} set {$sql_common} ";
        sql_query($sql);
    }*/
}

alert("저장했습니다.", "mw.level.php?bo_table=".$bo_table);

