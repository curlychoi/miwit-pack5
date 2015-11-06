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

$mw_basic[cf_watermark_path] = "$g4[bbs_path]/$mw_basic[cf_watermark_path]";

// 기존 워터마크 삭제
if ($handle = opendir($watermark_path)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && $file != "index.php") {
            @unlink("$watermark_path/$file");
        }
    }
    closedir($handle);
}

$sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and bf_width > 0  order by wr_id, bf_no";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry))
{
    $file = "$file_path/$row[bf_file]";

    // 원본 강제 리사이징
    if ($mw_basic[cf_original_width] && $mw_basic[cf_original_height]) {
        mw_make_thumbnail($mw_basic[cf_original_width], $mw_basic[cf_original_height], $file, $file, true);
        $size = getImageSize($file);
        sql_query("update $g4[board_file_table] set bf_width = '$size[0]', bf_height = '$size[1]'
            where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$row[bf_no]'");
    }

    // 기존 워터마크 삭제
    //@unlink("$watermark_path/$row[bf_file]");

    // 워터마크 생성
    if ($mw_basic[cf_watermark_use] && file_exists($mw_basic[cf_watermark_path])) {
        mw_watermark_file($file);
    }

    // 워터마크 썸네일 재생성
    if ($mw_basic[cf_watermark_use_thumb] && file_exists($mw_basic[cf_watermark_path])) {
        mw_make_thumbnail($mw_basic[cf_thumb_width], $mw_basic[cf_thumb_height], $source_file,
            "{$thumb_path}/{$wr_id}", $mw_basic[cf_thumb_keep]);
        mw_make_thumbnail($mw_basic[cf_thumb2_width], $mw_basic[cf_thumb2_height], $source_file,
            "{$thumb2_path}/{$wr_id}", $mw_basic[cf_thumb2_keep]);
        mw_make_thumbnail($mw_basic[cf_thumb3_width], $mw_basic[cf_thumb3_height], $source_file,
            "{$thumb3_path}/{$wr_id}", $mw_basic[cf_thumb3_keep]);
        mw_make_thumbnail($mw_basic[cf_thumb4_width], $mw_basic[cf_thumb4_height], $source_file,
            "{$thumb4_path}/{$wr_id}", $mw_basic[cf_thumb4_keep]);
        mw_make_thumbnail($mw_basic[cf_thumb5_width], $mw_basic[cf_thumb5_height], $source_file,
            "{$thumb5_path}/{$wr_id}", $mw_basic[cf_thumb5_keep]);
    }
}

echo "워터마크를 모두 재생성하였습니다.";
