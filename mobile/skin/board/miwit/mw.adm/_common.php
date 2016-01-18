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

// common.php 의 상대 경로
$g4_path = "../../../..";
if (is_file($g4_path."/common.php")) {
    // pc 스킨
    include_once($g4_path."/common.php");
}
else {
    $g4_path = "../../../../..";
    if (is_file($g4_path."/common.php")) {
        // g5 모바일 스킨
        include_once($g4_path."/common.php");
    }
    else {
        $g4_path = "../../../../../..";
        // 배추 모바일 스킨
        include_once($g4_path."/common.php");
    }
}

if (defined('G5_PATH'))
    header("Content-Type: text/html; charset=utf-8");
else
    header("Content-Type: text/html; charset=".$g4['charset']);

$admin_menu = array("config" => "tab", "board_member" => "tab");

