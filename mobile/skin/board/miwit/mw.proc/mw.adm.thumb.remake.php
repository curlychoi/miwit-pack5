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

if ($is_admin != 'super')
    die("로그인 해주세요.");

if (!$bo_table)
    die("bo_table 값이 없습니다.");

if (!$token or get_session("ss_config_token") != $token) 
    die("토큰 에러로 실행 불가합니다.");

// 원본 이미지 크기 다시 계산
$sql = " select * from $g4[board_file_table] where bo_table = '$bo_table' and bf_width = 0 ";
$sql.= " and ( ";
$sql.= " right(lower(bf_source), 3) = 'jpg' ";
$sql.= " or right(lower(bf_source), 3) = 'gif' ";
$sql.= " or right(lower(bf_source), 3) = 'png') ";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry)) {
    $file = "$g4[path]/data/file/$bo_table/$row[bf_file]";
    $size = @getImageSize($file);
    sql_query(" update $g4[board_file_table] set bf_width = '$size[0]', bf_height = '$size[1]', bf_type = '$size[2]' where bo_table = '$bo_table' and wr_id = '$row[wr_id]' and bf_no = '$row[bf_no]' ");
}

// 전체 썸네일 삭제
$files = glob("{$thumb_path}/*"); array_map('unlink', $files);
$files = glob("{$thumb2_path}/*"); array_map('unlink', $files);
$files = glob("{$thumb3_path}/*"); array_map('unlink', $files);
$files = glob("{$thumb4_path}/*"); array_map('unlink', $files);
$files = glob("{$thumb5_path}/*"); array_map('unlink', $files);

$sql = "select wr_id, wr_content, wr_datetime, wr_link1, wr_link2 from $write_table where wr_is_comment = '0' order by wr_num";
$qry = sql_query($sql);
while ($write = sql_fetch_array($qry)) {
    $wr_id = $write[wr_id];
    $wr_content = $write[wr_content];

    $thumb_file = mw_thumb_jpg($thumb_path.'/'.$wr_id);
    $thumb2_file = mw_thumb_jpg($thumb2_path.'/'.$wr_id);
    $thumb3_file = mw_thumb_jpg($thumb3_path.'/'.$wr_id);
    $thumb4_file = mw_thumb_jpg($thumb4_path.'/'.$wr_id);
    $thumb5_file = mw_thumb_jpg($thumb5_path.'/'.$wr_id);

    $is_thumb = mw_make_thumbnail_row($bo_table, $wr_id, $wr_content, true);

    if (!$is_thumb) {
        if (preg_match("/youtu/i", $write['wr_link1']))
            mw_get_youtube_thumb($wr_id, $write['wr_link1'], $write['wr_datetime']);
        else if (preg_match("/youtu/i", $write['wr_link2']))
            mw_get_youtube_thumb($wr_id, $write['wr_link2'], $write['wr_datetime']);
        else if (preg_match("/vimeo/i", $write['wr_link1']))
            mw_get_vimeo_thumb($wr_id, $write['wr_link1'], $write['wr_datetime']);
        else if (preg_match("/vimeo/i", $write['wr_link2']))
            mw_get_vimeo_thumb($wr_id, $write['wr_link2'], $write['wr_datetime']);
        else {
            $pt = mw_youtube_pattern($write['wr_content']);
            if ($pt) {
                preg_match($pt, $write['wr_content'], $mat);
                mw_get_youtube_thumb($wr_id, $mat[1]);
            }
            else {
                $pt = mw_vimeo_pattern($write['wr_content']);
                if ($pt) {
                    preg_match($pt, $write['wr_content'], $mat);
                    mw_get_vimeo_thumb($wr_id, $mat[1]);
                }
            }
        }
    }
}

echo "썸네일을 모두 재생성하였습니다.";
