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

mw_bomb();
mw_basic_move_cate($bo_table, $wr_id);

$mb = get_member($view[mb_id], 'mb_level');

// is_notice 그누보드 버그 보완
if (!is_g5())
    $view[is_notice] = preg_match("/(^|[\r\n]){$wr_id}($|[\r\n])/",$board[bo_notice]); 

// 별점평가
$rate = mw_rate($bo_table, $wr_id);
$rate_count = $rate['cnt'];
$write['wr_rate'] = $rate['rate'];

$move_flag = false;
// 자동이동
if (!$view[is_notice] and !$write[wr_auto_move] and $mw_basic[cf_auto_move]['use'] and $mw_basic[cf_auto_move]['bo_table']
    and (!$mw_basic[cf_auto_move]['day'] or $mw_basic[cf_auto_move]['day'] > ($g4[server_time]-strtotime($write[wr_datetime]))/(60*60*24)))
{
/*
    if (($mw_basic[cf_auto_move]['hit'] and $mw_basic[cf_auto_move]['hit'] <= $write[wr_hit])
     or ($mw_basic[cf_auto_move]['good'] and $mw_basic[cf_auto_move]['good'] <= $write[wr_good] && !$mw_basic[cf_auto_move]['sub'])
     or ($mw_basic[cf_auto_move]['nogood'] and $mw_basic[cf_auto_move]['nogood'] <= $write[wr_nogood] && !$mw_basic[cf_auto_move]['sub'])
     or ($mw_basic[cf_auto_move]['sub'] and $mw_basic[cf_auto_move]['good'] <= ($write[wr_good]-$write[wr_nogood]))
     or ($mw_basic[cf_auto_move]['singo'] and $mw_basic[cf_auto_move]['singo'] <= $write[wr_singo])
     or ($mw_basic[cf_auto_move]['comment'] and $mw_basic[cf_auto_move]['comment'] <= $write[wr_comment]))
*/
    if ($mw_basic[cf_auto_move]['sub']) {
        if (    ($mw_basic[cf_auto_move]['hit'] <= $write[wr_hit])
            and ($mw_basic[cf_auto_move]['good'] <= ($write[wr_good]-$write[wr_nogood]))
            and ($mw_basic[cf_auto_move]['singo'] <= $write[wr_singo])
            and ($mw_basic[cf_auto_move]['rate'] <= $write[wr_rate])
            and ($mw_basic[cf_auto_move]['comment'] <= $write[wr_comment]))
        {
            $move_flag = true;
        }
    }
    else {
        if (    ($mw_basic[cf_auto_move]['hit'] <= $write[wr_hit])
            and ($mw_basic[cf_auto_move]['good'] <= $write[wr_good])
            and ($mw_basic[cf_auto_move]['nogood'] <= $write[wr_nogood])
            and ($mw_basic[cf_auto_move]['singo'] <= $write[wr_singo])
            and ($mw_basic[cf_auto_move]['rate'] <= $write[wr_rate])
            and ($mw_basic[cf_auto_move]['comment'] <= $write[wr_comment]))
        {
            $move_flag = true;
        }
    }
}
if ($move_flag) {
    sql_query("update $write_table set wr_auto_move = '1' where wr_id = '$wr_id' ", false);
    mw_move($board, $wr_id, $mw_basic[cf_auto_move]['bo_table'], $mw_basic[cf_auto_move]['use']);
}

// 실명인증 & 성인인증
if (($mw_basic[cf_kcb_read] || $write[wr_kcb_use]) && !is_okname()) {
    check_okname();
    return;
}

$mw_membership = array();
$mw_membership_icon = array();

// 링크로그
for ($i=1; $i<=$g4['link_count']; $i++)
{
    //if ($mw_basic[cf_link_log])  {
        $view['link'][$i] = set_http(get_text($view["wr_link{$i}"]));
        $view['link_href'][$i] = "$board_skin_path/link.php?bo_table=$board[bo_table]&wr_id=$view[wr_id]&no=$i" . $qstr;
        $view['link_hit'][$i] = (int)$view["wr_link{$i}_hit"];
    //}
    $view['link_target'][$i] = $view["wr_link{$i}_target"];
    if (!$view['link_target'][$i])
        $view['link_target'][$i] = '_blank';
}

// 링크게시판
if ($mw_basic[cf_link_board] && !$is_admin && $view[mb_id] != $member[mb_id] && $view[link][1]) {
    //goto_url("board.php?bo_table=$bo_table$qstr");
    goto_url($view['link_href'][1]);
}
// 게시물별 링크
elseif ($write[wr_link_write] && !$is_admin && ($view[mb_id] != $member[mb_id] or !$view[mb_id]) && $view[link][1]) {
    if ($mw_basic[cf_read_level] && $list[$i][wr_read_level]) {
        if ($list[$i][wr_read_level] <= $member[mb_level])
            goto_url($view['link_href'][1]);
        else
            alert("글을 읽을 권한이 없습니다. ", "board.php?bo_table=$bo_table$qstr");
    }
    else if ($member[mb_level] >= $board[bo_read_level])
        goto_url($view['link_href'][1]);
    else
        alert("글을 읽을 권한이 없습니다. ", "board.php?bo_table=$bo_table$qstr");
}

if (!$is_admin and $mw_basic['cf_link_level'] and $mw_basic['cf_link_level'] > $member['mb_level']) {
    for ($i=1; $i<=$g4['link_count']; $i++) {
        $view['link'][$i] = '';
        $view['link_href'][$i] = '';
    }
}

