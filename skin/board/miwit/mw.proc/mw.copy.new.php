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

include_once("./_common.php");

// 게시판 관리자 이상 복사, 이동 가능
if ($is_admin != 'board' && $is_admin != 'group' && $is_admin != 'super') 
    die("false|게시판 관리자 이상 접근이 가능합니다.");

if (!$token or get_session("ss_delete_token") != $token) 
    die("false|토큰 에러로 실행 불가합니다.");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

// 원본 파일 디렉토리
$src_dir = "$g4[path]/data/file/$bo_table";

$save = array();
$save_count_write = 0;
$save_count_comment = 0;
$cnt = 0;

$move_bo_table = $bo_table;
$move_write_table = $write_table;

$src_dir = "$g4[path]/data/file/$bo_table"; // 원본 디렉토리
$dst_dir = "$g4[path]/data/file/$move_bo_table"; // 복사본 디렉토리

$count_write = 0;
$count_comment = 0;

$next_wr_num = get_next_num($move_write_table);
$nick = cut_str($member[mb_nick], $config[cf_cut_name]);

$sql = " insert into $move_write_table
            set wr_num            = '$next_wr_num',
                wr_reply          = '$write[wr_reply]',
                wr_is_comment     = '$write[wr_is_comment]',
                wr_comment        = '0',
                wr_comment_reply  = '$write[wr_comment_reply]',
                ca_name           = '".addslashes($write[ca_name])."',
                wr_option         = '$write[wr_option]',
                wr_subject        = '".addslashes($write[wr_subject])."',
                wr_content        = '".addslashes($write[wr_content])."',
                wr_link1          = '".addslashes($write[wr_link1])."',
                wr_link2          = '".addslashes($write[wr_link2])."',
                wr_link1_hit      = '0',
                wr_link2_hit      = '0',
                wr_hit            = '0',
                wr_good           = '0',
                wr_nogood         = '0',
                mb_id             = '$write[mb_id]',
                wr_password       = '$write[wr_password]',
                wr_name           = '".addslashes($write[wr_name])."',
                wr_email          = '".addslashes($write[wr_email])."',
                wr_homepage       = '".addslashes($write[wr_homepage])."',
                wr_datetime       = '$g4[time_ymdhis]',
                wr_last           = '$g4[time_ymdhis]',
                wr_ip             = '$write[wr_ip]',
                wr_1              = '".addslashes($write[wr_1])."',
                wr_2              = '".addslashes($write[wr_2])."',
                wr_3              = '".addslashes($write[wr_3])."',
                wr_4              = '".addslashes($write[wr_4])."',
                wr_5              = '".addslashes($write[wr_5])."',
                wr_6              = '".addslashes($write[wr_6])."',
                wr_7              = '".addslashes($write[wr_7])."',
                wr_8              = '".addslashes($write[wr_8])."',
                wr_9              = '".addslashes($write[wr_9])."',
                wr_10             = '".addslashes($write[wr_10])."' ";
sql_query($sql);

$insert_id = sql_insert_id();
$save_parent = $insert_id;
sql_query(" update $move_write_table set wr_parent = '$save_parent' where wr_id = '$insert_id' ");

$sql3 = " select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$write[wr_id]' order by bf_no ";
$result3 = sql_query($sql3);
for ($k=0; $row3 = sql_fetch_array($result3); $k++) 
{
    $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

    $filename = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $row3[bf_source]);

    shuffle($chars_array);
    $shuffle = implode("", $chars_array);

    $filename = abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr($shuffle,0,8).'_'.str_replace('%', '', urlencode(str_replace(' ', '_', $filename))); 

    if ($row3[bf_file]) 
    {
        // 원본파일을 복사하고 퍼미션을 변경
        @copy("$src_dir/$row3[bf_file]", "$dst_dir/$filename");
        @chmod("$dst_dir/$filename", 0606);
    }

    $sql = " insert into $g4[board_file_table] 
                set bo_table = '$move_bo_table', 
                    wr_id = '$insert_id', 
                    bf_no = '$row3[bf_no]', 
                    bf_source = '".addslashes($row3[bf_source])."', 
                    bf_file = '$filename', 
                    bf_download = '0', 
                    bf_content = '".addslashes($row3[bf_content])."',
                    bf_filesize = '$row3[bf_filesize]',
                    bf_width = '$row3[bf_width]',
                    bf_height = '$row3[bf_height]',
                    bf_type = '$row3[bf_type]',
                    bf_datetime = '$g4[time_ymdhis]' ";
    sql_query($sql);
}

