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

@ini_set('memory_limit', '-1');
@set_time_limit(0);

if (!mw_singo_admin($member[mb_id]))
    alert_close("접근 권한이 없습니다.");

if (!strstr($_SERVER[HTTP_REFERER], "mw.proc/mw.intercept.php"))
    alert_close("잘못된 접근입니다.");

$token = md5(session_id().$member[mb_today_login].$member[mb_login_ip]);
if (($token != get_session("ss_token")) || ($token != $form_token))
    alert_close("잘못된 접근입니다.");

$is_ip = false;

$mb = get_member($mb_id);
if (!$mb) {
    $name = $mb_id;
    if (preg_match("/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/", $mb_id)) {
        $is_ip = true;
    }
    else {
        alert_close("회원정보가 잘못되었습니다.");
    }
}
else {
    $name = $mb[mb_nick];
}

if ($config[cf_admin] == $mb_id)
    alert_close("최고관리자는 접근 차단할 수 없습니다.");

if (!$is_ip) {
    $mb_intercept_date = date("Ymd", $g4[server_time]);
    $sql = " update $g4[member_table] set mb_level = '1' ";
    $sql.= " ,mb_intercept_date = '$mb_intercept_date'  ";
    $sql.= " ,mb_memo = '$mb_memo'  ";
    $sql.= " ,mb_mailling = ''  ";
    $sql.= " ,mb_sms = ''  ";
    $sql.= " ,mb_open = ''  ";
    $sql.= " where mb_id='$mb_id' ";
    sql_query($sql);
}

if ($cf_filter) {
    $tmp = explode(",", $config['cf_filter']);
    $is_filter_add = true;
    $filter_list = array();
    foreach ((array)$tmp as $f) {
        $f = trim($f);
        if (!$f) continue;
        if ($f == $cf_filter) {
            $is_filter_add = false;
            break;
        }
        $filter_list[] = $f;
    }
    if ($is_filter_add) {
        $tmp = explode(",", $cf_filter);
        foreach ((array)$tmp as $f) {
            $f = trim($f);
            if (!$f) continue;
            $filter_list[] = $f;
        }
        $cf_filter = addslashes(implode(",", $filter_list));
        sql_query("update {$g4['config_table']} set cf_filter = '{$cf_filter}'");
    }
}

$mw_syndi_path = $g4['path'].'/plugin/naver-syndi';
if (is_mw_file($mw_syndi_path.'/_config.php')) {
    include_once($mw_syndi_path.'/_config.php');
    include_once($mw_syndi_path.'/_lib.php');
}

if ($is_all_delete or $is_all_move) {
    $all_board_sql = "select * from $g4[board_table] ";
    $all_board_qry = sql_query($all_board_sql);
    while ($all_board_row = sql_fetch_array($all_board_qry)) {
        if ($is_ip)
            $all_write_sql = "select * from $g4[write_prefix]$all_board_row[bo_table] where mb_id = '' and wr_ip = '$mb_id' order by wr_num";
        else
            $all_write_sql = "select * from $g4[write_prefix]$all_board_row[bo_table] where mb_id = '$mb_id' order by wr_num";
        $all_write_qry = sql_query($all_write_sql);
        while ($all_write_row = sql_fetch_array($all_write_qry)) {
            if ($is_all_delete or $all_write_row[wr_is_comment]) {
                mw_delete_row($all_board_row, $all_write_row, "no");
            }
            elseif ($is_all_move) {
                if ($all_board_row['bo_table'] == $move_table) continue;
                mw_move($all_board_row, $all_write_row[wr_id], $move_table, 'move');
            }
            if ($intercept_ip and !strstr($config[cf_intercept_ip], $all_write_row[wr_ip])) {
                $config[cf_intercept_ip] = trim($config[cf_intercept_ip]) . "\n$all_write_row[wr_ip]";
                sql_query("update $g4[config_table] set cf_intercept_ip = '$config[cf_intercept_ip]'");
            }

            if (function_exists('mw_syndi_set_feed'))
                mw_syndi_set_feed($all_board_row['bo_table'], $all_write_row['wr_id'], '', 'd');

        } // write row
    } // board row
}

alert_close("{$name} 회원을 접근차단하였습니다.");

