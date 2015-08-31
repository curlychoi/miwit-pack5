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

$sql = "update $write_table set wr_option = '$wr_option' where wr_id = '$comment_id'";
sql_query($sql);

// 익명
if ($mw_basic[cf_anonymous]) {
    sql_query(" update $write_table set wr_anonymous = '$wr_anonymous' where wr_id = '$comment_id' ");
    if ($mw_basic[cf_anonymous_nopoint] && $wr_anonymous && $w == 'c') {
        delete_point($member[mb_id], $bo_table, $comment_id, '코멘트');
    }
}

// 짧은 글주소 사용
/*if ($mw_basic[cf_umz]) {
    $url = "$g4[url]/$g4[bbs]/board.php?bo_table=$bo_table&wr_id=$wr_id#c_$comment_id";
    $umz = umz_get_url($url);
    sql_query("update $write_table set wr_umz = '$umz' where wr_id = '$comment_id'");
}*/
// 모바일
if ($w == 'c') {
    if (mw_agent_mobile()) {
        sql_query("update $write_table set wr_is_mobile = '1' where wr_id = '$comment_id'", false);
    }
}

// 비회원 이름 쿠키 저장
if (!$is_member) {
    set_cookie("mw_cookie_name", $wr_name, -1*$g4[server_time]);
    set_cookie("mw_cookie_email", $wr_email, -1*$g4[server_time]);
    set_cookie("mw_cookie_homepage", $wr_homepage, -1*$g4[server_time]);
}

if ($w == 'c' && mw_is_rate($bo_table, $write['wr_id']) == '' && $wr_rate) {
    sql_query(" update {$write_table} set  wr_rate = '{$wr_rate}' where wr_id = '$comment_id' ", false);
    if ($mw_basic['cf_rate_point'])
        insert_point($member['mb_id'], $mw_basic['cf_rate_point'], "평가 참여 점수", $bo_table, $write['wr_id'], '@rate');

    mw_rate($bo_table, $write['wr_id']);
}