if ($mw_basic['cf_link_point'] && $is_member) {
    for ($i=1; $i<=$g4['link_count']; $i++) {
        if (!$view['link_href'][$i]) continue;
        $view['link_href'][$i] = "#;\" onclick=\"move_link(this, '{$mw_basic['cf_link_point']}', '{$view['link_href'][$i]}', '{$view['link_target'][$i]}')";
    }
}

if ($mw_basic[cf_read_level] && $write[wr_read_level] && $write[wr_read_level] > $member[mb_level]) {
    alert("글을 읽을 권한이 없습니다.");
}

// 글읽을 조건 
if ($mw_basic[cf_read_point] && !$is_admin) {
    if ($member[mb_point] < $mw_basic[cf_read_point]) {
        alert("이 게시판은 $mw_basic[cf_read_point] 포인트 이상 소지자만 글읽기가 가능합니다.");
    }
}
if ($mw_basic[cf_read_register] && !$is_admin) {
    $gap = ($g4[server_time] - strtotime($member[mb_datetime])) / (60*60*24);
    if ($gap < $mw_basic[cf_read_register]) {
        alert("이 게시판은 가입후 $mw_basic[cf_read_register] 일이 지나야 글읽기가 가능합니다.");
    }
}

if ($board[bo_read_point] < 0 && $view[mb_id] != $member[mb_id] && !$point && $is_member && !$is_admin && $mw_basic[cf_read_point_message]) {
    $tmp = sql_fetch(" select * from $g4[point_table] where mb_id = '$member[mb_id]' and po_rel_table = '$bo_table' and po_rel_id = '{$view[wr_id]}' and po_rel_action = '읽기' and po_datetime = '$g4[time_ymdhis]'");
    if ($tmp) {
        delete_point($member[mb_id], $bo_table, $view[wr_id], '읽기');
        set_session("ss_view_{$bo_table}_{$wr_id}", '');
        unset($_SESSION["ss_view_{$bo_table}_{$wr_id}"]);

        $sign = '&';
        if ($mw['config']['cf_seo_url']) {
            $url = mw_seo_url($bo_table, $wr_id, $qstr);
            $sign = '?';
        }

        echo <<<HEREDOC
        <script>
        if (confirm("글을 읽으시면 $board[bo_read_point] 포인트 차감됩니다.\\n\\n현재포인트 : {$member['mb_point']}p\\n\\n"))
            location.href = '{$_SERVER['REQUEST_URI']}{$sign}point=1';
        else
            history.back();
        </script>
HEREDOC;
        include_once($g4['path']."/tail.sub.php");
        exit;
    }
} 

if (!$is_admin && $write[wr_view_block])
    alert("이 게시물 보기는 차단되었습니다. 관리자만 접근 가능합니다.");

// 호칭
$view[name] = get_name_title($view[name], $view[wr_name]);
$view[name] = mw_sideview($view[name]);

// 멤버쉽 아이콘
if (function_exists("mw_cash_membership_icon") && $view[mb_id] != $config[cf_admin])
{
    if (!in_array($view[mb_id], $mw_membership)) {
        $mw_membership[] = $view[mb_id];
        $mw_membership_icon[$view[mb_id]] = mw_cash_membership_icon($view[mb_id]);
        $view[name] = $mw_membership_icon[$view[mb_id]].$view[name];
    } else {
        $view[name] = $mw_membership_icon[$view[mb_id]].$view[name];
    }
}

if ($view[wr_anonymous] || $mw_basic[cf_attribute] == 'anonymous') {
    $view[name] = mw_anonymous_nick($write[mb_id], $write[wr_ip]);
    $view[wr_name] = $view[name];
    $mw_basic[cf_latest] = false;
}

if (($mw_basic[cf_must_notice] || $mw_basic[cf_must_notice_read] || $mw_basic[cf_must_notice_comment]) && $view[is_notice]) // 공지 읽기 필수
{
    if ($member[mb_id]) {
        sql_query("insert into $mw[must_notice_table] set bo_table = '$bo_table', wr_id = '$wr_id', mb_id = '$member[mb_id]', mu_datetime = '$g4[time_ymdhis]'", false);
    }
}
else
{
    if ($mw_basic[cf_must_notice_read]) {
        //$tmp_notice = str_replace($notice_div, ",", trim($board[bo_notice]));
        $tmp_notice = implode(",", array_filter(explode($notice_div, trim($board[bo_notice])), "strlen"));
        $cnt_notice = sizeof(explode(",", $tmp_notice));

        if ($tmp_notice) {
            $sql = "select count(*) as cnt from $mw[must_notice_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and wr_id in ($tmp_notice)";
            $row = sql_fetch($sql);
            if ($row[cnt] != $cnt_notice)
                alert("$board[bo_subject] 공지를 모두 읽으셔야 글읽기가 가능합니다.", "$g4[bbs_path]/board.php?bo_table=$bo_table");
        }
    }
}

include($board_skin_path.'/mw.proc/mw.file.viewer.php');

if ($write[wr_singo] && $write[wr_singo] >= $mw_basic[cf_singo_number] && $mw_basic[cf_singo_write_block]) {
    $content = " <div class='singo_info'> 신고가 접수된 게시물입니다. (신고수 : $write[wr_singo]회)<br/>";
    $content.= " <span onclick=\"btn_singo_view({$view[wr_id]})\" class='btn_singo_block'>여기</span>를 클릭하시면 내용을 볼 수 있습니다.";
    if ($is_admin == "super")
        $content.= " <span class='btn_singo_block' onclick=\"btn_singo_clear({$view[wr_id]})\">[신고 초기화]</span> ";
    $content.= " </div>";
    $content.= " <div id='singo_block_{$view[wr_id]}' class='singo_block'> {$view[content]} </div>";

    $view[wr_subject] = "신고가 접수된 게시물입니다.";
    $view[subject] = $view[wr_subject];
    $view[rich_content] = $content;
}

