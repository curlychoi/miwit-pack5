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

$is_head = false;

// CCL 정보 업데이트
$wr_ccl = "";
if ($wr_ccl_by == "by") { $wr_ccl .= "by"; }
if ($wr_ccl_nc == "nc") { $wr_ccl .= $wr_ccl ? "-": ""; $wr_ccl .= "nc"; }
if ($wr_ccl_nd == "nd") { $wr_ccl .= $wr_ccl ? "-": ""; $wr_ccl .= "nd"; }
if ($wr_ccl_nd == "sa") { $wr_ccl .= $wr_ccl ? "-": ""; $wr_ccl .= "sa"; }
if ($wr_ccl)
    sql_query("update $write_table set wr_ccl = '$wr_ccl' where wr_id = '$wr_id'");

// 질문 업데이트
if ($mw_basic[cf_attribute] == 'qna')
{
    if ($w == '') {
        sql_query("update $write_table set wr_qna_point = '$wr_qna_point', wr_qna_status = '0' where wr_id = '$wr_id'");
        insert_point($mb_id, $wr_qna_point*-1, "질문 포인트", $bo_table, $wr_id, '@qna');
    }
    else if ($is_admin && $w == 'u' && $write[wr_qna_point] != $wr_qna_point) {
        delete_point($mb_id, $bo_table, $wr_id, '@qna');
        sql_query("update $write_table set wr_qna_point = '$wr_qna_point', wr_qna_status = '0' where wr_id = '$wr_id'");
        insert_point($mb_id, $wr_qna_point*-1, "질문 포인트", $bo_table, $wr_id, '@qna');
    }

    if (!$wr_qna_status) $wr_qna_status = '0';
    if (!$wr_qna_status && $notice && $is_admin) $wr_qna_status = '1';
    if ($is_admin)
        sql_query("update $write_table set wr_qna_status = '$wr_qna_status' where wr_id = '$wr_id'");

    if ($is_admin && $w == 'u' && $write[wr_qna_status] > 0 && $wr_qna_status == '0') {
        delete_point($write[mb_id], $bo_table, $wr_id, '@qna-hold');
        if ($write[wr_qna_id]) {
            $tmp = sql_fetch("select mb_id from $write_table where wr_id = '$write[wr_qna_id]' ");
            delete_point($tmp[mb_id], $bo_table, $wr_id, '@qna-choose');
        }
        sql_query("update $write_table set wr_qna_id = '0' where wr_id = '$wr_id'");
    }
    $write_run_time = mw_time_log($write_run_time, "[write] qna");
}

// 실명인증
if ($mw_basic[cf_kcb_post] && $mw_basic[cf_kcb_post_level] <= $member[mb_level]) {
    sql_query("update $write_table set wr_kcb_use = '$wr_kcb_use' where wr_id = '$wr_id'");
    $write_run_time = mw_time_log($write_run_time, "[write] kcb_post");
}

// 짤방 업데이트
if ($mw_basic[cf_zzal]) {
    sql_query("update $write_table set wr_zzal = '$wr_zzal' where wr_id = '$wr_id'");
    $write_run_time = mw_time_log($write_run_time, "[write] wr_zzal");
}

// 관련글 업데이트
if ($mw_basic[cf_related]) {
    sql_query("update $write_table set wr_related = '$wr_related' where wr_id = '$wr_id'");
    $write_run_time = mw_time_log($write_run_time, "[write] wr_related");
}

// 코멘트 허락
if ($mw_basic[cf_comment_ban] && $mw_basic[cf_comment_ban_level] <= $member[mb_level]) {
    sql_query("update $write_table set wr_comment_ban = '$wr_comment_ban' where wr_id = '$wr_id'");
    $write_run_time = mw_time_log($write_run_time, "[write] wr_comment_ban");
}

