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

if ($member[mb_level] < 2)
{
    if ($member[mb_id])
        alert("목록을 볼 권한이 없습니다.", $g4[path]);
    else
        alert("목록을 볼 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.",
            $g4['bbs_path']."/login.php?wr_id=$wr_id{$qstr}&url=".urlencode("board.php?bo_table=$bo_table&wr_id=$wr_id"));
}
 
$sop = strtolower($sop);
if ($sop != "and" && $sop != "or")
    $sop = "and";

// 분류 선택 또는 검색어가 있다면
if ($sca || $stx) 
{
    $sql_search = get_sql_search($sca, $sfl, $stx, $sop);

    // 가장 작은 번호를 얻어서 변수에 저장 (하단의 페이징에서 사용)
    $sql = " select MIN(wr_num) as min_wr_num from $write_table ";
    $row = sql_fetch($sql);
    $min_spt = $row[min_wr_num];

    if (!$spt) $spt = $min_spt;

    $sql_search .= " and (wr_num between '".$spt."' and '".($spt + $config[cf_search_part])."') ";

    // 원글만 얻는다. (코멘트의 내용도 검색하기 위함)
    $sql = " select distinct wr_parent from $write_table where (mb_id = '$member[mb_id]' or wr_to_id = '$member[mb_id]') and $sql_search ";
    $result = sql_query($sql);
    $total_count = sql_num_rows($result);
} 
else 
{
    $sql_search = "";

    // 원글만 얻는다. (코멘트의 내용도 검색하기 위함)
    $sql = " select distinct wr_parent from $write_table where (mb_id = '$member[mb_id]' or wr_to_id = '$member[mb_id]')";
    $result = sql_query($sql);
    $total_count = sql_num_rows($result);
}

$total_page  = ceil($total_count / $board[bo_page_rows]);  // 전체 페이지 계산
if (!$page) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $board[bo_page_rows]; // 시작 열을 구함

// 관리자라면 CheckBox 보임
$is_checkbox = false;
if ($member[mb_id] && ($is_admin == "super" || $group[gr_admin] == $member[mb_id] || $board[bo_admin] == $member[mb_id])) 
    $is_checkbox = true;

// 정렬에 사용하는 QUERY_STRING
$qstr2 = "bo_table=$bo_table&sop=$sop";

if ($board[bo_gallery_cols]) 
    $td_width = (int)(100 / $board[bo_gallery_cols]);

// 정렬
// 인덱스 필드가 아니면 정렬에 사용하지 않음
//if (!$sst || ($sst && !(strstr($sst, 'wr_id') || strstr($sst, "wr_datetime")))) {
if (!$sst) 
{
    if ($board[bo_sort_field])
        $sst = $board[bo_sort_field];
    else
        $sst  = "wr_num, wr_reply";
    $sod = "";
}
$sql_order = " order by $sst $sod ";

if ($sca || $stx) 
{
    $sql = " select distinct wr_parent from $write_table where (mb_id = '$member[mb_id]' or wr_to_id = '$member[mb_id]') and $sql_search $sql_order limit $from_record, $board[bo_page_rows] ";
} 
else
{
    $sql = " select * from $write_table where wr_is_comment = 0 and (mb_id = '$member[mb_id]' or wr_to_id = '$member[mb_id]') $sql_order limit $from_record, $board[bo_page_rows] ";
}

$result = sql_query($sql);

// 년도 2자리
$today2 = $g4[time_ymd];

$list = array();
$i = 0;

if (!$sca && !$stx && trim($board[bo_notice])) 
{
    $arr_notice = @split($notice_div, trim($board[bo_notice]));
    for ($k=0; $k<count($arr_notice); $k++) 
    {
        if (trim($arr_notice[$k])=='') continue;

        $row = sql_fetch(" select * from $write_table where wr_id = '{$arr_notice[$k]}' ");

        if (!$row[wr_id]) continue;

        $list[$i] = get_list($row, $board, $board_skin_path, $board[bo_subject_len]);
        $list[$i][is_notice] = true;

        $i++;
    }
}

$k = 0;

while ($row = sql_fetch_array($result)) 
{
    // 검색일 경우 wr_id만 얻었으므로 다시 한행을 얻는다
    if ($sca || $stx)
        $row = sql_fetch(" select * from $write_table where wr_id = '$row[wr_parent]' ");

    $list[$i] = get_list($row, $board, $board_skin_path, $board[bo_subject_len]);
    if (strstr($sfl, "subject"))
        $list[$i][subject] = search_font($stx, $list[$i][subject]);
    $list[$i][is_notice] = false;
    //$list[$i][num] = number_format($total_count - ($page - 1) * $board[bo_page_rows] - $k);
    $list[$i][num] = $total_count - ($page - 1) * $board[bo_page_rows] - $k;

    $i++;
    $k++;
}

$write_pages = get_paging($config[cf_write_pages], $page, $total_page, $g4['bbs_path']."/board.php?bo_table=$bo_table".$qstr."&page=");
