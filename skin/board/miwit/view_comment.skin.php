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

$mw_is_view = false;
$mw_is_list = false;
$mw_is_write = false;
$mw_is_comment = true;

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

define('SECRET_COMMENT', "비밀글 입니다.");

// 실명인증 & 성인인증
if (($mw_basic[cf_kcb_read] || $write[wr_kcb_use]) && !is_okname()) {
    check_okname();
} else {

if (!is_array($mw_membership)) {
    $mw_membership = array();
    $mw_membership_icon = array();
}

if ($cwin && $mw_basic[cf_read_level] && $write[wr_read_level] && $write[wr_read_level] > $member[mb_level]) {
    alert_close("글을 읽을 권한이 없습니다.");
}

if ($cwin && $mw_basic[cf_comment_level] && $mw_basic[cf_comment_level] > $member[mb_level]) {
    alert_close("글을 읽을 권한이 없습니다.");
}

if ($cwin && ($mw_basic[cf_must_notice_read] || $mw_basic[cf_must_notice_comment])) // 공지 읽기 필수
{
    //$tmp_notice = str_replace($notice_div, ",", trim($board[bo_notice]));
    $tmp_notice = implode(",", array_filter(explode($notice_div, trim($board[bo_notice])), "strlen"));
    $cnt_notice = sizeof(explode(",", $tmp_notice));

    if ($tmp_notice) {
        $sql = "select count(*) as cnt from $mw[must_notice_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and wr_id in ($tmp_notice)";
        $row = sql_fetch($sql);
        if ($row[cnt] != $cnt_notice)
            alert_close("$board[bo_subject] 공지를 모두 읽으셔야 글읽기가 가능합니다.");
    }
}

$write_error = '';
if (!$is_member && !$is_comment_write && $mw_basic[cf_comment_write]) {
    $write_error = "readonly onclick=\"alert('로그인 하신 후 코멘트를 작성하실 수 있습니다.'); return false;\"";
}

if ($mw_basic[cf_kcb_comment] && !is_okname()) {
    $is_comment_write = false;
    $write_error = '';
}

if ($is_comment_write) {
    if ($mw_basic[cf_comment_ban] && $write[wr_comment_ban]) {
        $is_comment_write = false;
    }
}

if ($mw_basic[cf_must_notice_comment]) {
    //$tmp_notice = str_replace($notice_div, ",", trim($board[bo_notice]));
    $tmp_notice = implode(",", array_filter(explode($notice_div, trim($board[bo_notice])), "strlen"));
    $cnt_notice = sizeof(explode(",", $tmp_notice));

    if ($tmp_notice) {
        $sql = "select count(*) as cnt from $mw[must_notice_table] where bo_table = '$bo_table' and mb_id = '$member[mb_id]' and wr_id in ($tmp_notice)";
        $row = sql_fetch($sql);
        if ($row[cnt] != $cnt_notice) {
            $is_comment_write = false;
            $write_error = "readonly onclick=\"alert('$board[bo_subject] 게시판의 공지를 모두 읽으셔야 코멘트를 작성하실 수 있습니다.'); return false;\"";
        }
            //alert("$board[bo_subject] 공지를 모두 읽으셔야 글읽기가 가능합니다.");
    }
}

if ($mw_basic[cf_comment_write_count]) {
    $sql = " select count(*) as cnt from $write_table where wr_num = '$write[wr_num]' and wr_is_comment = '1' ";
    if ($board[bo_comment_level] == 1 && !$is_member)
        $sql.= " and wr_ip = '$_SERVER[REMOTE_ADDR]' ";
    else
        $sql.= " and mb_id = '$member[mb_id]' ";

    $tmp = sql_fetch($sql);
    if ($tmp[cnt] >= $mw_basic[cf_comment_write_count]) {
        $is_comment_write = true;
        $write_error = "readonly onclick=\"alert('게시물당 코멘트를  {$mw_basic[cf_comment_write_count]}번만 작성하실 수 있습니다.'); return false;\"";
    }
}

if (!$write_error and $mw_basic[cf_qna_enough] and $write[wr_qna_status] > 0) {
    $write_error = "readonly onclick=\"alert('답변이 종료되었습니다.'); return false;\"";
}

// 컨텐츠샵 멤버쉽
if (function_exists("mw_cash_is_membership")) {
    $is_membership = @mw_cash_is_membership($member[mb_id], $bo_table, "mp_comment");
    if ($is_membership == "no")
        ;
    else if ($is_membership != "ok")
        $is_comment_write = false;
}

if (!$is_admin && $member['mb_sex'] == 'F' && !strstr($mw_basic['cf_gender_w'], 'c')) {
    $is_comment_write = false;
    $write_error = "readonly onclick=\"alert('여자는 댓글작성 권한이 없습니다.'); return false;\"";
}
if (!$is_admin && $member['mb_sex'] == 'M' && !strstr($mw_basic['cf_gender_m'], 'c')) {
    $is_comment_write = false;
    $write_error = "readonly onclick=\"alert('남자는 댓글작성 권한이 없습니다.'); return false;\"";
}

if (!$is_admin && $mw_basic['cf_cash_grade_use'] && !$grade['gd_comment']) {
    $is_comment_write = false;
    $write_error = "readonly onclick=\"alert('[{$my_cash_grade['gd_name']}] 등급은 권한이 없습니다. '); return false;\"";
}

if (!$is_admin && $mw_basic['cf_age'] && strstr($mw_basic['cf_age_opt'], 'c')) {
    $msg = mw_basic_age($mw_basic['cf_age'], "comment");
    if ($msg) {
        $is_comment_write = false;
        $write_error = "readonly onclick=\"alert('{$msg}'); return false;\"";
    }
}

if ($cwin==1) {
    echo "<link rel='stylesheet' href='$board_skin_path/style.common.css' type='text/css'>";
    echo "<style type='text/css'> #mw_basic { width:98%; padding:10px; } </style>";
    echo "<div id=mw_basic>";
}

if (!$is_admin && $write[wr_view_block] && $cwin)
    alert("이 게시물 보기는 차단되었습니다. 관리자만 접근 가능합니다.");

// 코멘트 작성 기간
if ($mw_basic[cf_comment_period] > 0) {
    if ($g4[server_time] - strtotime($write[wr_datetime]) > 60*60*24*$mw_basic[cf_comment_period]) {
        if ($mw_basic[cf_comment_default]) $mw_basic[cf_comment_default] .= "\n";
        $mw_basic[cf_comment_default] .= "작성한지 $mw_basic[cf_comment_period]일이 지난 게시물에는 코멘트를 작성할 수 없습니다.";
    }
}

$is_singo_admin = mw_singo_admin($member[mb_id]);

echo "<div style='margin:0;padding:0;font-size:0;line-height:0;height:0;clear:both;'></div>";

echo bc_code($mw_basic[cf_comment_head]);
?>
<link rel="stylesheet" href="<?php echo $board_skin_path?>/mw.js/mw.star.rate/jquery.mw.star.rate.css" type="text/css">
<script src="<?php echo $board_skin_path?>/mw.js/mw.star.rate/jquery.mw.star.rate.js"></script>

<? if ($mw_basic[cf_source_copy] && $cwin) { // 출처 자동 복사 ?>
<script type="text/javascript" src="<?=$board_skin_path?>/mw.js/autosourcing.open.compact.js"></script>
<style type="text/css">
DIV.autosourcing-stub { display:none }
DIV.autosourcing-stub-extra { position:absolute; opacity:0 }
</style>
<script type="text/javascript">
AutoSourcing.setTemplate("<p style='margin:11px 0 7px 0;padding:0'> <a href='{link}' target='_blank'> [출처] {title} - {link}</a> </p>");
AutoSourcing.setString(<?=$wr_id?> ,"<?=$config[cf_title];//$view[wr_subject]?>", "<?=$view[wr_name]?>", "<?=$copy_url?>");
AutoSourcing.init( 'view_%id%' , true);
</script>
<? } ?>

<script>
// 글자수 제한
var char_min = parseInt(<?=$comment_min?>); // 최소
var char_max = parseInt(<?=$comment_max?>); // 최대
</script>

<? if ($cwin==1) { ?>
<link href="<?php echo $board_skin_path?>/mw.css/font-awesome-4.2.0/css/font-awesome.css" rel="stylesheet">
<script type="text/javascript" src="<?="$board_skin_path/mw.js/mw_image_window.js"?>"></script>
<table width=100% cellpadding=10 align=center><tr><td>
<?}?>

<!-- 코멘트 리스트 -->
<div id="commentContents">

<?php if ($mw_basic[cf_comment_notice]) { ?>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
    <td></td>
    <td width="100%">
        <table width=100% cellpadding=0 cellspacing=0>
        <tr>
            <!-- 이름, 아이피 -->
            <td>
                <div class=mw_basic_comment_name><img src="<?=$board_skin_path?>/img/icon_notice.gif"></div>
            </td>
            <!-- 링크 버튼, 코멘트 작성시간 -->
            <td align=right>
                <!--
                <span class=mw_basic_comment_datetime><?=substr($view[wr_datetime],0,10)." (".get_yoil($view[wr_datetime]).") ".substr($view[wr_datetime],11,5)?></span>-->
            </td>
        </tr>
        </table>
        <table width=100% cellpadding=0 cellspacing=0 class=mw_basic_comment_notice>
        <tr>
            <td colspan=2>
                <div><?=mw_reg_str(nl2br($mw_basic[cf_comment_notice]))?></div>
            </td>
        </tr>
        </table>
    </td>
</tr>
</table>
<br/>

<?php }

$is_comment_best = array();
if ($mw_basic[cf_comment_best]) {
    $sql = " select * from $write_table where wr_parent = '$wr_id' and wr_is_comment = '1' and wr_good > 0 ";
    if ($mw_basic[cf_comment_best_limit]) {
        $sql .= " and wr_good >= '$mw_basic[cf_comment_best_limit]' ";
    }
    $sql.= " order by wr_good desc, wr_datetime asc limit $mw_basic[cf_comment_best] ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
    // ============================= 베플 루프 시작 =============================

    $is_comment_best[] = $row[wr_id];

    if ($mw_basic[cf_comment_best_point])
        insert_point($row[mb_id], $mw_basic[cf_comment_best_point], '베플 선정', $bo_table, $row[wr_id], '베플');

    $tmp_name = get_text(cut_str($row[wr_name], $config[cf_cut_name])); // 설정된 자리수 만큼만 이름 출력
    if ($board[bo_use_sideview])
        $row[name] = get_sideview($row[mb_id], $tmp_name, $row[wr_email], $row[wr_homepage]);
    else
        $row[name] = "<span class='".($row[mb_id]?'member':'guest')."'>$tmp_name</span>";

    $row[trackback] = url_auto_link($row[wr_trackback]);
    $row[datetime] = substr($row[wr_datetime],2,14);

    $row[ip] = $row[wr_ip];
    if (!$is_admin)
        $row[ip] = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", "\\1.♡.\\3.\\4", $row[wr_ip]);
    else if ($row[mb_id] == $config[cf_admin])
        $row[ip] = "";

    include("{$board_skin_path}/view_comment_head.skin.php");
?>

<div class="mw_basic_comment_best">
<table width=100% cellpadding=0 cellspacing=0>
<tr>
<?php if (!$mw_basic['cf_comment_image_no']) { ?>
<td valign="top" style="text-align:left;">
    <img src="<?=$comment_image?>" class="comment_image" 
        style="width:58px; height:58px; border:3px solid #f2f2f2; margin:0 10px 5px 0;">
    <?php
    if ($mw_basic[cf_icon_level] && !$view[wr_anonymous] && $mw_basic[cf_attribute] != "anonymous" && $row[mb_id] && $row[mb_id] != $config[cf_admin]) { 
        $level = mw_get_level($row[mb_id]);
        echo "<div class=\"icon_level".($level+1)."\">&nbsp;</div>";
        $exp = $icon_level_mb_point[$row[mb_id]] - $level*$mw_basic[cf_icon_level_point];
        $per = round($exp/$mw_basic[cf_icon_level_point]*100);
        if ($per > 100) $per = 100;
        echo "<div class=\"level_exp_bg_{$row[mb_id]}\"><div class=\"level_exp_dot_{$row[mb_id]}\">&nbsp;</div></div>";
        echo "<style type=\"text/css\">
            .level_exp_bg_{$row[mb_id]} { background:url($board_skin_path/img/level_exp_bg.gif); width:64px; height:3px; font-size:1px; line-height:1px; margin:5px 0 0 0; }
            .level_exp_dot_{$row[mb_id]} { background:url($board_skin_path/img/level_exp_dot.gif); width:$per%; height:3px; }
        </style>";
    }
    ?>
</td>
<?php } ?>
<td width="2" bgcolor="#dedede"><div style="width:2px;"></div></td>
<td><div style="width:10px;"></div></td>

<td width="100%" valign="top">
    <table width=100% height="28" cellpadding=0 cellspacing=0 style="background:url(<?=$board_skin_path?>/img/co_title_bg.gif);">
    <tr>
        <!-- 이름, 아이피 -->
        <td>
            <img src="<?=$board_skin_path?>/img/comment_best.gif" align="absmiddle">
            <? if ($mw_basic[cf_attribute] == 'qna' && $write[wr_qna_status] && $write[wr_qna_id] == $row[wr_id]) { ?> <img src="<?=$board_skin_path?>/img/icon_choose.png" align="absmiddle"> <? } ?>
            <span class=mw_basic_comment_name><?=$row[name]?></span>
            <? /*if ($is_ip_view && $row[ip]) { ?> <span class=mw_basic_comment_ip>(<?=$row[ip]?>)</span> <?}*/?>
            <? if ($is_admin or $is_singo_admin) { ?>
            <img src="<?=$board_skin_path?>/img/btn_intercept_small.gif" align=absmiddle title='접근차단' style="cursor:pointer" onclick="btn_intercept('<?=$row[mb_id]?>', '<?=$row[wr_ip]?>')">
            <img src="<?=$board_skin_path?>/img/btn_ip.gif" class="tooltip" align=absmiddle title='<?php echo $row['ip']?> 조회' style="cursor:pointer" onclick="btn_ip('<?=$row[wr_ip]?>')">
            <img src="<?=$board_skin_path?>/img/btn_ip_search.gif" class="tooltip" align=absmiddle title='<?php echo $row['ip']?> 검색' style="cursor:pointer" onclick="btn_ip_search('<?=$row[wr_ip]?>')">
            <? } ?>
            <span class="mw_basic_comment_datetime media-date"><?php echo $row['datetime2']?></span>
            <span class="mw_basic_comment_datetime media-date-sns"><?php echo $row['datetime_sns']?></span>

        </td>
        <!-- 링크 버튼, 코멘트 작성시간 -->
        <td align=right style="margin-right:10px;">
            <? if ($mw_basic[cf_comment_good]) { ?>
                <span class="mw_basic_comment_good"><a onclick="mw_comment_good(<?=$row[wr_id]?>, 'good')"><img src="<?=$board_skin_path?>/img/thumbs_up.png" align="absmiddle" alt="추천"/>추천</a>
                <span id="mw_comment_good_<?=$row[wr_id]?>"><?=$row[wr_good]?></span></span><? } ?>
            <? if ($mw_basic[cf_comment_nogood]) { ?>
                <span class="mw_basic_comment_nogood"><a onclick="mw_comment_good(<?=$row[wr_id]?>, 'nogood')">반대</a>
                <span id="mw_comment_nogood_<?=$row[wr_id]?>"><?=$row[wr_nogood]?></span></span><? } ?>
        </td>
    </tr>
    </table>

    <table width=100% cellpadding=0 cellspacing=0 class=mw_basic_comment_content>
    <tr>
        <td valign="top" style="background-color:#ffecd7;">
            <!-- 코멘트 출력 -->
            <div id=view_<?=$row[wr_id]?>_best>
            <?php echo $row[content] ?>
            </div>
            <? if ($row[trackback]) { echo "<p>".$row[trackback]."</p>"; } ?>
            <? if ($mw_basic[cf_source_copy]) { // 출처 자동 복사 ?>
            <? $copy_url = set_http("{$g4[url]}/{$g4[bbs]}/board.php?bo_table={$bo_table}&wr_id={$wr_id}#c_{$row[wr_id]}"); ?>
            <script type="text/javascript">
            AutoSourcing.setString(<?=$row[wr_id]?> ,"<?=$config[cf_title]?>", "<?=$row[wr_name]?>", "<?=$copy_url?>");
            </script>
            <? } ?>
        </td>
    </tr>
    </table>
</tr>
<tr>
<td colspan="4" height="10"></td>
</tr>
</table>
</div>

<?  } } ?>

