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

$mb = get_member($row[mb_id], 'mb_level');

$row[name] = get_name_title($row[name], $row[wr_name]); // 호칭
$row[name] = mw_sideview($row[name]);

// 멤버쉽 아이콘
if (function_exists("mw_cash_membership_icon") && $row[mb_id] != $config[cf_admin])
{
    if (!in_array($row[mb_id], (array)$mw_membership)) {
        $mw_membership[] = $row[mb_id];
        $mw_membership_icon[$row[mb_id]] = mw_cash_membership_icon($row[mb_id]);
        $row[name] = $mw_membership_icon[$row[mb_id]].$row[name];
    } else {
        $row[name] = $mw_membership_icon[$row[mb_id]].$row[name];
    }
}

if ($mw_basic[cf_attribute] == "anonymous") {
    $row[name] = mw_anonymous_nick($row[mb_id], $row[wr_ip]); 
    $row[wr_name] = '';
}
if ($row[wr_anonymous]) {
    $row[name] = mw_anonymous_nick($row[mb_id], $row[wr_ip]); 
    $row[wr_name] = '';
}

if ($i < $from_record) {
    $list[$i] = $row;
    return false;
}
    
$html = 0;
if (strstr($row['wr_option'], "html1")) $html = 1;
if (strstr($row['wr_option'], "html2")) $html = 2;

//if ($html > 0) {
    //$row[wr_content] = mw_tag_debug($row[wr_content]);
    $row[content] = $row[content1] = SECRET_COMMENT;
    if (!strstr($row[wr_option], "secret") ||
        $is_admin ||
        ($write[mb_id]==$member[mb_id] && $member[mb_id]) ||
        ($row[mb_id]==$member[mb_id] && $member[mb_id])) {
        $row[content1] = $row[wr_content];
        $row[content] = conv_content($row[wr_content], $html, 'wr_content');
        $row[content] = search_font($stx, $row[content]);
    }
//}

// 코멘트 비밀 리플 보이기
if ($row[content] == SECRET_COMMENT) {
    for ($j=$i-1; $j>=0; $j--) {
        if ($list[$j][wr_comment] == $row[wr_comment] && $list[$j][wr_comment_reply] == substr($row[wr_comment_reply], 0, strlen($row[wr_comment_reply])-1)) {
            if (trim($list[$j][mb_id]) && $list[$j][mb_id] == $member[mb_id]) {
                $row[content] = conv_content($row[wr_content], $html, 'wr_content');
                $row[content] = search_font($stx, $row[content]);
            }
            break;
        }
    }
}

// 코멘트 첨부파일
$file = get_comment_file($bo_table, $row[wr_id]);
if (preg_match("/\.($config[cf_movie_extension])$/i", $file[0][file])) {
    $tmp = '';
    ob_start();
    echo mw_jwplayer("{$g4[path]}/data/file/{$board[bo_table]}/{$file[0][file]}");
    if (trim($file[0][content])) echo $file[0][content];
    $jwcontent = ob_get_contents();
    ob_end_clean();

    if ($row[content] != SECRET_COMMENT)
        $row[content] = $jwcontent . "<br/><br/>" . $row[content];

    $file[0][movie] = true;
} 
elseif ($file[0][view]) {
    if ($board[bo_image_width]-200 < $file[0][image_width]) { // 이미지 크기 조절
        $img_width = $board[bo_image_width]-200;
    }
    else {
        $img_width = $file[0][image_width];
    }
    $file[0][view] = str_replace("<img", "<img style=\"max-width:{$img_width}px\"", $file[0][view]);
    $file[0][view] = preg_replace("/ width=\"[0-9]+\"/", "", $file[0][view]);
    $file[0][view] = preg_replace("/ height=\"[0-9]+\"/", "", $file[0][view]);
    if ($mw_basic[cf_image_save_close])
        $file[0][view] = str_replace("<img", "<img oncontextmenu=\"return false\" style=\"-webkit-touch-callout:none\" ", $file[0][view]);

    if ($mw_basic[cf_exif]) {
        $file[0][view] = str_replace("image_window(this)", "show_exif({$row[wr_id]}, this, event)", $file[0][view]);
        $file[0][view] = str_replace("title=''", "title='클릭하면 메타데이터를 보실 수 있습니다.'", $file[0][view]);
    } else {
        $file[0][view] = str_replace("onclick='image_window(this);'", 
            "onclick='mw_image_window(this, {$file[0][image_width]}, {$file[0][image_height]});'", $file[0][view]);
        // 제나빌더용 (그누보드 원본수정으로 인해 따옴표' 가 없음;)
        $file[0][view] = str_replace("onclick=image_window(this);", 
            "onclick='mw_image_window(this, {$file[0][image_width]}, {$file[0][image_height]});'", $file[0][view]); 
    }
    if ($row[content] != SECRET_COMMENT && !strstr($row[content], "{이미지:")) {
        $row[content] = $file[0][view] . "<br/><br/>" . $row[content];
    }
}

// 가변 파일
if ($file[0][source] && !$file[0][view] && !$file[0][movie]) {
    ob_start();
    ?>
    <div class="mw_basic_comment_download_file">
            <a href="<?=$board_skin_path?>/mw.proc/mw.comment.download.php?bo_table=<?=$bo_table?>&wr_id=<?=$row[wr_id]?>&bf_no=0"
                title="<?=$file[0][content]?>"><img src="<?=$board_skin_path?>/img/icon_file_down.gif" align=absmiddle>&nbsp;<?=$file[0][source]?></a>
            <span class=mw_basic_view_file_info> (<?=$file[0][size]?>), Down : <?=$file[0][download]?>, <?=$file[0][datetime]?></span>
    </div>
    <?
    $comment_file = ob_get_contents();
    ob_end_clean();
    if ($row[content] != SECRET_COMMENT)
        $row[content] = $comment_file . "<br/>" . $row[content];

    $ss_name = "ss_view_{$bo_table}_{$row[wr_id]}";
    set_session($ss_name, TRUE);
}