if ($mw_basic[cf_include_view_top] && is_mw_file($mw_basic[cf_include_view_top])) {
    include($mw_basic[cf_include_view_top]);
}

// 컨텐츠샵 멤버쉽
if (function_exists("mw_cash_is_membership") && $member[mb_id] != $write[mb_id]) {
    $is_membership = @mw_cash_is_membership($member[mb_id], $bo_table, "mp_view");
    if ($is_membership == "no")
        ;
    else if ($is_membership != "ok")
        mw_cash_alert_membership($is_membership);
        //alert("$is_membership 회원만 이용 가능합니다.");
}

// 관리자라면 CheckBox 보임
$is_checkbox = false;
if ($member[mb_id] && ($is_admin == "super" || $group[gr_admin] == $member[mb_id] || $board[bo_admin] == $member[mb_id])) 
    $is_checkbox = true;

$prev_wr_subject = str_replace("\"", "'", $prev_wr_subject);
$next_wr_subject = str_replace("\"", "'", $next_wr_subject);

$nosecret_href = '';
$secret_href = '';
if ($is_admin && strstr($write[wr_option], "secret")) {
    // 잠금 해제 버튼
    $nosecret_href = "btn_nosecret();";
} else if ($is_admin) {
    // 잠금 버튼
    $secret_href = "btn_secret();";
}

// 파일로그
$download_log_href = '';
if ($mw_basic[cf_download_log] && $is_admin) {
    $download_log_href = "btn_download_log()";
}

// 링크로그
$link_log_href = '';
if ($mw_basic[cf_link_log] && $is_admin) {
    $link_log_href = "btn_link_log()";
}

// 로그버튼
$history_href = '';
if ($mw_basic[cf_post_history] && $mw_basic[cf_post_history_level] && $member[mb_level] >= $mw_basic[cf_post_history_level]) {
    $history_href = "btn_history($wr_id)";
}

$is_singo_admin = mw_singo_admin($member[mb_id]);

// 신고 버튼
$singo_href = '';
if ($mw_basic[cf_singo]) {
    $singo_href = "javascript:btn_singo($wr_id, $wr_id)";
}

// 인쇄 버튼
$print_href = '';
if ($mw_basic[cf_print]) {
    $print_href = "javascript:btn_print()";
}

// 글쓰기 버튼에 분류저장
if ($sca && $write_href)
    $write_href .= "&sca=".urlencode($sca);

// 글쓰기 버튼 공지
if ($write_href && $mw_basic[cf_write_notice]) {
    $write_href = "javascript:btn_write_notice('$write_href');";
}

// 조회수, 추천수, 비추천수 컴마
if ($mw_basic[cf_comma]) {
    $view[wr_hit] = number_format($view[wr_hit]);
    $view[wr_good] = number_format($view[wr_good]);
    $view[wr_nogood] = number_format($view[wr_nogood]);
}

// 컨텐츠샵
$mw_price = "";
if ($mw_basic['cf_contents_shop']) {
    if (!$view['wr_contents_price'])
	$mw_price = "무료";
    else
	$mw_price = $mw_cash[cf_cash_name] . " " . number_format($view[wr_contents_price]).$mw_cash[cf_cash_unit];
}

if ($mw_basic[cf_attribute] == "1:1" && !$is_admin) {
    $prev_href = '';
    $next_href = '';
    $prev_wr_subject = '';
    $next_wr_subject = '';
}

// 전체목록보이기 사용 에서도 이전글, 다음글 버튼 출력
if ($mw_basic[cf_attribute] != "1:1" && (!$prev_href || !$next_href))
{
   if ($sql_search) {
        if (trim(substr($sql_search, 0, 4)) != "and") {
            $sql_search = " and " . $sql_search;
        }
    }

    // 윗글을 얻음
    $sql = " select wr_id, wr_subject from $write_table where wr_is_comment = 0 and wr_num = '$write[wr_num]' and wr_reply < '$write[wr_reply]' $sql_search order by wr_num desc, wr_reply desc limit 1 ";
    $prev = sql_fetch($sql);
    // 위의 쿼리문으로 값을 얻지 못했다면
    if (!$prev[wr_id])     {
        $sql = " select wr_id, wr_subject from $write_table where wr_is_comment = 0 and wr_num < '$write[wr_num]' $sql_search order by wr_num desc, wr_reply desc limit 1 ";
        $prev = sql_fetch($sql);
    }

    // 아래글을 얻음
    $sql = " select wr_id, wr_subject from $write_table where wr_is_comment = 0 and wr_num = '$write[wr_num]' and wr_reply > '$write[wr_reply]' $sql_search order by wr_num, wr_reply limit 1 ";
    $next = sql_fetch($sql);
    // 위의 쿼리문으로 값을 얻지 못했다면
    if (!$next[wr_id]) {
        $sql = " select wr_id, wr_subject from $write_table where wr_is_comment = 0 and wr_num > '$write[wr_num]' $sql_search order by wr_num, wr_reply limit 1 ";
        $next = sql_fetch($sql);
    }

    // 이전글 링크
    $prev_href = "";
    if ($prev[wr_id]) {
        $prev_wr_subject = get_text(cut_str($prev[wr_subject], 255));
        $prev_href = mw_seo_url($bo_table, $prev[wr_id], $qstr);
    }

    // 다음글 링크
    $next_href = "";
    if ($next[wr_id]) {
        $next_wr_subject = get_text(cut_str($next[wr_subject], 255));
        $next_href = mw_seo_url($bo_table, $next[wr_id], $qstr);
    }
}