<a id="cs" name="cs"></a>
<br>
<? /*if ($is_admin) { ?> <input onclick="$('input[name=chk_comment_id[]]').attr('checked', this.checked);" type=checkbox> 코멘트 전체 선택 <? }*/ ?>
<?php if ($is_admin) { ?>
<script>
function comment_check(obj) {
    $("input[name='chk_comment_id[]']").attr('checked', obj.checked);
    $("input[name='chk_comment_id[]']").prop('checked', obj.checked);
}
</script>
<input type="checkbox" onclick="comment_check(this)" id="all_comment">
<label for="all_comment">코멘트 전체 선택</label>
<br>
<br>
<?php } ?>

<?php
$total_count = count($list);

if ($mw_basic[cf_comment_page]) { // 코멘트 페이지
    $rows = $mw_basic[cf_comment_page_rows];;
    $total_page  = @ceil($total_count / $rows);  // 전체 페이지 계산
    if (!$total_page) $total_page = 1;
    
    if (!is_numeric($cpage)) { // 페이지가 없으면
        if ($board[bo_reply_order]) {
            if ($mw_basic[cf_comment_page_first])
                $cpage = 1;
            else
                $cpage = $total_page;
        }
        else {
            if ($mw_basic[cf_comment_page_first])
                $cpage = $total_page;
            else
                $cpage = 1;
        }
    }
    if ($_c) { // 코멘트 페이지 찾아가기
        $t_rows = 1;
        $t_page = 1;
        for ($i=0, $m=sizeof($list); $i<$m; $i++) {
            if ($list[$i][wr_id] == $_c) {
                $cpage = $t_page;
            } else {
                if ($t_rows++ % $rows == 0) {
                    $t_page++;
                }
            }
        }
    }
    $from_record = ($cpage - 1) * $rows; // 시작 열을 구함  */
    $to_record = $cpage == $total_page ? $total_count : $rows * $cpage;

    //$qstr = preg_replace("/(\&page=.*)/", "", $qstr);
    $comment_pages = get_paging($config[cf_write_pages], $cpage, $total_page, "{$_SERVER['SCRIPT_NAME']}?bo_table=$bo_table&wr_id=$wr_id{$qstr}&cpage=");
    if (is_g5()) {
        $comment_pages = preg_replace("/&cpage=&amp;page=([0-9]+)\"/i", "&cpage=$1\"", $comment_pages);
    }
    $comment_pages = preg_replace("/(\&cpage=[0-9]+)/", "$1#cs", $comment_pages);
    
} else {
    $from_record = 0;
    $to_record = $total_count;
}

