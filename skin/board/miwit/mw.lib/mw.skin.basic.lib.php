<?php
/**
 * Bechu basic skin for gnuboard4
 *
 * copyright (c) 2008 Choi Jae-Young <www.miwit.com>
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

$basic_run_time = get_microtime();

if (defined("G5_PATH")) {
    include_once($board_skin_path."/mw.proc/mw.g5.adapter.extend.php");
    //$basic_run_time = mw_time_log($basic_run_time, "[basic] include /mw.proc/mw.g5.adapter.extend.php");
}

if (defined("_MW_MOBILE_"))
    $board_skin_path = $mw_mobile['board_skin_path'];

$pc_skin_path = $board_skin_path;

if (!defined("_MW_BOARD_")) {
    include_once("$board_skin_path/mw.lib/mw.function.lib.php");
    $basic_run_time = mw_time_log($basic_run_time, "[basic] include /mw.lib/mw.function.lib.php");
}

@set_time_limit(0);
@ini_set("gd.jpeg_ignore_warning", true);
@ini_set('memory_limit', '-1'); 

$mw['basic_config_table'] = $g4['table_prefix']."mw_basic_config";
$mw['board_member_table'] = $g4['table_prefix']."mw_board_member";
$mw['download_log_table'] = $g4['table_prefix']."mw_download_log";
$mw['link_log_table']     = $g4['table_prefix']."mw_link_log";
$mw['post_history_table'] = $g4['table_prefix']."mw_post_history";
$mw['guploader_table']    = $g4['table_prefix']."mw_guploader";
$mw['vote_table']         = $g4['table_prefix']."mw_vote";
$mw['vote_item_table']    = $g4['table_prefix']."mw_vote_item";
$mw['vote_log_table']     = $g4['table_prefix']."mw_vote_log";
$mw['reward_table']       = $g4['table_prefix']."mw_reward";
$mw['reward_log_table']   = $g4['table_prefix']."mw_reward_log";
$mw['singo_log_table']    = $g4['table_prefix']."mw_singo_log";
$mw['must_notice_table']  = $g4['table_prefix']."mw_must_notice";
$mw['comment_good_table'] = $g4['table_prefix']."mw_comment_good";
$mw['comment_file_table'] = $g4['table_prefix']."mw_comment_file";
$mw['popup_notice_table'] = $g4['table_prefix']."mw_popup_notice";
$mw['okname_table']       = $g4['table_prefix']."mw_okname";
$mw['temp_table']         = $g4['table_prefix']."mw_temp";
$mw['bomb_table']         = $g4['table_prefix']."mw_bomb";
$mw['move_table']         = $g4['table_prefix']."mw_move";
$mw['jump_log_table']     = $g4['table_prefix']."mw_jump_log";
$mw['category_table']     = $g4['table_prefix']."mw_category";
$mw['level_table']        = $g4['table_prefix']."mw_level";
$mw['cash_grade_table']   = $g4['table_prefix']."mw_cash_grade";
$mw['mobile_dir']         = "plugin/mobile";

$default_charset = '';
if (preg_match("/^utf/i", $g4['charset']))
    $default_charset = "default charset=utf8;";

// 환경설정 파일 경로
$mw_basic_config_path = "{$g4['path']}/data/mw.basic.config";
$mw_basic_config_file = "$mw_basic_config_path/{$board['bo_table']}";
mw_mkdir($mw_basic_config_path, 0707);
$basic_run_time = mw_time_log($basic_run_time, "[basic] mw_mkdir($mw_basic_config_path, 0707)");

$mw_basic_upgrade_time_file = "$mw_basic_config_path/{$board['bo_table']}_upgrade_time";
//if (!is_mw_file($mw_basic_upgrade_time_file)) mw_write_file($mw_basic_upgrade_time_file, filectime("$board_skin_path/mw.adm/mw.upgrade.php"));

$mw_basic_upgrade_time = mw_read_file($mw_basic_upgrade_time_file);
$basic_run_time = mw_time_log($basic_run_time, "[basic] mw_read_file($mw_basic_upgrade_time_file)");

// 스킨 환경정보
//$sql = "select * from $mw['basic_config_table'] where bo_table = '{$bo_table}'";
//$mw_basic = sql_fetch($sql, false);

// 업그레이드 파일 생성시간을 검사해서 변경되었을 경우에만 업그레이드 파일 실행
if (!is_mw_file($mw_basic_upgrade_time_file) || filectime("$board_skin_path/mw.adm/mw.upgrade.php") != $mw_basic_upgrade_time) {
    include_once("$board_skin_path/mw.adm/mw.upgrade.php");
    mw_write_file($mw_basic_upgrade_time_file, filectime("$board_skin_path/mw.adm/mw.upgrade.php"));
    $basic_run_time = mw_time_log($basic_run_time, "[basic] mw_write_file($mw_basic_upgrade_time_file)");
}

// 환경설정  파일 없으면 생성
if (!is_mw_file($mw_basic_config_file)) {
    mw_basic_write_config_file();
    $basic_run_time = mw_time_log($basic_run_time, "[basic] mw_basic_write_config_file()");
}
    
// 환경설정 변수
$mw_basic = mw_basic_read_config_file();
$basic_run_time = mw_time_log($basic_run_time, "[basic] mw_basic_read_config_file()");

// 자동이동
$mw_basic['cf_auto_move'] = unserialize($mw_basic['cf_auto_move']);
$basic_run_time = mw_time_log($basic_run_time, "[basic] unserialize(mw_basic[cf_auto_move])");

global $mw_is_list;
global $mw_is_view;
global $mw_is_comment;

$mw_basic['cf_board_member'] = trim($mw_basic['cf_board_member']);
$mw_is_board_member = false;

if ($mw_basic['cf_board_member'] && !$is_admin)
{
    $ip1 = $_SERVER['REMOTE_ADDR'];
    $ip2 = preg_replace("/^([0-9]+\.[0-9]+\.[0-9]+\.)[0-9]+\$/", "$1+", $_SERVER['REMOTE_ADDR']);
    $ip3 = preg_replace("/^([0-9]+\.[0-9]+\.)[0-9]+\.[0-9]+\$/", "$1+", $_SERVER['REMOTE_ADDR']);
    $ip4 = preg_replace("/^([0-9]+\.)[0-9]+\.[0-9]+\.[0-9]+\$/", "$1+", $_SERVER['REMOTE_ADDR']);

    $sql = "select mb_id ";
    $sql.= "  from {$mw['board_member_table']} ";
    $sql.= "  where bo_table = '{$board['bo_table']}'";
    $sql.= "    and (mb_id = '{$member['mb_id']}' or mb_id = '{$ip1}' or mb_id = '{$ip2}' or mb_id = '{$ip3}' or mb_id = '{$ip4}') ";
    $sql.= "    and bm_limit >= '{$g4['time_ymd']}' ";

    //$qry = sql_query($sql, false);
    $row = sql_fetch($sql, false);

    // 접근권한 : 등록하지않은 회원 접근제한
    if ($mw_basic['cf_board_member'] == '1') {
        if (!$row) {
            if ($mw_basic['cf_board_member_list'] && $mw_is_list) { ; }
            elseif ($mw_basic['cf_board_member_view'] && $mw_is_view) { ; }
            elseif ($mw_basic['cf_board_member_comment'] && $mw_is_comment) { ; }
            else alert("게시판에 접근권한이 없습니다.");
        }
        else {
            $mw_is_board_member = true;
        }
    }
    // 접근권한 : 등록한 회원 접근제한
    else if ($mw_basic['cf_board_member'] == '2') {
        if ($row) {
            if ($mw_basic['cf_board_member_list'] && $mw_is_list) { ; }
            elseif ($mw_basic['cf_board_member_view'] && $mw_is_view) { ; }
            elseif ($mw_basic['cf_board_member_comment'] && $mw_is_comment) { ; }
            else alert("게시판에 접근권한이 없습니다.");
        } else {
            $mw_is_board_member = true;
        }
    }
    $basic_run_time = mw_time_log($basic_run_time, "[basic] board_member");
}

// 접근권한:나이, 성별
if (!trim($mw_basic['cf_gender_m']) and !trim($mw_basic['cf_gender_w'])) {
    $mw_basic['cf_gender_m'] = 'lvwc';
    $mw_basic['cf_gender_w'] = 'lvwc';
}

if (!$is_admin &&  $member['mb_sex'] == 'M') {
    if ($mw_is_list && !strstr($mw_basic['cf_gender_m'], 'l')) alert("남자는 접근할 수 없습니다.");
    if ($mw_is_view && !strstr($mw_basic['cf_gender_m'], 'v')) alert("남자는 읽을 수 없습니다.");
    if ($mw_is_write && !strstr($mw_basic['cf_gender_m'], 'w')) alert("남자는 글작성 권한이 없습니다.");
    if ($mw_is_comment && !strstr($mw_basic['cf_gender_m'], 'c')) alert("남자는 댓글작성 권한이 없습니다.");
}

if (!$is_admin &&  $member['mb_sex'] == 'F') {
    if ($mw_is_list && !strstr($mw_basic['cf_gender_w'], 'l')) alert("여자는 접근할 수 없습니다.");
    if ($mw_is_view && !strstr($mw_basic['cf_gender_w'], 'v')) alert("여자는 읽을 수 없습니다.");
    if ($mw_is_write && !strstr($mw_basic['cf_gender_w'], 'w')) alert("여자는 글작성 권한이 없습니다.");
    if ($mw_is_comment && !strstr($mw_basic['cf_gender_w'], 'c')) alert("여자는 댓글작성 권한이 없습니다.");
}

//if (!$is_admin && $mw_basic['cf_gender'] && $mw_basic['cf_gender'] == 'M' && $member['mb_sex'] != 'M') { alert("남자만 접근 가능합니다."); }
//if (!$is_admin && $mw_basic['cf_gender'] && $mw_basic['cf_gender'] == 'F' && $member['mb_sex'] != 'F') { alert("여자만 접근 가능합니다."); }

if (!$is_admin) {
    if ($mw_is_list && strstr($mw_basic['cf_age_opt'], 'l')) mw_basic_age($mw_basic['cf_age']);
    if ($mw_is_view && strstr($mw_basic['cf_age_opt'], 'v')) mw_basic_age($mw_basic['cf_age']);
    if ($mw_is_write && strstr($mw_basic['cf_age_opt'], 'w')) mw_basic_age($mw_basic['cf_age']);
    if ($mw_is_comment && strstr($mw_basic['cf_age_opt'], 'c')) mw_basic_age($mw_basic['cf_age']);
}


// 접근설정: 날짜
if (!$is_admin && $mw_basic['cf_board_sdate'] && $mw_basic['cf_board_sdate'] > $g4['time_ymd']) {
    alert("{$mw_basic['cf_board_sdate']} 부터 접근 가능합니다.");
}
if (!$is_admin && $mw_basic['cf_board_edate'] && $mw_basic['cf_board_edate'] < $g4['time_ymd']) {
    alert("{$mw_basic['cf_board_edate']} 까지 접근 가능합니다.");
}

// 접근설정: 시간
if (!$is_admin && $mw_basic['cf_board_stime'] && $mw_basic['cf_board_stime'] > $g4['time_his']) {
    alert("{$mw_basic['cf_board_stime']} 부터 접근 가능합니다.");
}
if (!$is_admin && $mw_basic['cf_board_etime'] && $mw_basic['cf_board_etime'] < $g4['time_his']) {
    alert("{$mw_basic['cf_board_etime']} 까지 접근 가능합니다.");
}

// 접근설정: 요일
$arr_yoil = array ("일", "월", "화", "수", "목", "금", "토");
if (!$is_admin && $mw_basic['cf_board_week'] && $mw_basic['cf_board_week'] != '0,0,0,0,0,0,0') {
    $tmp = explode(",", $mw_basic['cf_board_week']);
    $str = array();
    for ($i=0; $i<7; $i++) {
        if ($tmp[$i] == '1') {
            $str[] = $arr_yoil[$i];
        }
    }
    $str = implode(", ", $str);
    if ($tmp[date("w", $g4['server_time'])] == '0') {
        alert("{$str}요일에만 접근 가능합니다.");
    }
}
$basic_run_time = mw_time_log($basic_run_time, "[basic] gender, board_time, board_week");

if ($stx && !$is_admin && (!$mw_basic['cf_search_level'] || $mw_basic['cf_search_level'] > $member['mb_level'])) {
    alert("검색 권한이 없습니다.");
}

// 플러그인 컨텐츠샵
$sql = "select * from {$mw_cash['board_config_table']} limit 1";
$row = sql_fetch($sql, false);
$mw_cash['c_name'] = $row['c_name'];
$mw_cash['c_list'] = $row['c_list'];
$mw_cash['c_view'] = $row['c_view'];
$mw_cash['c_down'] = $row['c_down'];
$mw_cash['c_write'] = $row['c_write'];
$mw_cash['c_msg'] = $row['c_msg'];
$mw_cash['c_url'] = $row['c_url'];
$basic_run_time = mw_time_log($basic_run_time, "[basic] select * from {$mw_cash['board_config_table']} limit 1");

// 모아보기
$moa_path = "{$g4['path']}/plugin/smart-alarm";
if (is_mw_file("{$moa_path}/_config.php")) {
    include_once("{$moa_path}/_config.php");
    $basic_run_time = mw_time_log($basic_run_time, "[basic] include moa_config");
}

// 퀴즈
$quiz_path = "{$g4['path']}/plugin/quiz";
if (is_mw_file("{$quiz_path}/_config.php")) {
    include_once("{$quiz_path}/_config.php");
    $basic_run_time = mw_time_log($basic_run_time, "[basic] include quiz_config");
}

// 시험문제 
$exam_path = "{$g4['path']}/plugin/exam";
if (is_mw_file("{$exam_path}/_config.php")) {
    include_once("{$exam_path}/_config.php");
    $basic_run_time = mw_time_log($basic_run_time, "[basic] include exam_config");
}

// 럭키라이팅
$lucky_writing_path = "{$g4['path']}/plugin/lucky-writing";
if (is_mw_file("{$lucky_writing_path}/_lib.php")) {
    include_once("{$lucky_writing_path}/_lib.php");
    $basic_run_time = mw_time_log($basic_run_time, "[basic] include lucky_lib");
}

// RSS 수집기
$rss_collect_path = "{$g4['path']}/plugin/rss-collect";
if (is_mw_file("{$rss_collect_path}/_config.php")) {
    include_once("{$rss_collect_path}/_config.php");
    $basic_run_time = mw_time_log($basic_run_time, "[basic] include rss_config");
}

// youtube 수집기
$youtube_collect_path = "{$g4['path']}/plugin/youtube-collect";
if (is_mw_file("{$youtube_collect_path}/_config.php")) {
    include_once("{$youtube_collect_path}/_config.php");
    $basic_run_time = mw_time_log($basic_run_time, "[basic] include youtube_config");
}

// 카카오스토리 수집기
$kakao_collect_path = "{$g4['path']}/plugin/kakao-collect";
if (is_mw_file("{$kakao_collect_path}/_config.php")) {
    include_once("{$kakao_collect_path}/_config.php");
    $basic_run_time = mw_time_log($basic_run_time, "[basic] include kakao_config");
}

// 인스타그램 수집기
$instagram_collect_path = "{$g4['path']}/plugin/instagram-collect";
if (is_mw_file("{$instagram_collect_path}/_config.php")) {
    include_once("{$instagram_collect_path}/_config.php");
    $basic_run_time = mw_time_log($basic_run_time, "[basic] include instagram_config");
}

// MarketDB
$marketdb_path = "{$g4['path']}/plugin/marketdb";
if (is_mw_file("{$marketdb_path}/_config.php")) {
    include_once("{$marketdb_path}/_config.php");
    $basic_run_time = mw_time_log($basic_run_time, "[basic] include marketdb_config");
}

if ($mw_basic['cf_social_commerce']) {
    $social_commerce_path = "{$g4['path']}/plugin/social-commerce";
    if (!is_dir($social_commerce_path) || !is_mw_file("{$social_commerce_path}/list.skin.php")) {
        $mw_basic['cf_social_commerce'] = null;
    }
}

// 재능마켓
if ($mw_basic['cf_talent_market']) {
    $talent_market_path = "{$g4['path']}/plugin/talent-market";
    if (!is_dir($talent_market_path) || !is_mw_file("{$talent_market_path}/_config.php")) {
        $mw_basic['cf_talent_market'] = null;
    }
}

// 재능마켓
if ($mw_basic['cf_reward']) {
    $reward_path = "{$g4['path']}/plugin/reward";
    if (!is_dir($reward_path) || !is_mw_file("{$reward_path}/_config.php")) {
        $mw_basic['cf_reward'] = null;
    }
}


// 게시판 배너
$bbs_banner_path = "{$g4['path']}/plugin/bbs-banner";
if ($mw_basic['cf_bbs_banner']) {
    if (!is_dir($bbs_banner_path) || !is_mw_file("{$bbs_banner_path}/_config.php")) {
        $mw_basic['cf_bbs_banner'] = null;
    }
}

if ($mw_basic['cf_write_notice']) {
    $mw_basic['cf_write_notice'] = trim($mw_basic['cf_write_notice']);
    $mw_basic['cf_write_notice'] = str_replace("\r", "", $mw_basic['cf_write_notice']);
    $mw_basic['cf_write_notice'] = str_replace("\n", "\\n", $mw_basic['cf_write_notice']);
}

if (!$mw_basic['cf_singo_id'])
    $mw_basic['cf_singo_id'] = $config['cf_admin'];

if (!$mw_basic['cf_email'])
    $mw_basic['cf_email'] = "test@test.com\ntest@test.com\n";

if (!$mw_basic['cf_hp'])
    $mw_basic['cf_hp'] = "010-000-0000\n010-000-0000\n";

// CCL 정보
$view['wr_ccl'] = $write['wr_ccl'] = mw_get_ccl_info($write['wr_ccl']);

// 1:1 게시판
if ($mw_basic['cf_attribute'] == "1:1" && !$is_admin && $wr_id && $w != "u")
{
    if (!strstr($board['bo_notice'], "$wr_id") && $is_admin != 'super' && $member['mb_id'] != $write['mb_id'] && $member['mb_id'] != $write['wr_to_id'] && $mw_is_view) {
        goto_url("$g4[bbs_path]/board.php?bo_table={$board['bo_table']}");
    }

    if (!$board['bo_use_list_view']) {
        if (trim($sql_search) && substr(trim($sql_search), 0, 3) != "and")
            $sql_search = " and " . $sql_search;

        // 윗글을 얻음
        $sql = " select wr_id, wr_subject from $write_table where mb_id = '{$member['mb_id']}' and wr_is_comment = 0 and wr_num = '{$write['wr_num']}' and wr_reply < '{$write['wr_reply']}' $sql_search order by wr_num desc, wr_reply desc limit 1 ";
        $prev = sql_fetch($sql);
        // 위의 쿼리문으로 값을 얻지 못했다면
        if (!$prev['wr_id'])     {
            $sql = " select wr_id, wr_subject from $write_table where mb_id = '{$member['mb_id']}' and wr_is_comment = 0 and wr_num < '{$write['wr_num']}' $sql_search order by wr_num desc, wr_reply desc limit 1 ";
            $prev = sql_fetch($sql);
        }

        // 아래글을 얻음
        $sql = " select wr_id, wr_subject from $write_table where mb_id = '{$member['mb_id']}' and wr_is_comment = 0 and wr_num = '{$write['wr_num']}' and wr_reply > '{$write['wr_reply']}' $sql_search order by wr_num, wr_reply limit 1 ";
        $next = sql_fetch($sql);
        // 위의 쿼리문으로 값을 얻지 못했다면
        if (!$next['wr_id']) {
            $sql = " select wr_id, wr_subject from $write_table where mb_id = '{$member['mb_id']}' and wr_is_comment = 0 and wr_num > '{$write['wr_num']}' $sql_search order by wr_num, wr_reply limit 1 ";
            $next = sql_fetch($sql);
        }
    }

    // 이전글 링크
    $prev_href = "";
    if ($prev['wr_id']) {
        $prev_wr_subject = get_text(cut_str($prev['wr_subject'], 255));
        //$prev_href = "./board.php?bo_table={$board['bo_table']}&wr_id={$prev['wr_id']}&page=$page" . $qstr;
        $prev_href = mw_seo_url($board['bo_table'], $prev['wr_id'], "&page=$page" . $qstr);
    }

    // 다음글 링크
    $next_href = "";
    if ($next['wr_id']) {
        $next_wr_subject = get_text(cut_str($next['wr_subject'], 255));
        //$next_href = "./board.php?bo_table={$board['bo_table']}&wr_id={$next['wr_id']}&page=$page" . $qstr;
        $next_href = mw_seo_url($board['bo_table'], $next['wr_id'], "&page=$page" . $qstr);
    }
    $basic_run_time = mw_time_log($basic_run_time, "[basic] 1:1");
}

// 썸네일 경로
$file_path = "{$g4['path']}/data/file/{$board['bo_table']}";
$thumb_path = "{$file_path}/thumbnail";
$thumb2_path = "{$file_path}/thumbnail2";
$thumb3_path = "{$file_path}/thumbnail3";
$thumb4_path = "{$file_path}/thumbnail4";
$thumb5_path = "{$file_path}/thumbnail5";

mw_mkdir($file_path);
mw_mkdir($thumb_path);
mw_mkdir($thumb2_path);
mw_mkdir($thumb3_path);
mw_mkdir($thumb4_path);
mw_mkdir($thumb5_path);

if ($wr_id) {
    $thumb_file = mw_thumb_jpg($thumb_path.'/'.$wr_id);
    $thumb2_file = mw_thumb_jpg($thumb2_path.'/'.$wr_id);
    $thumb3_file = mw_thumb_jpg($thumb3_path.'/'.$wr_id);
    $thumb4_file = mw_thumb_jpg($thumb4_path.'/'.$wr_id);
    $thumb5_file = mw_thumb_jpg($thumb5_path.'/'.$wr_id);
}

$watermark_path = "{$file_path}/watermark";
mw_mkdir($watermark_path);

$lightbox_path = "{$file_path}/lightbox";
mw_mkdir($lightbox_path);

$basic_run_time = mw_time_log($basic_run_time, "[basic] thumb path make");

// 회원 코멘트 이미지 경로
$comment_image_path = "{$g4['path']}/data/mw.basic.comment.image";

// 서비스 점검중
if ($mw_basic['cf_under_construction'] && $is_admin != "super") {
    alert("죄송합니다. 현재 서비스 점검중입니다."); 
}

$mw_anonymous_list = array();
$mw_anonymous_index = 0;

if ($mw_basic['cf_sns'] == '1') {
    $mw_basic['cf_sns'] = "";
    $mw_basic['cf_sns'].= "/twitter/";
    //$mw_basic['cf_sns'].= "/me2day/";
    //$mw_basic['cf_sns'].= "/yozm/";
    $mw_basic['cf_sns'].= "/cyworld/";
    $mw_basic['cf_sns'].= "/facebook/";
    $mw_basic['cf_sns'].= "/facebook_good/";
    $mw_basic['cf_sns'].= "/google_plus/";
}

$bo_table_width = $board['bo_table_width'];
$bo_table_width = $bo_table_width . ($bo_table_width > 100 ? "px" : "%");
$width = $bo_table_width;

$ss_key_name = "ss_key_{$bo_table}";

$mw_category_list = array();
$mw_category = array();
if ($sca) {
    $mw_category = mw_category_info($sca);

    if ($mw_category['ca_type'])
        $mw_basic['cf_type'] = $mw_category['ca_type'];

    if ($mw_category['ca_level_list'] && $mw_is_list && $mw_category['ca_level_list'] > $member['mb_level']) {
        alert("{$sca} 분류의 목록을 볼 권한이 없습니다.");
    }

    if ($mw_category['ca_level_view'] && $mw_is_view && $mw_category['ca_level_view'] > $member['mb_level']) {
        alert("{$sca} 분류의 내용을 볼 권한이 없습니다.");
    }

    if ($mw_category['ca_level_write'] && $mw_is_write && $mw_category['ca_level_write'] > $member['mb_level']) {
        alert("{$sca} 분류의 글작성 권한이 없습니다.");
    }
}

if ($mw_basic['cf_cash_grade_use'] && !$is_admin)
{
    $my_cash_grade = mw_cash_grade($member['mb_id']);
    if (!$my_cash_grade) {
        $my_cash_grade['gd_id'] = 0;
        $my_cash_grade['gd_name'] = "일반";
    }

    $sql = " select * from {$mw['cash_grade_table']} ";
    $sql.= " where bo_table = '{$bo_table}' and gd_id = '{$my_cash_grade['gd_id']}'";
    $grade = sql_fetch($sql);

    if ($mw_is_list && !$grade['gd_list'])
        alert("[{$my_cash_grade['gd_name']}] 등급은 권한이 없습니다. ");

    if ($mw_is_read && !$grade['gd_read'])
        alert("[{$my_cash_grade['gd_name']}] 등급은 권한이 없습니다. ");

    if ($mw_is_write && !$grade['gd_write'])
        alert("[{$my_cash_grade['gd_name']}] 등급은 권한이 없습니다. ");

    if ($mw_is_comment && !$grade['gd_comment'])
        alert("[{$my_cash_grade['gd_name']}] 등급은 권한이 없습니다. ");

    $basic_run_time = mw_time_log($basic_run_time, "[basic] cash_grade");
}

// 쓰기버튼 항상 출력
if ($mw_basic[cf_write_button])
    $write_href = "./write.php?bo_table=$bo_table";

include($board_skin_path.'/mw.proc/mw.seo.php');
$basic_run_time = mw_time_log($basic_run_time, "[basic] include /mw.proc/mw.seo.php");

// 권한별 설정
$sql = "select * from {$mw['level_table']} ";
$sql.= " where bo_table = '{$bo_table}' and mb_level = '{$member['mb_level']}' and cf_use = '1' ";
$mw_basic_level = sql_fetch($sql, false);
if ($mw_basic_level) {
    if ($mw_basic_level['cf_write_day'])
        $mw_basic['cf_write_day'] = $mw_basic_level['cf_write_day'];
    if ($mw_basic_level['cf_write_day_count'])
        $mw_basic['cf_write_day_count'] = $mw_basic_level['cf_write_day_count'];
    if ($mw_basic_level['cf_qna_count'])
        $mw_basic['cf_qna_count'] = $mw_basic_level['cf_qna_count'];
}
$basic_run_time = mw_time_log($basic_run_time, "[basic] write_day ");

if ($board['bo_use_rss_view'] && $mw_basic['cf_rss'] && $rss_href)
{
    $rss_href = $g4['url'].'/skin/board/'.$board['bo_skin'].'/rss.php?bo_table='.$bo_table;
}

if ($is_file and is_g5() and !$board['bo_upload_count']) {
    $is_file = false;
}

$notice_div = "\n";
if (is_g5()) {
    $notice_div = ",";
}

$cf_css = $mw_basic['cf_css'];
$chrome_css = "div,table,td,span,li,a,h1,h2,h3,h4,h5,input,button {font-family:돋움;}";
if (strstr($_SERVER['HTTP_USER_AGENT'], "Chrome")) {
    //$cf_css .= "\n".$chrome_css.PHP_EOL;
}

if ($mw_basic['cf_thumb_round']) {
    $cf_css .= '
#mw_basic .mw_basic_list_thumb a img { -webkit-border-radius:1em; -moz-border-radius:1em; border-radius:1em; }
#mw_basic .box { -webkit-border-radius:1em; -moz-border-radius:1em; border-radius:1em; }
#mw_basic .gall_list div.gall_image img.thumb { -webkit-border-radius:1em; -moz-border-radius:1em; border-radius:1em; }
';
}
$mw_basic['cf_umz'] = 0;

$ca_cash_use = '';
if ($mw_basic['cf_contents_shop_category'] and $write) { // 분류별 결제
    $sql = sprintf(" select * from %s where bo_table = '%s' and ca_name = '%s'", $mw['category_table'], $bo_table, $write['ca_name']);
    $ro2 = sql_fetch($sql);

    if ($ro2['ca_cash']) {
        $write['wr_contents_price'] = $ro2['ca_cash'];
        $view['wr_contents_price'] = $ro2['ca_cash'];
    }

    $ca_cash_use = $ro2['ca_cash_use'];
}