if ($prev_href) {
    $tmp = sql_fetch(" select wr_singo, wr_view_block from {$write_table} where wr_id = '{$prev['wr_id']}' ");
    if ($tmp['wr_singo'] && $tmp['wr_singo'] >= $mw_basic['cf_singo_number'] && $mw_basic['cf_singo_write_block']) {
        $prev_wr_subject = "신고가 접수된 게시물입니다.";
    }
    if ($tmp['wr_view_block'])
        $next_wr_subject = "보기가 차단된 게시물입니다.";
}

if ($next_href) {
    $tmp = sql_fetch(" select wr_singo, wr_view_block from {$write_table} where wr_id = '{$next['wr_id']}' ");
    if ($tmp['wr_singo'] && $tmp['wr_singo'] >= $mw_basic['cf_singo_number'] && $mw_basic['cf_singo_write_block']) {
        $next_wr_subject = "신고가 접수된 게시물입니다.";
    }
    if ($tmp['wr_view_block'])
        $next_wr_subject = "보기가 차단된 게시물입니다.";
}

//$view[rich_content] = preg_replace_callback("/\[code\](.*)\[\/code\]/iUs", "_preg_callback", $view[rich_content]);

// 리워드
if ($mw_basic[cf_reward]) {
    $reward = sql_fetch("select * from $mw[reward_table] where bo_table = '$bo_table' and wr_id = '$wr_id'");
    if ($reward[re_edate] != "0000-00-00" && $reward[re_edate] < $g4[time_ymd]) { // 날짜 지나면 종료
        sql_query("update $mw[reward_table] set re_status = '' where bo_table = '$bo_table' and wr_id = '$wr_id'");
        $reward[re_status] = '';
    }
    else
        //$reward[url] = mw_get_reward_url($reward);
        $reward[url] = "$g4[path]/plugin/reward/go.php?bo_table=$bo_table&wr_id=$wr_id";

    if ($is_member)
        $reward[script] = "window.open('$reward[url]');";
    else
        $reward[script] = "alert('로그인 후 이용해주세요.');";
}

// 분류 사용 여부
$is_category = false;
if ($board[bo_use_category]) 
{
    $is_category = true;
    $category_location = mw_seo_url($bo_table, 0, "&sca=");
    $category_option = mw_get_category_option($bo_table); // SELECT OPTION 태그로 넘겨받음

    if ($mw_basic[cf_default_category] && !$sca) $sca = $mw_basic[cf_default_category];
}

// 분류 선택 또는 검색어가 있다면
if (!$total_count && ($sca || $stx))
{
    $sql_search = get_sql_search($sca, $sfl, $stx, $sop);

    // 가장 작은 번호를 얻어서 변수에 저장 (하단의 페이징에서 사용)
    $sql = " select MIN(wr_num) as min_wr_num from $write_table ";
    $row = sql_fetch($sql);
    $min_spt = $row[min_wr_num];

    if (!$spt) $spt = $min_spt;

    $sql_search .= " and (wr_num between '".$spt."' and '".($spt + $config[cf_search_part])."') ";

    // 원글만 얻는다. (코멘트의 내용도 검색하기 위함)
    $sql = " select distinct wr_parent from $write_table where $sql_search ";
    $result = sql_query($sql);
    $total_count = sql_num_rows($result);
} 
else 
{
    $sql_search = "";

    $total_count = $board[bo_count_write];
}

// 자동치환
//$view[rich_content] = mw_reg_str($view[rich_content]);
$view[wr_subject] = mw_reg_str($view[wr_subject]);
$view[wr_subject] = bc_code($view[wr_subject], 0, 0);

$prev_wr_subject = bc_code($prev_wr_subject, 0, 0);
$prev_wr_subject = mw_reg_str($prev_wr_subject);

$next_wr_subject = bc_code($next_wr_subject, 0, 0);
$next_wr_subject = mw_reg_str($next_wr_subject);

// IP보이기 사용 여부
$ip = "";
$is_ip_view = $board[bo_use_ip_view];
if ($is_admin) {
    $is_ip_view = true;
    $ip = $write[wr_ip];
} else if ($mw_basic[cf_attribute] == 'anonymous') {
    $ip = "";
} else if ($view[wr_anonymous]) {
    $ip = "";
} else if ($view[mb_id] == $config[cf_admin]) {
    $ip = "";
} else // 관리자가 아니라면 IP 주소를 감춘후 보여줍니다.
    $ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", "\\1.♡.\\3.\\4", $write[wr_ip]);

$shorten = set_http("{$g4[url]}/{$g4[bbs]}/board.php?bo_table={$bo_table}&wr_id={$wr_id}");

if ($mw_basic[cf_shorten])
    $shorten = "$g4[url]/$bo_table/$wr_id";

if ($mw['config']['cf_seo_url'])
    $shorten = mw_seo_url($bo_table, $wr_id);

$new_time = date("Y-m-d H:i:s", $g4[server_time] - ($board[bo_new] * 3600));
$row = sql_fetch(" select count(*) as cnt from $write_table where wr_is_comment = 0 and wr_datetime >= '$new_time' ");
$new_count = $row[cnt];

