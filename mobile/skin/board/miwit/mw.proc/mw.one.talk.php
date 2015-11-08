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

$is_notice = false;
if ($is_admin && $w != "r")
{
    $is_notice = true;

    if ($w == "u")
    {
        // 답변 수정시 공지 체크 없음
        if ($write[wr_reply])
            $is_notice = false;
        else
        {
            $notice_checked = "";
            //if (preg_match("/^".$wr_id."/m", trim($board[bo_notice])))
            //if (preg_match("/[^0-9]{0,1}{$wr_id}[\r]{0,1}/",$board[bo_notice]))
            if (in_array((int)$wr_id, $notice_array))
                $notice_checked = "checked";
        }
    }
}

$is_html = false;
if ($member[mb_level] >= $board[bo_html_level])
    $is_html = true;

/*
// 에서 무조건 비밀글 사용으로 인한 코드 수정 : 061021
$is_secret = false;
if ($board[bo_use_secret])
    $is_secret = true;
*/
$is_secret = $board[bo_use_secret];
// DHTML 에디터 사용 선택 가능하게 수정 : 061021
//$is_dhtml_editor = $board[bo_use_dhtml_editor];
// 090713
if ($board[bo_use_dhtml_editor] && $member[mb_level] >= $board[bo_html_level])
    $is_dhtml_editor = true;
else
    $is_dhtml_editor = false;

$is_mail = false;
if ($config[cf_email_use] && $board[bo_use_email])
    $is_mail = true;

$recv_email_checked = "";
if ($w == "" || strstr($write[wr_option], "mail"))
    $recv_email_checked = "checked";

$is_name = false;
$is_password = false;
$is_email = false;
if (!$member[mb_id] || ($is_admin && $w == 'u' && $member[mb_id] != $write[mb_id])) {
    $is_name = true;
    $is_password = true;
    $is_email = true;
    $is_homepage = true;
}

$is_category = false;
if ($board[bo_use_category]) {
    $ca_name = $write[ca_name];
    $category_option = get_category_option($bo_table);
    $is_category = true;
}

$is_link = false;
if ($member[mb_level] >= $board[bo_link_level])
    $is_link = true;

$is_file = false;
if ($member[mb_level] >= $board[bo_upload_level])
    $is_file = true;

$is_file_content = false;
if ($board[bo_use_file_content])
    $is_file_content = true;

// 트랙백
$is_trackback = false;
if ($board[bo_use_trackback] && $member[mb_level] >= $board[bo_trackback_level])
    $is_trackback = true;

if ($w == "" || $w == "r") {
    if ($member[mb_id]) {
        $name = get_text(cut_str($write[wr_name],20));
        $email = $member[mb_email];
        $homepage = get_text($member[mb_homepage]);
    }
}

$tmpsize = array(0, 0);
$is_comment_image = false;
$comment_image = mw_get_noimage();
if ($mw_basic[cf_attribute] != "anonymous" && !$view[wr_anonymous] && $member[mb_id] && file_exists("$comment_image_path/{$member[mb_id]}")) {
    $comment_image = "$comment_image_path/{$member[mb_id]}";
    $is_comment_image = true;
    $tmpsize = @getImageSize($comment_image);
}

?>

