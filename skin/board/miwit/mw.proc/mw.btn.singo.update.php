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

if (!strstr($_SERVER[HTTP_REFERER], "/mw.proc/mw.btn.singo.php"))
    alert_close("올바른 방법으로 이용해주세요.");

if (!$token or get_session("ss_singo_token") != $token) 
    alert_close("토큰 에러로 신고 불가합니다.");

unset($_SESSION["ss_singo_token"]);

if (!$is_member)
    alert_close("회원만 이용하실 수 있습니다.");

if ($write[mb_id] == $config[cf_admin])
    alert_close("최고관리자의 글은 신고하실 수 없습니다.");

if ($write[mb_id] == $member[mb_id])
    alert_close("본인의 글은 신고할 수 없습니다.");

if ($member[mb_level] < $mw_basic[cf_singo_level])
    alert_close("죄송합니다.\\n\\n신고 권한이 없습니다.");

$sql = "select * from $mw[singo_log_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and (mb_id = '$member[mb_id]' or si_ip = '$_SERVER[REMOTE_ADDR]')";
$row = sql_fetch($sql);
if ($row && !$is_admin)
    alert_close("이미 신고하셨습니다.");

if ($mw_basic[cf_singo] == '1') {
    $me_recv_mb_id = $mw_basic[cf_singo_id];

    $comment = "";
    if ($wr_id != $parent_id)
        $comment = "&_c={$wr_id}#c_{$wr_id}";

    $url = mw_seo_url($bo_table, $parent_id, $comment);

    $me_memo = "게시물 신고가 접수되었습니다.\n
    분류 : {$category}
    내용 : {$content}

    주소 : {$url}";

    $tmp_list = explode(",", $me_recv_mb_id);
    $me_recv_mb_id_list = "";
    $msg = "";
    $comma1 = $comma2 = "";
    $mb_list = array();
    $mb_array = array();
    for ($i=0; $i<count($tmp_list); $i++) {
        $tmp_list[$i] = trim($tmp_list[$i]);
        if (!$tmp_list[$i]) continue;
        $row = get_member($tmp_list[$i]);
        if ($row[mb_id]) {
            $me_recv_mb_id_list .= "$comma2$row[mb_nick]";
            $mb_list[] = $tmp_list[$i];
            $mb_array[] = $row;
            $comma2 = ",";
        }
    }

    for ($i=0; $i<count($mb_list); $i++)
    {
        if (trim($mb_list[$i])) {
            $tmp_row = sql_fetch(" select max(me_id) as max_me_id from $g4[memo_table] ");
            $me_id = $tmp_row[max_me_id] + 1;

            // 쪽지 INSERT
            $sql = " insert into $g4[memo_table]
                            ( me_id, me_recv_mb_id, me_send_mb_id, me_send_datetime, me_memo )
                     values ( '$me_id', '{$mb_list[$i]}', '$member[mb_id]', '$g4[time_ymdhis]', '$me_memo' ) ";
            sql_query($sql);

            // 실시간 쪽지 알림 기능
            $sql = " update $g4[member_table]
                        set mb_memo_call = '$member[mb_id]'
                      where mb_id = '$mb_list[$i]' ";
            sql_query($sql);

            if (!$is_admin) {
                $recv_mb_nick = get_text($mb_array[$i][mb_nick]);
                $recv_mb_id = $mb_array[$i][mb_id];
            }
        }
    }
}

// 본인통보
if ($mw_basic[cf_singo_writer]) {
    $tmp_row = sql_fetch(" select max(me_id) as max_me_id from $g4[memo_table] ");
    $me_id = $tmp_row[max_me_id] + 1;

    // 쪽지 INSERT
    $sql = " insert into $g4[memo_table] ( me_id, me_recv_mb_id, me_send_mb_id, me_send_datetime, me_memo ) ";
    $sql.= " values ( '$me_id', '$write[mb_id]', '$write[mb_id]', '$g4[time_ymdhis]', ";
    $sql.= " '게시물이 신고되었습니다.\n\n{$g4[url]}/{$g4[bbs]}/board.php?bo_table=$bo_table&wr_id=$parent_id$comment' ) ";
    sql_query($sql);

    // 실시간 쪽지 알림 기능
    $sql = " update $g4[member_table]
                set mb_memo_call = '$write[mb_id]'
              where mb_id = '$write[mb_id]' ";
    sql_query($sql);
}

$sql = "insert into $mw[singo_log_table] set bo_table = '$bo_table' ";
$sql.= ", wr_id = '$wr_id'";
$sql.= ", mb_id = '$member[mb_id]'";
$sql.= ", si_type = '$category'";
$sql.= ", si_memo = '$content'";
$sql.= ", si_datetime = '$g4[time_ymdhis]'";
$sql.= ", si_ip = '$_SERVER[REMOTE_ADDR]'";
sql_query($sql);

sql_query("update $write_table set wr_singo = wr_singo + 1 where wr_id = '$wr_id'");
$write[wr_singo]++;

if ($write[wr_singo] && $write[wr_singo] >= $mw_basic[cf_singo_number] && $mw_basic[cf_singo_write_secret])
{
    $html = $secret = $mail = '';
    if (strstr($write[wr_option], 'html1')) $html = 'html1';
    if (strstr($write[wr_option], 'html2')) $html = 'html2';
    //if (strstr($write[wr_option], 'secret')) $secret = 'secret';
    if (strstr($write[wr_option], 'mail')) $mail = 'mail';

    $secret = 'secret';

    sql_query("update $write_table set wr_option = '$html,$secret,$mail' where wr_id = '$wr_id'");
}

if ($write[wr_singo] && $write[wr_singo] >= $mw_basic[cf_singo_number])
{
    if ($mw_basic[cf_singo_id_block])
    {
        $mb = get_member($write[mb_id]);
        if (!$mb[mb_intercept_date]) {
            $mb_intercept_date = date("Ymd", $g4[server_time]);
            $mb_memo = "$mb[mb_memo]\n\n$mw_basic[cf_singo_number]회 신고에 의한 접근차단 : $g4[time_ymdhis]";
            sql_query("update $g4[member_table] set mb_level = '1', mb_intercept_date = '$mb_intercept_date', mb_memo = '$mb_memo' where mb_id='$write[mb_id]'");
        }
    }
}

alert_close("신고하였습니다.\\n\\n관심에 감사드립니다.");