// 최고, 그룹관리자라면 글 복사, 이동 가능
$copy_href = $move_href = "";
if ($write[wr_reply] == "" && ($is_admin == "super" || $is_admin == "group")) {
    $copy_href = "javascript:window.open('$board_skin_path/move.php?sw=copy&bo_table=$bo_table&wr_id=$wr_id&page=$page".$qstr."', 'boardcopy', 'left=50, top=50, width=500, height=550, scrollbars=1');";
    $move_href = "javascript:window.open('$board_skin_path/move.php?sw=move&bo_table=$bo_table&wr_id=$wr_id&page=$page".$qstr."', 'boardmove', 'left=50, top=50, width=500, height=550, scrollbars=1');";
}

if ($mw_basic[cf_umz]) { // 짧은 글주소 사용 
    //if ($write[wr_umz] == "") {
    if ($mw_basic[cf_umz2]) {
        if ($mw_basic['cf_umz2'] == 'my' && substr(trim($write[wr_umz]), 0, strlen($mw_basic[cf_umz_domain])+7) != "http://$mw_basic[cf_umz_domain]") {
            //$url = "$g4[url]/$g4[bbs]/board.php?bo_table=$bo_table&wr_id=$wr_id";
            $url = mw_seo_url($bo_table, $wr_id);
            $umz = umz_get_url($url);
            sql_query("update $write_table set wr_umz = '$umz' where wr_id = '$wr_id'");
            $view[wr_umz] = $umz;
        }
        else if (substr(trim($write[wr_umz]), 0, strlen($mw_basic[cf_umz2])+7) != "http://$mw_basic[cf_umz2]") {
            //$url = "$g4[url]/$g4[bbs]/board.php?bo_table=$bo_table&wr_id=$wr_id";

            $url = mw_seo_url($bo_table, $wr_id);
            $umz = umz_get_url($url);
            sql_query("update $write_table set wr_umz = '$umz' where wr_id = '$wr_id'");
            $view[wr_umz] = $umz;
        }
    }
    else {
        if (substr(trim($write[wr_umz]), 0, 10) != "http://umz") {
            //$url = "$g4[url]/$g4[bbs]/board.php?bo_table=$bo_table&wr_id=$wr_id";
            $url = mw_seo_url($bo_table, $wr_id);
            $umz = umz_get_url($url);
            sql_query("update $write_table set wr_umz = '$umz' where wr_id = '$wr_id'");
            $view[wr_umz] = $umz;
        }
    }
}

$view_sns = null;

