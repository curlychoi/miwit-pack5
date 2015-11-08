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

if (!$board[bo_table])
    alert_close("존재하지 않는 게시판입니다.", $g4[path]);

if ($write[wr_is_comment]) 
    alert_close("코멘트는 인쇄 하실 수 없습니다.");

if (!$bo_table) 
    alert_close("bo_table 값이 넘어오지 않았습니다.");

// wr_id 값이 있으면 글읽기 
if ($wr_id) 
{
    // 글이 없을 경우 해당 게시판 목록으로 이동
    if (!$write[wr_id]) 
    {
        $msg = "글이 존재하지 않습니다.\\n\\n글이 삭제되었거나 이동된 경우입니다.";
        alert_close($msg);
    }

    // 그룹접근 사용
    if ($group[gr_use_access]) 
    {
        if (!$member[mb_id]) {
            $msg = "비회원은 이 게시판에 접근할 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.";
            alert_close($msg);
        }

        // 그룹관리자 이상이라면 통과 
        if ($is_admin == "super" || $is_admin == "group") 
            ; 
        else 
        {
            // 그룹접근
            $sql = " select count(*) as cnt 
                       from $g4[group_member_table] 
                      where gr_id = '$board[gr_id]' and mb_id = '$member[mb_id]' ";
            $row = sql_fetch($sql);
            if (!$row[cnt]) 
                alert_close("접근 권한이 없으므로 글읽기가 불가합니다.\\n\\n궁금하신 사항은 관리자에게 문의 바랍니다.", $g4[path]);
        }
    }

    // 로그인된 회원의 권한이 설정된 읽기 권한보다 작다면
    if ($member[mb_level] < $board[bo_read_level]) 
    {
        if ($member[mb_id]) 
            alert_close("글을 읽을 권한이 없습니다.");
        else 
            alert_close("글을 읽을 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.");
    }

    // 자신의 글이거나 관리자라면 통과
    if (($write[mb_id] && $write[mb_id] == $member[mb_id]) || $is_admin)
        ;
    else 
    {
        // 비밀글이라면
        if (strstr($write[wr_option], "secret")) 
        {
            // 회원이 비밀글을 올리고 관리자가 답변글을 올렸을 경우
            // 회원이 관리자가 올린 답변글을 바로 볼 수 없던 오류를 수정
            $is_owner = false;
            if ($write[wr_reply] && $member[mb_id])
            {
                $sql = " select mb_id from $write_table 
                          where wr_num = '$write[wr_num]' 
                            and wr_reply = ''
                            and wr_is_comment = '0' ";
                $row = sql_fetch($sql);
                if ($row[mb_id] == $member[mb_id]) 
                    $is_owner = true;
            }

            $ss_name = "ss_secret_{$bo_table}_$write[wr_num]";
            
            if (!$is_owner)
            {
                //$ss_name = "ss_secret_{$bo_table}_{$wr_id}";
                // 한번 읽은 게시물의 번호는 세션에 저장되어 있고 같은 게시물을 읽을 경우는 다시 패스워드를 묻지 않습니다.
                // 이 게시물이 저장된 게시물이 아니면서 관리자가 아니라면
                //if ("$bo_table|$write[wr_num]" != get_session("ss_secret")) 
                if (!get_session($ss_name)) 
                    goto_url($g4['bbs_path']."/password.php?w=s&bo_table=$bo_table&wr_id=$wr_id{$qstr}");
            }

            set_session($ss_name, TRUE);
        }
    }

    // 한번 읽은글은 브라우저를 닫기전까지는 카운트를 증가시키지 않음
    $ss_name = "ss_view_{$bo_table}_{$wr_id}";
    if (!get_session($ss_name)) 
    {
        sql_query(" update $write_table set wr_hit = wr_hit + 1 where wr_id = '$wr_id' ");

        // 자신의 글이면 통과
        if ($write[mb_id] && $write[mb_id] == $member[mb_id])
            ;
        else 
        {
            // 회원이상 글읽기가 가능하다면
            if ($board[bo_read_level] > 1) {
                if ($member[mb_point] + $board[bo_read_point] < 0)
                    alert_close("보유하신 포인트(".number_format($member[mb_point]).")가 없거나 모자라서 글읽기(".number_format($board[bo_read_point]).")가 불가합니다.\\n\\n포인트를 모으신 후 다시 글읽기 해 주십시오.");

                insert_point($member[mb_id], $board[bo_read_point], "$board[bo_subject] $wr_id 글읽기", $bo_table, $wr_id, '읽기');
            }
        }

        set_session($ss_name, TRUE);
    }

    $g4[title] = "$group[gr_subject] > $board[bo_subject] > " . strip_tags(conv_subject($write[wr_subject], 255));
} 


