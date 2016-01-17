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
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

if ($wr_id) return;
if (!$mw_basic[cf_hot]) return;

if ($mw_is_list and !strstr($mw_basic['cf_hot_print'], 'l')) return;
if ($mw_is_view and !strstr($mw_basic['cf_hot_print'], 'v')) return;
if ($mw_is_write and !strstr($mw_basic['cf_hot_print'], 'w')) return;

if (!$mw_basic[cf_hot_len]) $mw_basic[cf_hot_len] = 90;
if (!$mw_basic[cf_hot_limit]) $mw_basic[cf_hot_limit] = 10;
?>
<style type="text/css">
#mw_basic #mw_basic_hot_list li.hot_icon_1 { background:url(<?=$board_skin_path?>/img/icon_hot_1.gif) no-repeat left 2px; }
#mw_basic #mw_basic_hot_list li.hot_icon_2 { background:url(<?=$board_skin_path?>/img/icon_hot_2.gif) no-repeat left 2px; }
#mw_basic #mw_basic_hot_list li.hot_icon_3 { background:url(<?=$board_skin_path?>/img/icon_hot_3.gif) no-repeat left 2px; }
#mw_basic #mw_basic_hot_list li.hot_icon_4 { background:url(<?=$board_skin_path?>/img/icon_hot_4.gif) no-repeat left 2px; }
#mw_basic #mw_basic_hot_list li.hot_icon_5 { background:url(<?=$board_skin_path?>/img/icon_hot_5.gif) no-repeat left 2px; }
#mw_basic #mw_basic_hot_list li.hot_icon_6 { background:url(<?=$board_skin_path?>/img/icon_hot_6.gif) no-repeat left 2px; }
#mw_basic #mw_basic_hot_list li.hot_icon_7 { background:url(<?=$board_skin_path?>/img/icon_hot_7.gif) no-repeat left 2px; }
#mw_basic #mw_basic_hot_list li.hot_icon_8 { background:url(<?=$board_skin_path?>/img/icon_hot_8.gif) no-repeat left 2px; }
#mw_basic #mw_basic_hot_list li.hot_icon_9 { background:url(<?=$board_skin_path?>/img/icon_hot_9.gif) no-repeat left 2px; }
#mw_basic #mw_basic_hot_list li.hot_icon_10 { background:url(<?=$board_skin_path?>/img/icon_hot_10.gif) no-repeat left 2px; }
</style>
<?php
$hot_list = array();

$hot_cache_path = "{$g4['path']}/data/mw.basic.cache";
$hot_cache_file = "{$hot_cache_path}/list-hot-{$board['bo_table']}";
mw_mkdir($hot_cache_path, 0707);

$hot_list = mw_board_cache_read($hot_cache_file, 10);

switch ($mw_basic[cf_hot]) {
    case "1": $hot_start = ""; $hot_title = "실시간"; break;
    case "2": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24*7); $hot_title = "주간"; break;
    case "3": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24*30); $hot_title = "월간"; break;
    case "4": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24); $hot_title = "일간"; break;
    case "5": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24*365); $hot_title = "연간"; break;
    case "6": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24*30*3); $hot_title = "3개월"; break;
    case "7": $hot_start = date("Y-m-d H:i:s", $g4[server_time]-60*60*24*30*6); $hot_title = "6개월"; break;
}

if (!$hot_list) {
    $sql_between = 1;
    if ($mw_basic[cf_hot] > 1) {
        $sql_between = " wr_datetime between '$hot_start' and '$g4[time_ymdhis]' ";
    }
    $sql_except = "";
    $tmp = explode($notice_div, $board[bo_notice]);
    for ($i=0, $m=sizeof($tmp); $i<$m; $i++) { 
        if (!trim($tmp[$i])) continue;
        $bo_notice[] = trim($tmp[$i]);
    }
    if (count($bo_notice)>0)
        $sql_except = " and wr_id not in (".implode(",", $bo_notice).") ";


    if ($mw_basic[cf_hot_basis] == 'file') {
        $sql_between = str_replace("wr_datetime", "bf_datetime", $sql_between);
        $sql = " select wr_id, sum(CAST(bf_download AS SIGNED)) as down from $g4[board_file_table] where bo_table = '$bo_table' and $sql_between $sql_except ";
        $sql.= " group by bo_table, wr_id order by down desc limit $mw_basic[cf_hot_limit]";
        $qry = sql_query($sql);
        while ($row = sql_fetch_array($qry)) {
            $hot_list[] = sql_fetch("select wr_id, wr_subject, wr_link1, wr_link_write from $write_table where wr_id = '$row[wr_id]'");
        }
    } else {
        $sql = " select wr_id, wr_subject, wr_link1, wr_link_write from $write_table where wr_is_comment = 0 and $sql_between $sql_except ";
        $sql.= " order by wr_{$mw_basic[cf_hot_basis]} desc limit $mw_basic[cf_hot_limit]";
        $qry = sql_query($sql);
        while ($row = sql_fetch_array($qry)) {
            $hot_list[] = $row;
        }
    }
    mw_board_cache_write($hot_cache_file, $hot_list);
}

for ($i=0, $m=count($hot_list); $row=$hot_list[$i]; $i++)
{
    $row = get_list($row, $board, $board_skin_path, $mw_basic[cf_hot_len]);
    $row = mw_list_link($row);

    $row[subject] = mw_reg_str($row[subject]);
    $row[subject] = bc_code($row[subject], 0);

    $hot_list[$i] = $row;
}
?>
<div id=mw_basic_hot_list>
    <h3> <?=$hot_title?> 인기 게시물 </h3>
    <ul class=mw_basic_hot_dot>
    <?php
    for ($i=0, $m=count($hot_list); $row=$hot_list[$i]; $i++) {
        ?>
        <li class=hot_icon_<?=($i+1)?>> 
            <nobr><a href="<?=$row[href]?>"><?=$row[subject]?></a></nobr>
        </li>
        <?
        if (($i+1)%($mw_basic[cf_hot_limit]/2)==0) echo "</ul><ul>";
    }
    ?>
    </ul>
    <div class="hot_list_block"></div>
</div>
