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

include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");

if (is_g5()) {
    // 쿠키에 저장된 ID값과 넘어온 ID값을 비교하여 같지 않을 경우 오류 발생
    // 다른곳에서 링크 거는것을 방지하기 위한 코드
    if (!get_session("ss_view_{$bo_table}_{$wr_id}"))
        alert("잘못된 접근입니다.");

    $sql = " select bf_source, bf_file from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$no' ";
    $file = sql_fetch($sql);
    if (!$file[bf_file])
        alert_close("파일 정보가 존재하지 않습니다.");

    if ($member[mb_level] < $board[bo_download_level]) {
        $alert_msg = "다운로드 권한이 없습니다.";
        if ($member[mb_id])
            alert($alert_msg);
        else
            alert($alert_msg . "\\n\\n회원이시라면 로그인 후 이용해 보십시오.", "./login.php?wr_id=$wr_id&$qstr&url=".urlencode("$g4[bbs_path]/board.php?bo_table=$bo_table&wr_id=$wr_id"));
    }

    $filepath = "$g4[path]/data/file/$bo_table/$file[bf_file]";
    $filepath = addslashes($filepath);
    if (!is_file($filepath) || !file_exists($filepath))
        alert("파일이 존재하지 않습니다.");

    include_once($board_skin_path.'/download.skin.php');
    exit;
}