<? if ($is_member) { ?>
<form name="fwrite" method="post" onsubmit="return fwrite_check(document.fwrite);" enctype="multipart/form-data">
<input type=hidden name=null>
<input type=hidden name=w        value="<?=$w?>">
<input type=hidden name=bo_table value="<?=$bo_table?>">
<input type=hidden name=wr_id    value="<?=$wr_id?>">
<input type=hidden name=sca      value="<?=$sca?>">
<input type=hidden name=sfl      value="<?=$sfl?>">
<input type=hidden name=stx      value="<?=$stx?>">
<input type=hidden name=spt      value="<?=$spt?>">
<input type=hidden name=sst      value="<?=$sst?>">
<input type=hidden name=sod      value="<?=$sod?>">
<input type=hidden name=page     value="<?=$page?>">

<?
// 익명게시판
if ($mw_basic[cf_attribute] == "anonymous" && $is_guest) {
$is_name = $is_email = $is_homepage = false;
echo "<input type=hidden name=wr_name value='익명'>\n";
} 
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<colgroup width=100>
<colgroup width=''>
<tr><td colspan=2 height=2 class=mw_basic_line_color></td></tr>
</table>

<input type="hidden" name="ca_name" value="<?=$sca?>">
<input type="hidden" name="wr_subject" value="한줄게시판">

<div style="margin:10px 0 10px 0; border:3px solid #efefef; padding:20px;">
<table border="0" cellspacing="0" cellpadding="0" width="90%" align="center">
<colgroup width=70>
<colgroup width=''>
<colgroup width=70>
<tr>
<td valign="top">
    <img src="<?=$comment_image?>" class="comment_image" onclick="mw_image_window(this, <?=$tmpsize[0]?>, <?=$tmpsize[1]?>);">

    <? if (($is_member && $member[mb_id] == $member[mb_id] && !$member[wr_anonymous]) || $is_admin) { ?>
    <div style="margin:0 0 0 10px;"><a href="javascript:mw_member_photo('<?=$member[mb_id]?>')"
        style="font:normal 11px 'gulim'; color:#888; text-decoration:none;"><? echo $is_comment_image ? "사진변경" : "사진등록"; ?></a></div>
    <? } ?>
    <script type="text/javascript">
    function mw_member_photo(mb_id) {
        window.open('<?=$board_skin_path?>/mw.proc/mw.comment.image.php?bo_table=<?=$bo_table?>&mb_id='+mb_id,'comment_image','width=500,height=350');
    }
    </script>
    <?
    if ($mw_basic[cf_icon_level] && !$member[wr_anonymous] && $mw_basic[cf_attribute] != "anonymous") { 
        $level = mw_get_level($member[mb_id]);
        echo "<div class=\"icon_level$level\">&nbsp;</div>";
        $exp = $icon_level_mb_point[$member[mb_id]] - $level*$mw_basic[cf_icon_level_point];
        $per = round($exp/$mw_basic[cf_icon_level_point]*100);
        echo "<div style=\"background:url($board_skin_path/img/level_exp_bg.gif); width:61px; height:3px; font-size:1px; line-height:1px; margin:5px 0 0 3px;\">";
        echo "<div style=\"background:url($board_skin_path/img/level_exp_dot.gif); width:$per%; height:3px;\">&nbsp;</div>";
        echo "</div>";
    }
    ?>
</td>
<td valign="top">

    <? if (!$is_dhtml_editor || $mw_basic[cf_editor] != "cheditor") { ?>
    <textarea id="wr_content" name="wr_content" style='width:100%; word-break:break-all;' rows=5 itemname="내용" required  class=mw_basic_textarea
    <? if ($is_dhtml_editor && $mw_basic[cf_editor] == "geditor") echo "geditor"; ?>
    <? if ($write_min || $write_max) { ?>onkeyup="check_byte('wr_content', 'char_count');"<?}?>><?=$content?></textarea>
    <? if (($write_min || $write_max) && !$is_dhtml_editor) { ?><script type="text/javascript"> check_byte('wr_content', 'char_count'); </script><?}?>
    <? } ?>

    <? if ($is_dhtml_editor && $mw_basic[cf_editor] == "cheditor") echo cheditor2('wr_content', $content); ?>

    <? if (!$is_dhtml_editor) { ?>
    <table width=100%>
    <tr>
        <td width=70% align=left valign=bottom>
            <span style="cursor: pointer;" onclick="textarea_decrease('wr_content', 5);"><img src="<?=$board_skin_path?>/img/btn_up.gif"></span>
            <span style="cursor: pointer;" onclick="textarea_original('wr_content', 5);"><img src="<?=$board_skin_path?>/img/btn_init.gif"></span>
            <span style="cursor: pointer;" onclick="textarea_increase('wr_content', 5);"><img src="<?=$board_skin_path?>/img/btn_down.gif"></span>

            <? if ($is_notice || $is_html || $is_secret || $is_mail || $mw_basic[cf_anonymous]) { ?>
                <? if ($is_notice) { ?><input type=checkbox name=notice value="1" <?=$notice_checked?>>공지&nbsp;<? } ?>
                <? if ($is_dhtml_editor) { ?>
                <input type=hidden value="html1" name="html">
                <? } else { ?>
                    <? if ($is_html) { ?>
                    <input onclick="html_auto_br(this);" type=checkbox value="<?=$html_value?>" name="html" <?=$html_checked?>><span class=w_title>html</span>&nbsp;
                    <? } ?>
                <? } ?>
                <? if ($is_secret) { ?>
                    <? if ($is_admin || $is_secret==1) { ?>
                    <input type=checkbox value="secret" name="secret" <?=$secret_checked?>><span class=w_title>비밀글</span>&nbsp;
                    <? } else { ?>
                    <input type=hidden value="secret" name="secret">
                    <? } ?>
                <? } ?>
                <? if ($is_mail) { ?><input type=checkbox value="mail" name="mail" <?=$recv_email_checked?>>답변메일받기&nbsp;<? } ?>
                <? if ($mw_basic[cf_anonymous]) {?>
                <input type="checkbox" name="wr_anonymous" value="1"> 익명
                <? } ?>
            <? } ?>
        </td>
        <td width=30% align=right><? if ($write_min || $write_max) { ?><span id=char_count></span>글자<?}?></td>
    </tr>
    </table>
    <? } ?>

</td>
<td valign="top" style="padding:0 0 0 10px;">
    <input type=image id="btn_submit" src="<?=$board_skin_path?>/img/btn_comment_ok.gif" border=0 accesskey='s'>&nbsp;
</td>
</tr>
</table>
</div>

</form>

<script type="text/javascript" src="<?="$g4[path]/js/jquery.kcaptcha.js"?>"></script>
<script type="text/javascript">

function html_auto_br(obj) {
    if (obj.checked) {
        result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
        if (result)
            obj.value = "html2";
        else
            obj.value = "html1";
    }
    else
        obj.value = "";
}

function fwrite_check(f) {

    if (document.getElementById('char_count')) {
        if (char_min > 0 || char_max > 0) {
            var cnt = parseInt(document.getElementById('char_count').innerHTML);
            if (char_min > 0 && char_min > cnt) {
                alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
                return false;
            }
            else if (char_max > 0 && char_max < cnt) {
                alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                return false;
            }
        }
    }

    if (document.getElementById('tx_wr_content')) {
        if (!ed_wr_content.outputBodyHTML()) { 
            alert('내용을 입력하십시오.'); 
            ed_wr_content.returnFalse();
            return false;
        }
    }

    <? if ($is_dhtml_editor && $mw_basic[cf_editor] == "cheditor") echo cheditor3('wr_content'); ?>

    var subject = "";
    var content = "";
    <? if (!$is_admin) { ?>
    $.ajax({
        url: "<?=$board_skin_path?>/ajax.filter.php",
        type: "POST",
        data: {
            "subject": f.wr_subject.value,
            "content": f.wr_content.value
        },
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            subject = data.subject;
            content = data.content;
        }
    });
    <? } ?>

    if (subject) {
        alert("제목에 금지단어('"+subject+"')가 포함되어있습니다");
        f.wr_subject.focus();
        return false;
    }

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        if (typeof(ed_wr_content) != "undefined") 
            ed_wr_content.returnFalse();
        else 
            f.wr_content.focus();
        return false;
    }

    if (!check_kcaptcha(f.wr_key)) {
        return false;
    }

    var geditor_status = document.getElementById("geditor_wr_content_geditor_status");
    if (geditor_status != null) {
        if (geditor_status.value == "TEXT") {
            f.html.value = "html2";
        }
        else if (geditor_status.value == "WYSIWYG") {
            f.html.value = "html1";
        }
    }

    f.action = "<?php echo $g4['bbs_path']?>/write_update.php";
}
</script>