if ($mw_basic[cf_sns])
{
    $view_url = mw_seo_url($bo_table, $wr_id);
    //$view_url = "$g4[url]/$g4[bbs]/board.php?bo_table=$bo_table&wr_id=$wr_id";

    /*if ($mw_basic[cf_umz] && $view[wr_umz]) $sns_url = $view[wr_umz];
    else if ($mw_basic[cf_shorten]) $sns_url = $shorten;
    else if ($trackback_url) $sns_url = $trackback_url;
    else $sns_url = $view_url;*/
    $sns_url = $view_url;

    $sns_url = trim($sns_url);

    $me2day_url = '';//"http://me2day.net/posts/new?new_post[body]=".urlencode(set_utf8($view[wr_subject])." - \"$sns_url\":$sns_url");
    //$twitter_url = "http://twitter.com/home?status=".urlencode(set_utf8($view[wr_subject])." - $sns_url");
    //$twitter_url = "http://twitter.com/?status=".str_replace("+", " ", urlencode(set_utf8($view[wr_subject])." - $sns_url"));
    $twitter_url = "https://twitter.com/intent/tweet?source=webclient&text=".str_replace("+", " ", urlencode(set_utf8($view[wr_subject])." - $sns_url"));
    $facebook_url = "http://www.facebook.com/share.php?u=".urlencode($view_url);
    $yozm_url = ''; //"http://yozm.daum.net/api/popup/prePost?sourceid=41&link={$sns_url}&prefix=".urlencode(set_utf8($view[wr_subject]));
    $cy_url = "javascript:window.open('http://csp.cyworld.com/bi/bi_recommend_pop.php?url={$sns_url}', ";
    $cy_url.= "'recom_icon_pop', 'width=400,height=364,scrollbars=no,resizable=no');";
    $naver_url = "http://bookmark.naver.com/post?ns=1&title=".urlencode(set_utf8($view[wr_subject]))."&url=".urlencode($sns_url);
    $google_plus_url = "https://plus.google.com/share?url=".$sns_url;
    $google_url = "http://www.google.com//bookmarks/mark?op=add&title=".urlencode(set_utf8($view[wr_subject]))."&bkmk={$sns_url}";
    $kakao_url = "kakaolink://sendurl?msg=".urlencode(set_utf8($view[wr_subject]))."&appver=1&appid={$_SERVER[HTTP_HOST]}&url=".urlencode($sns_url);
    $kakaostory_url = "storylink://posting?post=".urlencode(set_utf8($view[wr_subject])).urlencode("\n".$sns_url)."&apiver=1.0&appname=".urlencode($config[cf_title])."&appver=1&appid={$_SERVER[HTTP_HOST]}";

    $facebook_like_href = urlencode($view_url);

    /*
    $line_url = "http://line.me/R/msg/text/?".urlencode(set_utf8($view[wr_subject]))."%0D%0A".$sns_url;

    if (!strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile")) {
        $line_url = "#;\" onclick=\"alert('모바일 기기에서만 작동합니다.')\"";
    }
    */

    ob_start();
    ?>
    <!--<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>-->
    <? if (strstr($mw_basic[cf_sns], '/me2day/')) { ?>
    <div><a href="<?=$me2day_url?>" target="_blank" title="이 글을 미투데이로 보내기"><img 
        src="<?=$board_skin_path?>/img/send_me2day.png" border="0"></a></div>
    <? } ?>
    <? if (strstr($mw_basic[cf_sns], '/twitter/')) { ?>
    <div><a href="<?=$twitter_url?>" target="_blank" title="이 글을 트위터로 보내기"><img
        src="<?=$board_skin_path?>/img/send_twitter.png" border="0"></a></div>
    <? } ?>
    <? if (strstr($mw_basic[cf_sns], '/facebook/')) { ?>
    <div><a href="<?=$facebook_url?>" target="_blank" title="이 글을 페이스북으로 보내기"><img
        src="<?=$board_skin_path?>/img/send_facebook.png" border="0"></a></div>
    <? } ?>

    <? if (strstr($mw_basic[cf_sns], '/google_plus/')) { ?>
    <div><a href="<?=$google_plus_url?>" target="_blank" title="이 글을 구글플러스로 보내기"><img
        src="<?=$board_skin_path?>/img/send_google_plus.png" border="0"></a></div>
    <? } ?>

    <? if (strstr($mw_basic[cf_sns], '/yozm/') && $yozm_url) { ?>
    <div><a href="<?=$yozm_url?>" target="_blank" title="이 글을 요즘으로 보내기"><img
        src="<?=$board_skin_path?>/img/send_yozm.png" border="0"></a></div>
    <? } ?>
    <? if (strstr($mw_basic[cf_sns], '/cyworld/')) { ?>
    <div><img src="<?=$board_skin_path?>/img/send_cy.png" border="0" onclick="<?=$cy_url?>" style="cursor:pointer" title="싸이월드 공감"></div>
    <? } ?>
    <? if (strstr($mw_basic[cf_sns], '/naver/')) { ?>
    <div><a href="<?=$naver_url?>" target="_blank" title="이 글을 네이버 북마크로 보내기"><img
        src="<?=$board_skin_path?>/img/send_naver.png" border="0"></a></div>
    <? } ?>
    <? if (strstr($mw_basic[cf_sns], '/google/')) { ?>
    <div><a href="<?=$google_url?>" target="_blank" title="이 글을 구글 북마크로 보내기"><img
        src="<?=$board_skin_path?>/img/send_google.png" border="0"></a></div>
    <? } ?>
    <? if (strstr(strtolower($_SERVER[HTTP_USER_AGENT]), "mobile") or $is_admin) { ?>
        <?
        $kakao_name = mw_kakao_str($config[cf_title], 50);
        $kakao_subject = mw_kakao_str($view[wr_subject], 50);
        $kakao_content = mw_kakao_str($view[wr_content], 50);
        $kakao_thumb_path = $g4['path']."/data/file/{$bo_table}/thumbnail/".$wr_id;
        $kakao_thumb_url = $g4['url']."/data/file/{$bo_table}/thumbnail/".$wr_id;
        if ($mw_basic['cf_thumb_jpg']) {
            $kakao_thumb_path .= ".jpg";
            $kakao_thumb_url .= ".jpg";
        }

        if ($mw_basic['cf_thumb_width'] < 70 or $mw_basic['cf_thumb_width'] < 70) {
            for ($i=2, $m=5; $i<$m; ++$i) {
                if ($mw_basic['cf_thumb'.$i.'_width'] >= 70 and $mw_basic['cf_thumb'.$i.'_height'] >= 70) {
                    $kakao_thumb_path = str_replace("/thumbnail/", "/thumbnail{$i}/", $kakao_thumb_path);
                    $kakao_thumb_url = str_replace("/thumbnail/", "/thumbnail{$i}/", $kakao_thumb_url);
                    break;
                }
            }
        }

        if (!is_mw_file($kakao_thumb_path))
            $kakao_thumb_url = '';
        else
            $kakao_thumb_size = @getImageSize($kakao_thumb_path);

        if ($kakao_thumb_size[0] < 70 or $kakao_thumb_size[1] < 70) {
            $kakao_thumb_path = '';
            $kakao_thumb_url = '';
        }

        if (!strstr(strtolower($_SERVER[HTTP_USER_AGENT]), "mobile"))
            $kakao_url = "#;\" onclick=\"javascript:alert('모바일 기기에서만 작동합니다.');";

        if (strstr($mw_basic[cf_sns], '/kakao/')) { ?>
        <div><a href="#;" id="kakao-link-btn"><img src="<?=$board_skin_path?>/img/send_kakaotalk.png" valign="middle"></a></div>
        <script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>
        <script>
        // 사용할 앱의 Javascript 키를 설정해 주세요.
        Kakao.init('<?php echo $mw_basic['cf_kakao_key']?>');

        // 카카오톡 링크 버튼을 생성합니다. 처음 한번만 호출하면 됩니다.
        Kakao.Link.createTalkLinkButton({
            container: '#kakao-link-btn',
            label: "<?php echo $kakao_subject?>",
            <?php if ($kakao_thumb_url) { ?>
            image: {
                src: '<?php echo $kakao_thumb_url?>',
                width: '<?php echo $kakao_thumb_size[0]?>',
                height: '<?php echo $kakao_thumb_size[1]?>'
            },
            <?php } ?>
            webButton: {
                text: '<?php echo $kakao_name?>',
                url: '<?php echo $view_url?>' // 앱 설정의 웹 플랫폼에 등록한 도메인의 URL이어야 합니다.
            }
        });
        </script>
        <?php } ?>

        <?php if (strstr($mw_basic[cf_sns], '/kakaostory/')) { ?>
        <script>
        window.kakaoAsyncInit = function () {
            Kakao.Story.createShareButton({
                container: '#kakaostory-share-button'
            });
        };

        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//developers.kakao.com/sdk/js/kakao.story.min.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'kakao-js-sdk'));
        </script>

        <div id="kakaostory-share-button" data-url="<?php echo $sns_url?>"></div>
        <?php } ?>
    <?php } ?>

    <?php if (strstr($mw_basic[cf_sns], '/band/')) { ?>
    <style>.band img { width:24px; height:24px; }</style>
    <div class="band">
        <script src="//developers.band.us/js/share/band-button.js?v=20150509"></script>
        <script>
        new ShareBand.makeButton({"lang":"ko","type":"c"}  );
        </script>
    </div>
    <?php } ?>

    <?php if (strstr($mw_basic[cf_sns], '/line/')) { ?>
    <style>.line img { width:24px; height:24px; }</style>
    <div class="line">
    <script src="//media.line.me/js/line-button.js?v=20140411" ></script>
    <script>
    new media_line_me.LineButton({"lang":"ko","type":"b"});
    </script>
    </div>
    <?php } ?>

    <? if (strstr($mw_basic[cf_sns], '/facebook_good/')) { ?>
    <div id="facebook_good"><iframe src="http://www.facebook.com/plugins/like.php?href=<?=$facebook_like_href?>&amp;layout=button_count&amp;show_faces=true&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe></div>
    <? } ?>

    <? if (strstr($mw_basic[cf_sns], '/google_good/')) { ?>
    <!-- +1 버튼이 렌더링되기를 원하는 곳에 이 태그를 넣습니다. -->
    <div id="google_good"><g:plusone size="standard" annotation="bubble" width="150"></g:plusone></div>

    <!-- 적절한 곳에 이 렌더링 호출을 넣습니다. -->
    <script type="text/javascript">
      window.___gcfg = {lang: 'ko'};

      (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
      })();
    </script>
    <? } ?>

    <?php
    $view_sns = ob_get_contents();
    ob_end_clean();
}

