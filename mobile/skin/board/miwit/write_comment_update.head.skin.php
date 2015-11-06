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

$wr_content = mw_spelling($wr_content);

// 컨텐츠샵 멤버쉽
if (function_exists("mw_cash_is_membership")) {
    $is_membership = @mw_cash_is_membership($member[mb_id], $bo_table, "mp_comment");
    if ($is_membership == "no")
        ;
    else if ($is_membership != "ok")
        mw_cash_alert_membership($is_membership);
        //alert("$is_membership 회원만 이용 가능합니다.");
}

$is_comment_write = false;
if ($member[mb_level] >= $board[bo_comment_level]) 
    $is_comment_write = true;

if ($is_comment_write) {
    if ($mw_basic[cf_comment_ban] && $write[wr_comment_ban]) {
        $is_comment_write = false;
    }
}

if (!$is_comment_write)
    alert("코멘트를 작성할 수 없습니다.");

// 코멘트 작성 기간
if ($w != 'cu' && $mw_basic[cf_comment_period] > 0) {
    if ($g4[server_time] - strtotime($write[wr_datetime]) > 60*60*24*$mw_basic[cf_comment_period]) {
        alert("작성한지 $mw_basic[cf_comment_period]일이 지난 게시물에는 코멘트를 작성할 수 없습니다.");
    }
}

// 자동치환권한
if ($mw_basic[cf_replace_word] > $member[mb_level]) { 
    if (strstr($wr_subject, "{닉네임}") || strstr($wr_content, "{닉네임}")) {
        alert("{닉네임} 코드를 사용하실 수 없습니다.");
    }
    if (strstr($wr_subject, "{별명}") || strstr($wr_content, "{별명}")) {
        alert("{별명} 코드를 사용하실 수 없습니다.");
    }
}

// 댓글작성 조건 
if (($w == "" || $w == "c") && $mw_basic[cf_comment_point] && !$is_admin) {
    if ($member[mb_point] < $mw_basic[cf_comment_point]) {
        alert("이 게시판은 $mw_basic[cf_comment_point] 포인트 이상 소지자만 코멘트 작성이 가능합니다.");
    }
}
if (($w == "" || $w == "c") && $mw_basic[cf_comment_register] && !$is_admin) {
    $gap = ($g4[server_time] - strtotime($member[mb_datetime])) / (60*60*24);
    if ($gap < $mw_basic[cf_comment_register]) {
        alert("이 게시판은 가입후 $mw_basic[cf_comment_register] 일이 지나야 코멘트 작성이 가능합니다.");
    }
}

// 댓글작성 제한
if (($w != "cu") && $mw_basic[cf_comment_day] && $mw_basic[cf_comment_day_count] && !$is_admin) {
    $old = date("Y-m-d 00:00:00", $g4[server_time]-((60*60*24)*($mw_basic[cf_comment_day]-1)));
    $sql = "select count(wr_id) as cnt from $write_table ";
    $sql.= " where wr_is_comment = '1' ";
    $sql.= "   and wr_datetime between '$old' and '$g4[time_ymd] 23:59:59'";
    if ($mw_basic[cf_comment_day_ip])
        $sql.= "   and wr_ip = '$_SERVER[REMOTE_ADDR]' ";
    else
        $sql.= "   and mb_id = '$member[mb_id]' ";
    $row = sql_fetch($sql);

    if ($row[cnt] >= $mw_basic[cf_comment_day_count]) {
        alert("이 게시판은 $mw_basic[cf_comment_day]일에 $mw_basic[cf_comment_day_count]번만 코멘트 작성이 가능합니다.");
    }
}

