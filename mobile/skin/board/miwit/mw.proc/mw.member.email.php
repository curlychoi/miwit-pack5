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

include_once("_common.php");
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

header("Content-Type: text/html; charset=$g4[charset]");

if ($is_admin != 'super')
    die("로그인 해주세요.");

if (!$write[wr_id])
    die("데이터가 없습니다.");

if (!$token or get_session("ss_delete_token") != $token) 
    die("토큰 에러로 실행 불가합니다.");

$view = get_view($write, $board, $board_skin_path, 255);

$html = 0;
if (strstr($write[wr_option], "html1"))
    $html = 1;
else if (strstr($write[wr_option], "html2"))
    $html = 2;

ob_start();
for ($i=0; $i<=$view[file][count]; $i++)
{
    if ($view[file][$i][view])
    {
        $view[file][$i][view] = view_file_link2($view[file][$i][file], $view[file][$i][image_width], $view[file][$i][image_height], $view[file][$i][content]);
        echo $view[file][$i][view] . "<br/><br/>";
    }
}
$file_viewer = ob_get_contents();
ob_end_clean();

$write[wr_content] = conv_content($write[wr_content], $html);

if (!strstr($write[wr_content], "{이미지:"))// 파일 출력  
    $write[wr_content] = $file_viewer . $write[wr_content]; 
else
    $write[wr_content] = preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/ie", "view_image(\$view, '\\1', '\\2')", $write[wr_content]);

$write[wr_content] = preg_replace("/\[\<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\"\s*[^\>]*\>.*\<\/a\>\]/iUs",
                        "<img src='$1://$2.$3'>", $write[wr_content]);
$write[wr_content] = preg_replace("/\[(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\]/iUs",
                        "<img src='$1://$2.$3' id='target_resize_image[]' onclick='image_window(this);'>", $write[wr_content]);

// 배추코드
$write[wr_subject] = bc_code($write[wr_subject]);
$write[wr_content] = bc_code($write[wr_content]);

$sql = " insert $g4[mail_table]
            set ma_subject = '".addslashes($write[wr_subject])."',
                ma_content = '".addslashes($write[wr_content])."',
                ma_time    = '$g4[time_ymdhis]',
                ma_ip      = '$_SERVER[REMOTE_ADDR]' ";
sql_query($sql);

die("등록되었습니다. 회원메일발송 페이지로 이동하시겠습니까?");

function view_file_link2($file, $width, $height, $content="")
{
    global $config, $board;
    global $g4;
    static $ids;

    if (!$file) return;

    $ids++;

    // 파일의 폭이 게시판설정의 이미지폭 보다 크다면 게시판설정 폭으로 맞추고 비율에 따라 높이를 계산
    if ($width > $board[bo_image_width] && $board[bo_image_width])
    {
        $rate = $board[bo_image_width] / $width;
        $width = $board[bo_image_width];
        $height = (int)($height * $rate);
    }

    // 폭이 있는 경우 폭과 높이의 속성을 주고, 없으면 자동 계산되도록 코드를 만들지 않는다.
    if ($width)
        $attr = " width='$width' height='$height' ";
    else
        $attr = "";

    if (preg_match("/\.($config[cf_image_extension])$/i", $file))
        // 이미지에 속성을 주지 않는 이유는 이미지 클릭시 원본 이미지를 보여주기 위한것임
        // 게시판설정 이미지보다 크다면 스킨의 자바스크립트에서 이미지를 줄여준다
        return "<img src='$g4[url]/data/file/$board[bo_table]/".urlencode($file)."' title='$content'>";
}

?>