if (function_exists("mw_moa_read"))
    mw_moa_read($member['mb_id'], $bo_table, $wr_id);

if (!$mw_basic['cf_time_view'])
    $mw_basic['cf_time_view'] = "Y-m-d (w) H:i";

$view['datetime2'] = mw_get_date($write['wr_datetime'], $mw_basic['cf_time_view']);
$view['datetime_sns'] = mw_get_date($write['wr_datetime'], 'sns');

$mw_admin_button = '';
if ($is_admin || $history_href || $is_singo_admin)
{
    ob_start();
    ?>
    <script>
    $(document).ready(function () {
        $(".mw_manage_title").mouseenter(function () {
            $manage_button = $(this);
            $(".mw_manage").css("top", $manage_button.position().top+10);
            $(".mw_manage").css("left", $manage_button.position().left - ($(".mw_manage").width()-$manage_button.width()));
            $(".mw_manage").css("display", "block");
            $(".mw_manage .item").mouseenter(function () {
                $(this).css("background-color", "#ddd");
            });
            $(".mw_manage .item").mouseleave(function () {
                $(this).css("background-color", "#fff");
            });
        });
            $(".mw_manage").mouseleave(function () {
                $(this).css("display", "none");
            });

    });
    </script>

    <div class="mw_manage_title"><i class="fa fa-gear"></i> 관리</div>
    <div class="mw_manage">
    <?php
    echo "<div class=\"item\" onclick=\"mw_config()\"><i class=\"fa fa-gear\"></i> 스킨설정</div>";
    echo "<div class=\"item\" onclick=\"window.open('{$g4['admin_path']}/board_form.php?bo_table={$bo_table}&w=u')\"><i class=\"fa fa-gears\"></i> 보드설정</div>";
    if ($is_singo_admin && $view[mb_id] != $member[mb_id]) { 
        echo "<div class=\"item\" onclick=\"btn_intercept('{$write[mb_id]}', '{$write[wr_ip]}')\">
                <i class=\"fa fa-times-circle\"></i> 회원차단</div> ";
    }
    if ($history_href) {
        echo "<div class=\"item\" onclick=\"$history_href\"><i class=\"fa fa-file\"></i> 변경로그</div>";
    }
    if ($is_admin) {
        if ($download_log_href) {
            echo "<div class=\"item\" onclick=\"{$download_log_href}\"><i class=\"fa fa-download\"></i> 다운로그</div>";
        }
        if ($link_log_href) {
            echo "<div class=\"item\" onclick=\"{$link_log_href}\"><i class=\"fa fa-link\"></i> 링크로그</div>";
        }
        if ($copy_href) {
            echo "<div class=\"item\" onclick=\"{$copy_href}\"><i class=\"fa fa-copy\"></i> 복사</div>";
        }
        if ($move_href) {
            echo "<div class=\"item\" onclick=\"{$move_href}\"><i class=\"fa fa-arrow-right\"></i> 이동</div>";
        }
        if ($is_category) {
            echo "<div class=\"item\" onclick=\"mw_move_cate_one()\"><i class=\"fa fa-tag\"></i> 분류이동</div>";
        }
        if ($nosecret_href) {
            echo "<div class=\"item\" onclick=\"{$nosecret_href}\"><i class=\"fa fa-unlock\"></i> 잠금해제</div>";
        }
        if ($secret_href) {
            echo "<div class=\"item\" onclick=\"{$secret_href}\"><i class=\"fa fa-lock\"></i> 잠금</div>";
        }

        echo "<div class=\"item\" onclick=\"btn_now()\"><i class=\"fa fa-refresh\"></i> 시간갱신</div>";

        if ($view[is_notice])
            echo "<div class=\"item\" onclick=\"btn_notice()\"><i class=\"fa fa-bullhorn\"></i> 공지내림</div>";
        else 
            echo "<div class=\"item\" onclick=\"btn_notice()\"><i class=\"fa fa-bullhorn\"></i> 공지올림</div>";

        if ($view[wr_comment_hide])
            echo "<div class=\"item\" onclick=\"btn_comment_hide()\"><i class=\"fa fa-comment\"></i> 댓글보임</div>";
        else 
            echo "<div class=\"item\" onclick=\"btn_comment_hide()\"><i class=\"fa fa-comment-o\"></i> 댓글감춤</div>";

        if ($is_admin == "super") {
            echo "<div class=\"item\" onclick=\"void(mw_member_email())\"><i class=\"fa fa-envelope\"></i> 메일등록</div>";
        }

        $row = sql_fetch("select * from $mw[popup_notice_table] where bo_table = '$bo_table' and wr_id = '$wr_id'", false);
        if ($row)
            echo "<div class=\"item\" onclick=\"btn_popup()\"><i class=\"fa fa-level-down\"></i> 팝업내림</div>";
        else 
            echo "<div class=\"item\" onclick=\"btn_popup()\"><i class=\"fa fa-level-up\"></i> 팝업올림</div>";

        echo "<div class=\"item\" onclick=\"void(btn_copy_new())\"><i class=\"fa fa-upload\"></i> 새글등록</div>";

        if ($write[wr_view_block])
            echo "<div class=\"item\" onclick=\"btn_view_block()\"><i class=\"fa fa-exclamation-triangle\"></i> 차단해제</div>";
        else 
            echo "<div class=\"item\" onclick=\"btn_view_block()\"><i class=\"fa fa-exclamation-triangle\"></i> 보기차단</div>";
    }
    ?>
    </div><!--mw_manage-->
    <?php
    $mw_admin_button = ob_get_clean();
}