for ($i=0; $i<$to_record; $i++) {
    $row = $list[$i];
    $res = include("{$board_skin_path}/view_comment_head.skin.php");
    if (!$res) continue;
    $list[$i] = $row;

    if ($mw_basic[cf_include_comment_main] && is_mw_file($mw_basic[cf_include_comment_main])) {
        include($mw_basic[cf_include_comment_main]);
    }
?>
<a name="c_<?=$comment_id?>"></a>

<table width=100% height="1" cellpadding=0 cellspacing=0 style="margin-bottom:5px;">
<tr>
    <td style="line-height:0;"><? for ($k=0; $k<strlen($list[$i][wr_comment_reply]); $k++) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
    <td width="100%" height="1" style="border-top:1px solid #ddd;"></td>
</tr>
</table>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
    <td><? for ($k=0; $k<strlen($list[$i][wr_comment_reply]); $k++) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>

<?php if (!$mw_basic['cf_comment_image_no']) { ?>
    <td valign="top" style="text-align:left;">
        <div class="comment_image <?php echo $comment_class?>">
        <img src="<?php echo $comment_image?>"
            <?php
            if ($is_comment_image) { echo "onclick='mw_image_window(this, {$tmpsize[0]}, {$tmpsize[1]});'"; }
            else if (($is_member && $list[$i][mb_id] == $member[mb_id] && !$list[$i][wr_anonymous]) || $is_admin) { echo "onclick='mw_member_photo(\"{$list[$i]['mb_id']}\");'"; }?>>

        <? if (($is_member && $list[$i][mb_id] == $member[mb_id] && !$list[$i][wr_anonymous]) || $is_admin) { ?>
        <div><a href="javascript:mw_member_photo('<?=$list[$i][mb_id]?>')"
            style="font:normal 11px; color:#888; text-decoration:none;"><? echo $is_comment_image ? "사진변경" : "사진등록"; ?></a></div>
        <?php } ?>
        </div>

        <script>
        function mw_member_photo(mb_id) {
            window.open('<?=$board_skin_path?>/mw.proc/mw.comment.image.php?bo_table=<?=$bo_table?>&mb_id='+mb_id,'comment_image','width=500,height=350');
        }
        </script>

        <?
        if ($mw_basic[cf_icon_level] && !$list[$i][wr_anonymous] && $mw_basic[cf_attribute] != "anonymous" && $list[$i][mb_id] && $list[$i][mb_id] != $config[cf_admin]) { 
            $level = mw_get_level($list[$i][mb_id]);
            echo "<div class=\"icon_level".($level+1)."\">&nbsp;</div>";
            $exp = $icon_level_mb_point[$list[$i][mb_id]] - $level*$mw_basic[cf_icon_level_point];
            $per = round($exp/$mw_basic[cf_icon_level_point]*100);
            if ($per > 100) $per = 100;
            echo "<div style=\"background:url($board_skin_path/img/level_exp_bg.gif); width:61px; height:3px; font-size:1px; line-height:1px; margin:5px 0 0 3px;\">";
            echo "<div style=\"background:url($board_skin_path/img/level_exp_dot.gif); width:$per%; height:3px;\">&nbsp;</div>";
            echo "</div>";
        }
        ?>
        <?php if ($is_admin or $is_singo_admin) { ?>
            <div class="division" style="padding-top:10px;"></div>
            <div class="mw_basic_comment_func user">
                <a href="#;" title='접근차단' onclick="btn_intercept('<?=$list[$i][mb_id]?>', '<?=$list[$i][wr_ip]?>')"><i class="fa fa-user-times"></i></a>
                <a href="#;" class="tooltip" title='<?php echo $list[$i]['ip']?> 조회' onclick="btn_ip('<?=$list[$i][wr_ip]?>')"><i class="fa fa-info-circle"></i></a>
                <a href="#;" class="tooltip" title='<?php echo $list[$i]['ip']?> 검색' onclick="btn_ip_search('<?=$list[$i][wr_ip]?>')"><i class="fa fa-search"></i></a>
            </div>
            <div class="division"></div>
        <?php } ?>

    </td>
<?php } ?>
    <td width="2" bgcolor="#dedede"><div style="width:2px;"></div></td>
    <td><div style="width:10px;"></div></td>

    <td width="100%" valign="top">
        <table width=100% height="28" cellpadding=0 cellspacing=0>
        <tr>
            <!-- 이름, 아이피 -->
            <td>
                <? if ($list[$i][wr_is_mobile]) echo "<img src='$board_skin_path/img/icon_mobile.png' align='absmiddle' class='comment_mobile_icon'>"; ?>
                <? if ($is_admin) { ?> <input type="checkbox" name="chk_comment_id[]" class="chk_comment_id" value="<?=$list[$i][wr_id]?>"> <? } ?>
                <? if ($mw_basic[cf_attribute] == 'qna' && $write[wr_qna_status] && $write[wr_qna_id] == $list[$i][wr_id]) { ?> <img src="<?=$board_skin_path?>/img/icon_choose.png" align="absmiddle"> <? } ?>
                <span class=mw_basic_comment_name><?=$list[$i][name]?></span>
                <span class="mw_basic_comment_datetime media-date"><i class="fa fa-clock-o"></i> <?php echo $row['datetime2']?></span>
                <span class="mw_basic_comment_datetime media-date-sns"><i class="fa fa-clock-o"></i> <?php echo $row['datetime_sns']?></span>

                <div class="mw_basic_comment_func" style="float:right;">
                <? if ($list[$i][is_edit]) { echo "<a href=\"javascript:comment_box('{$comment_id}', 'cu');\" title='수정'><i class='fa fa-cut fa-square-o'></i></a> "; } ?>
                <? if ($list[$i][is_del])  { echo "<a href=\"javascript:comment_delete('{$list[$i][del_link]}');\" title='삭제'><i class='fa fa-remove'></i></a> "; } ?>
                </div><!--mw_basic_comment_func-->

            </td>
        </tr>
        </table>

        <table width=100% cellpadding=0 cellspacing=0 class=mw_basic_comment_content>
        <tr>
            <td valign="top">
                <?php if (in_array($list[$i][wr_id], $is_comment_best)) { ?>
                <div id="info_best_reply">
                    베플로 선택된 게시물입니다.
                    <input type="button" value="원문확인▼" id="btn_best_reply_view_<?=$list[$i][wr_id]?>">
                </div>
                <style>
                #info_best_reply { margin:0 0 20px 0; color:#F56C07; }
                #view_<?=$list[$i][wr_id]?> { display:none; } 
                #btn_best_reply_view_<?=$list[$i][wr_id]?> {
                    background-color:#fff; color:#444; cursor:pointer; font-size:12px; border:0; }
                </style>
                <script>
                $("#btn_best_reply_view_<?=$list[$i][wr_id]?>").click(function () {
                    $("#view_<?=$list[$i][wr_id]?>").toggle('slow');
                    if ($("#btn_best_reply_view").val() == "원문확인▲")
                        $("#btn_best_reply_view").val("원문확인▼");
                    else
                        $("#btn_best_reply_view").val("원문확인▲");
                });
                </script>
                <? } ?>
                <!-- 코멘트 출력 -->
                <div id=view_<?=$list[$i][wr_id]?>>
                <?php echo $row[content] ?>
                </div>
                <?php if ($list[$i][trackback]) { echo "<p>".$list[$i][trackback]."</p>"; } ?>
                <?php if ($mw_basic[cf_source_copy]) { // 출처 자동 복사 ?>
                <?php $copy_url = set_http("{$g4[url]}/{$g4[bbs]}/board.php?bo_table={$bo_table}&wr_id={$wr_id}#c_{$list[$i][wr_id]}"); ?>
                <script>
                AutoSourcing.setString(<?=$list[$i][wr_id]?>,
                    "<?=$config[cf_title]?>", "<?=$list[$i][wr_name]?>", "<?=$copy_url?>");
                </script>
                <? } ?>
            </td>
        </tr>
        </table>

        <div class="division"></div>

        <?php if ($list[$i]['mb_id'] != '@lucky-writing') { ?>
        <div class="comment_buttons">
            <?php if ($history_href) {?>
            <span class="button">
                <a href="<?php echo $history_href?>" title="변경기록">
                    <i class='fa fa-history'></i>
                    <span class='media-comment-button'>로그</span>
                </a>
            </span>
            <?php } ?>

            <?php if ($is_member and $list[$i]['singo_href']) { ?>
            <span class="button">
                <a href="<?=$list[$i][singo_href]?>">
                    <i class="fa fa-warning"></i>
                    <span class='media-comment-button'>신고</span>
                </a>
            </span>
            <?php } ?>

            <span class="mw_basic_comment_url button" value="<?=$list[$i][wr_id]?>">
                <i class="fa fa-anchor"></i>
                <span class='media-comment-button'>주소</span>
            </span>
            <?php
            if ($mw_basic[cf_attribute] == 'qna'
                && ($is_admin || !$write[wr_qna_status])
                && $member[mb_id]
                && ($member[mb_id] == $write[mb_id] || $is_admin)
                && !$view[is_notice]) { ?>
                <span class="mw_basic_qna_choose button">
                    <a onclick="mw_qna_choose(<?=$list[$i][wr_id]?>)">
                        <i class="fa fa-graduation-cap"></i>
                        <span class='media-comment-button'>채택</span>
                    </a>
                </span>
            <?php } ?>
            <?php if ($mw_basic[cf_comment_good]) { ?>
                <span class="mw_basic_comment_good button">
                    <a onclick="mw_comment_good(<?=$list[$i][wr_id]?>, 'good')">
                        <i class="fa fa-thumbs-o-up"></i>
                        <span class='media-comment-button'>추천</span>
                        <span id="mw_comment_good_<?=$list[$i][wr_id]?>"><?=$list[$i][wr_good]?></span>
                    </a>
                </span>
            <?php } ?>
            <?php if ($mw_basic[cf_comment_nogood]) { ?>
                <span class="mw_basic_comment_nogood button">
                    <a onclick="mw_comment_good(<?=$list[$i][wr_id]?>, 'nogood')">
                        <i class="fa fa-thumbs-o-down"></i>
                        <span class='media-comment-button'>반대</span>
                        <span id="mw_comment_nogood_<?=$list[$i][wr_id]?>"><?=$list[$i][wr_nogood]?></span>
                    </a>
                </span>
            <?php } ?>
            <?php if ($list[$i][is_reply]) { ?>
                <span class="mw_basic_comment_reply button">
                    <a href="javascript:comment_box('<?php echo $comment_id?>', 'c', '<?php echo $list[$i]['wr_name']?>');">
                    <i class='fa fa-reply fa-rotate-180'></i>
                    답글
                    </a>
                </span>
            <?php } ?>
        </div>
        <div style="font-size:0; line-height:0; height:0; clear:both;"></div>

        <div id='edit_<?=$comment_id?>' style='display:none;'></div><!-- 수정 -->
        <div id='reply_<?=$comment_id?>' style='display:none;'></div><!-- 답변 -->
        <input type="hidden" id='secret_<?=$comment_id?>'
            value="<?=strstr($list[$i][wr_option], 'secret')?'1':'';?>"><!-- 비밀글 -->
        <input type="hidden" id='html_<?=$comment_id?>'
            value="<?=strstr($list[$i][wr_option], 'html2')?'1':'';?>"><!-- html -->
        <textarea id="save_comment_<?php echo $comment_id?>"
            style="display:none;"><?php if ($is_admin or $list[$i]['mb_id'] == $member['mb_id']) echo get_text($list[$i][content1], 0)?></textarea>
        <?php } ?>
    </td>
</tr>
<tr>
    <td colspan="4" height="10"></td>
</tr>
</table>
<? } ?>
</div>
<!-- 코멘트 리스트 -->

