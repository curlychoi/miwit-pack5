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

if (!$is_admin and !($mw_basic['cf_jump_level'] && $mw_basic['cf_jump_level'] <= $member['mb_level'])) {
    return;
}

if (!$is_admin and $write['mb_id'] != $member['mb_id']) {
    return;
}

if ($mw_basic['cf_jump_count'] and !$is_admin) {
    $jump_days = $mw_basic['cf_jump_days'] - 1;
    $old = date("Y-m-d 00:00:00", strtotime("-{$jump_days} day", $g4['server_time']));

    $sql = " select count(*) as cnt from {$mw['jump_log_table']} ";
    $sql.= "  where mb_id = '{$member['mb_id']}' ";
    $sql.= "    and jp_datetime > '$old' ";
    $row = sql_fetch($sql);
    if ($row['cnt'] >= $mw_basic['cf_jump_count']) {
        return;
    }
}

$msg = "현재글을 새글로 갱신하시겠습니까?";
if ($mw_basic['cf_jump_point']) {
    $msg .= "\\n\\n{$mw_basic['cf_jump_point']}포인트가 차감됩니다.";
}

if ($mw_basic['cf_jump_days'] or $mw_basic['cf_jump_count']) {
    $jump_days = $mw_basic['cf_jump_days'] - 1;
    $old = date("Y-m-d 00:00:00", strtotime("-{$jump_days} day", $g4['server_time']));

    if (!$mw_basic['cf_jump_days']) $old = "";

    $sql = " select count(*) as cnt from {$mw['jump_log_table']} ";
    $sql.= "  where mb_id = '{$member['mb_id']}' ";
    $sql.= "    and jp_datetime > '$old' ";
    $row = sql_fetch($sql);

    $count = $row['cnt'];

    $msg .= "\\n\\n({$mw_basic['cf_jump_days']}일에 {$mw_basic['cf_jump_count']}번 가능, 현재 {$count}번 사용)";
}
?>

<button class="fa-button" id="btn_jump">
    <i class="fa fa-paper-plane-o"></i>
    <span class="media-no-text">점프</span>
</button>

<script>
$(document).ready(function () {
    $("#btn_jump").click(function () {
        if (!confirm("<?php echo $msg?>")) {
            return;
        }
        if (!Date.now) {
            Date.now = function() { return new Date().getTime(); };
        }
        var t = Date.now() ;

        $.get("<?php echo $pc_skin_path?>/mw.proc/mw.jump.update.php", {
            "bo_table":"<?php echo $bo_table?>",
            "wr_id":"<?php echo $wr_id?>",
            "t":t
        },
        function (str) {
            if (str == "ok") {
                location.href = "<?php echo mw_seo_url($bo_table, $wr_id, "&page=1")?>";
            }
            else {
                alert(str);
            }
        });
    });
});
</script>