if ($mw_basic[cf_contents_shop] == "1")  // 배추컨텐츠샵-다운로드 결제
{
    $is_per = true;
    $is_buy = false;
    $is_per_msg = '예외오류';

    if (!$is_member) {
	//alert("로그인 해주세요.");
        $is_per = false;
	$is_per_msg = "로그인 해주세요.";
    }

/*    if ($mw_basic['cf_contents_shop_category']) { // 분류별 결제
        $sql = sprintf(" select * from %s where bo_table = '%s' and ca_name = '%s'", $mw['category_table'], $bo_table, $write['ca_name']);
        $ro2 = sql_fetch($sql);

        if ($ro2['ca_cash'])
            $write['wr_contents_price'] = $ro2['ca_cash'];
    }
*/

    //if (!mw_is_buy_contents($member[mb_id], $bo_table, $wr_id) && $is_admin != "super")
    $con = mw_is_buy_contents($member[mb_id], $bo_table, $wr_id);
    if (!$con and $is_per)
    {
	//alert("결제 후 다운로드 하실 수 있습니다.");
        $is_per = false;
	$is_per_msg = "결제 후 다운로드 하실 수 있습니다.";

        if (!$ca_cash_use)
            $is_per_msg = '현재는 판매하고 있지 않습니다.';
    }
    else if (!$write[wr_contents_price]) ;
    else
    {
        if ($mw_basic[cf_contents_shop_download_count] and $is_per) {
            $sql1 = "select count(*) as cnt from $mw_cash[cash_list_table] where rel_table = '$bo_table' and rel_id = '$wr_id' and cl_cash < 0";
            $row1 = sql_fetch($sql1);
            $sql2 = "select count(*) as cnt from $mw[download_log_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and dl_datetime > '$con[cl_datetime]'";
            $row2 = sql_fetch($sql2);
            if ($row2[cnt] >= ($mw_basic[cf_contents_shop_download_count])) {
                //alert("다운로드 횟수 ($mw_basic[cf_contents_shop_download_count]회) 를 넘었습니다.\\n\\n재결제 후 다운로드 할 수 있습니다.");
                $is_per = false;
                $is_per_msg = "다운로드 횟수 ($mw_basic[cf_contents_shop_download_count]회) 를 넘었습니다.\\n\\n재결제 후 다운로드 할 수 있습니다.";
            }
        }

        if ($mw_basic[cf_contents_shop_download_day] and $is_per) {
            $gap = floor(($g4[server_time] - strtotime($con[cl_datetime])) / (60*60*24));
            if ($gap >= $mw_basic[cf_contents_shop_download_day]) {
                //alert("다운로드 기간 ($mw_basic[cf_contents_shop_download_day]일) 이 지났습니다.\\n\\n재결제 후 다운로드 할 수 있습니다.");
                $is_per = false;
                $is_per_msg = "다운로드 기간 ($mw_basic[cf_contents_shop_download_day]일) 이 지났습니다.\\n\\n재결제 후 다운로드 할 수 있습니다.";
            }
        }
    }
    if ($is_per) $is_buy = true;

    if (function_exists("mw_cash_is_membership") and !$is_buy)
    {
        $is_membership = @mw_cash_is_membership($member[mb_id], $bo_table, "mp_down");

        if ($is_membership == "no") // 멤버쉽 게시판이 아님
            ;
        else if ($is_membership == 'ok') {
            $is_per = true;
        }
        else {
            $is_per = false;
            //mw_cash_alert_membership($is_membership);
        }
    }
}