// 로그남김
if ($w == "u" && $mw_basic[cf_post_history]) {
    $wr_name2 = $board[bo_use_name] ? $member[mb_name] : $member[mb_nick];
    $sql = "insert into $mw[post_history_table]
               set bo_table = '$bo_table'
                   ,wr_id = '$wr_id'
                   ,wr_parent = '$write[wr_parent]'
                   ,mb_id = '$member[mb_id]'
                   ,ph_name = '$wr_name2'
                   ,ph_option = '$write[wr_option]'
                   ,ph_subject = '".addslashes($write[wr_subject])."'
                   ,ph_content = '".addslashes($write[wr_content])."'
                   ,ph_ip = '$_SERVER[REMOTE_ADDR]'
                   ,ph_datetime = '$g4[time_ymdhis]'";
    sql_query($sql);
    $write_run_time = mw_time_log($write_run_time, "[write] post_history");
}

// 지업로더
if ($mw_basic[cf_guploader] == "1" && $is_member) // 싱글모드
{
    $sql = "select * from $mw[guploader_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and bf_ip = '$_SERVER[REMOTE_ADDR]' order by bf_no";
    $qry = sql_query($sql, false);
    for ($i=0; $row=sql_fetch_array($qry); $i++) {
        $source = "$g4[path]/data/guploader/$row[bf_file]";
        $dest = "$g4[path]/data/file/$bo_table/$row[bf_file]";
        @copy($source, $dest);
        @unlink($source);
        sql_query("insert into $g4[board_file_table]
                   set bo_table = '$bo_table'
                     , wr_id = '$wr_id'
                     , bf_no = '$i'
                     , bf_source = '$row[bf_source]'
                     , bf_file = '$row[bf_file]'
                     , bf_filesize = '$row[bf_filesize]'
                     , bf_width = '$row[bf_width]'
                     , bf_height = '$row[bf_height]'
                     , bf_type = '$row[bf_type]'
                     , bf_datetime = '$row[bf_datetime]'");
    }
    sql_query("delete from $mw[guploader_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and bf_ip = '$_SERVER[REMOTE_ADDR]'", false);
    $write_run_time = mw_time_log($write_run_time, "[write] guploader");
}

// 원본 강제 리사이징
/*
if ($mw_basic[cf_original_width] && $mw_basic[cf_original_height]) {
    $sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_width > 0  order by bf_no";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $file = "$file_path/$row[bf_file]";
        $size = getImageSize($file);
        if ($size[0] > $mw_basic[cf_original_width] || $mw_basic[cf_original_height] < $size[1]) {
            mw_make_thumbnail($mw_basic[cf_original_width], $mw_basic[cf_original_height], $file, $file, true);
            $size = getImageSize($file);
        }
        sql_query("update $g4[board_file_table] set bf_width = '$size[0]', bf_height = '$size[1]',
            bf_filesize = '".filesize($file)."'
            where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$row[bf_no]'");
    }
}
*/

// 오토 로테이트
if ($mw_basic['cf_image_auto_rotate'])
{
    $sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_width > 0  order by bf_no";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry))
        mw_image_auto_rotate("$file_path/$row[bf_file]");

    $write_run_time = mw_time_log($write_run_time, "[write] auto_rotate");
}

// 원본 강제 리사이징 (첨부파일)
if ($mw_basic[cf_resize_original]) {
    $sql = " select * from $g4[board_file_table] ";
    $sql.= " where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_width > 0  order by bf_no";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $file = "$file_path/$row[bf_file]";
        $size = getImageSize($file);
        //if ($size[0] > $mw_basic[cf_resize_original] || $mw_basic[cf_resize_original] < $size[1]) {
            mw_make_thumbnail($mw_basic[cf_resize_original], $mw_basic[cf_resize_original], $file, $file, true);
            $size = getImageSize($file);
        //}
        sql_query("update $g4[board_file_table] set bf_width = '$size[0]', bf_height = '$size[1]',
            bf_filesize = '".filesize($file)."'
            where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$row[bf_no]'");
    }

    // 원본 강제 리사이징 (에디터)
    preg_match_all("/<img.*src=\\\"(.*)\\\"/iUs", stripslashes($_POST[wr_content]), $matchs);
    for ($i=0, $m=count($matchs[1]); $i<$m; ++$i) {
        $mat = $matchs[1][$i];
        if (strstr($mat, "mw.basic.comment.image")) $mat = '';
        if (strstr($mat, "mw.emoticon")) $mat = '';
        if (preg_match("/cheditor[0-9]\/icon/i", $mat)) $mat = '';
        if ($mat) {
            //$mat = str_replace($g4[url], "..", $mat);
            $mat = preg_replace("/(http:\/\/.*)\/data\//i", "../data/", $mat);
            if (file_exists($mat)) {
                $file = $mat;
                $size = getImageSize($file);
                if ($size[0] > $mw_basic[cf_resize_original] || $mw_basic[cf_resize_original] < $size[1]) {
                    mw_make_thumbnail($mw_basic[cf_resize_original], $mw_basic[cf_resize_original], $file, $file, true);
                }
            }
        }
    }
    $write_run_time = mw_time_log($write_run_time, "[write] resize_original");
}
     

// 첨부이미지 사이즈 사용자 변경
if ($mw_basic[cf_change_image_size] && $member[mb_level] >= $mw_basic[cf_change_image_size_level] && $change_image_size) {
    $sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_width > 0  order by bf_no";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $file = "$file_path/$row[bf_file]";
        mw_make_thumbnail($change_image_size, $change_image_size, $file, $file, true);
        $size = getImageSize($file);
        sql_query("update $g4[board_file_table] set bf_width = '$size[0]', bf_height = '$size[1]',
            bf_filesize = '".filesize($file)."'
            where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$row[bf_no]'");
    }
    $write_run_time = mw_time_log($write_run_time, "[write] resize_custom");
}
// 썸네일
$thumb_file = mw_thumb_jpg($thumb_path.'/'.$wr_id);
$thumb2_file = mw_thumb_jpg($thumb2_path.'/'.$wr_id);
$thumb3_file = mw_thumb_jpg($thumb3_path.'/'.$wr_id);
$thumb4_file = mw_thumb_jpg($thumb4_path.'/'.$wr_id);
$thumb5_file = mw_thumb_jpg($thumb5_path.'/'.$wr_id);

// 썸네일 생성
$is_thumb = mw_make_thumbnail_row($bo_table, $wr_id, $_POST['wr_content'], $mw_basic['cf_image_remote_save']);
$write_run_time = mw_time_log($write_run_time, "[write] mw_make_thumbnail_row");

// 원본 워터마크
for ($i=0, $m=sizeof($watermark_files); $i<$m; $i++) {// 기존 원터마크 파일 삭제
    unlink($watermark_files[$i]);
    $write_run_time = mw_time_log($write_run_time, "[write] unlink watermark_files");
}

if ($mw_basic[cf_watermark_use] && is_mw_file($mw_basic[cf_watermark_path]))
{
    $sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_width > 0  order by bf_no";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $watermark_file = mw_watermark_file("$file_path/$row[bf_file]");
        if ($mw_basic['cf_image_outline']) {
            mw_image_outline($watermark_file, null, $mw_basic['cf_image_outline_color']);
        }
    }
    $write_run_time = mw_time_log($write_run_time, "[write] watermark and outline");
}