<? if ($mw_basic[cf_kcb_comment] && !is_okname()) { ?>
<div style="text-align:center; padding:20px 0 20px 0; margin:10px 0 10px 0; border:1px solid #eaeaea; color:#777;">
    <?=$mw_basic[cf_kcb_type]=='okname'?'실명인증':'성인인증'?> 후 댓글을 입력하실 수 있습니다.
    <a style="cursor:pointer; color:#777;" onclick="window.open('<?=$board_skin_path?>/mw.okname/?bo_table=<?=$bo_table?>', 'okname', 'width=600,height=500')">[인증하기]</a>
</div>
<? } ?>

<? if ($mw_basic[cf_comment_page]) { // 코멘트 페이지 ?>
<div class="mw_basic_comment_page">
<?
/*
$comment_pages = str_replace("처음", "<img src='$board_skin_path/img/page_begin.gif' border='0' align='absmiddle' title='처음'>", $comment_pages);
$comment_pages = str_replace("이전", "<img src='$board_skin_path/img/page_prev.gif' border='0' align='absmiddle' title='이전'>", $comment_pages);
$comment_pages = str_replace("다음", "<img src='$board_skin_path/img/page_next.gif' border='0' align='absmiddle' title='다음'>", $comment_pages);
$comment_pages = str_replace("맨끝", "<img src='$board_skin_path/img/page_end.gif' border='0' align='absmiddle' title='맨끝'>", $comment_pages);
*/
echo $comment_pages;
?>
</div>
<? } ?>

