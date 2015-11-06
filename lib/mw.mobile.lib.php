<?php
/**
 * 배추 모바일 빌더 (Mobile for Gnuboard4)
 *
 * Copyright (c) 2010 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

if (!function_exists("mw_seo_url")) {
function mw_seo_url($bo_table, $wr_id=0, $qstr='', $mobile=1)
{
    global $g4;
    global $mw;
    global $mw_basic;
    global $mw_mobile;
    global $is_admin;
    global $mw_mobile;

    $url = $g4['url'];

    if (!$mobile && $mw_mobile['m_subdomain'])
        $url = preg_replace("/^http:\/\/m\./", "http://", $url);

    if (($mobile && mw_is_mobile_builder()) or ($mobile == 2))  {
        if ($mw_mobile['m_subdomain'] && !preg_match("/^http:\/\/m\./", $url)) {
            $url = mw_sub_domain_url("m", $url);
        }
        $seo_path = '/'.$mw_mobile['dir'];
    }
    else
        $seo_path = '/'.$g4['bbs'];

    if ($bo_table)
        $url .= $seo_path.'/board.php?bo_table='.$bo_table;

    if ($wr_id)
        $url .= "&wr_id=".$wr_id;

    if ($qstr)
        $url .= $qstr;

    return $url;
}}

/*
if (!function_exists("is_g5")) {
function is_g5()
{
    if (defined('G5_PATH'))
        return true;

    return false;
}}*/

if (!function_exists("mw_is_mobile_builder")) {
function mw_is_mobile_builder()
{
    global $mw_mobile;

    if ($mw_mobile) return true;

    $is_mobile = false;

    if (strstr($_SERVER['PHP_SELF'], "/".$mw_mobile['dir']))
        $is_mobile = true;
    else if (strstr($_SERVER['PHP_SELF'], "/m/b/"))
        $is_mobile = true;

    return $is_mobile;
}}

if (!function_exists("mw_sub_domain_url")) {
function mw_sub_domain_url($sub, $url) {
    global $g4;
    if (strstr($url, "www.")) {
        $url = str_replace("www.", "$sub.", $url);
    } else {
        //echo "$url\n";
        $cookie_domain = str_replace(".", "\\.", $g4[cookie_domain]);
        $pattern = "/http:\/\/(.*)$cookie_domain(.*)/i";
        $change = "http://{$sub}{$g4[cookie_domain]}\$2";
        //echo "$pattern --- $change\n";
        $url = preg_replace($pattern, $change, $url);
        //echo $url;exit;
    }
    return $url;
}}

function mw_mobile_total_alarm()
{
    global $g4;
    global $member;
    global $mw_moa_table;

    $mb_id = $member['mb_id'];

    $memo_not_read = 0;
    // 읽지 않은 쪽지가 있다면
    if ($mb_id) {
        $sql = " select count(*) as cnt from {$g4['memo_table']} where me_recv_mb_id = '{$mb_id}' and me_read_datetime = '0000-00-00 00:00:00' ";
        $row = sql_fetch($sql);
        $memo_not_read = $row['cnt'];
    }

    $is_smart_alarm = false;
    $moa_count = 0;

    if (is_file($g4['path']."/plugin/smart-alarm/_config.php")) {
        include($g4['path']."/plugin/smart-alarm/_config.php");

        if (function_exists("mw_moa_count")) {
            $moa_count = mw_moa_count();
        }
        $is_smart_alarm = true;
    }

    $total_alarm = $moa_count + $memo_not_read;

    return array("total_alarm"=>$total_alarm, "is_smart_alarm"=>$is_smart_alarm, "moa_count"=>$moa_count, "memo_not_read"=>$memo_not_read);
}