// 생성된 썸네일이 없고, 유튜브 링크를 사용할 경우
// 유튜브 섬네일 가져오기
if (!$is_thumb) {// && !is_mw_file($thumb_file)) {
    if (preg_match("/youtu/i", $wr_link1)) mw_get_youtube_thumb($wr_id, $wr_link1);
    else if (preg_match("/youtu/i", $wr_link2)) mw_get_youtube_thumb($wr_id, $wr_link2);
    else if (preg_match("/vimeo/i", $wr_link1)) mw_get_vimeo_thumb($wr_id, $wr_link1);
    else if (preg_match("/vimeo/i", $wr_link2)) mw_get_vimeo_thumb($wr_id, $wr_link2);
    else {
        $pt = mw_youtube_pattern($wr_content);
        if ($pt) {
            preg_match($pt, stripslashes($wr_content), $mat);
            mw_get_youtube_thumb($wr_id, $mat[1]);
        }
        else {
            $pt = mw_vimeo_pattern($wr_content);
            if ($pt) {
                preg_match($pt, stripslashes($wr_content), $mat);
                mw_get_vimeo_thumb($wr_id, $mat[1]);
            }
        }
    }
    $write_run_time = mw_time_log($write_run_time, "[write] thumb youtube");
}

// 메일발송 사용 (수정글은 발송하지 않음)
if (!($w == "u" || $w == "cu") && $config[cf_email_use])
{
    $emails = explode("\n", $mw_basic[cf_email]);

    if (count($emails) > 0)
    {
        $wr_subject = get_text(stripslashes($wr_subject));

        $tmp_html = 0;
        if (strstr($html, "html1"))
            $tmp_html = 1;
        else if (strstr($html, "html2"))
            $tmp_html = 2;

        $wr_content = conv_content(stripslashes($_POST[wr_content]), $tmp_html);

        $warr = array( ""=>"입력", "u"=>"수정", "r"=>"답변", "c"=>"코멘트", "cu"=>"코멘트 수정" );
        $str = $warr[$w];

        $subject = "'{$board[bo_subject]}' 게시판에 {$str}글이 올라왔습니다.";
        //$link_url = "$g4[url]/$g4[bbs]/board.php?bo_table=$bo_table&wr_id=$wr_id&$qstr";
        $link_url = mw_seo_url($bo_table, $wr_id);

        include_once("$g4[path]/lib/mailer.lib.php");

        ob_start();
        include ("$g4[bbs_path]/write_update_mail.php");
        $content = ob_get_contents();
        ob_end_clean();

        foreach ($emails as $email)
        {
            $email = trim($email);
            if (!$email) continue;
            if ($email == "test@test.com") continue;
            mailer($wr_name, $wr_email, $email, $subject, $content, 1);
	    //write_log("$g4[path]/data/mail.log", "$email\n");
        }
    }
    $write_run_time = mw_time_log($write_run_time, "[write] mail");
}

