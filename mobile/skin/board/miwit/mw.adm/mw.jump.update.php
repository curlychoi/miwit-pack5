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
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

if (!$bo_table) die("데이터가 없습니다.");

include_once("{$board_skin_path}/mw.lib/mw.skin.basic.lib.php");

if (!$is_admin) {
    die("권한이 없습니다.");
}

$tmp_array = array();
$tmp_array = $_POST['chk_wr_id'];

$chk_count = count($tmp_array);

for ($i=$chk_count-1; $i>=0; $i--) 
{
    $wr_id = $tmp_array[$i];
    mw_jump($bo_table, $wr_id);
}

goto_url(mw_seo_url($bo_table, 0, "&page=$page" . $qstr));

