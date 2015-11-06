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

$mw_is_view = true;

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

if ($good == "good" && !$mw_basic[cf_good_level] && !$is_member) {
    die("회원만 가능합니다.");
}
if ($good == "nogood" && !$mw_basic[cf_nogood_level] && !$is_member) {
    die("회원만 가능합니다.");
}
if ($good == "good" && $member[mb_level] < $mw_basic[cf_good_level]) {
    die("추천 권한이 없습니다.");
}
if ($good == "nogood" && $member[mb_level] < $mw_basic[cf_nogood_level]) {
    die("비추천 권한이 없습니다.");
}

$mb_id = $member[mb_id];
if (!$is_member)
    $mb_id = $_SERVER[REMOTE_ADDR];

if (!($bo_table && $wr_id))
    die("값이 제대로 넘어오지 않았습니다.");

$ss_name = "ss_view_{$bo_table}_{$wr_id}";
if (!get_session($ss_name))
    die("해당 게시물에서만 추천 또는 비추천 하실 수 있습니다.");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

$row = sql_fetch(" select count(*) as cnt from {$g4[write_prefix]}{$bo_table} ", FALSE);
if (!$row[cnt])
    die("존재하는 게시판이 아닙니다.");

$sql = " select * from $g4[board_good_table]
          where bo_table = '$bo_table'
            and wr_id = '$wr_id' 
            and mb_id = '$mb_id' 
            and bg_flag = 'nogood' ";
$row = sql_fetch($sql);

$flag = "false";

if ($row)
    $flag = "true";

echo $flag;
exit;