// SMS 전송
if ($w == "" && $mw_basic[cf_sms_id] && $mw_basic[cf_sms_pw] && trim($mw_basic[cf_hp]) && $is_admin != "super")
{
    $strDest = array();
    $hps = explode("\r\n", $mw_basic[cf_hp]);
    foreach ($hps as $hp) {
        $hp = mw_get_hp($hp, 0);
        if (trim($hp)) {
            $strDest[] = $hp;
        }
    }
    $strCallBack = str_replace("-", "", $mw_basic[cf_hp_reply]);
    if (!$strCallBack)
        $strCallBack = '0000';

    $strData = "[".cut_str($config['cf_title'], 20)."]";
    $strData.= "{$board[bo_subject]} 게시판에 {$wr_name} 님이 글을 올리셨습니다.";

    $umz = umz_get_url(mw_seo_url($bo_table, $wr_id));
    if ($umz) {
        sql_query("update $write_table set wr_umz = '$umz' where wr_id = '$wr_id'");   
        $strData .= "\n$umz";
    }
    include("$board_skin_path/mw.proc/mw.proc.sms.php");
    $write_run_time = mw_time_log($write_run_time, "[write] sms");
}

// 글등록 쪽지 알림
if ($w == "" && trim($mw_basic[cf_memo_id]))// && $is_admin != "super")
{
    $me_memo = "{$board[bo_subject]} 게시판에 [{$wr_name}] 님이 글을 올리셨습니다.\n\n";
    $me_memo.= mw_seo_url($bo_table, $wr_id);

    $list = explode(",", $mw_basic[cf_memo_id]);
    for ($i=0, $m=count($list); $i<$m; $i++) {
        $memo_id = trim($list[$i]);
        if (!$memo_id) continue;

        $tmp_row = sql_fetch(" select max(me_id) as max_me_id from $g4[memo_table] ");
        $me_id = $tmp_row[max_me_id] + 1;

        // 쪽지 INSERT
        $sql = " insert into $g4[memo_table]
                        ( me_id, me_recv_mb_id, me_send_mb_id, me_send_datetime, me_memo )
                 values ( '$me_id', '{$memo_id}', '$member[mb_id]', '$g4[time_ymdhis]', '$me_memo' ) ";
        sql_query($sql);

        // 실시간 쪽지 알림 기능
        $sql = " update $g4[member_table]
                    set mb_memo_call = '$member[mb_id]'
                  where mb_id = '$memo_id' ";
        sql_query($sql);
    }
    $write_run_time = mw_time_log($write_run_time, "[write] memo");
}

