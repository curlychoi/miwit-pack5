<?php
/**
 * Bechu-Basic Skin for Gnuboard
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

include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");

$tmp_array = array();
if ($wr_id) // 건별삭제
    $tmp_array[0] = $wr_id;
else // 일괄삭제
    $tmp_array = $_POST['chk_wr_id'];

// 거꾸로 읽는 이유는 답변글부터 삭제가 되어야 하기 때문임
for ($i=count($tmp_array)-1; $i>=0; $i--) 
{
    $wr_id = $tmp_array[$i];
    $write = sql_fetch(" select * from {$write_table} where wr_id = '{$wr_id}' ");

    if ($is_admin == "super") // 최고관리자 통과
        ;
    else if ($is_admin == "group") // 그룹관리자
    {
        $mb = get_member($write['mb_id']);
        if ($member['mb_id'] == $group['gr_admin']) // 자신이 관리하는 그룹인가?
        {
            if ($member['mb_level'] >= $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
                ;
            else
                continue;
        } 
        else
            continue;
    } 
    else if ($is_admin == "board") // 게시판관리자이면
    {
        $mb = get_member($write['mb_id']);
        if ($member['mb_id'] == $board['bo_admin']) // 자신이 관리하는 게시판인가?
            if ($member['mb_level'] >= $mb['mb_level']) // 자신의 레벨이 크거나 같다면 통과
                ;
            else
                continue;
        else
            continue;
    } 
    else if ($member['mb_id'] && $member['mb_id'] == $write['mb_id']) // 자신의 글이라면
    {
        ;
    } 
    else if ($wr_password && !$write['mb_id'] && sql_password($wr_password) == $write['wr_password']) // 패스워드가 같다면
    {
        ;
    } 
    else
        continue;   // 나머지는 삭제 불가

    if ($opt == 'secret') {
        $sql = " update {$write_table} ";
        $sql.= "    set wr_option = if (wr_option is null, 'secret', concat(wr_option,',secret')) ";
        $sql.= "  where wr_id = '{$write['wr_id']}' ";
    }
    else {
        $sql = " update {$write_table} ";
        $sql.= "    set wr_option = replace(wr_option, 'secret', '') ";
        $sql.= "  where wr_id = '{$write['wr_id']}' ";
    }
    sql_query($sql);
}

goto_url(mw_seo_url($bo_table, 0, "&page=$page" . $qstr));
exit;