<script type="text/javascript" src="<?="$g4[path]/js/board.js"?>"></script>

<? if ($is_dhtml_editor && $mw_basic[cf_editor] == "geditor") { ?>
    <script type="text/javascript"> var g4_skin_path = "<?=$board_skin_path?>"; </script>
    <script type="text/javascript" src="<?=$board_skin_path?>/mw.geditor/geditor.js"></script>
    <? if (strstr($write[wr_option], "html2")) { ?>
	<script type="text/javascript"> geditor_wr_content.mode_change(); </script>
    <? } ?>
<? } ?>

<? } // 입력폼 끝 ?>


<?
@include("$mw_basic[cf_include_list_main]");

// 댓글감춤
if ($list[$i][wr_comment_hide])
    $list[$i][comment_cnt] = 0;

// 호칭
$list[$i][name] = get_name_title($list[$i][name], $list[$i][wr_name]);

// 자동치환
$list[$i][subject] = mw_reg_str($list[$i][subject]);

// 멤버쉽 아이콘
if (function_exists("mw_cash_membership_icon"))
{
    if (!in_array($list[$i][mb_id], $mw_membership)) {
        $mw_membership[] = $list[$i][mb_id];
        $mw_membership_icon[$list[$i][mb_id]] = mw_cash_membership_icon($list[$i][mb_id]);
        $list[$i][name] = $mw_membership_icon[$list[$i][mb_id]].$list[$i][name];
    } else {
        $list[$i][name] = $mw_membership_icon[$list[$i][mb_id]].$list[$i][name];
    }
}