if ($mw_basic[cf_type] == 'desc' && $mw_basic[cf_desc_use] && $mw_basic[cf_desc_use] <= $member[mb_level]) {
    $sql = " update $write_table set ";
    $sql.= " wr_contents_preview = '$wr_contents_preview' ";
    $sql.= " where wr_id = '$wr_id' ";
    sql_query($sql);
    $write_run_time = mw_time_log($write_run_time, "[write] wr_contents_preview ");
}

//컨텐츠 가격 및 사용도메인
if ($mw_basic[cf_contents_shop]) {
    $sql = " update $write_table set ";
    $sql.= "  wr_contents_preview = '$wr_contents_preview' ";
    $sql.= " ,wr_contents_price = '$wr_contents_price' ";
    $sql.= " ,wr_contents_domain = '$wr_contents_domain' ";
    $sql.= " where wr_id = '$wr_id' ";
    sql_query($sql);
    $write_run_time = mw_time_log($write_run_time, "[write] wr_contents_price");
}

// 제목 스타일
if ($mw_basic[cf_subject_style] && $mw_basic[cf_subject_style_level] <= $member[mb_level]) {
    $sql = "update $write_table set ";
    $sql.= "   wr_subject_font = '$wr_subject_font' ";
    if ($wr_subject_color != $mw_basic['cf_subject_style_color_default'])
        $sql.= " , wr_subject_color = '$wr_subject_color' ";
    $sql.= " , wr_subject_bold = '$wr_subject_bold' ";
    $sql.= " where wr_id = '$wr_id'";
    sql_query($sql);
    $write_run_time = mw_time_log($write_run_time, "[write] subject style");
}

// 퀴즈 
if ($mw_basic[cf_quiz] && $mw_basic[cf_quiz_level] <= $member[mb_level] && $w == '' && $qz_id) {
    sql_query(" update $mw_quiz[quiz_table] set wr_id = '$wr_id' where qz_id = '$qz_id' ");
    $write_run_time = mw_time_log($write_run_time, "[write] quiz_update");
}

// 시험문제
if ($mw_basic['cf_exam'] && $mw_basic['cf_exam_level'] <= $member['mb_level'] && $w == '' && $ex_id) {
    include("{$exam_path}/write_update.skin.php");
    $write_run_time = mw_time_log($write_run_time, "[write] exam_update");
}

// 게시판 배너
if ($mw_basic['cf_bbs_banner']) {
    include("{$bbs_banner_path}/write_update.skin.php");
    $write_run_time = mw_time_log($write_run_time, "[write] bbs_banner_update");
}