include_once("$g4[path]/head.sub.php");

$width = $board[bo_table_width];
if ($width <= 100) $width .= '%'; 

// IP보이기 사용 여부
$ip = "";
$is_ip_view = $board[bo_use_ip_view];
if ($is_admin) {
    $is_ip_view = true;
    $ip = $write[wr_ip];
} else // 관리자가 아니라면 IP 주소를 감춘후 보여줍니다.
    $ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", "\\1.♡.\\3.\\4", $write[wr_ip]);

// 분류 사용
$is_category = false;
$category_name = "";
if ($board[bo_use_category]) {
    $is_category = true;
    $category_name = $write[ca_name]; // 분류명
}

$view = get_view($write, $board, $board_skin_path, 255);

if (strstr($sfl, "subject"))
    $view[subject] = search_font($stx, $view[subject]);

$html = 0;
if (strstr($view[wr_option], "html1"))
    $html = 1;
else if (strstr($view[wr_option], "html2"))
    $html = 2;

$view[content] = conv_content($view[wr_content], $html);
if (strstr($sfl, "content"))
    $view[content] = search_font($stx, $view[content]);
$view[content] = preg_replace("/(\<img )([^\>]*)(\>)/i", "\\1 name='target_resize_image[]' onclick='image_window(this)' style='cursor:pointer;' \\2 \\3", $view[content]);

//$view[rich_content] = preg_replace("/{img\:([0-9]+)[:]?([^}]*)}/ie", "view_image(\$view, '\\1', '\\2')", $view[content]);
$view[rich_content] = preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/ie", "view_image(\$view, '\\1', '\\2')", $view[content]);

$is_signature = false;
$signature = "";
if ($board[bo_use_signature] && $view[mb_id])
{
    $is_signature = true;
    $mb = get_member($view[mb_id]);
    $signature = $mb[mb_signature];

    //$signature = bad_tag_convert($signature);
    // 081022 : CSRF 보안 결함으로 인한 코드 수정
    $signature = conv_content($signature, 1);
}

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");
include_once("$board_skin_path/view_head.skin.php");

$this_url = "$g4[url]/$g4[bbs]/board.php?bo_table=$bo_table&wr_id=$wr_id";

if ($mw_basic[cf_umz]) 
    $this_url = $view[wr_umz];
else if ($mw_basic[cf_shorten]) 
    $this_url = $shorten;
?>
<style type="text/css">
body { padding:10px; margin:0; }
div#mw_print { border:1px solid #d9d9d9; padding:20px; }
div#mw_print div { margin:0 0 5px 0; }
div#mw_print .mw_subject { font-size:15px; height:30px; font-weight:bold; margin:0 0 10px 0; border-bottom:1px solid #d9d9d9; }
div#mw_print .mw_name { font-size:12px; height:20px; }
div#mw_print .mw_date { font-size:12px; height:20px; }
div#mw_print .mw_content { font-size:12px; border-top:1px solid #ddd; padding:20px 10px; line-height:20px; }
div#mw_print .mw_label { float:left; width:50px; font-weight:bold; }
</style>

<script type="text/javascript" src="<?=$board_skin_path?>/mw.js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    window.print();
});
</script>


<div id="mw_print">

    <div class="mw_subject">
	<? if ($is_category) { echo ($category_name ? "[$view[ca_name]] " : ""); } ?>
	<?=cut_hangul_last(get_text($view[wr_subject]))?> <?=$view[icon_secret]?>
    </div>

    <div class="mw_name"><span class="mw_label">글쓴이</span> : <? if ($mw_basic[cf_attribute] != "anonymous") { ?><?=$view[wr_name]?> <? } ?></div>
    <div class="mw_date"><span class="mw_label">작성일</span> : <?=substr($view[wr_datetime],2,14)?></div>
    <div class="mw_url"><span class="mw_label">글주소</span> : <?=$this_url?></div>

    <div><?=$mw_basic[cf_content_head]?></div>

    <div class="mw_content">
	<?echo $view[rich_content]; // {이미지:0} 과 같은 코드를 사용할 경우?>
    </div>


    <div><?=$mw_basic[cf_content_tail]?></div>

</div>

<?
include_once("$g4[path]/tail.sub.php");
