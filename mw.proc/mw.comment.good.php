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

$ss_name = "ss_view_{$bo_table}_{$parent_id}";
if (!get_session($ss_name)) die("해당 게시물에서만 추천 또는 반대 하실 수 있습니다.");

$row = sql_fetch(" select count(*) as cnt from {$g4[write_prefix]}{$bo_table} ", FALSE);
if (!$row[cnt])
    die("존재하는 게시판이 아닙니다.");

if ($good == "good" || $good == "nogood") 
{
    if($write[mb_id] == $member[mb_id])
        die("자신의 글에는 추천 또는 반대 하실 수 없습니다.");

    if (!$mw_basic[cf_comment_good] && $good == "good")
        die("이 게시판은 코멘트 추천 기능을 사용하지 않습니다.");

    if (!$mw_basic[cf_comment_nogood] && $good == "nogood")
        die("이 게시판은 코멘트 반대 기능을 사용하지 않습니다.");

    $sql = " select * from $mw[comment_good_table]
              where bo_table = '$bo_table'
                and parent_id = '$parent_id' 
                and wr_id = '$wr_id' 
                and mb_id = '$member[mb_id]' 
                and bg_flag in ('good', 'nogood') ";
    $row = sql_fetch($sql);
    if ($row[bg_flag])
    {
        if ($row[bg_flag] == "good")
            $status = "추천";
        else 
            $status = "반대";
        
        if (!$mw_basic[cf_good_cancel] or $good != $row[bg_flag]) {
            die("이미 '$status' 하신 글 입니다.");
        }
        else {
            if ($mw_basic[cf_good_cancel_days] and ($g4[server_time] > strtotime($row[bg_datetime])+($mw_basic[cf_good_cancel_days]*86400))) {
                die("'$status'을 취소할 수 있는 기간($mw_basic[cf_good_cancel_days]일)이 지났습니다.");
            }

            // 추천(찬성), 비추천(반대) 카운트 증가
            if ($write["wr_{$row[bg_flag]}"] > 0)
                sql_query(" update {$g4[write_prefix]}{$bo_table} set wr_{$row[bg_flag]} = wr_{$row[bg_flag]} - 1 where wr_id = '$wr_id' ");
            else
                sql_query(" update {$g4[write_prefix]}{$bo_table} set wr_{$row[bg_flag]} = 0 where wr_id = '$wr_id' ");

            // 내역 삭제
            sql_query(" delete from $g4[board_good_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and mb_id = '$member[mb_id]' ");

            sql_query(" delete from $mw[comment_good_table] where bo_table = '$bo_table' and parent_id = '$parent_id' and wr_id = '$wr_id' and mb_id = '$member[mb_id]' ");

            if ($row[bg_flag] == "good")
            { 
                delete_point($write[mb_id], $bo_table, $wr_id, $member[mb_id].'@good');
                delete_point($member[mb_id], $bo_table, $wr_id, $member[mb_id].'@good_re');
            }
            else
            {
                delete_point($write[mb_id], $bo_table, $wr_id, $member[mb_id].'@nogood');
                delete_point($member[mb_id], $bo_table, $wr_id, $member[mb_id].'@nogood_re');
            }
            $row = sql_fetch(" select wr_{$good} from {$g4[write_prefix]}{$bo_table} where wr_id = '$wr_id' ");
            die("이 코멘트 '$status'을 취소 하셨습니다.|".$row["wr_{$good}"]);
        }
    }
    else
    {
        if ($mw_basic[cf_good_days]) {
            if ($g4[server_time] - strtotime($write[wr_datetime]) > $mw_basic[cf_good_days] * 86400) {
                die ("추천/비추천 할 수 있는 기간이 지났습니다.\n\n글 작성 후 {$mw_basic[cf_good_days]}일 동안만 추천/비추천이 가능합니다. ");
            }
        }

        if ($mw_basic[cf_good_count]) {
            $sql = " select count(*) as cnt from $g4[board_good_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and bg_datetime like '$g4[time_ymd]%' ";
            $tm1 = sql_fetch($sql);
            $sql = " select count(*) as cnt from $mw[comment_good_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and bg_datetime like '$g4[time_ymd]%' ";
            $tm2 = sql_fetch($sql);
            if ($tm1[cnt]+$tm2[cnt] >= $mw_basic[cf_good_count])
                die("추천/비추천은 하루에 $mw_basic[cf_good_count]번만 가능합니다.");
        }

        // 추천(찬성), 반대(반대) 카운트 증가
        sql_query(" update {$g4[write_prefix]}{$bo_table} set wr_{$good} = wr_{$good} + 1 where wr_id = '$wr_id' ");
        // 내역 생성
        sql_query(" insert $mw[comment_good_table] set bo_table = '$bo_table', parent_id = '$parent_id', wr_id = '$wr_id', mb_id = '$member[mb_id]', bg_flag = '$good', bg_datetime = '$g4[time_ymdhis]' ");

        if ($good == "good")
        { 
            $status = "추천";
            if ($mw_basic[cf_comment_good_point])
                insert_point($write[mb_id], $mw_basic[cf_comment_good_point], "코멘트 추천 점수를 받았습니다.", $bo_table, $wr_id, $member[mb_id].'@good');
            if ($mw_basic[cf_comment_good_re_point])
                insert_point($member[mb_id], $mw_basic[cf_comment_good_re_point], "코멘트를 추천 했습니다.", $bo_table, $wr_id, $member[mb_id].'@good_re');
 
        }
        else
        {
            $status = "비추천";
            if ($mw_basic[cf_comment_nogood_point])
                insert_point($write[mb_id], $mw_basic[cf_comment_nogood_point], "코멘트 비추천 점수를 받았습니다.", $bo_table, $wr_id, $member[mb_id].'@nogood');
            if ($mw_basic[cf_comment_nogood_re_point])
                insert_point($member[mb_id], $mw_basic[cf_comment_nogood_re_point], "코멘트를 비추천 했습니다.", $bo_table, $wr_id, $member[mb_id].'@nogood_re');

        }

        $row = sql_fetch(" select wr_{$good} from {$g4[write_prefix]}{$bo_table} where wr_id = '$wr_id' ");

        die("이 코멘트를 '$status' 하셨습니다.|".$row["wr_{$good}"]);
    }
}