// 익명
if ($list[$i][wr_anonymous]) {
    $list[$i][name] = "익명";
    $list[$i][wr_name] = $list[$i][name];
}

// 공지사항 상단
if ($mw_basic[cf_notice_top] && $mw_basic[cf_type] != 'gall') {
    if ($list[$i][is_notice]) continue;
    if (in_array($list[$i][wr_id], $notice_list) && !$stx) continue;
}

// 리워드
if ($mw_basic[cf_reward]) {
    $reward = sql_fetch("select * from $mw[reward_table] where bo_table = '$bo_table' and wr_id = '{$list[$i][wr_id]}'");
    if ($reward[re_edate] != "0000-00-00" && $reward[re_edate] < $g4[time_ymd]) { // 날짜 지나면 종료
        sql_query("update $mw[reward_table] set re_status = '' where bo_table = '$bo_table' and wr_id = '{$list[$i][wr_id]}'");
        $reward[re_status] = '';
    }
    if ($reward[re_edate] == "0000-00-00")
        $reward[re_edate] = "&nbsp;";
    else
        $reward[re_edate] = substr($reward[re_edate], 5, 5);
}

// 컨텐츠샵
$mw_price = "";
if ($mw_basic[cf_contents_shop] == "1") {
    if (!$list[$i][wr_contents_price])
	$mw_price = "무료";
    else
	$mw_price = number_format($list[$i][wr_contents_price]).$mw_cash[cf_cash_unit];
}

// 링크로그
if ($mw_basic[cf_link_log])  {
    for ($j=1; $j<=$g4['link_count']; $j++)
    {
        $list[$i]['link'][$j] = set_http(get_text($list[$i]["wr_link{$j}"]));
        $list[$i]['link_href'][$j] = "$board_skin_path/link.php?bo_table=$board[bo_table]&wr_id={$list[$i][wr_id]}&no=$j" . $qstr;
        $list[$i]['link_hit'][$j] = (int)$list[$i]["wr_link{$j}_hit"];
    }
}

