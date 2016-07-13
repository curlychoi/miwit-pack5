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

//if (function_exists('mw_moa_insert') && !$wr_anonymous && $mw_basic[cf_attribute] != 'anonymous') {
if (function_exists('mw_moa_insert')) {
    mw_moa_insert($wr_id, $comment_id, $write[mb_id], $member[mb_id]);
}

if (function_exists('mw_lucky_writing') && $w == 'c') {
    mw_lucky_writing($bo_table, $comment_id);
}

if ($mw_basic[cf_comment_file]) {
//------------------------------------------------------------------------------
// 가변 파일 업로드
// 나중에 테이블에 저장하는 이유는 $comment_id 값을 저장해야 하기 때문입니다.

    $i = 0;

    $row = sql_fetch(" select count(*) as cnt from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$comment_id' and bf_no = '$i' ");
    if ($row[cnt]) 
    {
        // 삭제에 체크가 있거나 파일이 있다면 업데이트를 합니다.
        // 그렇지 않다면 내용만 업데이트 합니다.
        if ($upload[del_check] || $upload[file]) 
        {
            $sql = " update $mw[comment_file_table]
                        set bf_source = '{$upload[source]}',
                            bf_file = '{$upload[file]}',
                            bf_content = '{$bf_content}',
                            bf_filesize = '{$upload[filesize]}',
                            bf_width = '{$upload[image][0]}',
                            bf_height = '{$upload[image][1]}',
                            bf_type = '{$upload[image][2]}',
                            bf_datetime = '$g4[time_ymdhis]'
                      where bo_table = '$bo_table'
                        and wr_id = '$comment_id'
                        and bf_no = '$i' ";
            sql_query($sql);
        } 
        else 
        {
            $sql = " update $mw[comment_file_table]
                        set bf_content = '{$bf_content}' 
                      where bo_table = '$bo_table'
                        and wr_id = '$comment_id'
                        and bf_no = '$i' ";
            sql_query($sql);
        }
    } 
    else 
    {
        $sql = " insert into $mw[comment_file_table]
                    set bo_table = '$bo_table',
                        wr_id = '$comment_id',
                        bf_no = '$i',
                        bf_source = '{$upload[source]}',
                        bf_file = '{$upload[file]}',
                        bf_content = '{$bf_content}',
                        bf_download = 0,
                        bf_filesize = '{$upload[filesize]}',
                        bf_width = '{$upload[image][0]}',
                        bf_height = '{$upload[image][1]}',
                        bf_type = '{$upload[image][2]}',
                        bf_datetime = '$g4[time_ymdhis]' ";
        sql_query($sql);
    }

    // 업로드된 파일 내용에서 가장 큰 번호를 얻어 거꾸로 확인해 가면서
    // 파일 정보가 없다면 테이블의 내용을 삭제합니다.
    $row = sql_fetch(" select max(bf_no) as max_bf_no from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$comment_id' ");
    for ($i=(int)$row[max_bf_no]; $i>=0; $i--) 
    {
        $row2 = sql_fetch(" select bf_file from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$comment_id' and bf_no = '$i' ");

        // 정보가 있다면 빠집니다.
        if ($row2[bf_file]) break;

        // 그렇지 않다면 정보를 삭제합니다.
        sql_query(" delete from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$comment_id' and bf_no = '$i' ");
    }
//------------------------------------------------------------------------------
}

if ($file_upload_msg)
    alert($file_upload_msg, $g4['bbs_path']."/board.php?bo_table=$bo_table&wr_id=$wr[wr_parent]&page=$page" . $qstr . "&cwin=$cwin#c_{$comment_id}");

// 원본 강제 리사이징 (첨부파일)
if ($mw_basic[cf_resize_original]) {
    $sql = " select * from $mw[comment_file_table] ";
    $sql.= " where bo_table = '$bo_table' and wr_id = '$comment_id' and bf_width > 0  order by bf_no";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $file = "$file_path/$row[bf_file]";
        $size = getImageSize($file);
        //if ($size[0] > $mw_basic[cf_resize_original] || $mw_basic[cf_resize_original] < $size[1]) {
            mw_make_thumbnail($mw_basic[cf_resize_original], $mw_basic[cf_resize_original], $file, $file, true);
            $size = getImageSize($file);
        //} 
        sql_query("update $mw[comment_file_table] set bf_width = '$size[0]', bf_height = '$size[1]',
            bf_filesize = '".filesize($file)."'
            where bo_table = '$bo_table' and wr_id = '$comment_id' and bf_no = '$row[bf_no]'");
    }   
}

if ($mw_basic['cf_image_outline']) {
    mw_image_outline($dest_file, null, $mw_basic['cf_image_outline_color']);

    $editor_image = mw_get_editor_image($_POST['wr_content']);
    for ($j=0, $m=count($editor_image['local_path']); $j<$m; $j++) {
        mw_image_outline($editor_image['local_path'][$j], null, $mw_basic['cf_image_outline_color']);
    }
}

include_once($board_skin_path.'/mw.proc/naver_syndi.php');