// 설문 
if ($mw_basic[cf_vote] && $mw_basic[cf_vote_level] <= $member[mb_level])
{
    if ($vt_sdate && $vt_stime) 
        $vt_sdate = "$vt_sdate $vt_stime:00:00";
    else
        $vt_sdate = '0000-00-00 00:00:00';

    if ($vt_edate && $vt_etime)
        $vt_edate = "$vt_edate $vt_etime:00:00";
    else
        $vt_edate = '0000-00-00 00:00:00';

    $tmp = array();
    $tmp2 = array();
    if (strstr($vt_item[0], "|")) {
        $tmp2 = $vt_item;
        array_shift($tmp2);
        $tmp = array_map("trim", explode("|", $vt_item[0]));
        $vt_item = array_merge($tmp, $tmp2);
    }

    $tmp = array();
    for ($i=0, $m=sizeof($vt_item); $i<$m; $i++) {
        if (trim($vt_item[$i])) {
            $tmp[] = strip_tags(trim($vt_item[$i]));
        }
    }
    $vt_item = $tmp;
    if ($w == "" && sizeof($vt_item)) {
        $sql = "insert into $mw[vote_table] set bo_table = '$bo_table', wr_id = '$wr_id', vt_sdate = '$vt_sdate', vt_edate = '$vt_edate', vt_point = '$vt_point', vt_multi = '$vt_multi', vt_comment = '$vt_comment' ";
        $qry = sql_query($sql);
        $vt_id = sql_insert_id();

        for ($i=0, $m=sizeof($vt_item); $i<$m; $i++) {
            $sql = "insert into $mw[vote_item_table] set vt_id = '$vt_id', vt_num = '$i', vt_item = '{$vt_item[$i]}'";
            $qry = sql_query($sql);
        }
    }
    //else if ($w == "u" && sizeof($vt_item)) {
    else if ($w == "u") {

        $sql = "select vt_id from $mw[vote_table] where bo_table = '$bo_table' and wr_id = '$wr_id'";
        $row = sql_fetch($sql);
        if (!$row) {
            $sql = "insert into $mw[vote_table] set bo_table = '$bo_table', wr_id = '$wr_id', vt_sdate = '$vt_sdate', vt_edate = '$vt_edate', vt_point = '$vt_point', vt_multi = '$vt_multi', vt_comment = '$vt_comment' ";
            $qry = sql_query($sql);
            $vt_id = sql_insert_id();
        } else {
            $vt_id = $row[vt_id];

            $sql = "update $mw[vote_table] set vt_sdate = '$vt_sdate', vt_edate = '$vt_edate', vt_point = '$vt_point', vt_multi = '$vt_multi', vt_comment = '$vt_comment' where bo_table = '$bo_table' and wr_id = '$wr_id'";
            $qry = sql_query($sql);
        }

        for ($i=0, $m=sizeof($vt_item); $i<$m; $i++) {
            $sql = "select * from $mw[vote_item_table] where vt_id = '$vt_id' and vt_num = '$i'";
            $row = sql_fetch($sql);

            if ($row) {
                $sql = "update $mw[vote_item_table] set vt_item = '{$vt_item[$i]}' where vt_id = '$vt_id' and vt_num = '$i' ";
                $qry = sql_query($sql);
            } else {
                $sql = "insert into $mw[vote_item_table] set vt_id = '$vt_id', vt_num = '$i', vt_item = '{$vt_item[$i]}'";
                $qry = sql_query($sql);
            }
        }
        $sql = "delete from $mw[vote_item_table] where vt_id = '$vt_id' and vt_num >= '$i'";
        $qry = sql_query($sql);

        $sql = "delete from $mw[vote_log_table] where vt_id = '$vt_id' and vt_num >= '$i'";
        $qry = sql_query($sql);

        if (!$i) sql_query("delete from $mw[vote_table] where vt_id = '$vt_id'");
    }
    else if (!sizeof($vt_item)) {
        $sql = "delete from $mw[vote_table] where vt_id = '$vt_id'";
        $qry = sql_query($sql);

        $sql = "delete from $mw[vote_item_table] where vt_id = '$vt_id'";
        $qry = sql_query($sql);

        $sql = "delete from $mw[vote_log_table] where vt_id = '$vt_id'";
        $qry = sql_query($sql);
    }
    $write_run_time = mw_time_log($write_run_time, "[write] vote update");
}

// 리워드
if ($mw_basic[cf_reward])
{
    $sql_common = "bo_table = '$bo_table'";
    $sql_common.= ", wr_id = '$wr_id'";
    $sql_common.= ", re_company = '$re_company'";
    $sql_common.= ", re_site = '$re_site'";
    $sql_common.= ", re_point = '$re_point'";
    $sql_common.= ", re_url = '$re_url'";
    $sql_common.= ", re_edate = '$re_edate'";

    if ($w == "") {
        $sql = "insert into $mw[reward_table] set $sql_common, re_status = '1'";
        $qry = sql_query($sql);
    } else {
        $sql = "update $mw[reward_table] set $sql_common, re_status = '$re_status' where bo_table = '$bo_table' and wr_id = '$wr_id'";
        $qry = sql_query($sql);
    }
    $write_run_time = mw_time_log($write_run_time, "[write] reward update");
}

