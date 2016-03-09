<?
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
 
if ($mw_basic[cf_vote_join_level] > 1 && !$is_member) exit("로그인 후 이용해주세요.");
if ($mw_basic[cf_vote_join_level] > $member[mb_level]) exit("설문참여 권한이 없습니다.");
if (!$mw_basic[cf_vote]) exit("설문기능을 사용하지 않습니다.");
if ($write[mb_id] == $member[mb_id]) exit("설문작성자는 설문에 참여하실 수 없습니다.");

$ss_name = "ss_view_{$bo_table}_{$wr_id}";
if (!get_session($ss_name))
    die("해당 게시물에서만 설문에 참여하실 수 있습니다.");

$vote = sql_fetch("select * from $mw[vote_table] where bo_table = '$bo_table' and wr_id = '$wr_id'");

if ($vote[vt_sdate] != "0000-00-00 00:00:00" && $vote[vt_sdate] > $g4[time_ymdhis]) exit("설문 시작전입니다.");
if ($vote[vt_edate] != "0000-00-00 00:00:00" && $vote[vt_edate] < $g4[time_ymdhis]) exit("설문종료날짜가 지났습니다.");

if ($vote[vt_comment]) {
    $sql = "select wr_id from $write_table where wr_parent = '$wr_id' and mb_id = '$member[mb_id]' and wr_is_comment = '1'";
    $tmp = sql_fetch($sql);
    if (!$tmp)
        exit("코멘트를 작성해주셔야 설문에 참여가능합니다.");
}

if ($is_member) $row = sql_fetch("select * from $mw[vote_log_table] where vt_id = '$vote[vt_id]' and mb_id = '$member[mb_id]'");
else $row = sql_fetch("select * from $mw[vote_log_table] where vt_id = '$vote[vt_id]' and vt_ip = '$_SERVER[REMOTE_ADDR]'");
if ($row) exit("이미 설문에 참여하셨습니다.");

$tmp = explode(",", $vt_num);
if ($vote[vt_multi] > 1 && count($tmp)-1 > $vote[vt_multi])
    exit("{$vote[vt_multi]}개 까지만 복수선택 하실 수 있습니다.");

for ($i=0, $m=count($tmp)-1; $i<$m; $i++) {
    if ($tmp[$i] == '') continue;
    $vt_num = $tmp[$i];

    $row = sql_fetch("select * from $mw[vote_item_table] where vt_id = '$vote[vt_id]' and vt_num = '$vt_num'");
    if (!$row) exit("존재하지 않는 설문항목입니다.");
}

for ($i=0, $m=count($tmp)-1; $i<$m; $i++) {
    if ($tmp[$i] == '') continue;
    $vt_num = $tmp[$i];

    sql_query("insert into $mw[vote_log_table] set vt_id = '$vote[vt_id]', vt_num = '$vt_num', mb_id = '$member[mb_id]', vt_ip = '$_SERVER[REMOTE_ADDR]', vt_datetime = '$g4[time_ymdhis]'");
    sql_query("update $mw[vote_item_table] set vt_hit = vt_hit + 1 where vt_id = '$vote[vt_id]' and vt_num = '$vt_num'");
    sql_query("update $mw[vote_table] set vt_total = vt_total + 1 where vt_id = '$vote[vt_id]'");
}

if ($vote[vt_point] > 0)
    insert_point($member[mb_id], $vote[vt_point], "설문참여 포인트 지급", $bo_table, $wr_id, "설문");

exit("설문에 참여해주셔서 감사합니다.");