$count_write++;

$sql = " update $move_write_table set ";
$sql.= "  wr_ccl = '$write[wr_ccl]' ";
$sql.= ", wr_singo = '$write[wr_singo]' ";
$sql.= ", wr_zzal = '$write[wr_zzal]' ";
$sql.= ", wr_related = '$write[wr_related]' ";
$sql.= ", wr_comment_ban = '$write[wr_comment_ban]' ";
$sql.= ", wr_contents_price = '$write[wr_contents_price]' ";
$sql.= ", wr_contents_domain = '$write[wr_contents_domain]' ";
//$sql.= ", wr_umz = '$write[wr_umz]' ";
$sql.= ", wr_subject_font = '$write[wr_subject_font]' ";
$sql.= ", wr_subject_color = '$write[wr_subject_color]' ";
$sql.= ", wr_anonymous = '$write[wr_anonymous]' ";
$sql.= ", wr_comment_hide = '$write[wr_comment_hide]' ";
$sql.= ", wr_read_level = '$write[wr_read_level]' ";
$sql.= ", wr_kcb_use = '$write[wr_kcb_use]' ";
$sql.= ", wr_qna_status = '$write[wr_qna_status]' ";
$sql.= ", wr_qna_point = '$write[wr_qna_point]' ";
$sql.= ", wr_qna_id = '$write[wr_qna_id]' ";
$sql.= " where wr_id = '$insert_id' ";
sql_query($sql);

// 리워드
$tmp = sql_fetch("select * from $mw[reward_table] where bo_table = '$bo_table' and wr_id = '$write[wr_id]'");
if ($tmp) {
    $sql_common = "bo_table     = '$move_bo_table'";
    $sql_common.= ", wr_id      = '$insert_id'";
    $sql_common.= ", re_site    = '".addslashes($tmp[re_site])."'";
    $sql_common.= ", re_point   = '$tmp[re_point]'";
    $sql_common.= ", re_url     = '".addslashes($tmp[re_url])."'";
    $sql_common.= ", re_edate   = '$tmp[re_edate]'";
    sql_query("insert into $mw[reward_table] set $sql_common, re_status = '1'");
}

// 설문
$tmp = sql_fetch("select * from $mw[vote_table] where bo_table = '$bo_table' and wr_id = '$write[wr_id]'");
if ($tmp) {
    $vt_id = $tmp[vt_id];

    $sql = "insert into $mw[vote_table] set bo_table = '$move_bo_table'";
    $sql.= ", wr_id = '$insert_id' ";
    $sql.= ", vt_edate = '$tmp[vt_edate]' ";
    $sql.= ", vt_total = '$tmp[vt_total]' ";
    $sql.= ", vt_point = '$tmp[vt_point]' ";
    sql_query($sql);

    $insert_vt_id = sql_insert_id();

    $qry = sql_query("select * from $mw[vote_item_table] where vt_id = '$vt_id' order by vt_num");
    while ($tmp = sql_fetch_array($qry)) {
        sql_query("insert into $mw[vote_item_table] set vt_id = '$insert_vt_id', vt_num = '$tmp[vt_num]', vt_item = '$tmp[vt_item]', vt_hit = '$tmp[vt_hit]'");
    }
}

sql_query(" update $g4[board_table] set bo_count_write   = bo_count_write   + 1 where bo_table = '$move_bo_table' ");
sql_query(" insert into $g4[board_new_table] ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '$bo_table', '$insert_id', '$insert_id', '$g4[time_ymdhis]', '$write[mb_id]' ) ");

echo "true|$insert_id";
exit;

