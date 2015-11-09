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

if (!$bo_table or !$wr_id or !$is_member)
    die("no data");

include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");

$sql = " select * from {$g4['point_table']} ";
$sql.= "  where mb_id = '{$member['mb_id']}' ";
$sql.= "    and po_rel_table = '{$bo_table}' ";
$sql.= "    and po_rel_id = '{$wr_id}' ";
$sql.= "    and po_rel_action = '링크' ";
$tmp = sql_fetch($sql);

if ($tmp) echo '1';

exit;
