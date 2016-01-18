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
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

if (!$is_member)
    die("회원만가능합니다.");

if (!($bo_table && $wr_id))
    die("값이 제대로 넘어오지 않았습니다.");

$ss_name = "ss_view_{$bo_table}_{$wr_id}";
if (!get_session($ss_name)) die("해당 게시물에서만 답변을 채택하실 수 있습니다.");

if ($mw_basic[cf_attribute] != 'qna')
    die("이 게시판은 질문 채택 기능을 사용하지 않습니다.");

if (!$is_admin && $write[wr_qna_status] > 0) 
    die("이미 답변이 채택되었거나 보류되었습니다.");

if (!($member[mb_id] && ($member[mb_id] == $write[mb_id] || $is_admin))) 
    die("답변을 채택할 권한이 없습니다.");

if ($choose_id) { //채택
    $answer = sql_fetch("select * from $write_table where wr_id = '$choose_id'");

    if ($answer[mb_id] == $write[mb_id] && !$is_admin)
        die("자신의 답변은 채택하실 수 없습니다.");

    if ($answer[wr_ip] == $write[wr_ip] && !$is_admin)
        die("자신의 답변은 채택하실 수 없습니다.");

    if ($answer[mb_id] == '@lucky-writing') {
        $mb = get_member("@lucky-writing", "mb_nick");
        die("{$mb[mb_nick]}은 채택하실 수 없습니다.");
    }

    $row = sql_fetch("select wr_id from $write_table where wr_id = '$choose_id' and wr_parent = '$wr_id' and wr_is_comment = '1'");
    if (!$row)
        die("존재하지 않는 게시물입니다.");

    sql_query("update $write_table set wr_qna_status = '1', wr_qna_id = '$choose_id' where wr_id = '$wr_id'");

    $qna_save_point = round($write[wr_qna_point] * round($mw_basic[cf_qna_save]/100,2));
    $qna_total_point = $qna_save_point + $mw_basic[cf_qna_point_add];

    delete_point($write[mb_id], $bo_table, $wr_id, '@qna-hold');
    insert_point($answer[mb_id], $qna_total_point, "답변채택 포인트", $bo_table, $wr_id, '@qna-choose');

    if (function_exists('mw_moa_insert')) {
        $w = 'a';
        mw_moa_insert($wr_id, $choose_id, $answer[mb_id], $write[mb_id]);
    }

    die("답변이 채택되었습니다.|ok");

} else { // 보류

    sql_query("update $write_table set wr_qna_status = '2' where wr_id = '$wr_id'");

    $hold_point = round($write[wr_qna_point] * $mw_basic[cf_qna_hold]/100, 0);
    insert_point($write[mb_id], $hold_point, "질문 보류, 포인트 $mw_basic[cf_qna_hold]% 환원", $bo_table, $wr_id, '@qna-hold');

    die("질문이 보류되었습니다.|ok");
}

