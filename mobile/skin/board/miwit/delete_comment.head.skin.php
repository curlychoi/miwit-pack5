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

$mw_is_comment = true;

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

$write = sql_fetch(" select * from $write_table where wr_id = '$comment_id' ");

if ($mw_basic[cf_download_comment] && !$is_admin) {
    $sql = "select * from $mw[download_log_table] where bo_table = '$bo_table' and wr_id = '$write[wr_parent]' and mb_id = '$member[mb_id]'";
    $row = sql_fetch($sql);
    if ($row) {
        alert("첨부파일을 다운로드했기 때문에 코멘트를 삭제할 수 없습니다.");
    }
}

if (!$write[wr_id] || !$write[wr_is_comment])
    alert("등록된 코멘트가 없거나 코멘트 글이 아닙니다.");

if ($is_admin == "super") // 최고관리자 통과
    ;
else if ($is_admin == "group") { // 그룹관리자
    $mb = get_member($write[mb_id]);
    if ($member[mb_id] == $group[gr_admin]) { // 자신이 관리하는 그룹인가?
        if ($member[mb_level] >= $mb[mb_level]) // 자신의 레벨이 크거나 같다면 통과
            ;
        else
            alert("그룹관리자의 권한보다 높은 회원의 코멘트이므로 삭제할 수 없습니다.");
    } else
        alert("자신이 관리하는 그룹의 게시판이 아니므로 코멘트를 삭제할 수 없습니다.");
} else if ($is_admin == "board") { // 게시판관리자이면
    $mb = get_member($write[mb_id]);
    if ($member[mb_id] == $board[bo_admin]) { // 자신이 관리하는 게시판인가?
        if ($member[mb_level] >= $mb[mb_level]) // 자신의 레벨이 크거나 같다면 통과
            ;
        else
            alert("게시판관리자의 권한보다 높은 회원의 코멘트이므로 삭제할 수 없습니다.");
    } else
        alert("자신이 관리하는 게시판이 아니므로 코멘트를 삭제할 수 없습니다.");
} else if ($member[mb_id]) {
    if ($member[mb_id] != $write[mb_id])
        alert("자신의 글이 아니므로 삭제할 수 없습니다.");
} else {
    if (sql_password($wr_password) != $write[wr_password])
        alert("패스워드가 틀립니다.");
}

$len = strlen($write[wr_comment_reply]);
if ($len < 0) $len = 0;
$comment_reply = substr($write[wr_comment_reply], 0, $len);

$sql = " select count(*) as cnt from $write_table
          where wr_comment_reply like '$comment_reply%'
            and wr_id <> '$comment_id'
            and wr_parent = '$write[wr_parent]'
            and wr_comment = '$write[wr_comment]'
            and wr_is_comment = 1 ";
$row = sql_fetch($sql);
if ($row[cnt] && !$is_admin)
    alert("이 코멘트와 관련된 답변코멘트가 존재하므로 삭제 할 수 없습니다.");

mw_delete_row($board, $write);

// 사용자 코드 실행
@include_once("$board_skin_path/delete_comment.skin.php");
// 4.1
@include_once("$board_skin_path/delete_comment.tail.skin.php");

$url = mw_bbs_path("./board.php?bo_table=$bo_table&wr_id=$write[wr_parent]&cwin=$cwin&page=$page" . $qstr);
goto_url($url);
exit;