if ($w != "cu" && $mw_basic[cf_comment_write_count]) {
    $sql = " select count(*) as cnt from $write_table where wr_num = '$write[wr_num]' and wr_is_comment = '1' ";
    if ($board[bo_comment_level] == 1 && !$is_member)
        $sql.= " and wr_ip = '$_SERVER[REMOTE_ADDR]' ";
    else
        $sql.= " and mb_id = '$member[mb_id]' ";

    $tmp = sql_fetch($sql);
    if ($tmp[cnt] >= $mw_basic[cf_comment_write_count]) {
        alert("게시물당 코멘트를  {$mw_basic[cf_comment_write_count]}번만 작성하실 수 있습니다.");
    }
}

if ($mw_basic[cf_must_notice_comment]) {
    $tmp_notice = str_replace($notice_div, ",", trim($board[bo_notice]));
    $cnt_notice = sizeof(explode(",", $tmp_notice));

    if ($tmp_notice) {
        $sql = "select count(*) as cnt from $mw[must_notice_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and wr_id in ($tmp_notice)";
        $row = sql_fetch($sql);
        if ($row[cnt] != $cnt_notice) {
            alert("$board[bo_subject] 게시판의 공지를 모두 읽으셔야 코멘트를 작성하실 수 있습니다.");
        }
    }
}

if ($mw_basic[cf_qna_enough] and $write[wr_qna_status] > 0) {
    alert('답변이 종료되었습니다.');
}

// 로그남김
if ($w == "cu" && $mw_basic[cf_post_history]) {
    $write2 = sql_fetch("select * from $write_table where wr_id = '$comment_id'");
    $wr_name2 = $board[bo_use_name] ? $member[mb_name] : $member[mb_nick];
    $sql = "insert into $mw[post_history_table]
               set bo_table = '$bo_table'
                   ,wr_id = '$comment_id'
                   ,wr_parent = '$write2[wr_parent]'
                   ,mb_id = '$member[mb_id]'
                   ,ph_name = '$wr_name2'
                   ,ph_option = '$write2[wr_option]'
                   ,ph_subject = '".addslashes($write2[wr_subject])."'
                   ,ph_content = '".addslashes($write2[wr_content])."'
                   ,ph_ip = '$_SERVER[REMOTE_ADDR]'
                   ,ph_datetime = '$g4[time_ymdhis]'";
    sql_query($sql);
}

$sql_option = "";

//if ($mw_basic[cf_comment_editor]) {
    //$wr_option .= "html1";
    $wr_option .= $html;
//}

if ($mw_basic['cf_comment_secret_no'] <= $member['mb_level'] && $wr_secret) {
    $wr_option .= $wr_option ? "," : "";
    $wr_option .= "$wr_secret";
}
else if (($w == "c" || $w == 'cu') && $comment_id && $mw_basic['cf_comment_secret_no'] > $member['mb_level']) {
    $sql = " select wr_option from {$write_table} where wr_id = '{$comment_id}' ";
    $row = sql_fetch($sql);
    if (strstr($row['wr_option'], 'secret')) {
        $wr_option .= $wr_option ? "," : "";
        $wr_option .= "secret";
    }
} 

