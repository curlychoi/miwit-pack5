<?php
/**
 * 스마트알람 (Smart-Alarm for Gnuboard4)
 *
 * Copyright (c) 2011 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

function mw_moa_104() {}

function mw_moa_read($mb_id, $bo_table, $wr_id)
{
    global $write_table, $mw_moa_table;

    $sql = " update {$mw_moa_table} set mo_flag = '1' ";
    $sql.= "  where mb_id = '{$mb_id}' ";
    $sql.= "    and bo_table = '{$bo_table}' ";
    $sql.= "    and wr_parent = '{$wr_id}' ";
    $sql.= "    and mo_flag = '' ";
    sql_query($sql);
}

function mw_moa_count()
{
    global $g4, $mw_moa_table, $member;

    $sql = " select count(*) as cnt from {$mw_moa_table} where mb_id = '{$member['mb_id']}' and mo_flag = '' ";
    $row = sql_fetch($sql);

    return $row['cnt'];
}

// $wr_parent : 원본 글번호
// $wr_id : 답글 글번호
// $mb_id : 원본 글쓴이
// $re_mb_id : 답글 글쓴이
function mw_moa_insert($wr_parent, $wr_id, $mb_id, $re_mb_id)
{
    global $g4, $bo_table, $mw_moa_table, $mw_moa_config_table, $w, $_POST, $mw_basic, $wr_anonymous;
    global $wr_content, $mw_moa_date;

    sql_query(" delete from {$mw_moa_table} where mo_flag = '1' and mo_datetime < '".date("Y-m-d H:i:s", $g4['server_time'] - (86400*$mw_moa_date))."' ");

    if (!$wr_parent) $wr_parent = $wr_id;
    if (!$mb_id) $mb_id = $re_mb_id;

    // 신규, 댓글, 답글만 
    if (!($w == '' || $w == 'c' || $w == 'r' || $w == 'a')) return;

    preg_match_all("/\[@([^\]]+)\]/iUs", $wr_content, $matchs);

    $mention_list = array();

    for ($i=0, $m=count($matchs[1]); $i<$m; $i++) {
        $mb_nick = addslashes(trim($matchs[1][$i]));
        $row = sql_fetch("select mb_id from {$g4['member_table']} where mb_nick = '{$mb_nick}'");
        if ($row) {
            if ($row['mb_id'] == $re_mb_id) continue;

            $sql = " select * from {$mw_moa_table} where ";
            $sql.= "    mb_id = '{$row['mb_id']}' ";
            $sql.= "and bo_table = '{$bo_table}' ";
            $sql.= "and wr_parent = '{$wr_parent}' ";
            $sql.= "and wr_id = '{$wr_id}' ";
            $sql.= "and wr_mb_id = '{$row['mb_id']}' ";
            $sql.= "and re_mb_id = '{$tmp_re_mb_id}' ";
            $sql.= "and mo_flag = '' ";
            $tmp = sql_fetch($sql);
            if ($tmp) continue;

            $sql = " insert into {$mw_moa_table} set ";
            $sql.= "  mb_id = '{$row['mb_id']}' ";
            $sql.= ", bo_table = '{$bo_table}' ";
            $sql.= ", wr_parent = '{$wr_parent}' ";
            $sql.= ", wr_id = '{$wr_id}' ";
            $sql.= ", wr_mb_id = '{$row['mb_id']}' ";
            $sql.= ", re_mb_id = '{$re_mb_id}' ";
            $sql.= ", mo_work = 'm' ";
            $sql.= ", mo_datetime = '{$g4['time_ymdhis']}' ";
            sql_query($sql);

            $mention_list[] = array('mb_id'=>$row['mb_id'], 're_id'=>$re_mb_id);
        }
    }

    // 자기 게시물에 자기가 쓴 글은 알림하지 않음
    if ($wr_parent == $wr_id && $mb_id == $re_mb_id) return;

    $sql = " select mb_id, wr_anonymous from {$g4['write_prefix']}{$bo_table} where wr_id = '{$wr_parent}' ";
    $row = sql_fetch($sql);

    $parent_anonymous = $row['wr_anonymous'];

    $sql = " select mb_id, wr_anonymous from {$g4['write_prefix']}{$bo_table} where wr_parent = '{$wr_parent}' group by mb_id ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry))
    {
        if (!$row['mb_id']) continue;
        if ($row['mb_id'] == $re_mb_id) continue;

        $moa_config = sql_fetch("select * from $mw_moa_config_table where mb_id = '{$row['mb_id']}'", false);
        if (strstr($moa_config['cf_reject_board'], "$bo_table,")) continue; // 제외게시판
        if ($moa_config['cf_config'] == 'close') continue; // 알림안함
        if ($moa_config['cf_config'] == 'only_reply') { // 내글의 답글만 알림
            if ($w == 'c' && $_POST['comment_id']) {
                $sql = "select mb_id, wr_anonymous from {$g4['write_prefix']}{$bo_table} where wr_id = '{$_POST['comment_id']}'";
                $ori = sql_fetch($sql);
                $parent_anonymous = $ori['wr_anonymous'];
                if ($ori['mb_id'] != $row['mb_id']) continue;
            }
            else if ($row['mb_id'] != $mb_id) continue;
        }

        if ($w == 'a') { // 답변 채택
            if ($row['mb_id'] != $mb_id) continue;
        }

        $tmp_mb_id = $mb_id;
        $tmp_re_mb_id = $re_mb_id;

        if ($parent_anonymous) $tmp_mb_id = '@anonymous';
        if ($wr_anonymous) $tmp_re_mb_id = '@anonymous';
        if ($mw_basic['cf_attribute'] == 'anonymous') $tmp_mb_id = $tmp_re_mb_id = '@anonymous';

        $sql = " select * from {$mw_moa_table} where ";
        $sql.= "    mb_id = '{$row['mb_id']}' ";
        $sql.= "and bo_table = '{$bo_table}' ";
        $sql.= "and wr_parent = '{$wr_parent}' ";
        //$sql.= "and wr_id = '{$wr_id}' ";
        $sql.= "and wr_mb_id = '{$tmp_mb_id}' ";
        $sql.= "and re_mb_id = '{$tmp_re_mb_id}' ";
        //$sql.= "and mo_work = '{$w}' ";
        $sql.= "and mo_flag = '' ";
        $tmp = sql_fetch($sql);
        if ($tmp) continue;

        $is_mention = false;
        foreach ((array)$mention_list as $mention) {
            if ($mention['mb_id'] == $row['mb_id'] && $mention['re_id'] == $tmp_re_mb_id) {
                $is_mention = true;
                break;
            }
        }
        if ($is_mention) continue;

        $sql = " insert into {$mw_moa_table} set ";
        $sql.= "  mb_id = '{$row['mb_id']}' ";
        $sql.= ", bo_table = '{$bo_table}' ";
        $sql.= ", wr_parent = '{$wr_parent}' ";
        $sql.= ", wr_id = '{$wr_id}' ";
        $sql.= ", wr_mb_id = '{$tmp_mb_id}' ";
        $sql.= ", re_mb_id = '{$tmp_re_mb_id}' ";
        $sql.= ", mo_work = '{$w}' ";
        $sql.= ", mo_datetime = '{$g4['time_ymdhis']}' ";
        sql_query($sql);
    }
}

function mw_moa_delete($wr_id)
{
    global $g4, $bo_table, $mw_moa_table, $w, $mw_moa_date;

    sql_query(" delete from {$mw_moa_table} where mo_flag = '1' and mo_datetime < '".date("Y-m-d H:i:s", $g4['server_time'] - (86400*$mw_moa_date))."' ");
    sql_query(" delete from {$mw_moa_table} where bo_table = '{$bo_table}' and wr_parent = '{$wr_id}' ");
    sql_query(" delete from {$mw_moa_table} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' ");
}

function mw_moa_row($row)
{
    global $g4, $mw_moa_table, $mw_moa_path, $member, $comment_image_path, $mw, $now_path;

    $comment_image_path_now = str_replace($g4['path'], $now_path, $comment_image_path);
    $mw_moa_path_now = str_replace($g4['path'], $now_path, $mw_moa_path);

    if ($row['wr_mb_id'] == '@anonymous') {
        $wr_mb = '익명';
    } else {
        $mb = get_member($row['wr_mb_id'], "mb_id, mb_name, mb_nick, mb_email, mb_homepage");
        $wr_mb = $mb['mb_nick'];
    }

    if ($row['re_mb_id'] == '@anonymous') {
        $re_mb = '익명';
    } else {
        $mb = get_member($row['re_mb_id'], "mb_id, mb_name, mb_nick, mb_email, mb_homepage");
        $re_mb = $mb['mb_nick'];
    }

    if ($row['wr_mb_id'] == $row['re_mb_id'] and $row['wr_mb_id'] == '@anonymous')
        $name = "<span class='name'>익명</span>";
    elseif ($row['wr_mb_id'] == $member['mb_id'])
        $name = "<span class='name'>회원</span>님";
    elseif ($row['wr_mb_id'] == $row['re_mb_id'])
        $name = "<span class='name'>본인</span>";
    else
        $name = "<span class='name'>$wr_mb</span>님";

    if ($row['mo_work'] == 'r') {
        $row['href'] = "{$g4['url']}/{$g4['bbs']}/board.php?bo_table={$row['bo_table']}&wr_id={$row['wr_id']}";
        if (function_exists("mw_seo_url"))
            $row['href'] = mw_seo_url($row['bo_table'], $row['wr_id']);
        $row['msg'] = "<span class='name'>$re_mb</span> 회원님이 {$name}의 게시물에 답글을 남겼습니다.";
    }
    elseif ($row['mo_work'] == 'c') {
        $row['href'] = "{$g4['url']}/{$g4['bbs']}/board.php?bo_table={$row['bo_table']}&wr_id={$row['wr_parent']}#c_{$row['wr_id']}";
        if (function_exists("mw_seo_url"))
            $row['href'] = mw_seo_url($row['bo_table'], $row['wr_parent'], "#c_".$row['wr_id']);
        $row['msg'] = "<span class='name'>$re_mb</span> 회원님이 {$name}의 게시물에 댓글을 남겼습니다.";
    }
    elseif ($row['mo_work'] == 'm') {
        $row['href'] = "{$g4['url']}/{$g4['bbs']}/board.php?bo_table={$row['bo_table']}&wr_id={$row['wr_parent']}";
        if (function_exists("mw_seo_url"))
            $row['href'] = mw_seo_url($row['bo_table'], $row['wr_parent']);
        if ($row['wr_parent'] != $row['wr_id'])
            $row['href'] .= "#c_".$row['wr_id'];
        $row['msg'] = "<span class='name'>$re_mb</span> 님이 회원님을 언급하셨습니다.";
    }
    elseif ($row['mo_work'] == 'a') {
        $row['href'] = "{$g4['url']}/{$g4['bbs']}/board.php?bo_table={$row['bo_table']}&wr_id={$row['wr_parent']}";
        if (function_exists("mw_seo_url"))
            $row['href'] = mw_seo_url($row['bo_table'], $row['wr_parent']);
        if ($row['wr_parent'] != $row['wr_id'])
            $row['href'] .= "#c_".$row['wr_id'];
        $row['msg'] = "<span class='name'>$re_mb</span> 님이 회원님의 답변을 채택했습니다.";
    }

    // 그룹별 서브도메인 접속
    if (function_exists("mw_sub_domain_url")) {
        $board = sql_fetch("select * from {$g4['board_table']} where bo_table = '{$row['bo_table']}'");
        $group = sql_fetch("select * from {$g4['group_table']} where gr_id = '{$board['gr_id']}'");

        if ($group['gr_sub_domain'] && !$mw['config']['cf_sub_domain_off']) {
            $row['href'] = mw_sub_domain_url($group['gr_sub_domain'], $row['href']);
        }
    }

    $second = strtotime($g4['time_ymdhis']) - strtotime($row['mo_datetime']);
    $day = floor($second / 86400);
    $hour = floor($second / 3600);
    $minute = floor($second / 60);

    if ($day) $time = "약 {$day}일 전";
    elseif ($hour) $time = "약 {$hour}시간 전";
    elseif ($minute) $time = "약 {$minute}분 전";
    elseif ($second) $time = "약 {$second}초 전";
    $row['time'] = $time;

    $is_comment_image = false;
    $comment_image = "$mw_moa_path_now/img/noimage.gif";

    if ($row['re_mb_id'] && file_exists("$comment_image_path/{$row['re_mb_id']}")) {
        $comment_image = "$comment_image_path_now/{$row['re_mb_id']}";
        $is_comment_image = true;
    }

    $row['comment_image'] = $comment_image;

    return $row;
}