// 익명
if ($mw_basic[cf_anonymous]) {
    sql_query(" update $write_table set wr_anonymous = '$wr_anonymous' where wr_id = '$wr_id' ");

    if ($mw_basic[cf_anonymous_nopoint] && $wr_anonymous) {
        if ($w == '') {
            delete_point($member[mb_id], $bo_table, $wr_id, '쓰기');
        }
        else if ($w == 'r') {
            delete_point($member[mb_id], $bo_table, $wr_id, '쓰기');
        }
    }
    $write_run_time = mw_time_log($write_run_time, "[write] anonymous point");
}

// 글읽기 레벨
if ($mw_basic[cf_read_level] && $mw_basic[cf_read_level_own] <= $member[mb_level]) {
    sql_query(" update $write_table set wr_read_level = '$wr_read_level' where wr_id = '$wr_id' ");
    $write_run_time = mw_time_log($write_run_time, "[write] update read_level");
}

// 모바일
if ($w == '' || $w == 'r') {
    if (mw_agent_mobile()) {
        sql_query("update $write_table set wr_is_mobile = '1' where wr_id = '$wr_id'", false);
    }
    $write_run_time = mw_time_log($write_run_time, "[write] update is_mobile");
}

// 소셜커머스
if ($mw_basic[cf_social_commerce]) {
    include("$social_commerce_path/write_update.skin.php");
    $write_run_time = mw_time_log($write_run_time, "[write] include social_commerce update");
}


// 재능마켓
if ($mw_basic[cf_talent_market]) {
    include("$talent_market_path/write_update.skin.php");
    $write_run_time = mw_time_log($write_run_time, "[write] include telent_market update");
}

// 마케팅DB
if ($mw_basic[cf_marketdb]) {
    include("$marketdb_path/write_update.skin.php");
    $write_run_time = mw_time_log($write_run_time, "[write] include marketdb update");
}

// 구글지도
if ($mw_basic[cf_google_map]) {
    sql_query(" update $write_table set wr_google_map = '$wr_google_map' where wr_id = '$wr_id' ");
    $write_run_time = mw_time_log($write_run_time, "[write] update google_map");
}

// 게시물별 링크 게시판
if ($mw_basic[cf_link_write] && $mw_basic[cf_link_write] <= $member[mb_level]) {
    sql_query(" update $write_table set wr_link_write = '$wr_link_write' where wr_id = '$wr_id' ");
    $write_run_time = mw_time_log($write_run_time, "[write] update link_write");
}

// 링크 타겟
if ($mw_basic[cf_link_target_level] && $mw_basic[cf_link_target_level] <= $member[mb_level]) {
    if ($wr_link1) sql_query(" update $write_table set wr_link1_target = '$wr_link1_target' where wr_id = '$wr_id' ");
    if ($wr_link2) sql_query(" update $write_table set wr_link2_target = '$wr_link2_target' where wr_id = '$wr_id' ");
    $write_run_time = mw_time_log($write_run_time, "[write] update link_target");
}

// 자동폭파
if ($mw_basic[cf_bomb_level] && $mw_basic[cf_bomb_level] <= $member[mb_level]) {
    $bm_datetime = '';

    if (checkdate($bm_month, $bm_day, $bm_year))
        $bm_datetime = "$bm_year-$bm_month-$bm_day $bm_hour:$bm_minute:00";

    if ($mw_basic[cf_bomb_time] && !$is_admin)
        $bm_datetime = date("Y-m-d H:i:00", $g4[server_time] + ($mw_basic[cf_bomb_time]*60*60));

    if ($bm_datetime) {
        if ($g4[server_time] > strtotime($bm_datetime))
            alert("자동폭파는 미래의 시간으로 설정해주세요.");

        if (!$mw_basic[cf_bomb_time]) {
            if ($mw_basic[cf_bomb_days_min] && ($g4[server_time] + ($mw_basic[cf_bomb_days_min]*60*60*24) > strtotime($bm_datetime)))
                alert("자동폭파는 최소 $mw_basic[cf_bomb_days_min]일 이상 설정 가능합니다.");

            if ($mw_basic[cf_bomb_days_max] && ($g4[server_time] + ($mw_basic[cf_bomb_days_max]*60*60*24) < strtotime($bm_datetime)))
                alert("자동폭파는 최대 $mw_basic[cf_bomb_days_max]일까지만 설정 가능합니다.");
        }

        $sql = "replace into $mw[bomb_table] set ";
        $sql.= "   bo_table = '$bo_table' ";
        $sql.= " , wr_id = '$wr_id' ";
        $sql.= " , bm_datetime = '$bm_datetime' ";
        $sql.= " , bm_log = '$bm_log' ";
        if ($is_admin == 'super')
            $sql.= " , bm_move_table = '$bm_move_table' ";
        sql_query($sql);
    }
    else {
        sql_query("delete from $mw[bomb_table] where bo_table = '$bo_table' and wr_id = '$wr_id'");
    }
    $write_run_time = mw_time_log($write_run_time, "[write] update bomb");
}