<? if ($is_comment_write || $write_error) { ?>

<!-- 질문 보류 -->
<?
if ($mw_basic[cf_attribute] == 'qna' && ($member[mb_id] == $write[mb_id] || $is_admin) && $write[mb_id] && $write[wr_qna_status] == 0 && !$view[is_notice]) {
    $hold_point = round($write[wr_qna_point] * $mw_basic[cf_qna_hold]/100, 0);
?>
<div class="mw_basic_qna_info">
    <div>
        <b><?=$write[wr_name]?></b>님! 원하시는 답변이 없으면 질문을 보류상태로 변경하실 수 있습니다.
        <a href="javascript:mw_qna_choose(0)">[보류하기]</a>
    </div>
    <? if ($mw_basic[cf_qna_hold]) { ?>
    <div class="info2">
        질문을 보류하면 질문 포인트의 <span class="num"><?=$mw_basic[cf_qna_hold]?>% (<b><?=$hold_point?></b> 포인트)</span> 만 되돌려드립니다.
    </div>
    <? } ?>
</div>
<? } ?>

<?php echo bc_code($mw_basic[cf_comment_tail]); ?>

<!-- 코멘트 입력 -->

<?
// 에디터
if (($mw_basic[cf_comment_editor] && $is_comment_write) || ($mw_basic[cf_admin_dhtml_comment] && $is_admin))
    $is_comment_editor = true;
else
    $is_comment_editor = false;

// 모바일 접근시 에디터 사용안함
if (mw_agent_mobile()) {
    $is_comment_editor = false;
}

if (!$mw_basic[cf_comment_default])
    $mw_basic[cf_comment_default] = trim($mw_basic[cf_comment_write_notice]);

if ($mw_basic[cf_comment_default] && $is_comment_editor)
    $mw_basic[cf_comment_default] = nl2br($mw_basic[cf_comment_default]);

if (!$mw_basic[cf_editor])
    $mw_basic[cf_editor] = "cheditor";

if (is_g5())
{
    $is_comment_editor = false;
    include_once(G5_EDITOR_LIB);

    /*
    $editor_html = editor_html('wr_content', '', $is_comment_editor);
    $editor_js = '';
    $editor_js .= get_editor_js('wr_content', $is_comment_editor);
    $editor_js .= chk_editor_js('wr_content', $is_comment_editor);*/

}
else if ($is_comment_editor && $mw_basic[cf_editor] == "cheditor") {
    include_once("$g4[path]/lib/cheditor4.lib.php");
    echo "<script src='$g4[cheditor4_path]/cheditor.js'></script>";
    echo cheditor1('wr_content', '100%', '100');
}
?>


<a name="c_write"></a>

<div style="padding:5px 0 0 0;">
<a href="javascript:comment_box('', 'c');" class="fa-button"><i class="fa fa-pencil"></i> 코멘트입력</a>
<?php if ($is_admin) { ?>
    <button class="fa-button" onclick="comment_all_delete()"><i class="fa fa-remove"></i> 코멘트삭제</button>
<?php } ?>
</div>

<?php if ($mw_basic['cf_rate_level'] && $mw_basic['cf_rate_level'] <= $member['mb_level']) { ?>
<div id="rate_ajax"></div>
<script>
$(document).ready(function() {
    comment_rate_run();
});

function comment_rate_run()
{
    if (!Date.now) {
        Date.now = function() { return new Date().getTime(); };
    }
    var t = Date.now() ;

    $.get("<?php echo $board_skin_path?>/mw.proc/mw.rate.php", {
        "bo_table" : "<?php echo $bo_table?>",
        "wr_id" : "<?php echo $wr_id?>",
        "t" : t
    }, function (html) {
        $("#rate_ajax").html(html);
    });
}
</script>
<?php } //cf_rate_level?>

<div id=mw_basic_comment_write>

<div id=mw_basic_comment_write_form>

<form name="fviewcomment" method="post" action="<?php echo $g4['bbs_path']?>/write_comment_update.php" onsubmit="return fviewcomment_submit(this);" autocomplete="off" style="margin:0;" enctype="multipart/form-data">
<input type=hidden name=w           id=w value='c'>
<input type=hidden name=bo_table    value='<?=$bo_table?>'>
<input type=hidden name=wr_id       value='<?=$wr_id?>'>
<input type=hidden name=comment_id  id='comment_id' value=''>
<input type=hidden name=sca         value='<?=$sca?>' >
<input type=hidden name=sfl         value='<?=$sfl?>' >
<input type=hidden name=stx         value='<?=$stx?>'>
<input type=hidden name=spt         value='<?=$spt?>'>
<input type=hidden name=page        value='<?=$page?>'>
<input type=hidden name=cwin        value='<?=$cwin?>'>
<? if ($is_comment_editor) { ?>
<input type=hidden name=html        value='html1'>
<? } ?>

<input type="hidden" name="wr_rate" id="wr_rate" value="0">

<?php
if (!$is_member) {
    if (!$name) $name = get_cookie("mw_cookie_name");
    if (!$email) $email = get_cookie("mw_cookie_email");
    if (!$homepage) $homepage = get_cookie("mw_cookie_homepage");
}
?>

<table border="0" cellpadding="0" cellspacing="0" style="margin-left:10px;" class="comment_write">
<?php if ($is_guest && !$write_error) { ?>
<tr>
    <td width="80"> 이름 </td>
    <td style="padding:3px 0 3px 0;">
        <input type=text maxlength=20 style="width:80px;" name="wr_name" value="<?php echo $name?>" itemname="이름" required class="mw_basic_text" <?=$write_error?>>
    </td>
</tr>
<tr>
    <td> 비밀번호 </td>
    <td style="padding:3px 0 3px 0;">
        <input type=password maxlength=20 style="width:80px;" name="wr_password" itemname="패스워드" required class=mw_basic_text <?=$write_error?>>
    </td>
</tr>
<?}?>

<?php if ($is_guest && $captcha_html && is_g5()) { //자동등록방지  ?>
<tr>
    <td>자동등록방지</td>
    <td style="padding:3px 0 3px 0;">
        <?php echo $captcha_html ?>
    </td>
</tr>
<?php } else if (is_mw_file("$g4[bbs_path]/kcaptcha_session.php") && $is_guest && !$write_error) { ?>
<tr>
    <td> 자동등록방지 </td>
    <td style="padding:3px 0 3px 0;">
        <script type="text/javascript"> var md5_norobot_key = ''; </script>
        <table border=0 cellpadding=0 cellspacing=0 style="padding:2px 0 2px 0;">
        <tr>
            <td>
                <input title="우측의 글자를 입력하세요." type="input" name="wr_key" style="width:80px;" itemname="자동등록방지" required class="mw_basic_text">
                우측의 글자를 입력하세요.
            </td>
            <td width=85>
                <img id="kcaptcha_image" style="position:absolute; margin-top:-50px;"/>
            </td>

        </tr>
        </table>
        <? } elseif ($is_norobot) { ?>
        <table border=0 cellpadding=0 cellspacing=0 style="padding:2px 0 2px 0;">
        <tr>
            <td width=85>
                <?
                // 이미지 생성이 가능한 경우 자동등록체크코드를 이미지로 만든다.
                if (function_exists("imagecreate") && $mw_basic[cf_norobot_image]) {
                    echo "<img src=\"$g4[bbs_path]/norobot_image.php?{$g4['server_time']}\" border=0 align=absmiddle>";
                    $norobot_msg = "* 왼쪽의 자동등록방지 코드를 입력하세요.";
                }
                else {
                    echo $norobot_str;
                    $norobot_msg = "* 왼쪽의 글자중 <FONT COLOR='red'>빨간글자</font>만 순서대로 입력하세요.";
                }
                ?>
            </td>
            <td>
                <input title="왼쪽의 글자중 빨간글자만 순서대로 입력하세요." type=text size=10 name=wr_key itemname="자동등록방지" required class=mw_basic_text <?=$write_error?>>
                <?=$norobot_msg?>
            </td>
        </tr>
        </table>
        <?}?>
    </td>
</tr>

<tr>
    <td colspan="2" style="line-height:30px;">
        <? if (!$is_comment_editor) { ?>
        <? if ($mw_basic[cf_comment_html]) echo "<input type=\"checkbox\" id=\"wr_html\" name=\"html\" value=\"html2\"> <label for='wr_html'>html</label>"; ?>
        <? } ?>

        <? if (!$write_error && $mw_basic['cf_comment_secret_no'] <= $member['mb_level']) { ?>
        <input type=checkbox id="wr_secret" name="wr_secret" value="secret" <? if ($mw_basic[cf_comment_secret]) echo "checked" ?>>
        <label for="wr_secret">비밀글 </label>
        <? } else { ?>
        <span id="secret_reply" style="display:none">
            <input type=checkbox id="wr_secret" name="wr_secret" checked disabled> <label for="wr_secret">비밀글 </label>
        </span>
        <? } ?>

        <? if ($mw_basic[cf_anonymous]) {?>
        <input type="checkbox" name="wr_anonymous" id="wr_anonymous" value="1">
        <label for="wr_anonymous">익명</label>
        <? } ?>
        
        <?php
        if (!$is_comment_editor && ($comment_min || $comment_max)) {
            echo "<input type='checkbox' disabled>";
            if ($comment_min > 0) { echo "$comment_min 글자 이상 "; }
            if ($comment_max > 0) { echo "$comment_max 글자 까지 "; }
            echo " 작성하실수 있습니다, ";
            echo "현재 <span id=char_count>0</span> 글자 작성하셨습니다. ";
        }
        ?>

    </td>
</tr>
</table>

<table width=98% cellpadding=0 cellspacing=0 border=0>
<tr>
    <td>
        <?//php if (!is_g5() && (!$is_comment_editor || $mw_basic[cf_editor] != "cheditor")) { ?>
        <?php if ((!$is_comment_editor || $mw_basic[cf_editor] != "cheditor")) { ?>
        <textarea id="wr_content" name="wr_content" rows="6" itemname="내용" required
            <?php
            if (!$write_error) { 
                if ($is_comment_editor && $mw_basic[cf_editor] == "geditor") echo "geditor gtag=off "; //mode=off";
            }
            else
                echo $write_error;

            if (!$is_comment_editor && ($comment_min || $comment_max)) {
                echo " onkeyup=\"check_byte('wr_content', 'char_count');\" ";
            }
            ?>
            class=mw_basic_textarea style="width:100%; word-break:break-all;"><?=$mw_basic[cf_comment_default]?></textarea>
        <?php if (!$is_comment_editor && ($comment_min || $comment_max)) { ?>
        <script> check_byte('wr_content', 'char_count'); </script><?}?>
        <?php } ?>
        <?php
        if (is_g5())
            ;//echo $editor_html;
        else if ($is_comment_editor && $mw_basic[cf_editor] == "cheditor")
            echo "<textarea name='wr_content' id='tx_wr_content'>{$mw_basic[cf_comment_default]}</textarea>\n";
        ?>
    </td>
</tr>
</table>

<?php
if (trim($mw_basic[cf_comment_write_notice])) { 
    $comment_write_notice = $mw_basic[cf_comment_write_notice];
    $comment_write_notice = addslashes($comment_write_notice);

    $comment_write_notice_html = $comment_write_notice;
    $comment_write_notice_html = nl2br($comment_write_notice_html);
    $comment_write_notice_html = preg_replace("/\n/", "", $comment_write_notice_html);
    $comment_write_notice_html = preg_replace("/\r/", "", $comment_write_notice_html);

    $comment_write_notice = preg_replace("/\n/", "\\n", $comment_write_notice);
    $comment_write_notice = preg_replace("/\r/", "", $comment_write_notice);

if (!is_g5()) {
?>
<script>
$(document).ready(function () {
<?php if ($is_comment_editor) { ?>
    <?php if ($mw_basic[cf_editor] == "cheditor") { ?>
    ed_wr_content.editArea.blur();
    ed_wr_content.editArea.onfocus = function () {
        var ed = ed_wr_content.outputBodyHTML();
        if (ed == "<?=$comment_write_notice_html?>") {
            ed_wr_content.doc.body.innerHTML = '';
        }
    }
    <?php } else if ($mw_basic[cf_editor] == 'geditor') { ?>
    ged = document.getElementById("geditor_wr_content_frame").contentWindow.document.body;
    ged.onfocus = function () {
        var ed = document.getElementById('wr_content').value;
        if (ed == "<?$comment_write_notice_html?>") {
            ged.innerHTML = '';
        }
    }
    <?php } ?>
<?php } else { ?>
    $("#wr_content").focus(function () {
        if ($("#wr_content").val() == "<?=$comment_write_notice?>") {
            $("#wr_content").val('');
        }
    });
<?php } ?>
});

</script>
<?php } } ?>

<div style="height:40px; clear:both;">
    <div class="comment_submit_button">
        <div><button type="submit" class="fa-button primary center" accesskey="s" id="btn_comment_submit"><i class="fa fa-comment"></i> 입력</button></div>
        <?php if ($good_href || $nogood_href) { // 추천, 비추천?>
        <div><a href="#;" class="fa-button" onclick="mw_good_act('good')"><i class="fa fa-thumbs-o-up"></i> 추천</a></div>
        <div><a href="#;" class="fa-button" onclick="good_submit(fviewcomment, 'good')"><i class="fa fa-thumbs-o-up"></i> + <i class="fa fa-comment-o"></i></a></div>
        <?php } //good_href ?>
    </div>

    <div class="comment_function">
    <?php if ($mw_basic[cf_comment_emoticon] && !$is_comment_editor && !$write_error) {?>
    <button type="button" class="fa-button" name="btn_emoticon" style="*margin-right:10px;"><i class="fa fa-smile-o"></i> <span class="media-comment-button">이모티콘</span></button>
    <script>
    board_skin_path = '<?php echo $board_skin_path?>';
    bo_table = '<?php echo $bo_table?>';
    </script>
    <script src="<?php echo $board_skin_path?>/mw.js/mw.emoticon.js"></script>

    <?php } //comment_emoticon ?>

    <?php if ($mw_basic['cf_comment_specialchars']) {?>
    <button type="button" class="fa-button" name="btn_special"><i class="fa fa-magic"></i>
        <span class="media-comment-button">특수문자</span></button>
    <script>
    board_skin_path = '<?php echo $board_skin_path?>';
    </script>
    <script src="<?php echo $board_skin_path?>/mw.js/mw.specialchars.js"></script>
    <?php }//comment_specialchars ?>

    <?php if ($mw_basic[cf_comment_file] && $mw_basic[cf_comment_file] <= $member['mb_level'] && !$write_error) { ?>
    <button type="button" class="fa-button" onclick="$('#comment_file_layer').toggle('slow');"><i class="fa fa-save"></i> <span class="media-comment-button">첨부파일</span></button>
    <?php } // comment_file ?>
    </div>
</div>

<? if ($mw_basic[cf_comment_file] && $mw_basic[cf_comment_file] <= $member['mb_level']) { ?>
<div id="comment_file_layer" style="padding:5px 0 5px 5px; display:none;">
    <input type="file" name="bf_file" size="50" title='파일 용량 <?=$upload_max_filesize?> 이하만 업로드 가능'>
    <input type="checkbox" name="bf_file_del" value="1"> 첨부파일 삭제
</div>
<? } ?>

</form>

</div>
<div style="height:7px; line-height:0; font-size:0; clear:both;"></div>
</div> <!-- 코멘트 입력 끝 -->

<script src="<?="$g4[path]/js/jquery.kcaptcha.js"?>"></script>

<script>
var save_before = '';
var save_html = document.getElementById('mw_basic_comment_write').innerHTML;
function good_submit(f, good) {
    <? if (!is_g5() && $is_comment_editor && $mw_basic[cf_editor] == "cheditor") { ?>
    var ed = ed_wr_content.outputBodyHTML();
    <? } else { ?>
    var ed = document.getElementById('wr_content').value;
    <? } ?>

    if (is_empty(ed)) {
         alert("내용을 입력해주세요.");
         return false;   
    }

    if (!fviewcomment_submit(f)) return false;
    $.get("<?=$board_skin_path?>/mw.proc/mw.good.act.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&good="+good, function (data) {
        //alert(data);
        f.submit();
    });
}

function is_empty(ed)
{
    ed = ed.replace(/(^\s*)|(\s*$)/g, "");
    ed = ed.replace(/[&]nbsp[;]/gi,""); 
    ed = ed.replace(/[<]br[^>]*[>]/gi, "");
    ed = ed.replace(/[<]div[^>]*[>]/gi, "");
    ed = ed.replace(/[<][\/]div[^>]*[>]/gi, "");
    ed = ed.replace(/\s/g, "");

    return !ed;
}

<?php if (is_g5()) { ?>
function g5_editor_to_text()
{
    <?php echo get_editor_js('wr_content', $is_comment_editor)?>
}
<?php } ?>

function fviewcomment_submit(f)
{
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

    /*
    var s;
    if (s = word_filter_check(document.getElementById('wr_content').value))
    {
        alert("내용에 금지단어('"+s+"')가 포함되어있습니다");
        //document.getElementById('wr_content').focus();
        return false;
    }
    */

    <?php
    if (is_g5())
        echo $editor_js;
    elseif ($is_comment_editor && $mw_basic[cf_editor] == "cheditor")
        echo cheditor3('wr_content');
    ?>

    <?php if (!is_g5()) { ?>
    if (document.getElementById('tx_wr_content')) {
        if (is_empty(ed_wr_content.outputBodyHTML())) { 
            alert('내용을 입력하십시오.'); 
            ed_wr_content.returnFalse();
            return false;
        }
    }
    <?php } ?>
 
    var subject = "";
    var content = "";
    $.ajax({
        url: "<?=$board_skin_path?>/ajax.filter.php",
        type: "POST",
        data: {
            "subject": "",
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

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        f.wr_content.focus();
        return false;
    }

    // 양쪽 공백 없애기
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
    if (document.getElementById('wr_content')) {
        document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");
        <? if (!$is_comment_editor && ($comment_min || $comment_max)) { ?>
        if (char_min > 0 || char_max > 0)
        {
            check_byte('wr_content', 'char_count');
            var cnt = parseInt(document.getElementById('char_count').innerHTML);
            if (char_min > 0 && char_min > cnt)
            {
                alert("코멘트는 "+char_min+"글자 이상 쓰셔야 합니다.");
                return false;
            } else if (char_max > 0 && char_max < cnt)
            {
                alert("코멘트는 "+char_max+"글자 이하로 쓰셔야 합니다.");
                return false;
            }
        }
        else <? } ?> if (!document.getElementById('wr_content').value)
        {
            alert("코멘트를 입력하여 주십시오.");
            return false;
        }
    }
    if (typeof(f.wr_name) != 'undefined')
    {
        f.wr_name.value = f.wr_name.value.replace(pattern, "");
        if (f.wr_name.value == '')
        {
            alert('이름이 입력되지 않았습니다.');
            f.wr_name.focus();
            return false;
        }
    }

    if (typeof(f.wr_password) != 'undefined')
    {
        f.wr_password.value = f.wr_password.value.replace(pattern, "");
        if (f.wr_password.value == '')
        {
            alert('패스워드가 입력되지 않았습니다.');
            f.wr_password.focus();
            return false;
        }
    }

    <?php if(is_g5() && $is_guest) {?>
        <?php echo chk_captcha_js();  ?>
    <?php } else if ($is_guest) { ?>
    if (!check_kcaptcha(f.wr_key)) {
        return false;
    }
    <?php } ?>

    var geditor_status = document.getElementById("geditor_wr_content_geditor_status");
    if (geditor_status != null) {
        if (geditor_status.value == "TEXT") {
            f.html.value = "html2";
        }
        else if (geditor_status.value == "WYSIWYG") {
            f.html.value = "html1";
        }
    }

    //$("#btn_submit").html($("#loading").html());
    $(".comment_submit_button i").addClass("fa-spin fa-circle-o-notch");
    $(".comment_submit_button button").attr("disabled", "true");
    $(".comment_submit_button button").css("cursor", "not-allowed");

    return true;
}

function comment_box(comment_id, work, mb_nick)
{
    $("#secret_reply").css("display", "none");

    var el_id;
    // 코멘트 아이디가 넘어오면 답변, 수정
    if (comment_id)
    {
        if (work == 'c')
            el_id = 'reply_' + comment_id;
        else
            el_id = 'edit_' + comment_id;
    }
    else
        el_id = 'mw_basic_comment_write';

    if (save_before != el_id)
    {
        if (save_before)
        {
            $("#"+save_before).css("display", "none");
            $("#"+save_before).html('');
        }

        $("#"+el_id).css("display", "");
        $("#"+el_id).html(save_html);

        <?php if ($mw_basic['cf_comment_mention']) { ?>
        if (mb_nick != undefined && mb_nick != '') {
            <?php if ($is_comment_editor && $mw_basic['cf_editor'] == "cheditor") { ?>
                $("#tx_wr_content").val("[@"+mb_nick+"] ");
            <?php } else { ?>
                $("#wr_content").val("[@"+mb_nick+"] ");
            <?php } ?>
        }
        <?php } ?>

        // 코멘트 수정
        if (work == 'cu')
        {
            <?php if (!is_g5() && ($is_comment_editor && $mw_basic[cf_editor] == "cheditor")) { ?>
                $("#tx_wr_content").val($("#save_comment_" + comment_id).val());
            <?php } else if (is_g5() && $is_comment_editor) { ?>
                g5_editor_to_text();
            <?php } else { ?>
                $("#wr_content").val($("#save_comment_" + comment_id).val());

                $("#wr_content").removeAttr("readonly");
                $("#wr_content").removeAttr("onclick");
                $("#btn_comment_submit").removeAttr("readonly");
                $("#btn_comment_submit").removeAttr("onclick");

                <?php if (!$mw_basic[cf_comment_editor] && ($comment_min || $comment_max)) { ?>
                if (typeof char_count != 'undefined')
                    check_byte('wr_content', 'char_count');
                <?php } ?>

            <?php } ?>
            if ($("#secret_"+comment_id).val() == '1') {
                $("#secret_reply").css("display", "inline");
                $("#wr_secret").attr("checked", "true");
            }
            if ($("#html_"+comment_id).val() == '1')
                $("#wr_html").attr("checked", "true");
        }

        $("#comment_id").val(comment_id);
        $("#w").val(work);

        $("#wr_content").autogrow();

        save_before = el_id;

        <? if (!is_g5() && $is_comment_editor && $mw_basic[cf_editor] == "cheditor") { ?> ed_wr_content.run(); <? } ?> 
    }

    if (typeof geditor_textareas != "undefined") {
        geditor_load();
    }

    if (work == 'c') {
        <?php if (is_g5() && $is_guest) { ?>
            $("#captcha_reload").trigger("click");
	<?php } else  if (is_mw_file("$g4[bbs_path]/kcaptcha_session.php") && $is_guest && !$write_error) { ?>
            $.kcaptcha_run();
        <?php } ?>

        if ($("#secret_"+comment_id).val() == "1") {
            $("#secret_reply").css("display", "inline");
            $("#wr_secret").attr("checked", true);
            $("#wr_secret").prop("checked", true);
        }
    }
}

<? if ($is_admin) { ?>
function comment_all_delete()
{
    if (!$("input[name='chk_comment_id[]']:checked").length) {
        alert("삭제할 코멘트를 하나 이상 선택하세요.");
        return false;
    }

    if (!confirm("선택한 코멘트를 정말 삭제 하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다")) {
        return false;
    }

    comment_id = $("input[name='chk_comment_id[]']:checked").map(function () { return $(this).val() }).get().join(',');

    $.post("<?=$board_skin_path?>/mw.proc/mw.comment.delete.php", {
        'bo_table' : '<?=$bo_table?>',
        'comment_id' : comment_id,
        'token' : '<?=$token?>' },
    function (ret) {
        if (ret == 'ok')
            location.reload();
        else
            alert(ret);
    });
}
<? } ?>

//$(document).ready(function () {
<?php if (!is_g5()) { ?>
    comment_box('', 'c');
<?php } ?>
//});
</script>

<? } ?>

<script type="text/javascript">
function comment_delete(url)
{
    if (confirm("이 코멘트를 삭제하시겠습니까?")) location.href = url;
}
</script>

<? if ($mw_basic[cf_attribute] == 'qna' && ($is_admin || !$write[wr_qna_status]) && $member[mb_id] && ($member[mb_id] == $write[mb_id] || $is_admin) && !$view[is_notice]) { ?>
<script type="text/javascript">
function mw_qna_choose(wr_id) {
    if (wr_id) {
        if (!confirm("이 답변을 채택하시겠습니까?")) return;
    } else {
        if (!confirm("이 질문을 보류하시겠습니까?")) return;
    }

    $.get("<?=$board_skin_path?>/mw.proc/mw.qna.choose.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&choose_id="+wr_id, function (data) {
        data = data.split('|');
        alert(data[0]);
        if (data[1] == 'ok') location.reload();
    });
}
</script>
<? } ?>

<?  if ($mw_basic[cf_comment_good] || $mw_basic[cf_comment_nogood]) { ?>
<script type="text/javascript">
function mw_comment_good(wr_id, good) {
    $.get("<?=$board_skin_path?>/mw.proc/mw.comment.good.php?bo_table=<?=$bo_table?>&parent_id=<?=$wr_id?>&wr_id="+wr_id+"&good="+good, function (data) {
        data = data.split('|');
        alert(data[0]);
        if (good == 'good') {
            $("#mw_comment_good_"+wr_id).html(data[1]);
        } else {
            $("#mw_comment_nogood_"+wr_id).html(data[1]);
        }
    });
}
</script>
<? } ?>

<? if ($is_comment_editor && $mw_basic[cf_editor] == "geditor") { ?>
<script type="text/javascript">
var g4_skin_path = "<?=$board_skin_path?>";
</script>
<script type="text/javascript" src="<?=$board_skin_path?>/mw.geditor/geditor.js"></script>
<? } ?>

<? if ($mw_basic[cf_icon_level]) { ?>
<style type="text/css">
<? for ($i=0; $i<=99; $i++) { ?>
#mw_basic .icon_level<?=$i?> { background:url(<?=$board_skin_path?>/img/icon_level.png) 0 -<?=($i*10)?>px no-repeat; width:50px; height:10px; font-size:10px; line-height:10px; }
<? } ?>
</style>
<? } ?>


<? if($cwin==1) { ?>
</td><tr></table><p align=center><a href="javascript:window.close();"><img src="<?=$board_skin_path?>/img/btn_close.gif" border="0"></a><br><br></div>
<?}?>

<? } // 실명인증 ?>

<? if ($cwin) { ?> <script type="text/javascript" src="<?=$board_skin_path?>/mw.js/ZeroClipboard.js"></script> <? } ?>
<script type="text/javascript">
$(document).ready(function () {
    $(".tooltip").removeClass("tooltip");
    $(".mw_basic_comment_url").click(function () {
        var comment_id = $(this).attr("value");
        var top = $(this).position().top + 15 ;
        var left = $(this).position().left;

        if ($("#comment_url_popup").css("display") != "block" || comment_id != old_comment_id) {
            $(this).append("<img src='<?=$board_skin_path?>/img/icon_loading.gif' style='position:absolute;' id='comment_url_loading'>");
            $.get("<?=$board_skin_path?>/mw.proc/mw.get.comment.url.php", {
                "bo_table" : "<?=$bo_table?>",
                "wr_id" : comment_id
            }, function (dat) {
                //$("#comment_url").html(dat);
                $("#comment_url").val(dat);
                $("#comment_url").attr('size', $("#comment_url").val().length+5);
                $("#comment_url").on('focus', function () { $(this).select(); } );
                $("#comment_url_popup").css("display", "block");
                $("#comment_url_popup").css("position", "absolute");
                $("#comment_url_popup").css("top", top);
                $("#comment_url_popup").css("left", left - $("#comment_url_popup").width()+50);
                $("#comment_url_popup").css("width", $("#comment_url").outerWidth());
                old_comment_id = comment_id;

                $("#comment_url_loading").remove();
                $("#comment_url_copy").css("cursor", "pointer");

            });
        }
        else {
            $("#comment_url").html("");
            $("#comment_url_popup").css("display", "none");
        }
    });
});
</script>
<div id="comment_url_popup" style="display:none;">
    <input type="text" id="comment_url" value="" readonly/>
</div>
</div>

<? if ($cwin) { ?>
<script type="text/javascript"> document.title = "<?=mw_reg_str(addslashes($write[wr_subject]))?>"; </script>
<script type="text/javascript">
function btn_ip_search(ip) {
    window.open("<?=$g4[admin_path]?>/member_list.php?sfl=mb_ip&stx=" + ip);
}
</script>
<? if ($mw_basic[cf_post_history]) { ?>
<script type="text/javascript">
function btn_history(wr_id) {
    window.open("<?=$board_skin_path?>/mw.proc/mw.history.list.php?bo_table=<?=$bo_table?>&wr_id=" + wr_id, "mw_history", "width=500, height=300, scrollbars=yes");
}
</script>
<? } ?>
<? if ($mw_basic[cf_singo]) { ?>
<script type="text/javascript">
function btn_singo(wr_id, parent_id) {
    //if (confirm("이 게시물을 정말 신고하시겠습니까?")) {
    //hiddenframe.location.href = "<?=$board_skin_path?>/mw.proc/mw.btn.singo.php?bo_table=<?=$bo_table?>&wr_id=" + wr_id + "&parent_id=" + parent_id;
    window.open("<?=$board_skin_path?>/mw.proc/mw.btn.singo.php?bo_table=<?=$bo_table?>&wr_id=" + wr_id + "&parent_id=" + parent_id, "win_singo", "width=500,height=300,scrollbars=yes");
    //}
}
function btn_singo_view(wr_id) {
    var id = "singo_block_" + wr_id;

    if (document.getElementById(id).style.display == 'block')
        document.getElementById(id).style.display = 'none';
    else
        document.getElementById(id).style.display = 'block';
}

function btn_singo_clear(wr_id) {
    if (confirm("정말 초기화 하시겠습니까?")) {
        $.get("<?=$board_skin_path?>/mw.proc/mw.btn.singo.clear.php?bo_table=<?=$bo_table?>&wr_id="+wr_id, function(msg) {
            alert(msg);
        });
    }
}

</script>
<? } ?>

<?php
if ($is_singo_admin) { ?>
<script>
function btn_intercept(mb_id, wr_ip) {
    if (mb_id == undefined || mb_id == '') {
        mb_id = wr_ip;
    }
    window.open("<?=$board_skin_path?>/mw.proc/mw.intercept.php?bo_table=<?=$bo_table?>&mb_id=" + mb_id, "intercept", "width=500,height=300,scrollbars=yes");
}
</script>
<? } ?>

<? } // if ($cwin) ?>

<style type="text/css">
/* 댓글 img */
#mw_basic .mw_basic_comment_content img {
    max-width:<?=$board[bo_image_width]?>px;
    height:auto; 
}
#loading { display:none; }
</style>

<?php if (!$is_comment_editor) { ?>
<script src="<?php echo $board_skin_path?>/mw.js/autogrow.js"></script>
<script>
$(document).ready(function () {
    $("#wr_content").autogrow();
});
</script>
<?php } ?>

<div id="loading"><img src="<?=$board_skin_path?>/img/icon_loading.gif"/></div>

