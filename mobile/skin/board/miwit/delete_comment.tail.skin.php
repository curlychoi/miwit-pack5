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

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// 업로드된 파일이 있다면 파일삭제
$sql = " select * from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$comment_id' ";

$qry = sql_query($sql);
while ($row = sql_fetch_array($qry))
    @unlink("$g4[path]/data/file/$bo_table/$row[bf_file]");
    
// 파일테이블 행 삭제
sql_query(" delete from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$comment_id' ");

// 모아보기 삭제
if (function_exists('mw_moa_delete')) mw_moa_delete($comment_id);

?>