if ($mw_basic[cf_comment_file]) // 코멘트 첨부파일
{
    $upload = array();

    $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

    // 삭제에 체크가 되어있다면 파일을 삭제합니다.
    if ($_POST[bf_file_del]) 
    {
        $upload[del_check] = true;

        $row = sql_fetch(" select bf_file from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$comment_id' and bf_no = '0' ");
        @unlink("$g4[path]/data/file/$bo_table/$row[bf_file]");
    }
    else
        $upload[del_check] = false;

    $tmp_file  = $_FILES[bf_file][tmp_name];
    $filesize  = $_FILES[bf_file][size];
    $filename  = $_FILES[bf_file][name];
    $filename  = preg_replace('/(\s|\<|\>|\=|\(|\))/', '_', $filename);
    $filename  = get_safe_filename($filename);

    // 서버에 설정된 값보다 큰파일을 업로드 한다면
    if ($filename)
    {
        if ($_FILES[bf_file][error] == 1)
        {
            alert("\'{$filename}\' 파일의 용량이 서버에 설정($upload_max_filesize)된 값보다 크므로 업로드 할 수 없습니다.\\n");
        }
        else if ($_FILES[bf_file][error] != 0)
        {
            alert("\'{$filename}\' 파일이 정상적으로 업로드 되지 않았습니다.\\n");
        }
    }

    if (is_uploaded_file($tmp_file)) 
    {
        // 관리자가 아니면서 설정한 업로드 사이즈보다 크다면 건너뜀
        if (!$is_admin && $filesize > $board[bo_upload_size]) 
        {
            alert("\'{$filename}\' 파일의 용량(".number_format($filesize)." 바이트)이 게시판에 설정(".number_format($board[bo_upload_size])." 바이트)된 값보다 크므로 업로드 하지 않습니다.\\n");
            //continue;
        }

        //=================================================================\
        // 090714
        // 이미지나 플래시 파일에 악성코드를 심어 업로드 하는 경우를 방지
        // 에러메세지는 출력하지 않는다.
        //-----------------------------------------------------------------
        $timg = @getimagesize($tmp_file);
        // image type
        if ( preg_match("/\.($config[cf_image_extension])$/i", $filename) ||
             preg_match("/\.($config[cf_flash_extension])$/i", $filename) ) 
        {
            if ($timg[2] < 1 || $timg[2] > 16)
            {
                alert("\'{$filename}\' 파일이 이미지나 플래시 파일이 아닙니다.\\n");
                //continue;
            }
        }
        //=================================================================

        $upload[image] = $timg;

        // 4.00.11 - 글답변에서 파일 업로드시 원글의 파일이 삭제되는 오류를 수정
        if ($w == 'u')
        {
            // 존재하는 파일이 있다면 삭제합니다.
            $row = sql_fetch(" select bf_file from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$comment_id' and bf_no = '0' ");
            @unlink("$g4[path]/data/file/$bo_table/$row[bf_file]");
        }

        // 프로그램 원래 파일명
        $upload[source] = $filename;
        $upload[filesize] = $filesize;

        // 아래의 문자열이 들어간 파일은 -x 를 붙여서 웹경로를 알더라도 실행을 하지 못하도록 함
        $filename = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $filename);

        // 접미사를 붙인 파일명
        //$upload[file] = abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr(md5(uniqid($g4[server_time])),0,8).'_'.urlencode($filename);
        // 달빛온도님 수정 : 한글파일은 urlencode($filename) 처리를 할경우 '%'를 붙여주게 되는데 '%'표시는 미디어플레이어가 인식을 못하기 때문에 재생이 안됩니다. 그래서 변경한 파일명에서 '%'부분을 빼주면 해결됩니다. 
        //$upload[file] = abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr(md5(uniqid($g4[server_time])),0,8).'_'.str_replace('%', '', urlencode($filename)); 
        shuffle($chars_array);
        $shuffle = implode("", $chars_array);

        // 첨부파일 첨부시 첨부파일명에 공백이 포함되어 있으면 일부 PC에서 보이지 않거나 다운로드 되지 않는 현상이 있습니다. (길상여의 님 090925)
        //$upload[file] = abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr($shuffle,0,8).'_'.str_replace('%', '', urlencode($filename)); 
        $upload[file] = abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr($shuffle,0,8).'_'.str_replace('%', '', urlencode(str_replace(' ', '_', $filename))); 

        $dest_file = "$g4[path]/data/file/$bo_table/" . $upload[file];

        // 업로드가 안된다면 에러메세지 출력하고 죽어버립니다.
        $error_code = move_uploaded_file($tmp_file, $dest_file) or die($_FILES[bf_file][error]);

        // 올라간 파일의 퍼미션을 변경합니다.
        chmod($dest_file, 0606);

        //$upload[image] = @getimagesize($dest_file);

    }
}
?>