// 예약이동
if ($mw_basic[cf_move_level] && $mw_basic[cf_move_level] <= $member[mb_level]) {
    if (checkdate($mv_month, $mv_day, $mv_year)) {
        $mv_datetime = "$mv_year-$mv_month-$mv_day $mv_hour:$mv_minute:00";
        $sql = " replace into $mw[move_table] set ";
        $sql.= " bo_table = '$bo_table', wr_id = '$wr_id', mv_cate = '$mv_cate', mv_notice = '$mv_notice', mv_datetime = '$mv_datetime'";
        $qry = sql_query($sql);
    } else {
        sql_query("delete from $mw[move_table] where bo_table = '$bo_table' and wr_id = '$wr_id'");
    }
    $write_run_time = mw_time_log($write_run_time, "[write] move");
}

// 라이트박스
if ($mw_basic['cf_lightbox'] && $mw_basic['cf_lightbox'] <= $member['mb_level']) {
    sql_query(" update $write_table set wr_lightbox = '$wr_lightbox' where wr_id = '$wr_id' ");

    if ($wr_lightbox) {
        $files = glob("{$lightbox_path}/{$wr_id}-*");
        array_map('unlink', $files);
        mw_make_lightbox();
    }

    $write_run_time = mw_time_log($write_run_time, "[write] lightbox");
}
 
// 비회원 이름 쿠키 저장
if (!$is_member) {
    set_cookie("mw_cookie_name", $wr_name, -1*$g4[server_time]);
    set_cookie("mw_cookie_email", $wr_email, -1*$g4[server_time]);
    set_cookie("mw_cookie_homepage", $wr_homepage, -1*$g4[server_time]);
}

// 열람 패스워드
if ($mw_basic['cf_key_level'] && $mw_basic['cf_key_level'] <= $member['mb_level']) {
    if ($wr_key_password) {
        $wr_key_password = sql_password($wr_key_password);
        sql_query("update $write_table set wr_key_password = '$wr_key_password' where wr_id = '$wr_id' ");
    }
    else if ($wr_key_password_del) {
        sql_query("update $write_table set wr_key_password = '' where wr_id = '$wr_id' ");
    }
    $write_run_time = mw_time_log($write_run_time, "[write] update key_password");
}

// 숨김링크
if ($mw_basic['cf_hidden_link'] && $mw_basic['cf_hidden_link'] <= $member['mb_level']) {
    sql_query("update {$write_table} set wr_hidden_link1 = '{$wr_hidden_link1}' where wr_id = '{$wr_id}' ");
    sql_query("update {$write_table} set wr_hidden_link2 = '{$wr_hidden_link2}' where wr_id = '{$wr_id}' ");
    $write_run_time = mw_time_log($write_run_time, "[write] update hidden_link");
}

if ($mw_basic['cf_include_write_update'] && is_mw_file($mw_basic['cf_include_write_update'])) {
    include($mw_basic['cf_include_write_update']);
    $write_run_time = mw_time_log($write_run_time, "[write] include write_update");
}

// 일반회원 공지글 수정시 공지 내려가는 현상 보완 (그누보드 버그)
if ($is_notice)
    sql_query("update {$g4['board_table']} set bo_notice = '{$board['bo_notice']}' where bo_table = '{$bo_table}' ");

