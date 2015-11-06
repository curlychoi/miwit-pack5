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

$width = $_GET['width'];
if (!$width) {
    if ($board['bo_table_width'] > 100)
        $width = @intval($board['bo_table_width']*.5);
    else
        $width = 350;
}

if ($mw_basic[cf_vote]) {
    $vote = sql_fetch("select * from $mw[vote_table] where bo_table = '$bo_table' and wr_id = '$wr_id'");
    $vote_list = array();

    $max = 0;
    $sql = "select * from $mw[vote_item_table] where vt_id = '$vote[vt_id]'";
    $qry = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($qry); $i++) {
        //$row[vt_rate] = @round($row[vt_hit] / $vote[vt_total], 4) * 100;
        if ($max < $row['vt_hit'])
            $max = $row['vt_hit'];
        $row[vt_rate] = @round($row[vt_hit] / $vote[vt_total], 2) * 100;
        if ($row[vt_rate])
            $row[vt_rate] = "$row[vt_rate]% <span class='count'> (".number_format($row[vt_hit]).") </span>";
        else
            $row[vt_rate] = "<span class='zero'>0</span>";

        $row[vt_width] = @intval($width * ($row[vt_rate] / 100));
        $row[vt_item] = cut_str(get_text(strip_tags($row[vt_item])), 50);
        $vote_list[$i] = $row;
    }

    // 출력 비율 변경 (100% -> $max%)
    foreach ((array)$vote_list as $i=>$row) {
        $vote_list[$i]['vt_width'] = @round($width*$row['vt_hit']/$max);
    }

    if ($vote[vt_multi]) {
        $qry = sql_query("select count(*) as cnt from $mw[vote_log_table] where vt_id = '$vote[vt_id]' group by mb_id");
        $vt_total = mysql_num_rows($qry);
    }
    else {
        $vt_total = $vote[vt_total];
    }

    if ($write[mb_id] == $member[mb_id]) { // 자신의 글은 그냥 출력
        $is_vote = true;
    } else {
        $is_vote = false;
        if ($vote[vt_sdate] != "0000-00-00 00:00:00" && $g4[time_ymdhis] < $vote[vt_sdate]) {
            $is_vote = true;
        } else if ($vote[vt_edate] != "0000-00-00 00:00:00" && $g4[time_ymdhis] > $vote[vt_edate]) {
            $is_vote = true;
        } else  {
            if ($is_member) $row = sql_fetch("select * from $mw[vote_log_table] where vt_id = '$vote[vt_id]' and mb_id = '$member[mb_id]'");
            else $row = sql_fetch("select * from $mw[vote_log_table] where vt_id = '$vote[vt_id]' and vt_ip = '$_SERVER[REMOTE_ADDR]'");
            if ($row)
                $is_vote = true;
        }
    }
}

$gr = array();
for ($i=1; $i<=10; $i++) $gr[] = $i;
shuffle($gr);

$img_path = "$g4[url]/skin/board/$board[bo_skin]/img/";

if ($mw_basic[cf_vote] && $vote && sizeof($vote_list)) {
?>
    <h3><img src="<?=$img_path?>/vote.png" align="absmiddle"> 설문조사 
        <span class="info">(<?
            if ($vote[vt_sdate] != "0000-00-00 00:00:00") echo substr($vote[vt_sdate], 0, 13)."시 시작, ";
            if ($vote[vt_edate] != "0000-00-00 00:00:00") echo substr($vote[vt_edate], 0, 13)."시 종료, ";
            if ($vote[vt_point]) echo number_format($vote[vt_point])." 포인트 지급, ";
            if ($vote[vt_multi]) echo $vote[vt_multi]."개까지 복수선택 가능, ";
            echo number_format($vt_total)."명 참여";
        ?>)
        <? if ($is_admin) { ?>
        [<a href="#;" onclick="window.open('<?=$board_skin_path?>/mw.proc/mw.vote.list.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>', 'vote_list', 'width=600,height=500,scrollbars=1');">참여목록</a>]
        <? } ?>
        <? if ($is_admin or ($write[mb_id] && $member[mb_id] && $write[mb_id] == $member[mb_id])) { ?>
        [<a href="#;" onclick="mw_vote_init()">초기화</a>]
        <? } ?>
        </span>
    </h3>

    <? if (!$is_vote && !$result_view) { ?>
        <div class="mw_vote_list">
            <? for ($i=0; $i<sizeof($vote_list); $i++) { ?>
            <div class="item">
                <? if (!$vote[vt_multi]) { ?>
                <input type="radio" id="vt_num_<?=$i?>" name="vt_num" value="<?=$i?>">
                <? } else { ?>
                <input type="checkbox" id="vt_num_<?=$i?>" name="vt_num" value="<?=$i?>">
                <? } ?>
                <label for="vt_num_<?=$i?>"><?=$vote_list[$i][vt_item]?></label>
            </div>
            <? } ?>
            <div class="btns">
                <input type="button" value="설문참여" class="btn" onclick="mw_vote_join()">
                <input type="button" value="결과보기" class="btn" onclick="mw_vote_result()">
            </div>
        </div>
    <? } else { ?>
        <div class="mw_vote_list">
            <div class="mw_vote_result">
            <? for ($i=0, $m=sizeof($vote_list); $i<$m; $i++) { ?>
            <div>
                <span class="item"><?=$vote_list[$i][vt_item]?> </span>
                <img src="<?=$img_path?>/vote_<?=$gr[abs($i%9)]?>.gif"
                     width="<?=$vote_list[$i][vt_width]?>" height="5" align="absmiddle"/>
                <span class="rate"> <nobr><?=$vote_list[$i][vt_rate]?></nobr> </span>
            </div>
            <? } ?>
            </div>
            <? if ($result_view) { ?>
            <div class="btns">
                <input type="button" value="설문참여" class="btn" onclick="mw_vote_load()">
            </div>
            <? } ?>
        </div>
    <? } ?>

<?
}