// 링크게시판
if ($mw_basic[cf_link_board]) {
    //if (!$is_admin && $member[mb_id] && $list[$i][mb_id] != $member[mb_id])
    if (!$is_admin && $list[$i][mb_id] != $member[mb_id])
        $list[$i][href] = "javascript:void(window.open('{$list[$i][link_href][1]}'))";    
    $list[$i][wr_hit] = $list[$i][link_hit][1];    
}

// 공지사항 출력 항목
if ($mw_basic[cf_post_name]) $list[$i][name] = "";
if ($mw_basic[cf_post_date]) $list[$i][datetime2] = "";
if ($mw_basic[cf_post_hit]) $list[$i][wr_hit] = "";

if ($list[$i][is_notice]) {
    if ($mw_basic[cf_notice_name]) $list[$i][name] = "";
    if ($mw_basic[cf_notice_date]) $list[$i][datetime2] = "";
    if ($mw_basic[cf_notice_hit]) $list[$i][wr_hit] = "";
}

// 조회수, 추천수, 글번호에 세자리마다 컴마, 사용
if ($mw_basic[cf_comma]) {
    $list[$i][num] = number_format($list[$i][num]);
    $list[$i][wr_hit] = number_format($list[$i][wr_hit]);
    $list[$i][wr_good] = number_format($list[$i][wr_good]);
    $list[$i][wr_nogood] = number_format($list[$i][wr_nogood]);
}

// 신고된 게시물
$is_singo = false;
if ($list[$i][wr_singo] && $list[$i][wr_singo] >= $mw_basic[cf_singo_number] && $mw_basic[cf_singo_write_block]) {
    $list[$i][subject] = "신고가 접수된 게시물입니다.";
    $is_singo = true;
}

