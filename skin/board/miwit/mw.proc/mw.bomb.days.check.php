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

if (!$bo_table) die("bo_table 이 없습니다.");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

$tmp = explode("-", substr($bomb_day, 0, 10));
if (!@checkdate($tmp[1], $tmp[2], $tmp[0])) die("자동폭파 날짜가 올바르지 않습니다.");

if ($g4[server_time] > strtotime("$bomb_day:00"))
    die("자동폭파는 미래의 시간으로 설정해주세요.");

if ($mw_basic[cf_bomb_days_min] && $g4[server_time] + ($mw_basic[cf_bomb_days_min]*60*60*24) > strtotime("$bomb_day:00"))
    die("자동폭파는 최소 {$mw_basic[cf_bomb_days_min]}일 이상 설정 가능합니다.");

if ($mw_basic[cf_bomb_days_max] && $g4[server_time] + ($mw_basic[cf_bomb_days_max]*60*60*24) < strtotime("$bomb_day:00"))
    die("자동폭파는 최대 {$mw_basic[cf_bomb_days_max]}일 까지만 설정 가능합니다.");

