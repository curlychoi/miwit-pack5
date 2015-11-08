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

if (!$is_admin)
    alert("로그를 열람할 권한이 없습니다.");

if (!trim($me_memo))
    alert("내용을 입력해주세요.");

$sql = "select mb_id from $mw[download_log_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and mb_id <> '' group by mb_id ";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry))
{
    if (!$row[mb_id]) continue;

    $tmp_row = sql_fetch(" select max(me_id) as max_me_id from $g4[memo_table] ");
    $me_id = $tmp_row[max_me_id] + 1;

    // 쪽지 INSERT
    $sql = " insert into $g4[memo_table]
                    ( me_id, me_recv_mb_id, me_send_mb_id, me_send_datetime, me_memo )
             values ( '$me_id', '{$row[mb_id]}', '$member[mb_id]', '$g4[time_ymdhis]', '$me_memo' ) ";
    sql_query($sql);

    // 실시간 쪽지 알림 기능
    $sql = " update $g4[member_table]
                set mb_memo_call = '$member[mb_id]'
              where mb_id = '$row[mb_id]' ";
    sql_query($sql);
}

include_once("$g4[path]/head.sub.php");
?>
<script type="text/javascript">
$(document).ready(function () {
    alert("쪽지를 보냈습니다.");
    location.href = "mw.download.log.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>";
});
</script>
<?
include_once("$g4[path]/tail.sub.php");