if ($mw_basic[cf_type] != "list")
{
    $set_width = $mw_basic[cf_thumb_width];
    $set_height = $mw_basic[cf_thumb_height];

    // 섬네일 생성
    $thumb_file = "";
    $file = mw_get_first_file($bo_table, $list[$i][wr_id], true);
    if (!empty($file)) {
        $source_file = "$file_path/{$file[bf_file]}";

        //if ($mw_basic[cf_img_1_noview])
        //    $thumb_file = "$file_path/{$file[bf_file]}";
        //else
            $thumb_file = "$thumb_path/{$list[$i][wr_id]}";

        if (!file_exists($thumb_file)) {
            mw_make_thumbnail($set_width, $set_height, $source_file, $thumb_file, $mw_basic[cf_thumb_keep]);
        } else {
            //if (!$mw_basic[cf_img_1_noview]) {
            if ($mw_basic[cf_thumb_keep]) {
                $size = @getImageSize($source_file);
                $size = mw_thumbnail_keep($size, $set_width, $set_height);
                $set_width = $size[0];
                $set_height = $size[1];
            } else
                $size = @getImageSize($thumb_file);

            if ($size[0] != $set_width || $size[1] != $set_height) {
                mw_make_thumbnail($mw_basic[cf_thumb_width], $mw_basic[cf_thumb_height], $source_file, $thumb_file, $mw_basic[cf_thumb_keep]);
                if ($mw_basic[cf_thumb2_width])
                    @mw_make_thumbnail($mw_basic[cf_thumb2_width], $mw_basic[cf_thumb2_height], $source_file, "{$thumb2_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb2_keep]);
                if ($mw_basic[cf_thumb3_width])
                    @mw_make_thumbnail($mw_basic[cf_thumb3_width], $mw_basic[cf_thumb3_height], $source_file, "{$thumb3_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb3_keep]);
            }
        //}
        }
    } else {
        $thumb_file = "$thumb_path/{$list[$i][wr_id]}";
        if (!file_exists($thumb_file)) {
            preg_match("/<img.*src=\"(.*)\"/iU", $list[$i][wr_content], $match);
            if ($match[1]) {
                $match[1] = str_replace($g4[url], "..", $match[1]);
                if (file_exists($match[1])) {
                    mw_make_thumbnail($mw_basic[cf_thumb_width], $mw_basic[cf_thumb_height], $match[1], $thumb_file, $mw_basic[cf_thumb_keep]);
                    if ($mw_basic[cf_thumb2_width])
                        @mw_make_thumbnail($mw_basic[cf_thumb2_width], $mw_basic[cf_thumb2_height], $match[1], "{$thumb2_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb2_keep]);
                    if ($mw_basic[cf_thumb3_width])
                        @mw_make_thumbnail($mw_basic[cf_thumb3_width], $mw_basic[cf_thumb3_height], $match[1], "{$thumb3_path}/{$list[$i][wr_id]}", $mw_basic[cf_thumb3_keep]);
     
                }
            }
        }
    }
}

if ($mw_basic[cf_type] == "gall")
{
    if ($list[$i][is_notice]) continue;

    if (!file_exists($thumb_file) || $list[$i][icon_secret]) {
        $thumb_file = mw_get_noimage();
        $thumb_width = "width='$mw_basic[cf_thumb_width]'";
        $thumb_height = "height='$mw_basic[cf_thumb_height]'";
    } else {
        $thumb_width = "";
        $thumb_height = "";
    }

    $style = "";
    $class = "";
    if ($list[$i][is_notice]) $style = " class=mw_basic_list_notice";

    if ($wr_id == $list[$i][wr_id]) { // 현재위치
        $style = " class=mw_basic_list_num_select";
        $class = " select";
    }

    $td_width = (int)(100 / $board[bo_gallery_cols]);

    // 제목스타일
    if ($mw_basic[cf_subject_style])
        $style .= " style='font-family:{$list[$i][wr_subject_font]}; color:{$list[$i][wr_subject_color]}'";

    $list[$i][subject] = "<span{$style}>{$list[$i][subject]}</span></a>";

    if (($line_number+1)%$colspan==1) echo "<tr>";
?>
    <td width="<?=$td_width?>%" class="mw_basic_list_gall <?=$class?>">
        <!--<div><a href="<?=$list[$i][href]?>"><img src="<?=$thumb_file?>" width=<?=$mw_basic[cf_thumb_width]?> height=<?=$mw_basic[cf_thumb_height]?> align=absmiddle></a></div>-->
        <div><a href="<?=$list[$i][href]?>"><img src="<?=$thumb_file?>" <?=$thumb_width?> <?=$thumb_height?> align=absmiddle></a></div>
        <!--<div class=mw_basic_list_subject_gall style="width:<?=$set_width?>px;">-->
        <div class=mw_basic_list_subject_gall>
            <? if ($is_checkbox) { ?> <input type=checkbox name=chk_wr_id[] value="<?=$list[$i][wr_id]?>"> <? } ?>
            <? if ($is_category && $list[$i][ca_name]) { ?>  <a href="<?=$list[$i][ca_name_href]?>" class=mw_basic_list_category>[<?=$list[$i][ca_name]?>]</a> <? } ?>
            <a href="<?=$list[$i][href]?>"><?=$list[$i][subject]?></a>
            <? if ($list[$i][comment_cnt]) { ?> <a href="<?=$list[$i][comment_href]?>" class=mw_basic_list_comment_count><?=$list[$i][comment_cnt]?></a> <? } ?>
        </div>
    </td>
    <? if (($line_number+1)%$colspan==0) echo "</tr>"; ?>

<? } else { ?>

<tr align=center <? if ($list[$i][is_notice]) echo "bgcolor='#f8f8f9'"; ?>>

    <!-- 글번호 -->
    <? if (!$mw_basic[cf_post_num]) { ?>
    <td>
        <?
	if ($list[$i][is_notice] && $mw_basic[cf_notice_hit]) $list[$i][wr_hit] = "";

        if ($list[$i][is_notice]) // 공지사항
            echo "<img src=\"$board_skin_path/img/icon_notice.gif\" width=30 height=16>";
        else if ($wr_id == $list[$i][wr_id]) // 현재위치
            echo "<span class=mw_basic_list_num_select>{$list[$i][num]}</span>";
        else // 일반
            echo "<span class=mw_basic_list_num>{$list[$i][num]}</span>";
        ?>
    </td>
    <? } ?>

    <? if ($is_checkbox) { ?>
    <!-- 관리자용 체크박스 -->
    <td> <input type=checkbox name=chk_wr_id[] value="<?=$list[$i][wr_id]?>"> </td>
    <? } ?>

    <? if ($mw_basic[cf_type] == "thumb") { ?>
    <? if (!file_exists($thumb_file) || $list[$i][icon_secret]) $thumb_file = mw_get_noimage(); ?>

    <!-- 썸네일 -->
    <td class=mw_basic_list_thumb><!-- 여백제거
        --><a href="<?=$list[$i][href]?>"><img src="<?=$thumb_file?>" width=<?=$mw_basic[cf_thumb_width]?> height=<?=$mw_basic[cf_thumb_height]?> align=absmiddle></a><!--
    --></td>
    <? } ?>

    <!-- 글제목 -->
    <td class=mw_basic_list_subject>
        <?
        if ($mw_basic[cf_type] == "desc" && file_exists($thumb_file)) {
            echo "<div class=mw_basic_list_thumb>";
            echo "<a href=\"{$list[$i][href]}\"><img src=\"{$thumb_file}\" width={$mw_basic[cf_thumb_width]} height={$mw_basic[cf_thumb_height]} align=absmiddle></a>";
            echo "</div>";
        }
        if ($mw_basic[cf_type] == "desc") {
            echo "<div class=mw_basic_list_subject_desc>";
        }
        echo $list[$i][reply];
        echo $list[$i][icon_reply];
        if ($is_category && $list[$i][ca_name]) {
            echo "<a href=\"{$list[$i][ca_name_href]}\" class=mw_basic_list_category>[{$list[$i][ca_name]}]</a>&nbsp;";
        }

        if ($mw_basic[cf_read_level] && $list[$i][wr_read_level])
            echo "<span class=mw_basic_list_level>[{$list[$i][wr_read_level]}레벨]</span>&nbsp;";

        $style = "";
        if ($list[$i][is_notice]) $style = " class=mw_basic_list_notice";

        if ($wr_id == $list[$i][wr_id]) // 현재위치
            $style = " class=mw_basic_list_num_select";

        if ($mw_basic[cf_type] == "list") {
            if ($is_singo)
                echo "<img src=\"$board_skin_path/img/icon_red.png\" align=absmiddle style=\"border-bottom:2px solid #fff;\">&nbsp;";
            elseif ($list[$i][wr_kcb_use])
                echo "<img src=\"$board_skin_path/img/icon_kcb.png\" align=absmiddle style=\"border-bottom:2px solid #fff;\">&nbsp;";
            elseif (in_array($list[$i][wr_id], $vote_id))
                echo "<img src=\"$board_skin_path/img/icon_vote.png\" align=absmiddle style=\"border-bottom:2px solid #fff;\">&nbsp;";
            else
                echo "<img src=\"$board_skin_path/img/icon_subject.gif\" align=absmiddle style=\"border-bottom:2px solid #fff;\">&nbsp;";
        }
        if (!$mw_basic[cf_subject_link] || $board[bo_read_level] <= $member[mb_level]) {
            if (!$mw_basic[cf_board_member] || $mw_is_board_member || $is_admin) {
                echo "<a href=\"{$list[$i][href]}\">";
            }
        }

        // 제목스타일
        if ($mw_basic[cf_subject_style])
            $style .= " style='font-family:{$list[$i][wr_subject_font]}; color:{$list[$i][wr_subject_color]}'";

        echo "<span{$style}>{$list[$i][subject]}</span></a>";

        if ($list[$i][comment_cnt])
            //echo " <span class=mw_basic_list_comment_count>{$list[$i][comment_cnt]}</span>";
            //echo " <a href=\"{$list[$i][comment_href]}\" class=mw_basic_list_comment_count>{$list[$i][comment_cnt]}</a>";
            echo " <a href=\"{$list[$i][comment_href]}\" class=mw_basic_list_comment_count>+{$list[$i][wr_comment]}</a>";

        echo " " . $list[$i][icon_new];
        echo " " . $list[$i][icon_file];
        echo " " . $list[$i][icon_link];
        echo " " . $list[$i][icon_hot];
        echo " " . $list[$i][icon_secret];

        if ($mw_basic[cf_type] == "desc") {
            echo "</div>";
            $desc = strip_tags($list[$i][wr_content]);
            $desc = preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/ie", "", $desc);
            $desc = cut_str($desc, $mw_basic[cf_desc_len]);
            echo "<div class=mw_basic_list_desc> $desc </div>";
        }
        ?>
    </td>
    <? if ($mw_basic[cf_reward]) { ?>
    <td class=mw_basic_list_reward_point><?=number_format($reward[re_point])?> P</td>
    <td class=mw_basic_list_reward_edate><?=$reward[re_edate]?></td>
    <td class=mw_basic_list_reward_status><img src="<?=$board_skin_path?>/img/btn_reward_<?=$reward[re_status]?>.gif" align="absmiddle"></td>
    <? } ?>
    <? if ($mw_basic[cf_contents_shop] == "1") { ?>
        <td class=mw_basic_list_contents_price><span><?=$mw_price?></span></td><?}?>
    <? if (!$mw_basic[cf_post_name]) { ?>
    <? if ($mw_basic[cf_attribute] != "anonymous") { ?> <td><nobr class=mw_basic_list_name><?=$list[$i][name]?></nobr></td> <?}?> <?}?>
    <? if ($mw_basic[cf_attribute] == 'qna') { ?>
        <td class=mw_basic_list_qna_status><img src="<?=$board_skin_path?>/img/icon_qna_<?=$list[$i][wr_qna_status]?>.png"></span></td> <?}?>
    <? if ($mw_basic[cf_attribute] == 'qna' && $mw_basic[cf_qna_point_use]) { ?> <td class=mw_basic_list_point><?=$list[$i][wr_qna_point]?></span></td> <?}?>
    <? if (!$mw_basic[cf_post_date]) { ?> <td class=mw_basic_list_datetime><?=$list[$i][datetime2]?></span></td> <?}?>
    <? if (!$mw_basic[cf_post_hit]) { ?> <td class=mw_basic_list_hit><?=$list[$i][wr_hit]?></span></td> <?}?>
    <? if (!$mw_basic[cf_list_good]) { ?>
    <? if ($is_good) { ?><td class=mw_basic_list_good><?=$list[$i][wr_good]?></td><? } ?>
    <? if ($is_nogood) { ?><td class=mw_basic_list_nogood><?=$list[$i][wr_nogood]?></td><? } ?>
    <? } ?>
</tr>
<? if ($i<count($list)-1) { // 마지막 라인 출력 안함 ?>
<!--<tr><td colspan=<?=$colspan?> height=1 bgcolor=#E7E7E7></td></tr>-->
<tr><td colspan=<?=$colspan?> height=1 style="border-top:1px dotted #e7e7e7"></td></tr>
<?}?>
<?}?>
<?  $line_number++; ?>
<?}?>


<? if (count($list) == 0) { echo "<tr><td colspan={$colspan} class=mw_basic_nolist>게시물이 없습니다.</td></tr>"; } ?>
<tr><td colspan=<?=$colspan?> class=mw_basic_line_color height=1></td></tr>
</table>


<script type="text/javascript" src="<?="$board_skin_path/mw.js/mw_image_window.js"?>"></script>