$tmp = array();
$tmp['file'] = $file;
$row[content] = preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/ie", "mw_view_image(\$tmp, '\\1', '\\2')", $row[content]);

if ($row[wr_singo] && $row[wr_singo] >= $mw_basic[cf_singo_number] && $mw_basic[cf_singo_write_block]) {
    $content = " <div class='singo_info'> 신고가 접수된 게시물입니다. (신고수 : {$row[wr_singo]}회)<br/>";
    $content.= " <span onclick=\"btn_singo_view({$row[wr_id]})\" class='btn_singo_block'>여기</span>를 클릭하시면 내용을 볼 수 있습니다.";
    if ($is_admin == "super")
        $content.= " <span class='btn_singo_block' onclick=\"btn_singo_clear({$row[wr_id]})\">[신고 초기화]</span> ";
    $content.= " </div>";
    $content.= " <div id='singo_block_{$row[wr_id]}' class='singo_block'> {$row[content]} </div>";
    $row[content] = $content;
}

$comment_id = $row[wr_id];
if ($mw_basic[cf_singo]) {
    $row[singo_href] = "javascript:btn_singo($comment_id, $write[wr_parent])";
}

// 로그버튼
$history_href = "";
if ($mw_basic[cf_post_history] && $mw_basic[cf_post_history_level] && $member[mb_level] >= $mw_basic[cf_post_history_level]) {
    $history_href = "javascript:btn_history({$row[wr_id]})";
}

if (!$is_comment_write) {
    $row[is_edit] = false;
    $row[is_reply] = false;
}

$tmpsize = array(0, 0);
$is_comment_image = false;
$comment_image = mw_get_noimage();
if ($mw_basic[cf_attribute] != "anonymous" && !$row[wr_anonymous] && $row[mb_id] && file_exists("$comment_image_path/{$row[mb_id]}")) {
    $comment_image = "$comment_image_path/{$row[mb_id]}";
    $is_comment_image = true;
    $tmpsize = @getImageSize($comment_image);
    $comment_image.= '?'.filemtime($comment_image);
    $comment_class = '';
}
else {
    $comment_class = 'noimage';
}

$row[content] = mw_reg_str($row[content]); // 자동치환

$row[content] = bc_code($row[content]);
if (strstr($row[wr_option], "html")) {
    $row[content] = mw_tag_debug($row[content]);
}
$row[content] = mw_set_sync_tag($row[content]); // 잘못된 태그교정
$row[content] = mw_youtube_content($row[content], "144"); // 유투브 자동 재생

if ($mw_basic[cf_iframe_level] && $mw_basic[cf_iframe_level] <= $mb[mb_level]) {
    $row[content] = mw_special_tag($row[content]);
}

// 관리자 게시물은 IP 주소를 보이지 않습니다
if ($row[mb_id] == $config[cf_admin]) $row[ip] = "";

$str = $row[content];
if (strstr($row[wr_option], "secret")) {
    $str = "<span class='mw_basic_comment_secret'>* $str</span>";
}
$str = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $str);
// FLASH XSS 공격에 의해 주석 처리
//$str = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(swf)\".*\<\/a\>\]/i", "<script>doc_write(flash_movie('$1://$2.$3'));</script>", $str);
// 검색시 적용안되는 문제
//$str = preg_replace("/\[\<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\"\s*[^\>]*\>[^\s]*\<\/a\>\]/i", "<img src='$1://$2.$3' id='target_resize_image[]' onclick='image_window(this);'>", $str);
$str = preg_replace("/\[\<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\"\s*[^\>]*\>.*\<\/a\>\]/iUs", "<img src='$1://$2.$3' id='target_resize_image[]' onclick='image_window(this);'>", $str);
$str = preg_replace("/\[\<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\]\"\s*[^\>]*\>.*\]\<\/a\>/iUs", "<img src='$1://$2.$3' id='target_resize_image[]' onclick='image_window(this);'>", $str);


$str = preg_replace_callback("/\[code\](.*)\[\/code\]/iU", "_preg_callback", $str);

$row[content] = $str;

if (!$mw_basic['cf_time_comment'])
    $mw_basic['cf_time_comment'] = "Y-m-d (w) H:i";

$row['datetime2'] = mw_get_date($row['wr_datetime'], $mw_basic['cf_time_comment']);
$row['datetime_sns'] = mw_get_date($row['wr_datetime'], 'sns');

if ($row[del_link])
    $row[del_link] = mw_bbs_path($row[del_link]);


$row[content] = preg_replace("/(\[@[^\]]+\])/iUs", "<span style='font-weight:bold;'>\\1</span>", $row[content]);

if ($mw_basic['cf_rate_level'] && $row['wr_rate'] > 0) {
    ob_start();
    ?>
    <div id="star_rate_<?php echo $row['wr_id']?>" style="margin:0 0 10px 0;"></div>
    <script>
    $(document).ready(function () {
        $("#star_rate_<?php echo $row['wr_id']?>").mw_star_rate({
            path : "<?php echo $board_skin_path?>/mw.js/mw.star.rate/",
            star : "star1",
            default_value : <?php echo round($row['wr_rate'], 1)?>,
            readonly : true,
            readonly_msg : '',
            half : true,
            max : 5,
        });
    });
    </script>
    <?
    $rate = ob_get_contents();
    ob_end_clean();

    $row['content'] = $rate . $row['content'];
}

return true;
