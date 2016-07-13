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
include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");

if ($is_admin != "super")
    alert_close("접근 권한이 없습니다.");

$admin_menu[config] = "select";

if (!$tn) $tn = 0;

if (!$mw_basic[cf_type]) $mw_basic[cf_type] = "list";

$g4[title] = "배추 BASIC SKIN 관리자";

$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

if (!$mw_basic[cf_thumb_width]) $mw_basic[cf_thumb_width] = 80;
if (!$mw_basic[cf_thumb_height]) $mw_basic[cf_thumb_height] = 50;
if (!$mw_basic[cf_write_width]) $mw_basic[cf_write_width] = "normal";
if (!$mw_basic[cf_write_height]) $mw_basic[cf_write_height] = 10;

$cash_admin_path = $g4['admin_path'] . '/mw.cash';
if (is_g5())
    $cash_admin_path .= '5';

set_session("ss_config_token", $token = uniqid(time()));
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="<?=$g4['charset']?>">
<title><?=$g4['title']?></title>
<script>
// 자바스크립트에서 사용하는 전역변수 선언
var g4_path      = "<?=$g4['path']?>";
var g4_bbs       = "<?=$g4['bbs']?>";
var g4_bbs_img   = "<?=$g4['bbs_img']?>";
var g4_url       = "<?=$g4['url']?>";
var g4_is_member = "<?=$is_member?>";
var g4_is_admin  = "<?=$is_admin?>";
var g4_bo_table  = "<?=isset($bo_table)?$bo_table:'';?>";
var g4_sca       = "<?=isset($sca)?$sca:'';?>";
var g4_charset   = "<?=$g4['charset']?>";
var g4_cookie_domain = "<?=$g4['cookie_domain']?>";
var g4_is_gecko  = navigator.userAgent.toLowerCase().indexOf("gecko") != -1;
var g4_is_ie     = navigator.userAgent.toLowerCase().indexOf("msie") != -1;
<? if ($is_admin) { echo "var g4_admin = '{$g4['admin']}';"; } ?>
</script>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<link href="//code.jquery.com/ui/1.11.2/themes/humanity/jquery-ui.css" rel="stylesheet" />
</head>
<body topmargin="0" leftmargin="0" <?=isset($g4['body_script']) ? $g4['body_script'] : "";?>>
<a name="g4_head"></a>

<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>

<!--
<link rel="stylesheet" href="<?=$board_skin_path?>/mw.js/ui-lightness/jquery-ui-1.7.2.custom.css" type="text/css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
-->
<script src="<?="$board_skin_path/mw.js/selectbox.js"?>"></script>

<?
//==============================================================================
// jquery date picker
//------------------------------------------------------------------------------
// 참고) ie 에서는 년, 월 select box 를 두번씩 클릭해야 하는 오류가 있습니다.
//------------------------------------------------------------------------------
// jquery-ui.css 의 테마를 변경해서 사용할 수 있습니다.
// base, black-tie, blitzer, cupertino, dark-hive, dot-luv, eggplant, excite-bike, flick, hot-sneaks, humanity, le-frog, mint-choc, overcast, pepper-grinder, redmond, smoothness, south-street, start, sunny, swanky-purse, trontastic, ui-darkness, ui-lightness, vader
// 아래 css 는 date picker 의 화면을 맞추는 코드입니다.
?>
<style>
<!--
.ui-datepicker { font:12px dotum; }
.ui-datepicker select.ui-datepicker-month, 
.ui-datepicker select.ui-datepicker-year { width: 70px;}
.ui-datepicker-trigger { margin:0 0 -5px 2px; }
-->
</style>
<script>
/* Korean initialisation for the jQuery calendar extension. */
/* Written by DaeKwon Kang (ncrash.dk@gmail.com). */
jQuery(function($){
        $.datepicker.regional['ko'] = {
                closeText: '닫기',
                prevText: '이전달',
                nextText: '다음달',
                currentText: '오늘',
                monthNames: ['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUN)',
                '7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
                monthNamesShort: ['1월','2월','3월','4월','5월','6월',
                '7월','8월','9월','10월','11월','12월'],
                dayNames: ['일','월','화','수','목','금','토'],
                dayNamesShort: ['일','월','화','수','목','금','토'],
                dayNamesMin: ['일','월','화','수','목','금','토'],
                weekHeader: 'Wk',
                dateFormat: 'yy-mm-dd',
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: true,
                yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['ko']);

    $('#cf_board_sdate').datepicker({
        showOn: 'button',
        buttonImage: '<?=$board_skin_path?>/img/calendar.gif',
        buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99'
    }); 


    $('#cf_board_edate').datepicker({
        showOn: 'button',
        buttonImage: '<?=$board_skin_path?>/img/calendar.gif',
        buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99'
    }); 
});
</script>
<?
//==============================================================================
?>

<script>
$(document).ready(function () {
    $("#tabs").tabs();
    $("#tabs").tabs('option', 'active', <?=$tn?>);

    $("#preloader").css('display', 'none');
    $("#load").css('display', 'block');
});
function fsend() {
    document.cf_form.tn.value = $('#tabs').tabs('option', 'active')
    document.cf_form.submit();
}
function reload_config() {
    $.get("mw.reload.config.php?bo_table=<?=$bo_table?>", function (ret) {
        alert(ret);
        location.reload();
    });
}
function copy_config() {
    window.open("mw.copy.config.php?bo_table=<?=$bo_table?>", "copy_config", "left=50, top=50, width=500, height=550, scrollbars=1");
}
function run_order(item, order)
{
    if (!order) order = 'asc';

    if (!confirm("정렬을 시작하시겠습니까?")) return false;

    var btn = $("#btn_order_"+item).html();

    $("#btn_order_"+item).html("<img src=\"../img/icon_loading.gif\">");

    var url = "../mw.proc/mw.adm.order.php";
    $.post (url, { 'bo_table':g4_bo_table, 'item':item, 'token':'<?=$token?>', 'order':order }, function (req) {
            alert(req);
            $("#btn_order_"+item).html(btn);
    });
}

function run_watermark_remake()
{
    if (!confirm("데이터 양에 따라 오래걸릴수도 있습니다.\n\n워터마크를 재생성을 시작하시겠습니까?")) {
        return false;
    }

    var btn = $("#btn_watermark_remake").html();

    $("#btn_watermark_remake").html("<img src=\"../img/icon_loading.gif\">");

    var url = "../mw.proc/mw.adm.watermark.remake.php";
    $.post(url, { 'bo_table':g4_bo_table, 'token':'<?=$token?>' }, function (req) {
            alert(req);
            $("#btn_watermark_remake").html(btn);
    });
}

function run_thumb_remake()
{
    if (!confirm("데이터 양에 따라 오래걸릴수도 있습니다.\n\n썸네일 재생성을 시작하시겠습니까?")) {
        return false;
    }

    var btn = $("#btn_thumb_remake").html();

    $("#btn_thumb_remake").html("<img src=\"../img/icon_loading.gif\">");

    var url = "../mw.proc/mw.adm.thumb.remake.php";
    $.post(url, { 'bo_table':g4_bo_table, 'token':'<?=$token?>' }, function (req) {
            alert(req);
            $("#btn_thumb_remake").html(btn);
    });
}

function run_emoticon_sync()
{
    if (!confirm("DB손실의 위험이 있으니 반드시 백업 후 진행하세요!\n\n이모티콘 싱크를 시작하시겠습니까?")) {
        return false;
    }

    var btn = $("#btn_emoticon_sync").html();

    $("#btn_emoticon_sync").html("<img src=\"../img/icon_loading.gif\">");

    var url = "../mw.proc/mw.adm.emoticon.sync.php";
    var param = "bo_table=" + g4_bo_table + "&token=<?=$token?>&em_old=" + encodeURIComponent($("#em_old").val()) + "&em_new=" + encodeURIComponent($("#em_new").val());
    $.ajax ({
	url:url,
        type: 'post',
        data: param,
        success: function (req)
        {
            alert(req);
            $("#btn_emoticon_sync").html(btn);
        }
    });
}

function run_subject_change()
{
    if (!confirm("DB손실의 위험이 있으니 반드시 백업 후 진행하세요!\n\n제목변경을 시작하시겠습니까?")) {
        return false;
    }

    var btn = $("#btn_subject_change").html();

    $("#btn_subject_change").html("<img src=\"../img/icon_loading.gif\">");

    var url = "../mw.proc/mw.adm.subject.sync.php";
    var param = "bo_table=" + g4_bo_table;
    param += "&token=<?=$token?>&su_old=" + encodeURIComponent($("#su_old").val());
    param += "&su_new=" + encodeURIComponent($("#su_new").val());
    param += "&su_regex=" + $("#su_regex").prop("checked");
    $.ajax ({
	url:url,
        type: 'post',
        data: param,
        success: function (req)
        {
            alert(req);
            $("#btn_subject_change").html(btn);
        }
    });
}

function run_category_change()
{
    if (!confirm("정말 변경하시겠습니까?")) {
        return false;
    }

    var btn = $("#btn_category_change").html();

    $("#btn_category_change").html("<img src=\"../img/icon_loading.gif\">");

    var url = "../mw.proc/mw.adm.category.change.php";
    var param = "bo_table=" + g4_bo_table + "&token=<?=$token?>&ca_old=" + encodeURIComponent($("#ca_old").val()) + "&ca_new=" + encodeURIComponent($("#ca_new").val());
    $.ajax ({
	url:url,
        type: 'post',
        data: param,
        success: function (req)
        {
            alert(req);
            $("#btn_category_change").html(btn);
        }
    });
}

function all_checked(sw) {
    var f = document.cf_form;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name.substr(0,4) == "chk[")
            f.elements[i].checked = sw;
    }
}

function log_del(obj, what)
{
    if (!confirm('정말 로그를 비우시겠습니까')) return;

    tmp = obj.value;

    obj.value = '로그를 비우는 중입니다. 잠시만 기다려주세요...';
    obj.disabled = true;

    $.post("<?php echo $board_skin_path?>/mw.proc/mw.log.del.php", {
        'what': what,
        'bo_table' : '<?php echo $bo_table?>',
        'token':'<?php echo $token?>' 
    }, function (str) {
        if (str == 'ok') {
            alert("로그를 비웠습니다.");
            obj.value = '(0건) 로그비움';
        }
        else {
            alert(str);
            obj.value = tmp;
        }
        
        obj.disabled = false;
    });
}
</script>

<style type="text/css">
img { border:0; }
body { text-align:center; }
#preloader { margin:300px auto 0 auto; text-align:center; }
#load { display:none; text-align:left; }
.notice { margin:10px; padding:5px 0 5px 10px; color:#999; border:1px solid #ddd; display:none; }
#board { margin:0 10px 5px 10px; font-weight:bold; text-align:right; }
#tabs { margin:10px; font-family:dotum; font-size:12px; }
#tabs .tabs { margin:0 0 20px 0; }
#tabs .tabs .cf_item { padding:5px 0 5px 0; clear:both; }
#tabs .tabs .cf_item .cf_title { float:left; width:180px; font-weight:bold; }
#tabs .tabs .cf_item .cf_content { float:left; display:block; }
#tabs .tabs .cf_item span.cf_info { font-size:11px; color:#999; margin:0 0 0 10px; }
#tabs .tabs .cf_item span.cf_info a { font-size:11px; color:#999; }
#tabs .tabs .cf_item div.cf_info { font-size:11px; color:#999; margin:5px 0 0 0; }
#tabs .tabs .block { clear:both; }

input.ed { height:20px; border:1px solid #9A9A9A; border-right:1px solid #D8D8D8; border-bottom:1px solid #D8D8D8; padding:0 0 0 3px; }
textarea { border:1px solid #9A9A9A; border-right:1px solid #D8D8D8; border-bottom:1px solid #D8D8D8; padding:0 0 0 3px; }
input.bt { background-color:#efefef; height:20px; cursor:pointer; font-size:11px; font-family:dotum; }

label {
    outline:none;
    -webkit-user-select:none;
    -moz-user-select:none;
    -ms-user-select:none;
    user-select:none;
    cursor:pointer;
}
</style>

<div id="preloader"><img src="<?=$board_skin_path?>/img/preloader.gif"></div>

<div id="load">

<div><a href="http://miwit.kr" target="_blank"><img src="<?=$board_skin_path?>/img/logo_miwit.gif"></a></div>

<div id="board">
<input type="button" class="bt" value="설정다시읽기" onclick="reload_config()">
<input type="button" class="bt" value="설정복사" onclick="copy_config()">
<span style="color:#1C94C4"><?=$board[bo_subject]?></span> 게시판 배추스킨 설정
</div>

<form name="cf_form" method="post" action="mw.config.update.php">
<input type="hidden" name="bo_table" value="<?=$bo_table?>">
<input type="hidden" name="tn" value="0">

<div id="tabs">
<ul>
    <li> <a href="#tabs-1">기본설정</a> </li>
    <li> <a href="#tabs-2">모양</a> </li>
    <li> <a href="#tabs-3">기능</a> </li>
    <li> <a href="#tabs-4">알림</a> </li>
    <li> <a href="#tabs-5">데이터</a> </li>
    <li> <a href="#tabs-5-2">이미지</a> </li>
    <li> <a href="#tabs-5-5">에디터</a> </li>
    <li> <a href="#tabs-6">접근권한</a> </li>
    <li> <a href="#tabs-7">플러그인</a> </li>
    <li> <a href="#tabs-8">컨텐츠샵</a> </li>
    <li> <a href="#tabs-8-5">i-PIN 인증</a> </li>
    <li> <a href="#tabs-9">통계</a> </li>
    <li> <a href="#tabs-10">버전확인</a> </li>
</ul>

<div class="notice">
    <input type=checkbox onclick="if (this.checked) all_checked(true); else all_checked(false);">
    체크시 이 그룹에 속한, 이 스킨을 사용하는 모든 게시판에 동일하게 적용합니다.
</div>

<div id="tabs-1" class="tabs"> <!-- 기본설정 -->
    <div class="cf_item">
	<div class="cf_title"><input type=checkbox name=chk[cf_type] value=1>&nbsp;  목록형태 </div>
	<div class="cf_content">
	    <table>
	    <colgroup width="80"/>
	    <colgroup width="80"/>
	    <colgroup width="80"/>
	    <colgroup width="80"/>
	    <tbody valign=top align=center>
	    <tr>
		<td>
		    <img src="<?=$board_skin_path?>/mw.adm/img/cf_type_list.gif" width=40 height=46 required itemname="목록 형태"> <br/>
		    <input type=radio name=cf_type value="list"> 목록형
		</td>
		<td>
		    <img src="<?=$board_skin_path?>/mw.adm/img/cf_type_thumb.gif" width=40 height=46 required itemname="목록 형태"> <br/>
		    <input type=radio name=cf_type value="thumb"> 썸네일형
		</td>
		<td>
		    <img src="<?=$board_skin_path?>/mw.adm/img/cf_type_gall.gif" width=40 height=46 required itemname="목록 형태"> <br/>
		    <input type=radio name=cf_type value="gall"> 갤러리형
		</td>
		<td>
		    <img src="<?=$board_skin_path?>/mw.adm/img/cf_type_desc.gif" width=40 height=46 required itemname="목록 형태"> <br/>
		    <input type=radio name=cf_type value="desc"> 요약형
		</td>
	    </tr>
	    </tbody>
	    </table>
	</div>
	<script>
	var ct = document.cf_form.cf_type;
	for (i=0; i<ct.length; i++) {
	    if (ct[i].value == "<?=$mw_basic[cf_type]?>") {
		break;
	    }
	}
	document.cf_form.cf_type[i].checked = true;
	</script>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_desc_len] value=1>&nbsp; 요약형 길이 </div>
	<div class="cf_content">
	    <input type=text size=5 name=cf_desc_len class=ed required itemname="요약형 길이" numeric value="<?=$mw_basic[cf_desc_len]?>">
	    <span class="cf_info">(목록에서의 요약내용 글자수. 잘리는 글은 … 로 표시)</span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_desc_use] value=1>&nbsp; 요약형 별도입력 </div>
	<div class="cf_content">
            <select name="cf_desc_use" id="cf_desc_use">
            <option value="0">사용안함</option>
            <?php for ($i=1; $i<=10; $i++) { ?>
            <option value="<?php echo $i?>"><?php echo $i?>레벨 이상</option>
            <?php } ?>
            </select>
	    <span class="cf_info">(글작성시 컨텐츠 요약 항목을 별도로 입력합니다.)</span>
            <script> $("#cf_desc_use").val("<?php echo $mw_basic['cf_desc_use']?>"); </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_attribute] value=1>&nbsp; 속성 </div>
	<div class="cf_content">
	    <select name=cf_attribute onchange="attchg(this.value)">
	    <option value="basic"> 일반 게시판</option>
	    <option value="1:1"> 1:1 게시판 </option>
	    <option value="anonymous"> 익명 게시판 </option>
	    <option value="qna"> 질문 게시판 </option>
	    </select>
	</div>
    </div>

    <div class="cf_item" id="cf_attqna" style="display:none">
        <div class="cf_title">  <input type=checkbox name=chk[cf_qna_point_use] value=1>&nbsp;  질문 게시판 설정 </div>
	<div class="cf_content">
            <input type="checkbox" name="cf_qna_point_use" value="1"> 포인트 사용
            <input type="text" class="ed" name="cf_qna_point_min" size="4" numeric value="<?=$mw_basic[cf_qna_point_min]?>"> 이상 ~
            <input type="text" class="ed" name="cf_qna_point_max" size="4" numeric value="<?=$mw_basic[cf_qna_point_max]?>"> 이하.
            채택자 <input type="text" class="ed" name="cf_qna_save" size="3" numeric value="<?=$mw_basic[cf_qna_save]?>"> % 적립
            + 추가 <input type="text" class="ed" name="cf_qna_point_add" size="4" numeric value="<?=$mw_basic[cf_qna_point_add]?>"> 포인트,
            보류시  <input type="text" class="ed" name="cf_qna_hold" size="4" numeric value="<?=$mw_basic[cf_qna_hold]?>">% 복원
            <div style="padding:10px 0 10px 0;">
            미해결 질문이 <input type="text" class="ed" name="cf_qna_count" size="4" numeric value="<?=$mw_basic[cf_qna_count]?>"> 개 
            이상이면 더이상 질문을 할 수 없음.
            <input type="button" value="권한별설정"
                onclick="window.open('mw.level.php?bo_table=<?=$bo_table?>', 'level', 'width=600,height=600')">
            <span class="cf_info">(0으로 하면 제한 없음)</span>
            </div>
            <div>
                <input type="checkbox" name="cf_qna_enough" value="1">
                답변이 채택되면 더이상 댓글를 작성할 수 없음
            </div>
        </div>
    </div>

    <script>
    document.cf_form.cf_attribute.value = "<?=$mw_basic[cf_attribute]?>";
    document.cf_form.cf_qna_point_use.checked = "<?=$mw_basic[cf_qna_point_use]?>";
    document.cf_form.cf_qna_enough.checked = "<?=$mw_basic[cf_qna_enough]?>";

    function attchg(v) {
        if (v == 'qna') {
            $("#cf_attqna").css("display", "block");
        } else {
            $("#cf_attqna").css("display", "none");
        }
    }
    attchg(document.cf_form.cf_attribute.value);
    </script>

    <?php
    if (!trim($mw_basic['cf_gender_m']) and !trim($mw_basic['cf_gender_w'])) {
        $mw_basic['cf_gender_m'] = 'lvwc';
        $mw_basic['cf_gender_w'] = 'lvwc';
    }
    ?>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_gender] value=1>&nbsp; 접근가능 성별 </div>
	<div class="cf_content">
            남자 :
            <input type="checkbox" name="cf_gender_m_list" value="1"> 목록
            <input type="checkbox" name="cf_gender_m_view" value="1"> 읽기
            <input type="checkbox" name="cf_gender_m_write" value="1"> 쓰기
            <input type="checkbox" name="cf_gender_m_comment" value="1"> 댓글
            <br/>
            여자 :
            <input type="checkbox" name="cf_gender_w_list" value="1"> 목록
            <input type="checkbox" name="cf_gender_w_view" value="1"> 읽기
            <input type="checkbox" name="cf_gender_w_write" value="1"> 쓰기
            <input type="checkbox" name="cf_gender_w_comment" value="1"> 댓글
            <script>
            $("input[name=cf_gender_m_list]").attr("checked", <?php
                if (strstr($mw_basic['cf_gender_m'], 'l')) echo "true"; else echo "false"; ?>);
            $("input[name=cf_gender_m_view]").attr("checked", <?php
                if (strstr($mw_basic['cf_gender_m'], 'v')) echo "true"; else echo "false"; ?>);
            $("input[name=cf_gender_m_write]").attr("checked", <?php
                if (strstr($mw_basic['cf_gender_m'], 'w')) echo "true"; else echo "false"; ?>);
            $("input[name=cf_gender_m_comment]").attr("checked", <?php
                if (strstr($mw_basic['cf_gender_m'], 'c')) echo "true"; else echo "false"; ?>);

            $("input[name=cf_gender_w_list]").attr("checked", <?php
                if (strstr($mw_basic['cf_gender_w'], 'l')) echo "true"; else echo "false"; ?>);
            $("input[name=cf_gender_w_view]").attr("checked", <?php
                if (strstr($mw_basic['cf_gender_w'], 'v')) echo "true"; else echo "false"; ?>);
            $("input[name=cf_gender_w_write]").attr("checked", <?php
                if (strstr($mw_basic['cf_gender_w'], 'w')) echo "true"; else echo "false"; ?>);
            $("input[name=cf_gender_w_comment]").attr("checked", <?php
                if (strstr($mw_basic['cf_gender_w'], 'c')) echo "true"; else echo "false"; ?>);
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_age] value=1>&nbsp; 접근가능 나이 </div>
	<div class="cf_content">
            <?
            preg_match("/^([0-9]+)([\+\-\=])$/", $mw_basic[cf_age], $match);
            $age = $match[1];
            $age_type = $match[2];
            ?>
            <input type="text" class="ed" size="3" name="cf_age" value="<?=$age?>">세
            <select name="cf_age_type" id="cf_age_type">
                <option value=""></option>
                <option value="+">이상 접근가능</option>
                <option value="-">미만 접근가능</option>
                <option value="=">만 접근가능</option>
            </select>

            <input type="checkbox" name="cf_age_list" value="1"> 목록
            <input type="checkbox" name="cf_age_view" value="1"> 읽기
            <input type="checkbox" name="cf_age_write" value="1"> 쓰기
            <input type="checkbox" name="cf_age_comment" value="1"> 댓글

            <script>
            $("#cf_age_type").val("<?=$age_type?>");

            $("input[name=cf_age_list]").attr("checked", <?php
                if (strstr($mw_basic['cf_age_opt'], 'l')) echo "true"; else echo "false"; ?>);
            $("input[name=cf_age_view]").attr("checked", <?php
                if (strstr($mw_basic['cf_age_opt'], 'v')) echo "true"; else echo "false"; ?>);
            $("input[name=cf_age_write]").attr("checked", <?php
                if (strstr($mw_basic['cf_age_opt'], 'w')) echo "true"; else echo "false"; ?>);
            $("input[name=cf_age_comment]").attr("checked", <?php
                if (strstr($mw_basic['cf_age_opt'], 'c')) echo "true"; else echo "false"; ?>);
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_board_date] value=1>&nbsp; 접근가능 날짜 </div>
	<div class="cf_content">
            <input type="text" size="10" name="cf_board_sdate" id="cf_board_sdate" readonly value="<?=$mw_basic[cf_board_sdate]?>"/> ~
            <input type="text" size="10" name="cf_board_edate" id="cf_board_edate" readonly value="<?=$mw_basic[cf_board_edate]?>"/>
            <input type="button" class="btn1" value="초기화" onclick="$('#cf_board_sdate').val('');$('#cf_board_edate').val('');"/>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_board_time] value=1>&nbsp; 접근가능 시간 </div>
	<div class="cf_content">
            <select name="cf_board_stime_hour" id="cf_board_stime_hour">
                <option value=""></option>
                <? for ($i=0; $i<=24; $i++) {?>
                <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
                <? } ?>
            </select> 시
            <select name="cf_board_stime_minute" id="cf_board_stime_minute">
                <option value=""></option>
                <? for ($i=0; $i<=59; $i++) {?>
                <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
                <? } ?>
            </select> 분
            ~
            <select name="cf_board_etime_hour" id="cf_board_etime_hour">
                <option value=""></option>
                <? for ($i=0; $i<=24; $i++) {?>
                <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
                <? } ?>
            </select> 시
            <select name="cf_board_etime_minute" id="cf_board_etime_minute">
                <option value=""></option>
                <? for ($i=0; $i<=59; $i++) {?>
                <option value="<?=sprintf("%02d", $i)?>"><?=sprintf("%02d", $i)?></option>
                <? } ?>
            </select> 분
            <input type="button" class="btn1" value="초기화" onclick="cf_board_time_ini()"/>
            <script>
            function cf_board_time_ini() {
                $('#cf_board_stime_hour').val('');
                $('#cf_board_stime_minute').val('');
                $('#cf_board_etime_hour').val('');
                $('#cf_board_etime_minute').val('');
            }
            cf_form.cf_board_stime_hour.value = "<?=substr($mw_basic[cf_board_stime], 0, 2)?>";
            cf_form.cf_board_stime_minute.value = "<?=substr($mw_basic[cf_board_stime], 3, 2)?>";
            cf_form.cf_board_etime_hour.value = "<?=substr($mw_basic[cf_board_etime], 0, 2)?>";
            cf_form.cf_board_etime_minute.value = "<?=substr($mw_basic[cf_board_etime], 3, 2)?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_board_week] value=1>&nbsp; 접근가능 요일 </div>
	<div class="cf_content">
            <? $week = explode(",", $mw_basic[cf_board_week]); ?>
            <? for ($i=0; $i<7; $i++) { ?>
            <input type="checkbox" name="cf_board_week[<?=$i?>]" value="1" <? if ($week[$i]) echo 'checked';?>> <?=$arr_yoil[$i]?> 
            <? } ?>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_hot] value=1>&nbsp; 인기 게시물 </div>
	<div class="cf_content">
	    <select name=cf_hot>
	    <option value="0"> 사용안함 </option>
	    <option value="1"> 실시간 </option>
	    <option value="2"> 주간 </option>
	    <option value="3"> 월간 </option>
	    <option value="4"> 일간 </option>
	    <option value="5"> 연간 </option>
	    <option value="6"> 최근3개월 </option>
	    <option value="7"> 최근6개월 </option>
	    </select>
	    <select name=cf_hot_basis>
	    <option value="hit"> 조회수 </option>
	    <option value="good"> 추천수 </option>
	    <option value="nogood"> 비추천수 </option>
	    <option value="comment"> 댓글수 </option>
	    <option value="link1_hit"> 링크1 클릭수 </option>
	    <option value="file"> 다운로드수 </option>
	    <option value="rate"> 별점평가 </option>
	    </select>
            <select name="cf_hot_limit">
            <? for ($i=2; $i<=10; $i=$i+2) { ?>
            <option value="<?=$i?>"><?=$i?></option>
            <? } ?>
            </select> 개<br>
            <div>
                길이 :
                <input type="text" name="cf_hot_len" id="cf_hot_len" size="3" value="<?=$mw_basic[cf_hot_len]?>"> 글자
                <span class="cf_info">(목록상단에 인기 게시물을 출력합니다.)</span>
            </div>
            <div>
                캐시 :
                <input type="text" name="cf_hot_cache" id="cf_hot_cache" size="3" value="<?=$mw_basic[cf_hot_cache]?>"> 분
                <span class="cf_info">(인기게시물을 파일로 저장해 DB 부담을 줄여줍니다.)</span>
            </div>
            <div>
                출력 :
                <input type="checkbox" name="cf_hot_list" value="1">목록 
                <input type="checkbox" name="cf_hot_view" value="1">읽기
                <input type="checkbox" name="cf_hot_write" value="1">쓰기
            </div>
	    <script>
	    document.cf_form.cf_hot.value = "<?=$mw_basic[cf_hot]?>";
	    document.cf_form.cf_hot_basis.value = "<?=$mw_basic[cf_hot_basis]?>";
	    document.cf_form.cf_hot_limit.value = "<?=$mw_basic[cf_hot_limit]?>";
	    document.cf_form.cf_hot_list.checked = "<?php echo strstr($mw_basic[cf_hot_print], "l") ? '1' : '' ?>";
	    document.cf_form.cf_hot_view.checked = "<?php echo strstr($mw_basic[cf_hot_print], "v") ? '1' : '' ?>";
	    document.cf_form.cf_hot_write.checked = "<?php echo strstr($mw_basic[cf_hot_print], "w") ? '1' : '' ?>";
	    </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_related] value=1>&nbsp; 관련글 </div>
	<div class="cf_content">
	    <select name=cf_related>
	    <option value="0"> 사용안함 </option>
	    <option value="3"> 3건 출력 </option>
	    <option value="5"> 5건 출력 </option>
	    <option value="7"> 7건 출력 </option>
	    <option value="10"> 10건 출력 </option>
	    </select>
	    <span class="cf_info">(본문하단에 관련 게시물을 출력합니다.)</span>
	    <script> document.cf_form.cf_related.value = "<?=$mw_basic[cf_related]?>"; </script>
            <div>
                <input type="checkbox" name="cf_related_subject" value="1"> 제목
                <input type="checkbox" name="cf_related_content" value="1"> 내용 
            </div>
	    <script>
            document.cf_form.cf_related_subject.checked = '<?php echo $mw_basic['cf_related_subject']?>';
            document.cf_form.cf_related_content.checked = '<?php echo $mw_basic['cf_related_content']?>';
            </script>

	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_related_table] value=1>&nbsp; 관련글 타게시판 </div>
	<div class="cf_content">
            <input type="text" size="50" class="ed" name="cf_related_table" value="<?=$mw_basic[cf_related_table]?>">
	    <div class="cf_info">(관련 게시물을 다른게시판에서 검색합니다. 게시판 TABLE 명을 넣어주세요. 복수의 게시판 , 컴마로 구분)</div>
            <div>
                <input type="checkbox" name="cf_related_table_div" value="1">
                현재 게시판, 타게시판 모두 출력
            </div>
	    <script> document.cf_form.cf_related_table_div.checked = '<?php echo $mw_basic['cf_related_table_div']?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_latest] value=1>&nbsp; 최신글 </div>
	<div class="cf_content">
	    <select name=cf_latest>
	    <option value="0"> 사용안함 </option>
	    <option value="3"> 3건 출력 </option>
	    <option value="5"> 5건 출력 </option>
	    <option value="7"> 7건 출력 </option>
	    <option value="10"> 10건 출력 </option>
	    </select>
	    <span class="cf_info">(본문하단에 글작성자의 최근게시물을 출력합니다.)</span>
	    <script> document.cf_form.cf_latest.value = "<?=$mw_basic[cf_latest]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_latest_table] value=1>&nbsp; 최신글 타게시판 </div>
	<div class="cf_content">
            <input type="text" size="10" class="ed" name="cf_latest_table" value="<?=$mw_basic[cf_latest_table]?>">
	    <span class="cf_info">(관련 게시물을 다른게시판에서 검색합니다. 게시판 TABLE 명을 넣어주세요.)</span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_print] value=1>&nbsp; 인쇄 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_print value=1> 사용 <span class="cf_info">(본문 인쇄 기능)</span>  
	    <script> document.cf_form.cf_print.checked = <?=$mw_basic[cf_print]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_link_level] value=1>&nbsp; 링크 권한  </div>
	<div class="cf_content">
            <select name="cf_link_level">
            <? for ($i=0; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
            <!--<input type="checkbox" name="cf_link_level_view" value="1"> 항상출력-->
	    <span class="cf_info">(링크를 보고 클릭할 수 있는 권한)</span> 
	    <script>
            document.cf_form.cf_link_level.value = <?=$mw_basic[cf_link_level]?>;
            //document.cf_form.cf_link_level_view.value = <?=$mw_basic[cf_link_level_view]?>;
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_link_board] value=1>&nbsp; 링크 게시판  </div>
	<div class="cf_content">
            <select name="cf_link_board">
            <? for ($i=0; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
	    <span class="cf_info">(제목을 클릭하면 link1 의 주소로 이동, 0이면 사용안함, 글쓴이와 관리자제외)</span> 
	    <script> document.cf_form.cf_link_board.value = <?=$mw_basic[cf_link_board]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_link_write] value=1>&nbsp; 링크 바로이동 사용권한  </div>
	<div class="cf_content">
            <select name="cf_link_write">
            <? for ($i=0; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
	    <span class="cf_info">(제목을 클릭하면 link1 의 주소로 이동, 게시물별 사용권한, 0이면 사용안함)</span> 
	    <script> document.cf_form.cf_link_write.value = <?=$mw_basic[cf_link_write]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_link_target_level] value=1>&nbsp; 링크 타겟 사용권한  </div>
	<div class="cf_content">
            <select name="cf_link_target_level">
            <? for ($i=0; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
	    <span class="cf_info">(새창, 현재창 등))</span> 
	    <script> document.cf_form.cf_link_target_level.value = <?=$mw_basic[cf_link_target_level]?>; </script>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_link_point] value=1>&nbsp; 링크 포인트  </div>
	<div class="cf_content">
            <input type="text" size="5" class="ed" name="cf_link_point" numeric value="<?=$mw_basic[cf_link_point]?>">
            포인트
	    <span class="cf_info">(게시물에 별도로 첨부된 링크 클릭시 포인트를 적용합니다. 플러스 마이너스 가능.)</span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_hidden_link] value=1>&nbsp; 숨김링크 </div>
	<div class="cf_content">
            <select name="cf_hidden_link">
            <? for ($i=0; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
	    <span class="cf_info">(보이는 링크와 실제 이동되는 링크를 다르게합니다. 0이면 사용안함)</span> 
	    <script> document.cf_form.cf_hidden_link.value = '<?=$mw_basic[cf_hidden_link]?>'; </script>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_list_shuffle] value=1>&nbsp; 게시물 목록 셔플 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_list_shuffle value=1> 사용 <span class="cf_info">(페이지 내의 게시물 목록을 섞습니다.)</span>  
	    <script> document.cf_form.cf_list_shuffle.checked = '<?=$mw_basic[cf_list_shuffle]?>'; </script>
	</div>
    </div>

    <!--
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_norobot_image] value=1>&nbsp; 이미지 방지코드 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_norobot_image value=1> 사용 <span class="cf_info">(그누보드 4.22.00 이전 버전은 사용불가)</span>
	    <script> document.cf_form.cf_norobot_image.checked = <?=$mw_basic[cf_norobot_image]?>; </script>
	</div>
    </div>
    -->

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_subject_link] value=1>&nbsp; 권한별 제목링크  </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_subject_link value=1> 사용 <span class="cf_info">(권한이 없으면 제목링크를 삭제)</span>
	    <script> document.cf_form.cf_subject_link.checked = <?=$mw_basic[cf_subject_link]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_write_button] value=1>&nbsp; 쓰기버튼 항상 출력 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_write_button value=1> 사용 <span class="cf_info">(권한이 없어도 쓰기버튼 출력)</span>
	    <script> document.cf_form.cf_write_button.checked = <?=$mw_basic[cf_write_button]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_write] value=1>&nbsp; 댓글 입력창 출력 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_write value=1> 항상
	    <span class="cf_info">(댓글 입력창을 항상 출력, 로그인 메시지 출력)</span>
	    <script> document.cf_form.cf_comment_write.checked = <?=$mw_basic[cf_comment_write]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_level] value=1>&nbsp; 댓글 조회권한 </div>
	<div class="cf_content">
            <select name="cf_comment_level">
            <? for ($i=1; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
	    <script>
            document.cf_form.cf_comment_level.value = "<?=$mw_basic[cf_comment_level]?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_default] value=1>&nbsp; 댓글 기본내용 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_comment_default cols=60 rows=5 class=edarea><?=$mw_basic[cf_comment_default]?></textarea>
	    <div class="cf_info">댓글 기본 입력 내용</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_write_notice] value=1>&nbsp; 댓글 작성전 공지 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_comment_write_notice cols=60 rows=5 class=edarea><?=$mw_basic[cf_comment_write_notice]?></textarea>
	    <div class="cf_info">댓글 작성창에 표시되는 안내 메시지 (댓글 기본입력내용이 있을시 표시되지 않습니다.)</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_default_category] value=1>&nbsp; 기본분류설정 </div>
	<div class="cf_content" height=110>
            <select name="cf_default_category">
                <option value="">전체</option>
                <?
                $ca_list = explode("|", $board[bo_category_list]);
                foreach ((array)$ca_list as $ca_name) {
                    if (!trim($ca_name)) continue;
                    echo "<option value='$ca_name'>$ca_name</option>\n";
                }
                ?>
            </select>
	    <span class="cf_info">(기본분류사용시 '전체' 는 출력되지 않습니다.)</span>
            <script>
            document.cf_form.cf_default_category.value = "<?=$mw_basic[cf_default_category]?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_search_level] value=1>&nbsp; 검색 레벨 </div>
	<div class="cf_content" height=110>
            <select name="cf_search_level">
	    <? for ($i=0; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i==0?'사용안함':"{$i}레벨 이상"?></option>
	    <? } ?>
            </select>
            <input type="checkbox" name="cf_search_level_view" value="1"> 권한 없어도 항상 출력
	    <span class="cf_info">(사용안함 선택시 관리자를 제외한 누구도 게시판 내에서 검색할 수 없습니다. 1레벨은 비회원.)</span>
            <script>
            document.cf_form.cf_search_level.value = "<?=$mw_basic[cf_search_level]?>";
            document.cf_form.cf_search_level_view.checked = "<?=$mw_basic[cf_search_level_checked]?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_rss] value=1>&nbsp; 전용 RSS </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_rss value=1> 사용
	    <span class="cf_info">(배추스킨 전용 RSS. 게시판 설정에서 RSS 사용에 먼저 체크해주세요.)</span>
            <br>
            출력 갯수 :
            <input type="text" size="10" class="ed" name="cf_rss_limit" value="<?=$mw_basic[cf_rss_limit]?>"> 개

	    <script> document.cf_form.cf_rss.checked = <?=$mw_basic[cf_rss]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_emoticon] value=1>&nbsp; 기본 이모티콘 </div>
	<div class="cf_content">
            <?php
            $dirs = array();
            foreach (glob(dirname(__FILE__).'/../mw.emoticon/*') as $row) {
                if (is_dir($row)) {
                    $dirs[] = basename($row);
                }
            }
            ?>
            <select name="cf_emoticon">
            <option value="">기본</option>
            <?php
            foreach ((array)$dirs as $dir) : 
                printf('<option value="%s">%s</option>', $dir, $dir);
            endforeach;
            ?>
            </select>
	    <script> document.cf_form.cf_emoticon.value = "<?php echo $mw_basic[cf_emoticon]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_prev_next] value=1>&nbsp; 이전글,다음글 변경 </div>
	<div class="cf_content">
	    <label><input type="checkbox" name="cf_prev_next" value=1> 의미변경</label>
	    <span class="cf_info">(이전글, 다음글 의미를 서로 변경합니다.)</span>
	    <script> document.cf_form.cf_prev_next.checked = "<?php echo $mw_basic[cf_prev_next]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_image_no] value=1>&nbsp; 댓글 프로필 이미지 </div>
	<div class="cf_content">
	    <label><input type="checkbox" name="cf_comment_image_no" value=1> 사용안함</label>
	    <script> document.cf_form.cf_comment_image_no.checked = "<?php echo $mw_basic[cf_comment_image_no]?>"; </script>
	</div>
    </div>


    <div class="block"></div>

</div> <!-- tabs-1 -->

<div id="tabs-2" class="tabs"> <!-- 모양 -->

    <div class="cf_item">
	<div class="cf_title"><input type=checkbox name=chk[cf_notice_name] value=1>&nbsp; 공지사항 이름 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_notice_name value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_notice_name.checked = <?=$mw_basic[cf_notice_name]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"><input type=checkbox name=chk[cf_notice_date] value=1>&nbsp; 공지사항 날짜</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_notice_date value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_notice_date.checked = <?=$mw_basic[cf_notice_date]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_notice_hit] value=1>&nbsp; 공지사항 조회수 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_notice_hit value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_notice_hit.checked = <?=$mw_basic[cf_notice_hit]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_notice_good] value=1>&nbsp; 공지사항 추천수 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_notice_good value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_notice_good.checked = '<?=$mw_basic[cf_notice_good]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_post_num] value=1>&nbsp; 게시물 번호 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_post_num value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_post_num.checked = <?=$mw_basic[cf_post_num]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_list_cate] value=1>&nbsp; 분류 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_list_cate value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_list_cate.checked = '<?=$mw_basic[cf_list_cate]?>'; </script>
	</div>
    </div>
	
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_post_name] value=1>&nbsp; 작성자 이름 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_post_name value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_post_name.checked = <?=$mw_basic[cf_post_name]?>; </script>
	</div>
    </div>
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_name_location] value=1>&nbsp; 이름 위치 </div>
	<div class="cf_content">
            <select name="cf_name_location">
                <option value=''> 제목 뒤
                <option value='1'> 제목 앞
            </select>
	    <script> document.cf_form.cf_name_location.value = '<?=$mw_basic[cf_name_location]?>'; </script>
	</div>
    </div>
    
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_search_name] value=1>&nbsp; 작성자 검색 </div>
	<div class="cf_content">
            <select name="cf_search_name">
	    <? for ($i=1; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?="{$i}레벨 이상"?></option>
	    <? } ?>
            </select>
	    <span class="cf_info">(권한이 없으면 검색에서 이름, 회원아이디를 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_search_name.value = '<?=$mw_basic[cf_search_name]?>'; </script>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_post_date] value=1>&nbsp; 게시물 날짜 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_post_date value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_post_date.checked = <?=$mw_basic[cf_post_date]?>; </script>
	</div>
    </div>
    
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_post_hit] value=1>&nbsp; 게시물 조회수 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_post_hit value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_post_hit.checked = <?=$mw_basic[cf_post_hit]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_list_good] value=1>&nbsp; 추천 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_list_good value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_list_good.checked = <?=$mw_basic[cf_list_good]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_list_nogood] value=1>&nbsp; 비추천 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_list_nogood value=1> 출력안함 
	    <span class="cf_info">(체크하면 목록에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_list_nogood.checked = <?=$mw_basic[cf_list_nogood]?>; </script>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_good_graph] value=1>&nbsp; 추천,비추천 그래프 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_good_graph value=1> 사용 <span class="cf_info">(추천, 비추천 모두 사용함으로 되어있어야 정상작동)</span>
	    <script> document.cf_form.cf_good_graph.checked = '<?=$mw_basic[cf_good_graph]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comma] value=1>&nbsp; 콤마 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comma value=1> 사용 <span class="cf_info">(글번호,조회수 등에 3자리마다 콤마출력)</span>
	    <script> document.cf_form.cf_comma.checked = <?=$mw_basic[cf_comma]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_subject_style] value=1>&nbsp; 제목 스타일 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_subject_style value=1> 사용
            <span class="cf_info">(제목에 글꼴, 색상을 설정할 수 있습니다.)</span>
            <br>
            <select name="cf_subject_style_level">
            <? for ($i=1; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상,
            <input type="checkbox" name="cf_subject_style_color_picker" value="1">컬러피커 사용,
            기본색 <input type="text" class="ed" size="10" id="cf_subject_style_color_default" name="cf_subject_style_color_default" value="<?=$mw_basic[cf_subject_style_color_default]?>"/>
            <input type="button" class="btn1" value="기본색 지우기" onclick="delete_subject_color()">
	    <script>
            document.cf_form.cf_subject_style.checked = <?=$mw_basic[cf_subject_style]?>;
            document.cf_form.cf_subject_style_level.value = "<?=$mw_basic[cf_subject_style_level]?>";
            document.cf_form.cf_subject_style_color_picker.checked = "<?=$mw_basic[cf_subject_style_color_picker]?>";
            function delete_subject_color()
            {
                msg = "기존에 등록된 제목색상이 기본색일 경우 색상정보를 삭제하시겠습니까?";
                if (!confirm(msg))
                    return;

                if (!Date.now) {
                    Date.now = function() { return new Date().getTime(); };
                }
                var t = Date.now() ;

                $.get("<?php echo $board_skin_path?>/mw.proc/mw.delete.subject.color.php?t="+t, {
                        "color": $("#cf_subject_style_color_default").val(),
                        "bo_table": "<?php echo $bo_table?>"
                    },
                    function (str) {
                        alert(str);
                    }
                );
            }
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_search_top] value=1>&nbsp; 검색폼 상단표시</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_search_top value=1> 사용 <span class="cf_info">(검색폼을 상단에 표시하여 검색활성화)</span>
	    <script> document.cf_form.cf_search_top.checked = "<?=$mw_basic[cf_search_top]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_category_tab] value=1>&nbsp; 분류탭</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_category_tab value=1> 사용 <span class="cf_info">(목록에서 분류를 탭으로 표시합니다.)</span>
	    <script> document.cf_form.cf_category_tab.checked = "<?=$mw_basic[cf_category_tab]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_category_radio] value=1>&nbsp; 분류 라디오버튼</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_category_radio value=1> 사용 <span class="cf_info">(글작성시 분류를 라디오버튼으로 나타냅니다.)</span>
	    <script> document.cf_form.cf_category_radio.checked = "<?=$mw_basic[cf_category_radio]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_notice_top] value=1>&nbsp; 공지사항 별도</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_notice_top value=1> 사용 <span class="cf_info">(공지사항을 목록이 아닌 상단에 별도로 표기합니다.)</span>
	    <script> document.cf_form.cf_notice_top.checked = "<?=$mw_basic[cf_notice_top]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_notice_top] value=1>&nbsp; 공지사항 별도 제목길이</div>
	<div class="cf_content">
	    <input type=text class=ed name=cf_notice_top_length size=3 maxlength=3 value="<?=$mw_basic[cf_notice_top_length]?>"> <span class="cf_info"></span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_file_head] value=1>&nbsp; 첨부파일 상단 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_file_head cols=60 rows=5 class=edarea><?=$mw_basic[cf_file_head]?></textarea>
	    <div class="cf_info">첨부파일 링크 상단에 출력될 코드 </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_file_tail] value=1>&nbsp; 첨부파일 하단 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_file_tail cols=60 rows=5 class=edarea><?=$mw_basic[cf_file_tail]?></textarea>
	    <div class="cf_info">첨부파일 링크 하단에 출력될 코드 </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_content_head] value=1>&nbsp; 본문상단 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_content_head cols=60 rows=5 class=edarea><?=$mw_basic[cf_content_head]?></textarea>
	    <div class="cf_info">게시글 본문 상단에 출력될 코드 </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_content_add] value=1>&nbsp; 본문추가 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_content_add cols=60 rows=5 class=edarea><?=$mw_basic[cf_content_add]?></textarea>
	    <div class="cf_info">게시글 본문에 추가 출력 코드 </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_content_tail] value=1>&nbsp; 본문하단 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_content_tail cols=60 rows=5 class=edarea><?=$mw_basic[cf_content_tail]?></textarea>
	    <div class="cf_info">게시글 본문 하단에 출력될 코드 </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_head] value=1>&nbsp; 댓글상단 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_comment_head cols=60 rows=5 class=edarea><?=$mw_basic[cf_comment_head]?></textarea>
	    <div class="cf_info">댓글 상단에 출력될 코드 </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_tail] value=1>&nbsp; 댓글하단 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_comment_tail cols=60 rows=5 class=edarea><?=$mw_basic[cf_comment_tail]?></textarea>
	    <div class="cf_info">댓글 하단, 댓글 입력창 상단에 출력될 코드 </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_css] value=1>&nbsp; 사용자정의 CSS </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_css cols=60 rows=5 class=edarea><?=$mw_basic[cf_css]?></textarea>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_view_top] value=1>&nbsp; View 최상단</div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_view_top" class="ed" value="<?=$mw_basic[cf_include_view_top]?>"> 
	    <div class="cf_info">글본문 최상단에 파일을 삽입합니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_view_head] value=1>&nbsp; View 본문 상단 파일 </div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_view_head" class="ed" value="<?=$mw_basic[cf_include_view_head]?>"> 
	    <div class="cf_info">글본문 상단에 파일을 삽입합니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_view] value=1>&nbsp; View 본문 추가 파일 </div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_view" class="ed" value="<?=$mw_basic[cf_include_view]?>"> 
	    <div class="cf_info">글본문에 파일을 삽입합니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_view_tail] value=1>&nbsp; View 본문 하단 파일 </div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_view_tail" class="ed" value="<?=$mw_basic[cf_include_view_tail]?>"> 
	    <div class="cf_info">글본문 하단에 파일을 삽입합니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_file_head] value=1>&nbsp; 첨부파일 상단 파일 </div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_file_head" class="ed" value="<?=$mw_basic[cf_include_file_head]?>"> 
	    <div class="cf_info">글파일 상단에 파일을 삽입합니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_file_tail] value=1>&nbsp; 첨부파일 하단 파일 </div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_file_tail" class="ed" value="<?=$mw_basic[cf_include_file_tail]?>"> 
	    <div class="cf_info">글파일 하단에 파일을 삽입합니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type="checkbox" name="chk[cf_include_head]" value=1>&nbsp; 게시판 상단 파일 </div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_head" class="ed" value="<?=$mw_basic['cf_include_head']?>"> 
            <input type="checkbox" name="cf_include_head_list" value="1"> 목록
            <input type="checkbox" name="cf_include_head_view" value="1"> 읽기
            <input type="checkbox" name="cf_include_head_write" value="1"> 쓰기 
	    <div class="cf_info">게시판 상단에 파일을 삽입합니다.</div>
	    <script>
            document.cf_form.cf_include_head_list.checked = '<? echo strstr($mw_basic[cf_include_head_page], '/l/')?'1':''; ?>';
            document.cf_form.cf_include_head_view.checked = '<? echo strstr($mw_basic[cf_include_head_page], '/v/')?'1':''; ?>';
            document.cf_form.cf_include_head_write.checked = '<? echo strstr($mw_basic[cf_include_head_page], '/w/')?'1':''; ?>';
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type="checkbox" name="chk[cf_include_tail]" value=1>&nbsp; 게시판 하단 파일 </div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_tail" class="ed" value="<?=$mw_basic[cf_include_tail]?>"> 
            <input type="checkbox" name="cf_include_tail_list" value="1"> 목록
            <input type="checkbox" name="cf_include_tail_view" value="1"> 읽기
            <input type="checkbox" name="cf_include_tail_write" value="1"> 쓰기 
	    <div class="cf_info">게시판 하단에 파일을 삽입합니다.</div>
	    <script>
            document.cf_form.cf_include_tail_list.checked = '<? echo strstr($mw_basic[cf_include_tail_page], '/l/')?'1':''; ?>';
            document.cf_form.cf_include_tail_view.checked = '<? echo strstr($mw_basic[cf_include_tail_page], '/v/')?'1':''; ?>';
            document.cf_form.cf_include_tail_write.checked = '<? echo strstr($mw_basic[cf_include_tail_page], '/w/')?'1':''; ?>';
            </script>
	</div>
    </div>


    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_list_main] value=1>&nbsp; 목록 글 파일 </div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_list_main" class="ed" value="<?=$mw_basic[cf_include_list_main]?>"> 
	    <div class="cf_info">목록의 게시물마다 반복되는 파일을 삽입합니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_comment_main] value=1>&nbsp; 댓글 파일</div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_comment_main" class="ed" value="<?=$mw_basic[cf_include_comment_main]?>"> 
	    <div class="cf_info">댓글마다 반복되는 파일을 삽입합니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_write_head] value=1>&nbsp; 글쓰기 상단</div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_write_head" class="ed" value="<?=$mw_basic[cf_include_write_head]?>"> 
	    <div class="cf_info">글작성 페이지 제목 상단 부분에 포함될 파일입니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_write_main] value=1>&nbsp; 글쓰기 중간</div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_write_main" class="ed" value="<?=$mw_basic[cf_include_write_main]?>"> 
	    <div class="cf_info">글작성 페이지 제목 하단 부분에 포함될 파일입니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_write_tail] value=1>&nbsp; 글쓰기 하단</div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_write_tail" class="ed" value="<?=$mw_basic[cf_include_write_tail]?>"> 
	    <div class="cf_info">글작성 페이지 내용 아래 부분에 포함될 파일입니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_write_update_head] value=1>&nbsp; 글쓰기 업데이트 HEAD</div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_write_update_head" class="ed" value="<?=$mw_basic[cf_include_write_update_head]?>"> 
	    <div class="cf_info">글작성 DB 업데이트 부분에에 포함될 파일입니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_write_update] value=1>&nbsp; 글쓰기 업데이트</div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_write_update" class="ed" value="<?=$mw_basic[cf_include_write_update]?>"> 
	    <div class="cf_info">글작성 DB 업데이트 부분에에 포함될 파일입니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_include_write_update_tail] value=1>&nbsp; 글쓰기 업데이트 TAIL</div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_include_write_update_tail" class="ed" value="<?=$mw_basic[cf_include_write_update_tail]?>"> 
	    <div class="cf_info">글작성 DB 업데이트 부분에에 포함될 파일입니다.</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_download_popup] value=1>&nbsp; 다운로드 팝업 </div>
	<div class="cf_content">
            <input type=checkbox name=cf_download_popup value=1> 사용
            <span class="cf_info">(다운로드시 안내문 팝업창 오픈)</span>
	    <script> document.cf_form.cf_download_popup.checked = <?=$mw_basic[cf_download_popup]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_download_popup_size] value=1>&nbsp; 다운로드 팝업 크기 </div>
	<div class="cf_content">
            가로 <input type="text" size="4" class="ed" name="cf_download_popup_w" value="<?=$mw_basic[cf_download_popup_w]?>"> px ×
            세로 <input type="text" size="4" class="ed" name="cf_download_popup_h" value="<?=$mw_basic[cf_download_popup_h]?>"> px
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_download_popup_msg] value=1>&nbsp; 다운로드 팝업 메세지 </div>
	<div class="cf_content">
            <textarea name=cf_download_popup_msg cols=60 rows=5 class=edarea><?=$mw_basic[cf_download_popup_msg]?></textarea>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_attach_count] value=1>&nbsp; 첨부파일 기본갯수 </div>
	<div class="cf_content" height=60>
            <input type="text" size="3" class="ed" name="cf_attach_count" value="<?=$mw_basic[cf_attach_count]?>"> 개
	    <span class="cf_info">(글작성시 첨부파일 기본 출력 갯수를 설정합니다.)</span>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_replace_word] value=1>&nbsp; 자동치환 </div>
	<div class="cf_content" height=60>
	    <select name=cf_replace_word>
	    <? for ($i=2; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i?></option>
	    <? } ?>
	    </select> 레벨
	    <span class="cf_info">({별명}을 게시물 조회자의 별명으로 자동치환, 사용할 수 있는 레벨제한)</span>
	    <script> document.cf_form.cf_replace_word.value = "<?=$mw_basic[cf_replace_word]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_name_title] value=1>&nbsp; 호칭 </div>
	<div class="cf_content" height=60>
            <input type="text" size="10" class="ed" name="cf_name_title" value="<?=$mw_basic[cf_name_title]?>">
	    <span class="cf_info">(이름옆에 호칭을 붙입니다.)</span>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_anonymous] value=1>&nbsp; 선택익명 </div>
	<div class="cf_content" height=60>
            <div>
                <input type="checkbox" name="cf_anonymous" value="1"> 사용
                <span class="cf_info">(글작성시 익명을 선택할 수 있습니다.)</span>
            </div>
            <div>
                <input type="checkbox" name="cf_anonymous_nopoint" value="1"> 포인트 적립안함
                <span class="cf_info">(선택익명 사용시 쓰기 포인트를 적립하지 않습니다.)</span>
            </div>
	    <script>
            document.cf_form.cf_anonymous.checked = "<?=$mw_basic[cf_anonymous]?>";
            document.cf_form.cf_anonymous_nopoint.checked = "<?=$mw_basic[cf_anonymous_nopoint]?>";
            </script>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"><input type=checkbox name=chk[cf_insert_subject] value=1>&nbsp; 글쓰기 기본 제목 </div>
	<div class="cf_content">
	    <input type="text" size="60" name="cf_insert_subject" class="ed" value="<?=$mw_basic[cf_insert_subject]?>"> 
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[bo_insert_content] value=1>&nbsp; 글쓰기 기본 내용 </div>
	<div class="cf_content">
            <textarea name=bo_insert_content cols=60 rows=5 class=edarea><?=$board[bo_insert_content]?></textarea>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_read_point_message] value=1>&nbsp; 포인트 차감 메시지 </div>
	<div class="cf_content">
       	    <input type=checkbox name=cf_read_point_message value=1> 사용 
	    <span class="cf_info">(글읽기 포인트가 마이너스일 경우 차감메시지를 출력합니다.)</span>
	    <script> document.cf_form.cf_read_point_message.checked = '<?=$mw_basic[cf_read_point_message]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_view_good] value=1>&nbsp; (비)추천 목록보기 권한 </div>
	<div class="cf_content" height=60>
	    <select name=cf_view_good>
	    <? for ($i=1; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i?></option>
	    <? } ?>
	    </select> 레벨
	    <span class="cf_info">(추천 또는 비추천한 회원 목록을 볼 수 있는 권한)</span>
	    <script> document.cf_form.cf_view_good.value = "<?=$mw_basic[cf_view_good]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type="checkbox" name="chk[cf_multimedia]" value=1>&nbsp; 멀티미디어</div>
	<div class="cf_content">
            <input type="checkbox" name="cf_multimedia_movie" value="1"> 동영상
            <input type="checkbox" name="cf_multimedia_image" value="1"> 이미지
            <input type="checkbox" name="cf_multimedia_flash" value="1"> 플래쉬
            <input type="checkbox" name="cf_multimedia_youtube" value="1"> 유튜브
            <input type="checkbox" name="cf_multimedia_link_movie" value="1"> 동영상(링크)
            <input type="checkbox" name="cf_multimedia_link_image" value="1"> 이미지(링크)
            <input type="checkbox" name="cf_multimedia_link_flash" value="1"> 플래쉬(링크)
	    <div class="cf_info">첨부파일 및 링크주소를 자동으로 재생합니다.</div>
	    <script>
            document.cf_form.cf_multimedia_movie.checked = '<? echo strstr($mw_basic[cf_multimedia], '/movie/')?'1':''; ?>';
            document.cf_form.cf_multimedia_image.checked = '<? echo strstr($mw_basic[cf_multimedia], '/image/')?'1':''; ?>';
            document.cf_form.cf_multimedia_flash.checked = '<? echo strstr($mw_basic[cf_multimedia], '/flash/')?'1':''; ?>';
            document.cf_form.cf_multimedia_youtube.checked = '<? echo strstr($mw_basic[cf_multimedia], '/youtube/')?'1':''; ?>';
            document.cf_form.cf_multimedia_link_movie.checked = '<? echo strstr($mw_basic[cf_multimedia], '/link_movie/')?'1':''; ?>';
            document.cf_form.cf_multimedia_link_image.checked = '<? echo strstr($mw_basic[cf_multimedia], '/link_image/')?'1':''; ?>';
            document.cf_form.cf_multimedia_link_flash.checked = '<? echo strstr($mw_basic[cf_multimedia], '/link_flash/')?'1':''; ?>';
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type="checkbox" name="chk[cf_youtube_only]" value=1>&nbsp; 멀티미디어 링크전용</div>
	<div class="cf_content">
            <input type="checkbox" name="cf_youtube_only" value="1"> 사용 
	    <span class="cf_info">링크주소를 멀티미디어 재생전용으로 사용합니다.</span>
	    <script> document.cf_form.cf_youtube_only.checked = '<?=$mw_basic['cf_youtube_only']?>'; </script>
	</div>
    </div>


    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_youtube_size] value=1>&nbsp; Youtube 크기 </div>
	<div class="cf_content" height=60>
	    <select name="cf_youtube_size">
                <option value="240">[240p] 560x315 </option>
                <option value="360">[360p] 640x394 </option>
                <option value="480">[480p] 854x516 </option>
                <option value="720">[720p] 1280x759 </option>
                <option value="1080">[1080p] 1920x1123 </option>
	    </select>
	    <span class="cf_info">(유튜브 재생시 기본 크기 지정,
                <a href="<?=$g4['admin_path']?>/board_form.php?w=u&bo_table=<?=$bo_table?>"
                target="_blank" style="text-decoration:underline;">게시판 관리자의 '이미지 폭 크기' 보다 커지지 않습니다.</a>)</span>
	    <script> document.cf_form.cf_youtube_size.value = "<?=$mw_basic[cf_youtube_size]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_jwplayer_version] value=1>&nbsp; jwplayer 버전 </div>
	<div class="cf_content" height=60>
	    <select name="cf_jwplayer_version">
                <option value="jwplayer6">jwplayer6</option>
                <option value="jwplayer5">jwplayer5</option>
	    </select>
            , 자동재생 <input type="checkbox" name="cf_jwplayer_autostart" id="cf_jwplayer_autostart" value="1">
	    <script>
            document.cf_form.cf_jwplayer_version.value = "<?=$mw_basic[cf_jwplayer_version]?>";
            document.cf_form.cf_jwplayer_autostart.checked = "<?=$mw_basic['cf_jwplayer_autostart']?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_player_size] value=1>&nbsp; 플레이어 사이즈 </div>
	<div class="cf_content" height=60>
            <?php $size = explode("x", $mw_basic['cf_player_size']); ?>
            가로 : <input type="text" size="5" name="cf_player_size_x" value="<?=$size[0]?>">px,
            세로 : <input type="text" size="5" name="cf_player_size_y" value="<?=$size[1]?>">px
	    <span class="cf_info">(유튜브, jwplayer 사이즈 사용자 정의)</span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"><input type=checkbox name=chk[cf_content_align] value=1>&nbsp; 본문정렬 선택 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_content_align value=1> 사용 
	    <span class="cf_info">(글작성시 본문 정렬 옵션을 사용합니다. 에디터 미사용시 작동. 예:왼쪽, 가운데, 오른쪽)</span>
	    <script> document.cf_form.cf_content_align.checked = '<?=$mw_basic[cf_content_align]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"><input type=checkbox name=chk[cf_write_width] value=1>&nbsp; 글쓰기 창 크기</div>
	<div class="cf_content">
            가로: <select name="cf_write_width">
                <option value="normal">보통</option>
                <option value="large">크게</option>
            </select>,
            세로: <input type="text" size="3" name="cf_write_height" value="<?=$mw_basic['cf_write_height']?>">라인
	    <script> document.cf_form.cf_write_width.value = '<?=$mw_basic[cf_write_width]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"><input type=checkbox name=chk[cf_time_list] value=1>&nbsp; 날짜 표시 </div>
	<div class="cf_content">
            <div>
                목록 :
                <select name="cf_time_list" id="cf_time_list">
                    <option value="">기본</option>
                    <option value="sns">SNS스타일(1분전, 2시간전, 3일전 ...)</option>
                    <option value="manual">사용자정의</option>
                </select>
                <input type="text" size="20" class="ed" name="cf_time_list_manual" id="cf_time_list_manual" style="display:none">
            </div>
            <div>
                본문 :
                <select name="cf_time_view" id="cf_time_view">
                    <option value="">기본</option>
                    <option value="sns">SNS스타일(1분전, 2시간전, 3일전 ...)</option>
                    <option value="manual">사용자정의</option>
                </select>
                <input type="text" size="20" class="ed" name="cf_time_view_manual" id="cf_time_view_manual" style="display:none">
            </div>
            <div>
                댓글 :
                <select name="cf_time_comment" id="cf_time_comment">
                    <option value="">기본</option>
                    <option value="sns">SNS스타일(1분전, 2시간전, 3일전 ...)</option>
                    <option value="manual">사용자정의</option>
                </select>
                <input type="text" size="20" class="ed" name="cf_time_comment_manual" id="cf_time_comment_manual" style="display:none">
            </div>
	    <script>
            <?php
            if ($mw_basic['cf_sns_datetime']) {
                $mw_basic['cf_time_list'] = "sns";
            }
            if ($mw_basic['cf_time_list'] && $mw_basic['cf_time_list'] != "sns") {
                $mw_basic['cf_time_list_manual'] = $mw_basic['cf_time_list'];
                $mw_basic['cf_time_list'] = 'manual';
            }
            if ($mw_basic['cf_time_view'] && $mw_basic['cf_time_view'] != "sns") {
                $mw_basic['cf_time_view_manual'] = $mw_basic['cf_time_view'];
                $mw_basic['cf_time_view'] = 'manual';
            }
            if ($mw_basic['cf_time_comment'] && $mw_basic['cf_time_comment'] != "sns") {
                $mw_basic['cf_time_comment_manual'] = $mw_basic['cf_time_comment'];
                $mw_basic['cf_time_comment'] = 'manual';
            }

            if (!$mw_basic['cf_time_list_manual']) $mw_basic['cf_time_list_manual'] = "m-d";
            if (!$mw_basic['cf_time_view_manual']) $mw_basic['cf_time_view_manual'] = "Y-m-d (w) H:i:s";
            if (!$mw_basic['cf_time_comment_manual']) $mw_basic['cf_time_comment_manual'] = "Y-m-d (w) H:i:s";
            ?>
            $(document).ready(function () {
                $("#cf_time_list").val('<?php echo $mw_basic['cf_time_list']?>');
                $("#cf_time_view").val('<?php echo $mw_basic['cf_time_view']?>');
                $("#cf_time_comment").val('<?php echo $mw_basic['cf_time_comment']?>');

                $("#cf_time_list").change(function () {
                    if ($(this).val() == "manual") {
                        $("#cf_time_list_manual").css("display", "inline");
                        $("#cf_time_list_manual").val("<?php echo $mw_basic['cf_time_list_manual']?>");
                    }
                    else
                        $("#cf_time_list_manual").css("display", "none");
                }).change();

                $("#cf_time_view").change(function () {
                    if ($(this).val() == "manual") {
                        $("#cf_time_view_manual").css("display", "inline");
                        $("#cf_time_view_manual").val("<?php echo $mw_basic['cf_time_view_manual']?>");
                    }
                    else
                        $("#cf_time_view_manual").css("display", "none");
                }).change();

                $("#cf_time_comment").change(function () {
                    if ($(this).val() == "manual") {
                        $("#cf_time_comment_manual").css("display", "inline");
                        $("#cf_time_comment_manual").val("<?php echo $mw_basic['cf_time_comment_manual']?>");
                    }
                    else
                        $("#cf_time_comment_manual").css("display", "none");
                }).change();

            });
            </script>
	</div>
    </div>


    <div class="cf_item">
	<div class="cf_title"><input type=checkbox name=chk[cf_ca_order] value=1>&nbsp; 분류정렬</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_ca_order value=1> 사용 
	    <span class="cf_info">(분류이름을 오른차순으로 정렬하여 출력합니다.)</span>
	    <script> document.cf_form.cf_ca_order.checked = '<?=$mw_basic[cf_ca_order]?>'; </script>
	</div>
    </div>


    <div class="block"></div>
</div>

<div id="tabs-3" class="tabs"> <!-- 기능 -->

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_ccl] value=1>&nbsp; CCL 표시 </div>
	<div class="cf_content">
	    <select name=cf_ccl>
	    <option value="0"> 사용안함 </option>
	    <option value="1"> 사용 </option>
	    </select>
	    <a href="http://www.creativecommons.or.kr/" target=_blank>CCL<span class="cf_info">(Creative Commons License 알아보기)</span></a>
	    <script> document.cf_form.cf_ccl.value = "<?=$mw_basic[cf_ccl]?>"; </script>
	</div>
    </div>
<!--
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_star] value=1>&nbsp; 댓글 별점 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_star value=1> 사용 <span class="cf_info">(댓글작성시 별점을 함께 입력합니다.)</span>  
	    <script> document.cf_form.cf_star.checked = <?=$mw_basic[cf_star]?>; </script>
	</div>
    </div>
-->

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_link_blank] value=1>&nbsp; 링크 새창 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_link_blank value=1> 사용 <span class="cf_info">(본문의 링크가 무조건 새창으로 열림)</span>
	    <script> document.cf_form.cf_link_blank.checked = <?=$mw_basic[cf_link_blank]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_zzal] value=1>&nbsp; 짤방 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_zzal value=1> 사용
	    <input type=checkbox name=cf_zzal_must value=1> 필수
	    <span class="cf_info">(첨부파일을 짤방으로 이용합니다.)</span>
	    <script> document.cf_form.cf_zzal.checked = <?=$mw_basic[cf_zzal]?>; </script>
	    <script> document.cf_form.cf_zzal_must.checked = <?=$mw_basic[cf_zzal_must]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_source_copy] value=1>&nbsp; 출처 자동복사 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_source_copy value=1> 사용 <span class="cf_info">(본문 복사시 출처를 자동으로 복사합니다. IE 전용)</span>
	    <script> document.cf_form.cf_source_copy.checked = <?=$mw_basic[cf_source_copy]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_post_emoticon] value=1>&nbsp; 글작성시 이모티콘 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_post_emoticon value=1> 사용
	    <span class="cf_info">(에디터 사용시 자동 사용)</span>
	    <script> document.cf_form.cf_post_emoticon.checked = "<?=$mw_basic[cf_post_emoticon]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_post_specialchars] value=1>&nbsp; 글작성시 특수문자 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_post_specialchars value=1> 사용
	    <script> document.cf_form.cf_post_specialchars.checked = "<?=$mw_basic[cf_post_specialchars]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_mention] value=1>&nbsp; 댓글 언급 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_mention value=1> 사용
            <span class="cf_info">(댓글 답글 사용시 닉네임이 자동 삽입됩니다.)</span>
	    <script> document.cf_form.cf_comment_mention.checked = "<?=$mw_basic[cf_comment_mention]?>"; </script>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_html] value=1>&nbsp; 댓글 html </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_html value=1> 허용 
	    <script> document.cf_form.cf_comment_html.checked = "<?=$mw_basic[cf_comment_html]?>"; </script>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_page] value=1>&nbsp; 댓글 페이징 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_page value=1> 사용,
	    한페이지당 <input type="text" name="cf_comment_page_rows" size="3" value="<?=$mw_basic[cf_comment_page_rows]?>" class="ed"> 개 출력
	    <input type="checkbox" name="cf_comment_page_first" value="1">첫 페이지부터 출력
	    <script>
            document.cf_form.cf_comment_page.checked = "<?=$mw_basic[cf_comment_page]?>";
            document.cf_form.cf_comment_page_first.checked = "<?=$mw_basic[cf_comment_page_first]?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type="checkbox" name="chk[cf_comment_file]" value="1">&nbsp; 댓글 첨부파일 </div>
	<div class="cf_content">
	    <select name="cf_comment_file">
	    <? for ($i=0; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i==0?'사용안함':"{$i}레벨 이상"?></option>
	    <? } ?>
	    </select>
	    <span class="cf_info">(댓글에 첨부파일 기능을 사용합니다.)</span>
	    <script> document.cf_form.cf_comment_file.value = "<?=$mw_basic[cf_comment_file]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_emoticon] value=1>&nbsp; 댓글 이모티콘 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_emoticon value=1> 사용
	    <span class="cf_info">(댓글 에디터 사용시 자동 사용)</span>
	    <script> document.cf_form.cf_comment_emoticon.checked = <?=$mw_basic[cf_comment_emoticon]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_specialchars] value=1>&nbsp; 댓글 특수문자 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_specialchars value=1> 사용
	    <script> document.cf_form.cf_comment_specialchars.checked = <?=$mw_basic[cf_comment_specialchars]?>; </script>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_ban] value=1>&nbsp; 댓글 허락 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_ban value=1> 사용
	    <span class="cf_info">(허락하지 않은 게시물에는 댓글를 작성할 수 없음)</span>
	    <script> document.cf_form.cf_comment_ban.checked = <?=$mw_basic[cf_comment_ban]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_ban_level] value=1>&nbsp; 댓글 허락 레벨 </div>
	<div class="cf_content">
	    <select name=cf_comment_ban_level>
	    <? for ($i=1; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i?></option>
	    <? } ?>
	    </select>
	    <span class="cf_info">(댓글 허락을 사용할 수 있는 레벨)</span>
	    <script> document.cf_form.cf_comment_ban_level.value = "<?=$mw_basic[cf_comment_ban_level]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_secret_no] value=1>&nbsp; 댓글 비밀글 </div>
	<div class="cf_content">
       	    <select name="cf_comment_secret_no">
	    <? for ($i=1; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i?></option>
	    <? } ?>
	    </select>
            레벨 이상 사용 가능
	    <script> document.cf_form.cf_comment_secret_no.value = '<?=$mw_basic[cf_comment_secret_no]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_secret] value=1>&nbsp; 댓글 비밀글 기본 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_secret value=1> 사용
	    <span class="cf_info">(댓글 입력시 비밀글 체크를 기본으로 합니다.)</span>
	    <script> document.cf_form.cf_comment_secret.checked = <?=$mw_basic[cf_comment_secret]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_period] value=1>&nbsp; 댓글 기간 </div>
	<div class="cf_content">
	    <input type="text" size="5" numeric name=cf_comment_period class=ed value="<?=$mw_basic[cf_comment_period]?>"> 일
	    <span class="cf_info">(글작성 후 이 기간이 지나면 댓글를 쓸 수 없음, 0이면 무제한)</span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_download_comment] value=1>&nbsp; 다운로드 제한 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_download_comment value=1> 댓글 필수,
	    <input type=checkbox name=cf_download_good value=1> 추천 필수,
	    <input type=text size=3 class=ed name=cf_download_day value="<?php echo $mw_basic['cf_download_day']?>">일에 
	    <input type=text size=3 class=ed name=cf_download_count value="<?php echo $mw_basic['cf_download_count']?>">번
	    <span class="cf_info">(다운로드 로그 사용시에만 작동합니다. )</span>
	    <script>
            document.cf_form.cf_download_comment.checked = <?=$mw_basic[cf_download_comment]?>;
            document.cf_form.cf_download_good.checked = "<?=$mw_basic[cf_download_good]?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_uploader_point] value=1>&nbsp; 업로더 포인트 </div>
	<div class="cf_content" height=50>
	    <input type=text size=5 name=cf_uploader_day value="<?=$mw_basic[cf_uploader_day]?>" class=ed> 일 동안
	    <input type=text size=5 name=cf_uploader_point value="<?=$mw_basic[cf_uploader_point]?>" class=ed> 포인트 제공<br/>
	    <span class="cf_info">(첨부파일 다운로드시 업로더에게 포인트를 제공. 0일 입력시 무제한)</span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_notice] value=1>&nbsp; 댓글 공지 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_comment_notice cols=60 rows=5 class=edarea><?=$mw_basic[cf_comment_notice]?></textarea>
	    <div class="cf_info">글 작성시 자동 첫번째 댓글 메시지 표시</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_write_notice] value=1>&nbsp; 글쓰기 버튼 공지 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_write_notice cols=60 rows=5 class=edarea><?=$mw_basic[cf_write_notice]?></textarea>
	    <div class="cf_info">글작성 버튼 클릭시 alert 공지 띄우기</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_guploader] value=1>&nbsp; 멀티업로더</div>
	<div class="cf_content">
            <select name="cf_guploader">
                <option value="">사용안함</option>
                <!--<option value="1">G-Uploader (이미지만 업로드, 회원만 가능)</option>
                <option value="2">SWF-Uploader</option>-->
                <option value="3">HTML5 방식 (IE10이상)</option>
            </select>
	    <span class="cf_info"></span>
	    <script> document.cf_form.cf_guploader.value = "<?=$mw_basic[cf_guploader]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_under_construction] value=1>&nbsp; 서비스 점검중 메세지</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_under_construction value=1> 사용
	    <span class="cf_info">(서비스 점검중 alert 메세지)</span>
	    <script> document.cf_form.cf_under_construction.checked = "<?=$mw_basic[cf_under_construction]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_no_delete] value=1>&nbsp; 삭제금지</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_no_delete value=1> 사용
	    <span class="cf_info">(관리자만 게시물 삭제 가능)</span>
	    <script> document.cf_form.cf_no_delete.checked = "<?=$mw_basic[cf_no_delete]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_only_one] value=1>&nbsp; 글한개만 작성가능 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_only_one value=1> 사용 
	    <span class="cf_info">(한사람당 글한개만 작성가능, 예:가입인사)</span>
	    <script> document.cf_form.cf_only_one.checked = '<?=$mw_basic[cf_only_one]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_read_register] value=1>&nbsp; 글읽을 조건 </div>
	<div class="cf_content">
	    <input type=text size=10 name=cf_read_point class=ed value="<?=$mw_basic[cf_read_point]?>"> 포인트 이상, 
	    가입후 <input type=text size=10 name=cf_read_register class=ed value="<?=$mw_basic[cf_read_register]?>"> 일 이상
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_write_day] value=1>&nbsp; 글작성 제한 </div>
	<div class="cf_content">
	    <input type=text size=10 name=cf_write_day class=ed value="<?=$mw_basic[cf_write_day]?>"> 일에
	    <input type=text size=10 name=cf_write_day_count class=ed value="<?=$mw_basic[cf_write_day_count]?>"> 번 이하 
            (<input type="checkbox" name="cf_write_day_ip" id="cf_write_day_ip" value="1"> IP체크)
            <?php if ($mw_basic['cf_write_day_ip']) { ?>
            <script> $("#cf_write_day_ip").attr("checked", "checked"); </script>
            <?php } ?>
            <input type="button" value="권한별설정"
                onclick="window.open('mw.level.php?bo_table=<?=$bo_table?>', 'level', 'width=600,height=600')">
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_write_register] value=1>&nbsp; 글작성 조건 </div>
	<div class="cf_content">
	    <input type=text size=10 name=cf_write_point class=ed value="<?=$mw_basic[cf_write_point]?>"> 포인트 이상, 
	    가입후 <input type=text size=10 name=cf_write_register class=ed value="<?=$mw_basic[cf_write_register]?>"> 일 이상
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_day] value=1>&nbsp; 댓글작성 제한 </div>
	<div class="cf_content">
	    <input type=text size=10 name=cf_comment_day class=ed value="<?=$mw_basic[cf_comment_day]?>"> 일에
	    <input type=text size=10 name=cf_comment_day_count class=ed value="<?=$mw_basic[cf_comment_day_count]?>"> 번 이하, 
            게시물당
	    <input type=text size=10 name=cf_comment_write_count class=ed value="<?=$mw_basic[cf_comment_write_count]?>"> 번 이하 
            (<input type="checkbox" name="cf_comment_day_ip" id="cf_comment_day_ip" value="1"> IP체크)
            <?php if ($mw_basic['cf_comment_day_ip']) { ?>
            <script> $("#cf_comment_day_ip").attr("checked", "checked"); </script>
            <?php } ?>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_register] value=1>&nbsp; 댓글작성 조건 </div>
	<div class="cf_content">
	    <input type=text size=10 name=cf_comment_point class=ed value="<?=$mw_basic[cf_comment_point]?>"> 포인트 이상, 
	    가입후 <input type=text size=10 name=cf_comment_register class=ed value="<?=$mw_basic[cf_comment_register]?>"> 일 이상
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_sns] value=1>&nbsp; SNS 퍼가기 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_sns_twitter value=1> 트위터
	    <!--<input type=checkbox name=cf_sns_me2day value=1> 미투데이 -->
	    <!--<input type=checkbox name=cf_sns_yozm value=1> 요즘-->
	    <input type=checkbox name=cf_sns_cyworld value=1> 싸이월드
	    <input type=checkbox name=cf_sns_naver value=1> 네이버 북마크
	    <input type=checkbox name=cf_sns_google value=1> 구글 북마크
	    <input type=checkbox name=cf_sns_facebook value=1> 페이스북
	    <input type=checkbox name=cf_sns_google_plus value=1> 구글플러스
	    <input type=checkbox name=cf_sns_facebook_good value=1> 페이스북 좋아요
	    <input type=checkbox name=cf_sns_google_good value=1> 구글 좋아요
            <br>
	    <input type=checkbox name=cf_sns_kakao value=1> 카카오톡 <span class="cf_info">(모바일에서만)</span>
	    <input type=checkbox name=cf_sns_kakaostory value=1> 카카오스토리 <span class="cf_info">(모바일에서만)</span>
	    <input type=checkbox name=cf_sns_line value=1> 라인 <span class="cf_info">(모바일에서만)</span>
	    <input type=checkbox name=cf_sns_band value=1> 밴드
            <br>
            카카오톡 앱 키 :
            <input type="text" size="20" class="ed" name="cf_kakao_key" value="<?php echo $mw_basic['cf_kakao_key']?>">
            (<a href="http://miwit.kr/b/mw_tip-4225" target="_blank">등록방법</a>, 썸네일은 가로,세로 80px이상만 전송됩니다.)
	    <script>
            document.cf_form.cf_sns_twitter.checked = '<? echo strstr($mw_basic[cf_sns], '/twitter/')?'1':''; ?>';
            //document.cf_form.cf_sns_me2day.checked = '<? echo strstr($mw_basic[cf_sns], '/me2day/')?'1':''; ?>';
            //document.cf_form.cf_sns_yozm.checked = '<? echo strstr($mw_basic[cf_sns], '/yozm/')?'1':''; ?>';
            document.cf_form.cf_sns_cyworld.checked = '<? echo strstr($mw_basic[cf_sns], '/cyworld/')?'1':''; ?>';
            document.cf_form.cf_sns_naver.checked = '<? echo strstr($mw_basic[cf_sns], '/naver/')?'1':''; ?>';
            document.cf_form.cf_sns_google.checked = '<? echo strstr($mw_basic[cf_sns], '/google/')?'1':''; ?>';
            document.cf_form.cf_sns_facebook.checked = '<? echo strstr($mw_basic[cf_sns], '/facebook/')?'1':''; ?>';
            document.cf_form.cf_sns_facebook_good.checked = '<? echo strstr($mw_basic[cf_sns], '/facebook_good/')?'1':''; ?>';
            document.cf_form.cf_sns_google_plus.checked = '<? echo strstr($mw_basic[cf_sns], '/google_plus/')?'1':''; ?>';
            document.cf_form.cf_sns_google_good.checked = '<? echo strstr($mw_basic[cf_sns], '/google_good/')?'1':''; ?>';
            document.cf_form.cf_sns_kakao.checked = '<? echo strstr($mw_basic[cf_sns], '/kakao/')?'1':''; ?>';
            document.cf_form.cf_sns_kakaostory.checked = '<? echo strstr($mw_basic[cf_sns], '/kakaostory/')?'1':''; ?>';
            document.cf_form.cf_sns_line.checked = '<? echo strstr($mw_basic[cf_sns], '/line/')?'1':''; ?>';
            document.cf_form.cf_sns_band.checked = '<? echo strstr($mw_basic[cf_sns], '/band/')?'1':''; ?>';
            </script>
	</div>
    </div>


    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_vote] value=1>&nbsp; 설문 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_vote value=1> 사용 
	    <span class="cf_info">(설문을 진행할 수 있습니다.)</span>
	    <script> document.cf_form.cf_vote.checked = '<?=$mw_basic[cf_vote]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_vote_level] value=1>&nbsp; 설문등록 가능레벨 </div>
	<div class="cf_content">
	    <select name=cf_vote_level>
	    <? for ($i=2; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i?></option>
	    <? } ?>
	    </select>
	    <span class="cf_info">(설문을 등록할 수 있는 레벨)</span>
	    <script> document.cf_form.cf_vote_level.value = "<?=$mw_basic[cf_vote_level]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_vote_join_level] value=1>&nbsp; 설문참여 가능레벨 </div>
	<div class="cf_content">
	    <select name=cf_vote_join_level>
	    <? for ($i=1; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i?></option>
	    <? } ?>
	    </select>
	    <span class="cf_info">(설문에 참여할 수 있는 레벨)</span>
	    <script> document.cf_form.cf_vote_join_level.value = "<?=$mw_basic[cf_vote_join_level]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_read_level] value=1>&nbsp; 게시물별 읽기 레벨 </div>
	<div class="cf_content">
	    <input type=checkbox name="cf_read_level" value="1"> 사용 
	    <select name="cf_read_level_own">
	    <? for ($i=1; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i?></option>
	    <? } ?>
	    </select>
	    <span class="cf_info">(설정가능한 레벨)</span>
	    <script>
            document.cf_form.cf_read_level.checked = "<?=$mw_basic[cf_read_level]?>";
            document.cf_form.cf_read_level_own.value = "<?=$mw_basic[cf_read_level_own]?>";
            </script>
	</div>
    </div>

    <? /* 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_preview_level] value=1>&nbsp; 본문 미리보기 </div>
	<div class="cf_content">
	    <select name="cf_preview_level">
	    <? for ($i=1; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i?></option>
	    <? } ?>
	    </select>
            레벨 미만는
            <input type="text" size="5" name="cf_preview_size" class="ed" value="<?=$mw_basic[cf_preview_size]?>">
            글자만 보입니다.
            <span class="cf_info">(글읽기 권한이 있어야 미리보기가 작동합니다. 0 이면 작동하지 않습니다.)</span>
	    <script>
            document.cf_form.cf_preview_level.value = "<?=$mw_basic[cf_preview_level]?>";
            </script>
	</div>
    </div>
    */ ?>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_must_notice] value=1>&nbsp; 공지필수</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_must_notice value=1> 쓰기
	    <input type=checkbox name=cf_must_notice_read value=1> 읽기
	    <input type=checkbox name=cf_must_notice_comment value=1> 댓글
	    <input type=checkbox name=cf_must_notice_down value=1> 다운 
	    <span class="cf_info">(게시판 공지를 모두 읽어야 가능함)</span>
	    <script>
            document.cf_form.cf_must_notice.checked = '<?=$mw_basic[cf_must_notice]?>';
            document.cf_form.cf_must_notice_read.checked = '<?=$mw_basic[cf_must_notice_read]?>';
            document.cf_form.cf_must_notice_comment.checked = '<?=$mw_basic[cf_must_notice_comment]?>';
            document.cf_form.cf_must_notice_down.checked = '<?=$mw_basic[cf_must_notice_down]?>';
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_good] value=1>&nbsp; 댓글 추천</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_good value=1> 사용 
	    <span class="cf_info">(댓글 추천 기능을 사용합니다.)</span>
	    <script> document.cf_form.cf_comment_good.checked = '<?=$mw_basic[cf_comment_good]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_nogood] value=1>&nbsp; 댓글 반대</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_nogood value=1> 사용 
	    <span class="cf_info">(댓글 반대 기능을 사용합니다.)</span>
	    <script> document.cf_form.cf_comment_nogood.checked = '<?=$mw_basic[cf_comment_nogood]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_best] value=1>&nbsp; 댓글 베플</div>
	<div class="cf_content">
            <input type="text" name="cf_comment_best" class="ed" size="3" value="<?=$mw_basic[cf_comment_best]?>"> 개 출력,
            추천 <input type="text" name="cf_comment_best_limit" class="ed" size="3"
                value="<?=$mw_basic[cf_comment_best_limit]?>"> 개 이상,
            적립 포인트 <input type="text" name="cf_comment_best_point" class="ed" size="5"
                value="<?=$mw_basic[cf_comment_best_point]?>"> 점

	    <!--<span class="cf_info">(베스트 댓글 기능을 사용합니다.)</span>-->
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_icon_level] value=1>&nbsp; 레벨아이콘</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_icon_level value=1> 사용, 
            레벨단위 <input type="text" name="cf_icon_level_point" class="ed" size="7" value="<?=$mw_basic[cf_icon_level_point]?>"> 포인트
	    <script> document.cf_form.cf_icon_level.checked = '<?=$mw_basic[cf_icon_level]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_good_point] value=1>&nbsp; 추천 포인트</div>
	<div class="cf_content">
            추천 받는 사람 :
            <input type="text" name="cf_good_point" class="ed" size="3" value="<?=$mw_basic[cf_good_point]?>"> 포인트,
            추천한 사람 :
            <input type="text" name="cf_good_re_point" class="ed" size="3" value="<?=$mw_basic[cf_good_re_point]?>"> 포인트
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_nogood_point] value=1>&nbsp; 비추 포인트</div>
	<div class="cf_content">
            비추 받는 사람 : 
            <input type="text" name="cf_nogood_point" class="ed" size="3" value="<?=$mw_basic[cf_nogood_point]?>"> 포인트,
            비추한 사람 :
            <input type="text" name="cf_nogood_re_point" class="ed" size="3" value="<?=$mw_basic[cf_nogood_re_point]?>"> 포인트
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_good_point] value=1>&nbsp; 댓글 추천 포인트</div>
	<div class="cf_content">
            추천 받는 사람 :
            <input type="text" name="cf_comment_good_point" class="ed" size="3" value="<?=$mw_basic[cf_comment_good_point]?>"> 포인트,
            추천한 사람 :
            <input type="text" name="cf_comment_good_re_point" class="ed" size="3" value="<?=$mw_basic[cf_comment_good_re_point]?>"> 포인트
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_nogood_point] value=1>&nbsp; 댓글 비추 포인트</div>
	<div class="cf_content">
            비추 받는 사람 : 
            <input type="text" name="cf_comment_nogood_point" class="ed" size="3" value="<?=$mw_basic[cf_comment_nogood_point]?>"> 포인트,
            비추한 사람 :
            <input type="text" name="cf_comment_nogood_re_point" class="ed" size="3" value="<?=$mw_basic[cf_comment_nogood_re_point]?>"> 포인트
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_good_days] value=1>&nbsp; 추천/비추 기간설정</div>
	<div class="cf_content">
            <input type="text" name="cf_good_days" class="ed" size="3" value="<?=$mw_basic[cf_good_days]?>"> 일
            <span class="cf_info">(0으로 하면 기간에 영향을 받지 않습니다.)</span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_good_cancel_days] value=1>&nbsp; 추천/비추 취소 기간</div>
	<div class="cf_content">
            <input type="text" name="cf_good_cancel_days" class="ed" size="3" value="<?=$mw_basic[cf_good_cancel_days]?>"> 일
            <span class="cf_info">(0으로 하면 기간에 영향을 받지 않습니다.)</span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_good_count] value=1>&nbsp; 추천/비추 횟수 제한</div>
	<div class="cf_content">
            <input type="text" name="cf_good_count" class="ed" size="3" value="<?=$mw_basic[cf_good_count]?>"> 회
            <span class="cf_info">(하루에 추천/비추 할 수 있는 횟수를 제한합니다. 0으로 하면 영향을 받지 않습니다.)</span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_good_cancel] value=1>&nbsp; 추천취소 사용</div>
	<div class="cf_content">
            <input type="checkbox" name="cf_good_cancel" value="1" <? if ($mw_basic[cf_good_cancel]) { echo "checked"; } ?>/> 사용
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_good_level] value=1>&nbsp; 추천권한</div>
	<div class="cf_content">
            <select name="cf_good_level">
            <option value="0"> 사용안함 </option>
            <option value="1"> 비회원 </option>
            <? for ($i=2; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
            <script>cf_form.cf_good_level.value = "<?=$mw_basic[cf_good_level]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_nogood_level] value=1>&nbsp; 비추천권한</div>
	<div class="cf_content">
            <select name="cf_nogood_level">
            <option value="0"> 사용안함 </option>
            <option value="1"> 비회원 </option>
            <? for ($i=2; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
            <script>cf_form.cf_nogood_level.value = "<?=$mw_basic[cf_nogood_level]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_iframe_level] value=1>&nbsp; <strong>특수태그 사용권한</strong> </div>
	<div class="cf_content">
            <select name="cf_iframe_level">
            <option value="0"> 사용안함 </option>
            <? for ($i=2; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
            <span class="cf_info">(iframe/script/embed 코드 사용 권한 )</span>
	    <script>
            document.cf_form.cf_iframe_level.value = "<?=$mw_basic[cf_iframe_level]?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_google_map] value=1>&nbsp; 구글지도</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_google_map value=1> 사용 
	    <span class="cf_info">(본문에 지도를 삽입합니다.)</span>
	    <script> document.cf_form.cf_google_map.checked = '<?=$mw_basic[cf_google_map]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_bomb_level] value=1>&nbsp; 게시물 자동폭파  </div>
	<div class="cf_content">
            <select name="cf_bomb_level">
            <? for ($i=0; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
	    <span class="cf_info">(지정한 시간에 게시물이 자동 삭제됩니다. 0이면 사용안함)</span> 
            <br/>
            <select name="cf_bomb_item_select">
                <option value="">전체삭제
                <option value="1">부분삭제 (이동시 작동안함)
            </select>
            <input type="checkbox" name="cf_bomb_item[]" value="subject"
                <? if (strstr($mw_basic[cf_bomb_item], "subject")) echo "checked"?>> 제목
            <input type="checkbox" name="cf_bomb_item[]" value="content"
                <? if (strstr($mw_basic[cf_bomb_item], "content")) echo "checked"?>> 내용
            <input type="checkbox" name="cf_bomb_item[]" value="file"
                <? if (strstr($mw_basic[cf_bomb_item], "file")) echo "checked"?>> 첨부파일
            <br/>
            고정 <input type="text" size="3" name="cf_bomb_time" value="<?=$mw_basic[cf_bomb_time]?>" class="ed"/> 시간
	    <span class="cf_info">(고정시간을 사용할 경우 새로작성되는 모든 게시물에 자동폭파가 적용됩니다.)</span> 
            <br/>
            최소 <input type="text" size="3" name="cf_bomb_days_min" value="<?=$mw_basic[cf_bomb_days_min]?>" class="ed"/> 일 ~
            최대 <input type="text" size="3" name="cf_bomb_days_max" value="<?=$mw_basic[cf_bomb_days_max]?>" class="ed"/> 일 까지 가능
            <br/>
            폭파후 <input type="text" size="30" name="cf_bomb_move_table" value="<?=$mw_basic[cf_bomb_move_table]?>"/> 게시판으로 이동
            <br><input type="checkbox" name="cf_bomb_move_time" value="1"/> 글등록시간 폭파시간으로 재설정
            <br><input type="checkbox" name="cf_bomb_move_cate" value="1"/> 게시판명을 분류명으로 설정
	    <script>
            document.cf_form.cf_bomb_level.value = "<?=$mw_basic[cf_bomb_level]?>";
            document.cf_form.cf_bomb_item_select.value = "<? echo $mw_basic[cf_bomb_item] ? '1' : '' ?>";
            document.cf_form.cf_bomb_move_time.checked = "<?=$mw_basic[cf_bomb_move_time]?>";
            document.cf_form.cf_bomb_move_cate.checked = "<?=$mw_basic[cf_bomb_move_cate]?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_move_level] value=1>&nbsp; 예약 이동  </div>
	<div class="cf_content">
            <select name="cf_move_level">
            <? for ($i=0; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
	    <span class="cf_info">(게시물이 지정한 시간에 자동으로 이동 됩니다. 0이면 사용안함)</span> 
	    <script> document.cf_form.cf_move_level.value = <?=$mw_basic[cf_move_level]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_download_date] value=1>&nbsp; 첨부파일명 날짜  </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_download_date value="1"> 사용
	    <span class="cf_info">(첨부파일 다운로드시 업로드된 날짜를 파일명에 추가합니다.)</span> 
	    <script> document.cf_form.cf_download_date.checked = "<?=$mw_basic[cf_download_date]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_auto_move] value=1>&nbsp; 게시물 자동 복사/이동  </div>
	<div class="cf_content">
	    <select name=cf_auto_move_use>
                <option value=""> 사용안함 </option>
                <option value="copy"> 복사 </option>
                <option value="move"> 이동 </option>
            </select>
	    게시판ID : <input type=text size=10 class=ed name=cf_auto_move_bo_table value="<?=$mw_basic[cf_auto_move][bo_table]?>">,
	    기간 : 최근 <input type=text size=3 class=ed name=cf_auto_move_day value="<?=$mw_basic[cf_auto_move][day]?>">일
            <br/>
	    조회수 : <input type=text size=3 class=ed name=cf_auto_move_hit value="<?=$mw_basic[cf_auto_move][hit]?>">,
	    별점수 : <input type=text size=3 class=ed name=cf_auto_move_rate value="<?=$mw_basic[cf_auto_move][rate]?>">,
	    신고수 : <input type=text size=3 class=ed name=cf_auto_move_singo value="<?=$mw_basic[cf_auto_move][singo]?>">,
	    댓글수 : <input type=text size=3 class=ed name=cf_auto_move_comment value="<?=$mw_basic[cf_auto_move][comment]?>">,
	    비추천수 : <input type=text size=3 class=ed name=cf_auto_move_nogood value="<?=$mw_basic[cf_auto_move][nogood]?>">,
	    추천수 : <input type=text size=3 class=ed name=cf_auto_move_good value="<?=$mw_basic[cf_auto_move][good]?>">
            (<input type='checkbox' name="cf_auto_move_sub" value='1'> 추천수=추천-비추천수)
	    <script>
            document.cf_form.cf_auto_move_use.value = "<?=$mw_basic[cf_auto_move]['use']?>";
            document.cf_form.cf_auto_move_sub.checked = "<?=$mw_basic[cf_auto_move]['sub']?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_ban_subject] value=1>&nbsp; 말머리 금지</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_ban_subject value=1> 사용 
	    <span class="cf_info">(제목에 말머리를 사용할 수 없게 합니다.)</span>
	    <script> document.cf_form.cf_ban_subject.checked = '<?=$mw_basic[cf_ban_subject]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_key_level] value=1>&nbsp; 게시물별 비밀번호</div>
	<div class="cf_content">
            <select name="cf_key_level" id="cf_key_level">
            <option value="0">사용안함</option>
            <?php for ($i=1; $i<=10; $i++) { ?>
            <option value="<?php echo $i?>"><?php echo $i?> 레벨</option>
            <?php } ?>
            </select>
	    <span class="cf_info">(비밀번호를 입력해야 게시물을 확인할 수 있습니다.)</span>
	    <script> document.cf_form.cf_key_level.value = '<?php echo $mw_basic['cf_key_level']?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_jump_level] value=1>&nbsp; 새글점프</div>
	<div class="cf_content">
            <select name="cf_jump_level" id="cf_jump_level">
            <option value="0">사용안함</option>
            <?php for ($i=1; $i<=10; $i++) { ?>
            <option value="<?php echo $i?>"><?php echo $i?> 레벨</option>
            <?php } ?>
            </select>
	    <span class="cf_info">(현재글을 새글로 갱신합니다.)</span>
	    <script> document.cf_form.cf_jump_level.value = '<?php echo $mw_basic['cf_jump_level']?>'; </script>
            <div>
                포인트 차감 : <input type="text" class="ed" name="cf_jump_point" size="4" numeric value="<?=$mw_basic[cf_jump_point]?>"> p
            </div>
            <div>
                횟수 제한 :
                <input type="text" class="ed" name="cf_jump_days" size="4" numeric value="<?=$mw_basic[cf_jump_days]?>"> 일에 
                <input type="text" class="ed" name="cf_jump_count" size="4" numeric value="<?=$mw_basic[cf_jump_count]?>"> 번
            </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_rate_level] value=1>&nbsp; 별점평가</div>
	<div class="cf_content">
            <select name="cf_rate_level" id="cf_rate_level">
            <option value="0">사용안함</option>
            <?php for ($i=1; $i<=10; $i++) { ?>
            <option value="<?php echo $i?>"><?php echo $i?> 레벨</option>
            <?php } ?>
            </select> 이상 평가 가능,
                포인트 :
                <input type="text" class="ed" name="cf_rate_point" size="4" numeric value="<?=$mw_basic['cf_rate_point']?>"> p
                <span class="cf_info">(마이너스 입력시 차감)</span>
            <div>
                <input type="checkbox" name="cf_rate_down" id="cf_rate_down" value="1">
                <label for="cf_rate_down">다운로드 한 사람만 평가 가능</label>
                <span class="cf_info">(다운로드 로그 사용시)</span>
            </div>
            <div>
                <input type="checkbox" name="cf_rate_buy" id="cf_rate_buy" value="1">
                <label for="cf_rate_buy">구매자만 평가 가능</label>
                <span class="cf_info">(소셜커머스, 재능마켓)</span>
            </div>
	    <script>
            $("select[name=cf_rate_level]").val("<?php echo $mw_basic['cf_rate_level']?>");
            $("input[name=cf_rate_down]").attr("checked", <?php echo $mw_basic['cf_rate_down']?'true':'false'?>);
            $("input[name=cf_rate_buy]").attr("checked", <?php echo $mw_basic['cf_rate_buy']?'true':'false'?>);
            </script>
	</div>
    </div>

    <div class="block"></div>

</div> <!-- tabs-3 -->

<div id="tabs-4" class="tabs"> <!-- 알림 -->
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_singo] value=1>&nbsp; 신고 </div>
	<div class="cf_content">
            <select name="cf_singo">
                <option value=""> 사용안함 </option>
                <option value="1"> 사용, 쪽지로 알림</option>
                <option value="2"> 사용, 쪽지로 알리지 않음</option>
            </select>
	    <script> document.cf_form.cf_singo.value = <?=$mw_basic[cf_singo]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_singo_id] value=1>&nbsp; 신고통보 아이디 </div>
	<div class="cf_content" height=60>
	    <input type=text size=60 name=cf_singo_id class=ed value="<?=$mw_basic[cf_singo_id]?>">
	    <div class="cf_info">아이디 , (컴마) 로 구분</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_singo_after] value=1>&nbsp; 신고가능 레벨 </div>
	<div class="cf_content" height=60>
	    <select name=cf_singo_level>
	    <? for ($i=2; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i?></option>
	    <? } ?>
	    </select>
	    <span class="cf_info">(게시물 신고를 할 수 있는 레벨)</span>
	    <script> document.cf_form.cf_singo_level.value = "<?=$mw_basic[cf_singo_level]?>"; </script>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_singo_after] value=1>&nbsp; 신고 후 </div>
	<div class="cf_content" height=60>
            <input type="text" size="3" name="cf_singo_number" value="<?=$mw_basic[cf_singo_number]?>" class="ed"> 회 이상
	    <input type=checkbox name=cf_singo_id_block value=1> 아이디 차단
	    <input type=checkbox name=cf_singo_write_block value=1> 게시물 차단
	    <input type=checkbox name=cf_singo_write_secret value=1> 게시물 잠금
	    <script>
            document.cf_form.cf_singo_id_block.checked = "<?=$mw_basic[cf_singo_id_block]?>";
            document.cf_form.cf_singo_write_block.checked = "<?=$mw_basic[cf_singo_write_block]?>";
            document.cf_form.cf_singo_write_secret.checked = "<?=$mw_basic[cf_singo_write_secret]?>";
            </script>
	</div>
    </div>
 
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_singo_writer] value=1>&nbsp; 신고 작성자 통보</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_singo_writer value=1> 사용
	    <span class="cf_info">(신고를 당한 게시물의 작성자에게 신고 여부만 통보합니다.)</span>
	    <script> document.cf_form.cf_singo_writer.checked = '<?=$mw_basic[cf_singo_writer]?>'; </script>
	</div>
    </div>


    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_email] value=1>&nbsp; 글등록 알림메일 </div>
	<div class="cf_content" height=110>
	    <textarea name=cf_email cols=60 rows=5 class=edarea><?=$mw_basic[cf_email]?></textarea>
	    <div class="cf_info">이메일주소 Enter 로 구분</div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_hp] value=1>&nbsp; 글등록 알림문자 </div>
	<div class="cf_content" height=140>
	    <div>
                회신 : <input type=text size=10 name=cf_hp_reply value="<?=$mw_basic[cf_hp_reply]?>" class=ed>
		ID : <input type=text size=10 name=cf_sms_id value="<?=$mw_basic[cf_sms_id]?>" class=ed>
		PW : <input type=text size=10 name=cf_sms_pw value="<?=$mw_basic[cf_sms_pw]?>" class=ed>
		<span class="cf_info">(<a href="http://www.icodekorea.com" target=_blank>ICODEKOREA</a>,
		<a href="http://icodekorea.com/res/join_company_fix_a.php?sellid=sir2" target=_blank>건당 16원 가입</a>)</span>
	    </div>
	    <textarea name=cf_hp cols=60 rows=5 class=edarea><?=$mw_basic[cf_hp]?></textarea>
	    <div class="cf_info">핸드폰번호 Enter 로 구분</div>
            
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_memo_id] value=1>&nbsp; 글등록 알림쪽지 </div>
	<div class="cf_content" height=140>
	    <input type=text size=60 name=cf_memo_id class=ed value="<?=$mw_basic[cf_memo_id]?>">
	    <div class="cf_info">아이디 , (컴마) 로 구분</div>
	</div>
    </div>

    <div class="block"></div>
</div> <!-- tabs-4 -->
 
<div id="tabs-5" class="tabs"> <!-- 데이터 -->

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; 정렬 </div>
	<div class="cf_content">
	    <div> 게시물을 날짜순으로 정렬합니다.
                <span id=btn_order_date>
                    <input type=button class="bt" value="정렬시작" onclick="run_order('date', 'asc')">
                    <input type=button class="bt" value="역순정렬" onclick="run_order('date', 'desc')">
                </span>
            </div>
	    <div> 게시물을 제목순으로 정렬합니다.
                <span id=btn_order_subject>
                    <input type=button class="bt" value="정렬시작" onclick="run_order('subject', 'asc')">
                    <input type=button class="bt" value="역순정렬" onclick="run_order('subject', 'desc')">
                </span>
            </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; 제목 변경 </div>
	<div class="cf_content" height=80>
	    기존 : <input type=text size=30 id=su_old class=ed value="">
            <input type="checkbox" id="su_regex" value="1">정규식
            <br/>
	    신규 : <input type=text size=30 id=su_new class=ed value="">
	    <span id=btn_subject_change><input type=button class="bt" value="시작" onclick="run_subject_change()"></span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; 이모티콘 싱크 </div>
	<div class="cf_content" height=80>
	    디렉토리명 변경으로 이모티콘이 엑박으로 출력되는 것을 수정합니다.<br/>
	    기존 : <input type=text size=30 id=em_old class=ed value="mw.basic.v.1.0.0/emoticon"><br/>
	    신규 : <input type=text size=30 id=em_new class=ed value="mw.basic/mw.emoticon">
	    <span id=btn_emoticon_sync><input type=button class="bt" value="시작" onclick="run_emoticon_sync()"></span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; 분류명 변경 </div>
	<div class="cf_content" height=80>
	    기존 : <input type=text size=30 id=ca_old class=ed value=""><br/>
	    신규 : <input type=text size=30 id=ca_new class=ed value="">
	    <span id=btn_category_change><input type=button class="bt" value="시작" onclick="run_category_change()"></span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; 분류 설정 </div>
	<div class="cf_content" height=80>
            <style>
            .mw_category { background-color:#ccc; }
            .mw_category td { background-color:#eee; text-align:center; }
            </style>
            <table border="0" cellpadding="3" cellspacing="1" class="mw_category">
            <tr>
                <td width="120">분류명</td>
                <td>출력형태</td>
                <td>목록권한</td>
                <td>읽기권한</td>
                <td>쓰기권한</td>
                <td>색상</td>
                <td>-</td>
            </tr>
            <?php
            $ca_list = array_filter(explode("|", $board['bo_category_list']), "trim");
            $db_list = implode("','", $ca_list);
            sql_query("delete from {$mw['category_table']} where bo_table = '{$bo_table}' and ca_name not in ('$db_list')");

            foreach ((array)$ca_list as $ca_name) {
                $row = mw_category_info($ca_name);
                if (!$row) {
                    sql_query("insert into {$mw['category_table']} set bo_table = '{$bo_table}', ca_name = '{$ca_name}' ");
                    $row['ca_id'] = sql_insert_id();
                }
                ?>
                <tr>
                    <td><?php echo $ca_name?></td>
                    <td>
                        <select id="ca_type_<?php echo $row['ca_id']?>" name="ca_type_<?php echo $row['ca_id']?>">
                            <option value="">기본</option>
                            <option value="list">목록형</option>
                            <option value="thumb">썸네일형</option>
                            <option value="gall">갤러리형</option>
                            <option value="desc">요약형</option>
                        </select>
                        <script> $("#ca_type_<?php echo $row['ca_id']?>").val("<?php echo $row['ca_type']?>"); </script>
                    </td>
                    <td>
                        <select id="ca_level_list_<?php echo $row['ca_id']?>" name="ca_level_list_<?php echo $row['ca_id']?>">
                            <option value="">기본</option>
                            <?php for ($i=$board['bo_list_level']; $i<=10; ++$i) { ?>
                            <option value="<?php echo $i?>"><?php echo $i?></option>
                            <?php } ?>
                        </select>
                        <script> $("#ca_level_list_<?php echo $row['ca_id']?>").val("<?php echo $row['ca_level_list']?>"); </script>
                    </td>
                    <td>
                        <select id="ca_level_view_<?php echo $row['ca_id']?>" name="ca_level_view_<?php echo $row['ca_id']?>">
                            <option value="">기본</option>
                            <?php for ($i=$board['bo_read_level']; $i<=10; ++$i) { ?>
                            <option value="<?php echo $i?>"><?php echo $i?></option>
                            <?php } ?>
                        </select>
                        <script> $("#ca_level_view_<?php echo $row['ca_id']?>").val("<?php echo $row['ca_level_view']?>"); </script>
                    </td>
                    <td>
                        <select id="ca_level_write_<?php echo $row['ca_id']?>" name="ca_level_write_<?php echo $row['ca_id']?>">
                            <option value="">기본</option>
                            <?php for ($i=$board['bo_write_level']; $i<=10; ++$i) { ?>
                            <option value="<?php echo $i?>"><?php echo $i?></option>
                            <?php } ?>
                        </select>
                        <script> $("#ca_level_write_<?php echo $row['ca_id']?>").val("<?php echo $row['ca_level_write']?>"); </script>
                    </td>
                    <td>
                        <input type="text" size="10" maxlength="7" id="ca_color_<?php echo $row['ca_id']?>" name="ca_color_<?php echo $row['ca_id']?>" value="#<?php echo $row['ca_color']?>">
                    </td>
                    <td>
                        <input type="button" class="bt" value="글삭제" onclick="mw_category_action('<?php echo $ca_name?>', 'del')">
                        <input type="button" class="bt" value="글이동" onclick="mw_category_action('<?php echo $ca_name?>', 'move')">
                        <input type="button" class="bt" value="글복사" onclick="mw_category_action('<?php echo $ca_name?>', 'copy')">
                    </td>
                </tr>
                <?php
            }
            ?>
            </table>
            <script>
            function mw_category_action(ca_name, act)
            {
                if (act == 'del') {
                    if (!confirm("정말 '" + ca_name + "' 분류의 게시물을 모두 삭제하시겠습니까?")) return;
                    $.get("<?php echo $board_skin_path?>/mw.adm/mw.category.delete.php", {
                        "bo_table" : "<?php echo $bo_table?>",
                        "ca_name" : ca_name
                    }, function (str) {
                        alert(str);
                    });
                    return;
                }
                var url = "<?php echo $board_skin_path?>/mw.adm/mw.category.move.php";
                url += "?bo_table=<?php echo $bo_table?>";
                url += "&ca_name=" + ca_name;
                url += "&sw=" + act;
                var sub_win = window.open(url, "move", "left=50, top=50, width=500, height=550, scrollbars=1");
            }
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_download_log] value=1>&nbsp; 다운로드 로그 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_download_log value=1> 사용
            <?php
            $row = sql_fetch("select count(*) as cnt from {$mw['download_log_table']} where bo_table = '{$bo_table}'");
            ?>
            <input type="button" class="bt" value="(<?php echo $row['cnt']?>건) 로그비움" onclick="log_del(this, 'down')">
	    <span class="cf_info">(다운로드 기록을 남깁니다.)</span>
	    <script> document.cf_form.cf_download_log.checked = "<?=$mw_basic[cf_download_log]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_post_history] value=1>&nbsp; 글변경 로그 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_post_history value=1> 사용
            <?php
            $row = sql_fetch("select count(*) as cnt from {$mw['post_history_table']} where bo_table = '{$bo_table}'");
            ?>
            <input type="button" class="bt" value="(<?php echo $row['cnt']?>건) 로그비움" onclick="log_del(this, 'post')">
	    <span class="cf_info">(수정된 글의 원본을 보관합니다.)</span>
	    <script> document.cf_form.cf_post_history.checked = "<?=$mw_basic[cf_post_history]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_link_log] value=1>&nbsp; 링크 로그 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_link_log value=1> 사용
            <?php
            $row = sql_fetch("select count(*) as cnt from {$mw['link_log_table']} where bo_table = '{$bo_table}'");
            ?>
            <input type="button" class="bt" value="(<?php echo $row['cnt']?>건) 로그비움" onclick="log_del(this, 'link')">
	    <span class="cf_info">(링크 클릭 기록을 남깁니다.)</span>
	    <script> document.cf_form.cf_link_log.checked = "<?=$mw_basic[cf_link_log]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_delete_log] value=1>&nbsp; 삭제글 남김 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_delete_log value=1> 원글 사용,
	    <input type=checkbox name=cf_comment_delete_log value=1> 댓글 사용 
	    <span class="cf_info">(글을 삭제하면 "삭제되었습니다" 로 변경, 선택삭제는 그냥 삭제됨)</span>
	    <script> document.cf_form.cf_delete_log.checked = "<?=$mw_basic[cf_delete_log]?>"; </script>
	    <script> document.cf_form.cf_comment_delete_log.checked = "<?=$mw_basic[cf_comment_delete_log]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_trash] value=1>&nbsp; 삭제글 이동 </div>
	<div class="cf_content">
            <input type="text" name="cf_trash" size="20" class="ed" value="<?php echo $mw_basic['cf_trash']?>">
	    <span class="cf_info">(글을 삭제하면 설정한 게시판으로 이동합니다. 휴지통 개념)</span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_post_history_level] value=1>&nbsp; 글변경 로그 확인 </div>
	<div class="cf_content">
	    <select name=cf_post_history_level>
	    <? for ($i=1; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i?></option>
	    <? } ?>
	    </select>
	    <span class="cf_info">(글변경 로그를 확인할 수 있는 레벨)</span>
	    <script> document.cf_form.cf_post_history_level.value = "<?=$mw_basic[cf_post_history_level]?>"; </script>
	</div>
    </div>

     <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; 공지사항 순서 변경 </div>
	<div class="cf_content">
	    <select name=bo_notice id=bo_notice multiple style="width:400px; height:200px;">
            <?
            $bo_notice = explode($notice_div, $board[bo_notice]);
            for ($i=0, $m=sizeof($bo_notice); $i<$m; $i++) { 
                if (!trim($bo_notice[$i])) continue;
                $row = sql_fetch("select wr_id, wr_subject from $write_table where wr_id = '{$bo_notice[$i]}'");
            ?>
                <option value="<?=$row[wr_id]?>"> <?=get_text($row[wr_subject],1)?> </option>
            <? } ?>
	    </select>
            <div style="margin:10px 0 0 0;">
                <input type="button" class="bt" value="↑" onclick="$('#bo_notice').moveOptionUp();">
                <input type="button" class="bt" value="↓" onclick="$('#bo_notice').moveOptionDown();">
                <input type="button" class="bt" value="저장" onclick="bo_notice_submit()">
            </div>
            <script>
            function bo_notice_submit() {
                var bo_notice = "";
                $('#bo_notice').selectAllOptions();
                $('#bo_notice :selected').each(function (i, sel) {
                    bo_notice += $(sel).val() + "<?php echo $notice_div?>";
                });
                $.post("<?=$board_skin_path?>/mw.proc/mw.bo_notice.php", { 
                    'bo_table':'<?=$bo_table?>', 
                    'bo_notice':bo_notice, 
                    'token':'<?=$token?>' }
                , function (req) {
                    if (req == "true")
                        alert("공지사항 순서변경이 완료되었습니다.");
                    else
                        alert(req);
                });
            }
            </script>
	</div>
    </div>

    <div class="block"></div>
</div> <!-- tabs-5 -->

<div id="tabs-5-2" class="tabs"> <!-- 이미지 -->

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_resize_quality] value=1>&nbsp; 이미지 퀄리티</div>
	<div class="cf_content">
            <select name="cf_resize_quality">
            <? for ($i=100; $i>=10; $i=$i-10) { ?>
            <option value="<?=$i?>"><?=$i?></option>
            <? } ?> 
            </select>
	    <script>
            document.cf_form.cf_resize_quality.value = '<?=$mw_basic[cf_resize_quality]?>';
            </script>
            <span class="cf_info">(리사이징, 자동회전, 워터마크 생성 등의 작업에 이미지 퀄리티를 설정합니다.)</span>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_resize_base] value=1>&nbsp; 리사이징 기준</div>
	<div class="cf_content">
            <select name="cf_resize_base">
                <option value="long">긴쪽</option>
                <option value="width">가로</option>
                <option value="height">세로</option>
            </select>
	    <script>
            document.cf_form.cf_resize_base.value = '<?=$mw_basic[cf_resize_base]?>';
            </script>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_noimage_path] value=1>&nbsp; NoImage 파일경로</div>
	<div class="cf_content">
            <input type="text" size="50" name="cf_noimage_path" class="ed" value="<?=$mw_basic[cf_noimage_path]?>">
            <span class="cf_info">(그누보드 bbs 디렉토리 기준 상대경로 입력 예: ../img/noimage.gif)</span>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_thumb_round] value=1>&nbsp; 라운드효과 </div>
	<div class="cf_content">
            <input type="checkbox" name="cf_thumb_round" value="1"> 사용 <span class="cf_info">(모서리를 둥글게 표현합니다)</span>
	    <script>
            document.cf_form.cf_thumb_round.checked = '<?=$mw_basic[cf_thumb_round]?>';
            </script>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_thumb] value=1>&nbsp; 썸네일1 (기본)</div>
	<div class="cf_content">
	    가로 <input type=text size=3 name=cf_thumb_width class=ed value="<?=$mw_basic[cf_thumb_width]?>">px
	    세로 <input type=text size=3 name=cf_thumb_height class=ed value="<?=$mw_basic[cf_thumb_height]?>">px
	    <input type=checkbox name=cf_thumb_keep value=1> 썸네일 비율을 유지합니다.
	    <script> document.cf_form.cf_thumb_keep.checked = '<?=$mw_basic[cf_thumb_keep]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_thumb2] value=1>&nbsp; 썸네일2 </div>
	<div class="cf_content">
	    가로 <input type=text size=3 name=cf_thumb2_width class=ed value="<?=$mw_basic[cf_thumb2_width]?>">px
	    세로 <input type=text size=3 name=cf_thumb2_height class=ed value="<?=$mw_basic[cf_thumb2_height]?>">px
	    <input type=checkbox name=cf_thumb2_keep value=1> 썸네일 비율을 유지합니다.
	    <script> document.cf_form.cf_thumb2_keep.checked = '<?=$mw_basic[cf_thumb2_keep]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_thumb3] value=1>&nbsp; 썸네일3 </div>
	<div class="cf_content">
	    가로 <input type=text size=3 name=cf_thumb3_width class=ed value="<?=$mw_basic[cf_thumb3_width]?>">px
	    세로 <input type=text size=3 name=cf_thumb3_height class=ed value="<?=$mw_basic[cf_thumb3_height]?>">px
	    <input type=checkbox name=cf_thumb3_keep value=1> 썸네일 비율을 유지합니다.
	    <script> document.cf_form.cf_thumb3_keep.checked = '<?=$mw_basic[cf_thumb3_keep]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_thumb4] value=1>&nbsp; 썸네일4 </div>
	<div class="cf_content">
	    가로 <input type=text size=3 name=cf_thumb4_width class=ed value="<?=$mw_basic[cf_thumb4_width]?>">px
	    세로 <input type=text size=3 name=cf_thumb4_height class=ed value="<?=$mw_basic[cf_thumb4_height]?>">px
	    <input type=checkbox name=cf_thumb4_keep value=1> 썸네일 비율을 유지합니다.
	    <script> document.cf_form.cf_thumb4_keep.checked = '<?=$mw_basic[cf_thumb4_keep]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_thumb5] value=1>&nbsp; 썸네일5 </div>
	<div class="cf_content">
	    가로 <input type=text size=3 name=cf_thumb5_width class=ed value="<?=$mw_basic[cf_thumb5_width]?>">px
	    세로 <input type=text size=3 name=cf_thumb5_height class=ed value="<?=$mw_basic[cf_thumb5_height]?>">px
	    <input type=checkbox name=cf_thumb5_keep value=1> 썸네일 비율을 유지합니다.
	    <script> document.cf_form.cf_thumb5_keep.checked = '<?=$mw_basic[cf_thumb5_keep]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_resize_original] value=1>&nbsp; 원본 강제 리사이징 </div>
	<div class="cf_content">
	    크기 <input type=text size=3 name=cf_resize_original class=ed value="<?=$mw_basic[cf_resize_original]?>">px
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_image_auto_rotate] value=1>&nbsp; 자동 회전 </div>
	<div class="cf_content">
            <input type="checkbox" name="cf_image_auto_rotate" value="1"> 사용 <span class="cf_info">(사진 방향을 자동으로 감지해 변환합니다.)</span>
	    <script>
            document.cf_form.cf_image_auto_rotate.checked = '<?=$mw_basic[cf_image_auto_rotate]?>';
            </script>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_watermark_use] value=1>&nbsp; 워터마크 </div>
	<div class="cf_content">
            <input type="checkbox" name="cf_watermark_use" value="1"> 본문에 사용 <span class="cf_info">(원본은 변경되지 않습니다.)</span>
            <input type="checkbox" name="cf_watermark_use_thumb" value="1"> 썸네일에 사용
	    <script>
            document.cf_form.cf_watermark_use.checked = '<?=$mw_basic[cf_watermark_use]?>';
            document.cf_form.cf_watermark_use_thumb.checked = '<?=$mw_basic[cf_watermark_use_thumb]?>';
            </script>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_watermark_path] value=1>&nbsp; 워터마크 파일경로</div>
	<div class="cf_content">
            <input type="text" size="50" name="cf_watermark_path" class="ed" value="<?=$mw_basic[cf_watermark_path]?>">
            <span class="cf_info">(그누보드 bbs 디렉토리 기준 상대경로 입력 예: ../img/watermark.gif)</span>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_watermark_position] value=1>&nbsp; 워터마크 위치</div>
	<div class="cf_content">
            <select name="cf_watermark_position">
                <option value="center">가운데</option>
                <option value="left_top">왼쪽 상단</option>
                <option value="left_bottom">왼쪽 하단</option>
                <option value="right_top">오른쪽 상단</option>
                <option value="right_bottom">오른쪽 하단</option>
            </select>
	    <script>
            document.cf_form.cf_watermark_position.value = '<?=$mw_basic[cf_watermark_position]?>';
            </script>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_watermark_transparency] value=1>&nbsp; 워터마크 선명도</div>
	<div class="cf_content">
            <select name="cf_watermark_transparency">
            <? for ($i=100; $i>=0; $i=$i-10) { ?>
            <option value="<?=$i?>"><?=$i?></option>
            <? } ?> 
            </select>
            <span class="cf_info">(jpg, gif, png 지원)</span>
	    <script>
            document.cf_form.cf_watermark_transparency.value = '<?=$mw_basic[cf_watermark_transparency]?>';
            </script>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_watermark_type] value=1>&nbsp; 워터마크 타입</div>
	<div class="cf_content">
            <select name="cf_watermark_type">
            <option value=""></option>
            <option value="jpg">jpg</option>
            <option value="png">png</option>
            </select>
	    <script>
            document.cf_form.cf_watermark_type.value = '<?=$mw_basic[cf_watermark_type]?>';
            </script>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; 워터마크 재생성 </div>
	<div class="cf_content">
	    워터마크를 모두 다시 생성합니다. <span class="cf_info">(본문)</span>
	    <span id=btn_watermark_remake><input type=button class="bt" value="시작" onclick="run_watermark_remake()"></span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; 썸네일 재생성 </div>
	<div class="cf_content">
	    썸네일을 모두 다시 생성합니다. <span class="cf_info">(썸네일 워터마크 포함)</span>
	    <span id=btn_thumb_remake><input type=button class="bt" value="시작" onclick="run_thumb_remake()"></span>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_exif] value=1>&nbsp; 이미지 메타정보 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_exif value=1> 사용 <span class="cf_info">(이미지 클릭시 출력됨, 사용시 사진확대기능 off)</span>
	    <script> document.cf_form.cf_exif.checked = '<?=$mw_basic[cf_exif]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_no_img_ext] value=1>&nbsp; 이미지 새창 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_no_img_ext value=1> 사용안함 <span class="cf_info">(사진확대기능 off)</span>
	    <script> document.cf_form.cf_no_img_ext.checked = '<?=$mw_basic[cf_no_img_ext]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_img_1_noview] value=1>&nbsp; 첫이미지 출력 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_img_1_noview value=1> 출력안함 
	    <span class="cf_info">(첫번째 첨부이미지를 본문에서 출력하지 않습니다.)</span>
	    <script> document.cf_form.cf_img_1_noview.checked = '<?=$mw_basic[cf_img_1_noview]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_change_image_size] value=1>&nbsp; 이미지크기 사용자설정 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_change_image_size value=1> 사용 
            <select name="cf_change_image_size_level">
            <? for ($i=1; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
	    <span class="cf_info">(글등록시 이미지 크기를 사용자가 설정할 수 있습니다. 작게만 가능)</span>

	    <script>
            document.cf_form.cf_change_image_size.checked = '<?=$mw_basic[cf_change_image_size]?>';
            document.cf_form.cf_change_image_size_level.value = '<?=$mw_basic[cf_change_image_size_level]?>';
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_lightbox] value=1>&nbsp; 라이트박스 </div>
	<div class="cf_content">
	    <select name="cf_lightbox">
	    <? for ($i=0; $i<=10; $i++) {?>
	    <option value="<?=$i?>"><?=$i==0?'사용안함':"{$i}레벨 이상"?></option>
	    <? } ?>
	    </select>
            크기 : <input type="text" class="ed" name="cf_lightbox_x" size="3" numeric value="<?=$mw_basic['cf_lightbox_x']?>">x
            <input type="text" class="ed" name="cf_lightbox_y" size="3" numeric value="<?=$mw_basic['cf_lightbox_y']?>">
	    <script> document.cf_form.cf_lightbox.value = "<?=$mw_basic[cf_lightbox]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_thumb_jpg] value=1>&nbsp; 썸네일 확장자 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_thumb_jpg value=1> .jpg 확장자 사용
	    <span class="cf_info">(기본:확장자 없음)</span>
	    <script> document.cf_form.cf_thumb_jpg.checked = '<?=$mw_basic[cf_thumb_jpg]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_image_save_close] value=1>&nbsp; 이미지 저장방지 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_image_save_close value=1> 사용
            <span class="cf_info">(마우스 오버, 스마트폰 클릭시 저장하는 것을 방지. 100% 막는것은 불가능)</span>
	    <script> document.cf_form.cf_image_save_close.checked = '<?=$mw_basic[cf_image_save_close]?>'; </script>
	</div>
    </div>

    <?php
    if (!$mw_basic[cf_image_outline_color])
        $mw_basic[cf_image_outline_color] = "#cccccc";
    ?>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_image_outline] value=1>&nbsp; 이미지 외곽선 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_image_outline value=1> 사용
	    , 색상 : <input type="text" class="ed" size="10" name="cf_image_outline_color" value="<?=$mw_basic[cf_image_outline_color]?>"> 사용
            <span class="cf_info">(이미지 업로드시 외곽선을 자동으로 그립니다.)</span>
	    <script> document.cf_form.cf_image_outline.checked = '<?=$mw_basic[cf_image_outline]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_image_remote_save] value=1>&nbsp; 외부썸네일 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_image_remote_save value=1> 사용
            <span class="cf_info">(본문에 외부이미지 링크가 있을 경우 썸네일을 만들어 우리서버에 저장합니다.)</span>
	    <script> document.cf_form.cf_image_remote_save.checked = '<?=$mw_basic[cf_image_remote_save]?>'; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_ani_nothumb] value=1>&nbsp; 애니GIF </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_ani_nothumb value=1> 썸네일 생성안함
	    <input type=checkbox name=cf_ani_nowatermark value=1> 워터마크 생성안함
            <span class="cf_info">(움직이는 GIF 이미지는 썸네일, 워터마크등을 생성하지 않습니다.)</span>
	    <script>
            document.cf_form.cf_ani_nothumb.checked = '<?=$mw_basic[cf_ani_nothumb]?>';
            document.cf_form.cf_ani_nowatermark.checked = '<?=$mw_basic[cf_ani_nowatermark]?>';
            </script>
	</div>
    </div>

    <div class="block"></div>
</div> <!-- tabs-5-2 -->

<div id="tabs-5-5" class="tabs"> <!-- 에디터 -->

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_editor] value=1>&nbsp; 에디터 종류 (그누보드4전용)</div>
	<div class="cf_content">
	    <select name="cf_editor">
	    <option value=""></option>
	    <option value="cheditor"> CHEditor </option>
	    <option value="geditor"> GEditor </option>
	    </select>
	    <script> document.cf_form.cf_editor.value = "<?=$mw_basic[cf_editor]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_comment_editor] value=1>&nbsp; 댓글 에디터 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_comment_editor value=1> 사용
	    <span class="cf_info">(최소, 최대 댓글수 제한 기능을 사용할 수 없음)</span>
	    <script> document.cf_form.cf_comment_editor.checked = <?=$mw_basic[cf_comment_editor]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_admin_dhtml] value=1>&nbsp; 관리자 에디터 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_admin_dhtml value=1> 본문 사용 
	    <input type=checkbox name=cf_admin_dhtml_comment value=1> 댓글 사용 
	    <span class="cf_info">(관리자만 DHTML 에디터를 사용하도록 합니다.)</span>
	    <script>
            document.cf_form.cf_admin_dhtml.checked = '<?=$mw_basic[cf_admin_dhtml]?>';
            document.cf_form.cf_admin_dhtml_comment.checked = '<?=$mw_basic[cf_admin_dhtml_comment]?>';
            </script>
	</div>
    </div>

    <div class="block"></div>
</div> <!-- tabs-5-5 -->

<div id="tabs-6" class="tabs"> <!-- 접근권한 -->

    <iframe width="900" height="300" style="margin:0 0 10px 0; border:1px solid #ccc;" src="mw.board.member.php?bo_table=<?=$bo_table?>"></iframe>

    <div class="cf_item">
	<div class=cf_title> 게시판 접근권한 </div>
	<div class=cf_content>
            <select name="cf_board_member">
                <option value=""> </option>
                <option value="1"> 등록되지 않은 회원의 게시판 접근권한을 제한함 </option>
                <option value="2"> 등록한 회원의 게시판 접근권한을 제한함 </option>
            </select>
	    <script> document.cf_form.cf_board_member.value = "<?=$mw_basic[cf_board_member]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class=cf_title> 게시판 접근목록 </div>
	<div class=cf_content>
	    <input type=checkbox name=cf_board_member_list value=1> 사용
	    <span class="cf_info">(접근권한이 없어도 목록은 보여줌)</span>
	    <script> document.cf_form.cf_board_member_list.checked = "<?=$mw_basic[cf_board_member_list]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class=cf_title> 게시판 접근내용 </div>
	<div class=cf_content>
	    <input type=checkbox name=cf_board_member_view value=1> 사용
	    <span class="cf_info">(접근권한이 없어도 내용은 보여줌)</span>
	    <script> document.cf_form.cf_board_member_view.checked = "<?=$mw_basic[cf_board_member_view]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class=cf_title> 게시판 접근댓글 </div>
	<div class=cf_content>
	    <input type=checkbox name=cf_board_member_comment value=1> 사용
	    <span class="cf_info">(접근권한이 없어도 댓글를 작성함)</span>
	    <script> document.cf_form.cf_board_member_comment.checked = "<?=$mw_basic[cf_board_member_comment]?>"; </script>
	</div>
    </div>


    <div class="block"></div>
</div> <!-- tabs-6 -->

<div id="tabs-7" class="tabs"> <!-- 플러그인 -->

    <!--
    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_seo_url] value=1>&nbsp; SEO URL </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_seo_url value=1> 사용
            <span class="cf_info">(퍼머링크, 게시물 고유주소, 추가설정 필요 ⇒  <a href="http://miwit.kr/b/mw_tip-3870" target="_blank" style="text-decoration:underline;">설정방법 클릭!</a>)</span>  
	    <script> document.cf_form.cf_seo_url.checked = <?=$mw_basic[cf_seo_url]?>; </script>
	</div>
    </div>
    -->

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_umz] value=1>&nbsp; 짧은링크 </div>
	<div class="cf_content">
	    <input type=checkbox name="cf_umz" id="cf_umz" value=1> 사용 <span class="cf_info">(게시물마다 umz.kr/xxxxx 형식의 짧은 글주소 자동생성)</span>  
            <div>도메인 선택  :
                <select name="cf_umz2" id="cf_umz2">
                    <!--<option value="">umz.kr</option>-->
                    <!--<option value="mwt.so">mwt.so</option>-->
                    <option value="my">내도메인 (솔루션 구매시)</option>
                </select>
                <span id="cf_umz_custom" style="display:none">
                    도메인 : http://<input type="text" size="30" name="cf_umz_domain" id="cf_umz_domain"
                        value="<?php echo $mw_basic['cf_umz_domain']?>">
                </span>
            </div>
	    <script>
            $(document).ready(function () {
                $("#cf_umz2").change(function () {
                    if ($(this).val() == "my") {
                        $("#cf_umz_custom").css("display", "inline");
                    }
                });
                $("#cf_umz").attr("checked", <?=$mw_basic[cf_umz]?"true":"false"?>);
                $("#cf_umz2").val("<?=$mw_basic[cf_umz2]?>");
                $("#cf_umz2").change();
            });
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_shorten] value=1>&nbsp; 짧은링크-자체도메인 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_shorten value=1> 사용 <span class="cf_info">(포워딩 기능, 주소:도메인/게시판/글번호 형식, <a href="http://miwit.kr/plugin/product/pr_shorten.php" target="_blank">플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>)</span>  
	    <script> document.cf_form.cf_shorten.checked = <?=$mw_basic[cf_shorten]?>; </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_reward] value=1>&nbsp; 제휴마케팅-리워드 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_reward value=1> 사용
                <span class="cf_info">(<!--
                --><a href="http://click.linkprice.com/click.php?m=linkprice&a=A100226477&l=0000" target="_blank"><u>링크프라이스 가입</u></a>
                , <a href="http://www.ilikeclick.com" target="_blank"><u>아이라이크클릭 가입</u></a>
                , <a href="http://miwit.kr/plugin/product/pr_reward.php" target="_blank">플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>
                )</span>  
	    <script> document.cf_form.cf_reward.checked = "<?=$mw_basic[cf_reward]?>"; </script>
	</div>
    </div>

    <div class="cf_item">
        <div class="cf_title">  <input type=checkbox name=chk[cf_lucky_writing_chance] value=1>&nbsp;  럭키라이팅 </div>
	<div class="cf_content">
            <?  $mb = get_member("@lucky-writing", "mb_nick"); ?>
            이름 변경 : <input type="text" class="ed" name="cf_lucky_writing_name" size="20" value="<?=$mb[mb_nick]?>">
            <span class="cf_info">(모든 게시판에 적용됨)</span>
            <span class="cf_info">(<a href="http://miwit.kr/plugin/product/pr_lucky_writing.php" target="_blank">플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>)</span><br/>

            <?php
            if (!$mw_basic['cf_lucky_writing_ment']) {
                $mw_basic['cf_lucky_writing_ment'] = "축하드립니다. ;)<br/>";
                $mw_basic['cf_lucky_writing_ment'].= "{별명}님은 ";
                $mw_basic['cf_lucky_writing_ment'].= "{럭키}에 당첨되어 {포인트}포인트 지급되었습니다.";
            }
            if (!$mw_basic['cf_lucky_writing_comment']) {
                $mw_basic['cf_lucky_writing_comment'] = $mw_basic['cf_lucky_writing_ment'];
            }
            if (!$mw_basic['cf_lucky_writing_comment_first']) {
                $mw_basic['cf_lucky_writing_comment_first'] = "축하드립니다. ;)<br/>";
                $mw_basic['cf_lucky_writing_comment_first'].= "{별명}님께 ";
                $mw_basic['cf_lucky_writing_comment_first'].= "첫 댓글 포인트 {포인트}점 지급되었습니다.";
            }
            ?>
            본문 문구 : <input type="text" class="ed" name="cf_lucky_writing_ment" size="50" value="<?=$mw_basic[cf_lucky_writing_ment]?>">
            <span class="cf_info">(사용가능 치환자 {별명}, {럭키}, {포인트})</span><br/>
            댓글 문구 : <input type="text" class="ed" name="cf_lucky_writing_comment" size="50" value="<?=$mw_basic[cf_lucky_writing_comment]?>"><br/>
            첫댓글 문구 : <input type="text" class="ed" name="cf_lucky_writing_comment_first" size="50" value="<?=$mw_basic[cf_lucky_writing_comment_first]?>">
            <span class="cf_info">(사용가능 치환자 {별명}, {럭키}, {포인트})</span><br/>

            본문 확률 : <input type="text" class="ed" name="cf_lucky_writing_chance" size="4" numeric value="<?=$mw_basic[cf_lucky_writing_chance]?>"> %, 포인트
            <input type="text" class="ed" name="cf_lucky_writing_point_start" size="4" numeric value="<?=$mw_basic[cf_lucky_writing_point_start]?>"> 이상 ~
            <input type="text" class="ed" name="cf_lucky_writing_point_end" size="4" numeric value="<?=$mw_basic[cf_lucky_writing_point_end]?>"> 이하.<br/>

            댓글 확률 : <input type="text" class="ed" name="cf_lucky_writing_comment_chance" size="4" numeric value="<?=$mw_basic[cf_lucky_writing_comment_chance]?>"> %, 포인트
            <input type="text" class="ed" name="cf_lucky_writing_comment_point_start" size="4" numeric value="<?=$mw_basic[cf_lucky_writing_comment_point_start]?>"> 이상 ~
            <input type="text" class="ed" name="cf_lucky_writing_comment_point_end" size="4" numeric value="<?=$mw_basic[cf_lucky_writing_comment_point_end]?>"> 이하.
            <br/>
            첫댓글 확률 : <input type="text" class="ed" name="cf_lucky_writing_comment_first_chance" size="4" numeric value="<?=$mw_basic[cf_lucky_writing_comment_first_chance]?>"> %, 포인트
            <input type="text" class="ed" name="cf_lucky_writing_comment_first_point_start" size="4" numeric value="<?=$mw_basic[cf_lucky_writing_comment_first_point_start]?>"> 이상 ~
            <input type="text" class="ed" name="cf_lucky_writing_comment_first_point_end" size="4" numeric value="<?=$mw_basic[cf_lucky_writing_comment_first_point_end]?>"> 이하.
            <br/>
            <input type="checkbox" name="cf_lucky_writing_no_admin" value="1"> 관리자 당첨 제외
	    <script> document.cf_form.cf_lucky_writing_no_admin.checked = "<?=$mw_basic['cf_lucky_writing_no_admin']?>"; </script>
        </div>
    </div>

    <div class="cf_item">
        <div class="cf_title">  <input type=checkbox name=chk[cf_social_commerce] value=1>&nbsp;  소셜커머스 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_social_commerce value=1> 사용
                <span class="cf_info">(<a href="http://miwit.kr/plugin/product/pr_social_commerce.php" target="_blank">플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>)</span>  
	    <br/><input type=checkbox name=cf_social_commerce_hp value=1> 주문문자 사용
                <span class="cf_info">(알림탭의 글등록 알림문자 옵션에서 ICODEKOREA 계정정보 입력시 사용가능)</span>

            <br/>진행시작일수 : <input type="text" class="ed" name="cf_social_commerce_begin" size="3" numeric value="<?=$mw_basic[cf_social_commerce_begin]?>"> 일 이내
            <br/>진행최대일수 : <input type="text" class="ed" name="cf_social_commerce_limit" size="3" numeric value="<?=$mw_basic[cf_social_commerce_limit]?>"> 일 까지
            <div><a href="#;" onclick="window.open('<?=$social_commerce_path?>/terms_write.php?bo_table=<?=$bo_table?>', 'terms', 'width=800,height=600,scrollbars=1')" style="text-decoration:underline;">이용약관 및 개인정보 제3자제공 동의 편집</a></div>
	    <script>
            document.cf_form.cf_social_commerce.checked = "<?=$mw_basic[cf_social_commerce]?>";
            document.cf_form.cf_social_commerce_hp.checked = "<?=$mw_basic[cf_social_commerce_hp]?>";
            </script>
        </div>
    </div>

    <div class="cf_item">
        <div class="cf_title">  <input type=checkbox name=chk[cf_quiz] value=1>&nbsp;  올림픽 퀴즈 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_quiz value=1> 사용
            <span class="cf_info">
                (<a href="http://miwit.kr/plugin/product/pr_quiz.php" target="_blank">플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>)</span>  
            <div> 퀴즈등록 가능레벨 
                <select name=cf_quiz_level>
                <? for ($i=2; $i<=10; $i++) {?>
                <option value="<?=$i?>"><?=$i?></option>
                <? } ?>
                </select>
                <span class="cf_info">(퀴즈를 등록할 수 있는 레벨)</span>
            </div>

            <div> 퀴즈참여 가능레벨
                <select name=cf_quiz_join_level>
                <? for ($i=2; $i<=10; $i++) {?>
                <option value="<?=$i?>"><?=$i?></option>
                <? } ?>
                </select>
                <span class="cf_info">(퀴즈에 참여할 수 있는 레벨)</span>
            </div>
            <script>
                document.cf_form.cf_quiz.checked = "<?=$mw_basic[cf_quiz]?>";
                document.cf_form.cf_quiz_level.value = "<?=$mw_basic[cf_quiz_level]?>";
                document.cf_form.cf_quiz_join_level.value = "<?=$mw_basic[cf_quiz_join_level]?>";
            </script>
        </div>
    </div>

    <div class="cf_item">
        <div class="cf_title">  <input type=checkbox name=chk[cf_collect] value=1>&nbsp; 수집기 </div>
	<div class="cf_content">
            <select name="cf_collect" id="cf_collect">
                <option value="">사용안함</option>
                <option value="rss">RSS 수집기 <? if (is_file("$rss_collect_path/_lib.php")) echo '(설치됨)'; else echo '(설치안됨)'; ?></option>
                <option value="youtube">Youtube 수집기 <? if (is_file("$youtube_collect_path/_lib.php")) echo '(설치됨)'; else echo '(설치안됨)'; ?></option>
                <option value="kakao">카카오스토리 수집기 <? if (is_file("$kakao_collect_path/_lib.php")) echo '(설치됨)'; else echo '(설치안됨)'; ?></option>
                <option value="instagram">인스타그램 수집기 <? if (is_file("$instagram_collect_path/_lib.php")) echo '(설치됨)'; else echo '(설치안됨)'; ?></option>
            </select>
            <span class="cf_info" id="cf_collect_info"></span>  
            <script>
            $("#cf_collect").change(func_collect_change);
            $(document).ready(func_collect_change);
            function func_collect_change () {
                if ($("#cf_collect").val() == "rss") {
                    $("#cf_collect_info").html('(<a href="http://mwt.so/0C5wZ" target="_blank">RSS 수집기 플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>)');
                }
                else if ($("#cf_collect").val() == "youtube") {
                    $("#cf_collect_info").html('(<a href="http://mwt.so/0FBEw" target="_blank">Youtube 수집기 플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>)');
                }
            }
            document.cf_form.cf_collect.value = "<?=$mw_basic[cf_collect]?>";
            </script>
        </div>
    </div>

    <div class="cf_item">
        <div class="cf_title">  <input type=checkbox name=chk[cf_collect] value=1>&nbsp; 재능마켓 </div>
	<div class="cf_content">
            <? if ($mw_basic[cf_talent_market] == '1') $mw_basic[cf_talent_market] = 'c'; ?>
            <select name="cf_talent_market">
                <option value=""> 미사용 </option>
                <option value="c"> 사이버 캐쉬 결제 (<?echo $mw_cash[cf_cash_name]?$mw_cash[cf_cash_name]:'컨텐츠샵 미설치'?>) </option>
                <option value="p"> 포인트 결제</option>
            </select>
            <span class="cf_info">
                (<a href="http://miwit.kr/plugin/product/pr_talent_market.php" target="_blank">플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>)</span>  
            <div> 관리자 커미션 : <input type="text" class="ed" size="5" name="cf_talent_market_commission" value="<?=$mw_basic[cf_talent_market_commission]?>">% </div>
            <div>
                최저금액 : <?=$mw_cash[cf_cash_name]?>
                <input type="text" class="ed" name="cf_talent_market_min" size="5" numeric value="<?=$mw_basic[cf_talent_market_min]?>">
                <?=$mw_cash[cf_cash_unit]?>,
                또는 
                <input type="text" class="ed" name="cf_talent_market_min_point" size="5" numeric value="<?=$mw_basic[cf_talent_market_min_point]?>">
                포인트
            </div>
            <div>
                최대금액 : <?=$mw_cash[cf_cash_name]?>
                <input type="text" class="ed" name="cf_talent_market_max" size="5" numeric value="<?=$mw_basic[cf_talent_market_max]?>">
                <?=$mw_cash[cf_cash_unit]?>,
                또는 
                <input type="text" class="ed" name="cf_talent_market_max_point" size="5" numeric value="<?=$mw_basic[cf_talent_market_max_point]?>">
                포인트
            </div>
            <div> 자동구매결정 : 발송 후 <input type="text" class="ed" size="2" name="cf_talent_market_auto" value="<?=$mw_basic[cf_talent_market_auto]?>">일 </div>

            <div>
                <input type="checkbox" name="cf_talent_market_app" value="1"> 관리자 승인 후 판매개시
                <br/><input type=checkbox name=cf_talent_market_hp value=1> 주문문자 사용
                <span class="cf_info">(알림탭의 글등록 알림문자 옵션에서 ICODEKOREA 계정정보 입력시 사용가능)</span>
                <script> document.cf_form.cf_talent_market_app.checked = "<?=$mw_basic[cf_talent_market_app]?>"; </script>
                <script> document.cf_form.cf_talent_market_hp.checked = "<?=$mw_basic[cf_talent_market_hp]?>"; </script>
            </div>
            <script> document.cf_form.cf_talent_market.value = "<?=$mw_basic[cf_talent_market]?>"; </script>
            <div><a href="#;" onclick="window.open('<?=$talent_market_path?>/terms_write.php?bo_table=<?=$bo_table?>', 'terms', 'width=800,height=600,scrollbars=1')" style="text-decoration:underline;">이용약관 및 개인정보 제3자제공 동의 편집</a></div>
        </div>
    </div>

    <div class="cf_item">
        <div class="cf_title">  <input type=checkbox name=chk[cf_marketdb] value=1>&nbsp; 마케팅DB </div>
	<div class="cf_content">
	    <!--<input type=checkbox name=cf_marketdb value=1> 사용-->
            <select name="cf_marketdb">
            <option value="0"> 사용안함 </option>
            <? for ($i=2; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> 레벨 이상 </option>
            <? } ?>
            </select>

                <span class="cf_info">(<a href="http://miwit.kr/plugin/product/pr_marketdb.php" target="_blank">플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>)</span>  
	    <br/><input type=checkbox name=cf_marketdb_hp value=1> 문자 사용
                <span class="cf_info">(알림탭의 글등록 알림문자 옵션에서 ICODEKOREA 계정정보 입력시 사용가능)</span>
	    <script>
            document.cf_form.cf_marketdb.value = "<?=$mw_basic[cf_marketdb]?>";
            document.cf_form.cf_marketdb_hp.checked = "<?=$mw_basic[cf_marketdb_hp]?>";
            </script>
        </div>
    </div>

    <div class="cf_item">
        <div class="cf_title">  <input type=checkbox name=chk[cf_exam] value=1>&nbsp; 시험문제 </div>
	<div class="cf_content">
	    <input type=checkbox name=cf_exam value=1> 사용
            <span class="cf_info">
                (<a href="http://miwit.kr/plugin/product/pr_exam.php" target="_blank">플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>)</span>  
            <div> 시험문제등록 가능레벨 
                <select name=cf_exam_level>
                <? for ($i=2; $i<=10; $i++) {?>
                <option value="<?=$i?>"><?=$i?></option>
                <? } ?>
                </select>
                <span class="cf_info">(시험문제를 등록할 수 있는 레벨)</span>
            </div>
	    <input type=checkbox name=cf_exam_notice value=1> 공지에 있는 시험문제를 모두 치뤄야 글 등록 가능
	    <input type=checkbox name=cf_exam_download value=1> 공지에 있는 시험문제를 모두 치뤄야 다운로드 가능

            <!--<div> 시험문제참여 가능레벨
                <select name=cf_exam_join_level>
                <? for ($i=2; $i<=10; $i++) {?>
                <option value="<?=$i?>"><?=$i?></option>
                <? } ?>
                </select>
                <span class="cf_info">(시험문제에 참여할 수 있는 레벨)</span>
            </div>-->
            <script>
            document.cf_form.cf_exam.checked = "<?=$mw_basic[cf_exam]?>";
            document.cf_form.cf_exam_level.value = "<?=$mw_basic[cf_exam_level]?>";
            document.cf_form.cf_exam_notice.checked = "<?=$mw_basic[cf_exam_notice]?>";
            //document.cf_form.cf_exam_join_notice.value = "<?=$mw_basic[cf_exam_join_notice]?>";
            </script>
        </div>
    </div>

    <div class="cf_item">
        <div class="cf_title">  <input type=checkbox name=chk[cf_bbs_banner] value=1>&nbsp; 애드보드</div>
	<div class="cf_content">
	    <input type=checkbox name=cf_bbs_banner value=1> 사용

            <input type="button" value="설정" class="bt" onclick="open_bbs_banner()">
            <span class="cf_info"> (<a href="http://miwit.kr/plugin/product/pr_bbs_banner.php" target="_blank">플러그인 설치 후 사용가능 ⇒ <u>다운로드 클릭!</u></a>)</span>  
            <div>
                <input type="checkbox" name="cf_bbs_banner_list" value="1"> 목록
                <input type="checkbox" name="cf_bbs_banner_view" value="1"> 읽기
                <input type="checkbox" name="cf_bbs_banner_write" value="1"> 쓰기 
            </div>

            <script>
            document.cf_form.cf_bbs_banner.checked = "<?=$mw_basic[cf_bbs_banner]?>";
            document.cf_form.cf_bbs_banner_list.checked = '<? echo strstr($mw_basic[cf_bbs_banner_page], '/l/')?'1':''; ?>';
            document.cf_form.cf_bbs_banner_view.checked = '<? echo strstr($mw_basic[cf_bbs_banner_page], '/v/')?'1':''; ?>';
            document.cf_form.cf_bbs_banner_write.checked = '<? echo strstr($mw_basic[cf_bbs_banner_page], '/w/')?'1':''; ?>';
            function open_bbs_banner() {
                var bbs_banner = window.open("<?php echo $bbs_banner_path?>/list.php?bo_table=<?php echo $bo_table?>", "bbs_banner", "width=800,height=600,scrollbars=yes");
                bbs_banner.focus();
            }
            </script>
        </div>
    </div>

    <div class="block"></div>
</div> <!-- tabs-7 -->

<div id="tabs-8" class="tabs"> <!-- 컨텐츠샵 -->

    <div class="cf_item" style="font-weight:bold; text-align:center; background-color:#fff; border:1px solid #ddd;
        padding:20px 0 20px 0; margin:0 0 10px 0; color:#ff0000;">
        <? if ($mw_cash[cf_cash_name]) { ?>
        <div style="color:#444;">컨텐츠샵이 설치되어 있는 것으로 추정됩니다. 캐쉬이름은 [<?=$mw_cash[cf_cash_name]?>] 입니다.</div>
        <? } else {?>
        컨텐츠샵이 설치되어 있지 않습니다.<br/><br/> 컨텐츠샵은
        배추 패밀리 회원만 이용하실 수 있습니다.  ⇒ <a href="http://miwit.kr/" target="_blank"><u>가입하기</u></a>
        <? } ?>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; <strong>멤버쉽</strong> </div>
	<div class="cf_content">
            <input type=checkbox disabled> 
            <a href="<?php echo $cash_admin_path?>/mw.membership.php"
                target="_blank" style="text-decoration:underline">관리자 - 컨텐츠샵 - 멤버쉽 설정</a> 메뉴에서 설정해주세요.
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_contents_shop] value=1>&nbsp; 컨텐츠샵 방식<strong></strong> </div>
	<div class="cf_content">
            <select name="cf_contents_shop" onchange="change_contents_shop(this.value)">
                <option value="">사용안함</option>
                <option value="1">다운로드 결제</option>
                <option value="2">내용보기 결제</option>
            </select>
            <div id="cf_contents_shop_download_option" style="display:none;">
                <input type=checkbox disabled> 
                다운로드 <input type="text" size="3" name="cf_contents_shop_download_count"
                    class="ed" value="<?=$mw_basic[cf_contents_shop_download_count]?>"> 회,
                기간 <input type="text" size="3" name="cf_contents_shop_download_day"
                    class="ed" value="<?=$mw_basic[cf_contents_shop_download_day]?>"> 일 제한
                <div class="cf_info">&nbsp;&nbsp;▶ 0 으로 설정하면 제한하지 않습니다.</div>
                <div class="cf_info">&nbsp;&nbsp;▶ 데이터-다운로드 로그 사용 기능을 사용해야 제한 가능합니다.</div>
            </div>
            <div>
                <input type=checkbox disabled> 
                가격제한 : 최소 <?=$mw_cash[cf_cash_name]?>
                    <input type="text" size="5" numeric name="cf_contents_shop_min"
                    value="<?=$mw_basic[cf_contents_shop_min]?>"> <?=$mw_cash[cf_cash_unit]?>~
                최대 <?=$mw_cash[cf_cash_name]?>
                    <input type="text" size="5" numeric name="cf_contents_shop_max"
                    value="<?=$mw_basic[cf_contents_shop_max]?>"> <?=$mw_cash[cf_cash_unit]?>
            </div>
            <div>
                <input type="checkbox" name="cf_contents_shop_fix" <? if ($mw_basic[cf_contents_shop_fix]) echo 'checked';?>/>
                가격 변동 금지
            </div>

            <script>
            document.cf_form.cf_contents_shop.value = '<?=$mw_basic[cf_contents_shop]?>';
            function change_contents_shop (val) {
                if (val == '1')
                    $("#cf_contents_shop_download_option").css('display','block');
                else
                    $("#cf_contents_shop_download_option").css('display','none');
            }
            $(document).ready(function() {
                change_contents_shop('<?=$mw_basic[cf_contents_shop]?>');
            });
            </script>
        </div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_contents_shop_uploader] value=1>&nbsp; <strong>업로더 수익</strong> </div>
	<div class="cf_content">
            <div>
                <input type=checkbox name=cf_contents_shop_uploader value=1> 사용 : 판매가격의
                <input type="text" size="3" name="cf_contents_shop_uploader_cash" class="ed" value="<?=$mw_basic[cf_contents_shop_uploader_cash]?>">%
                <script>
                document.cf_form.cf_contents_shop_uploader.checked = '<?=$mw_basic[cf_contents_shop_uploader]?>';
                </script>
            </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_contents_write] value=1>&nbsp; <strong>글작성시 결제</strong> </div>
	<div class="cf_content">
            <div>
                <input type=checkbox name=cf_contents_shop_write value=1> 사용 : 
                <?=$mw_cash[cf_cash_name]?>
                <input type="text" size="3" name="cf_contents_shop_write_cash" class="ed" value="<?=$mw_basic[cf_contents_shop_write_cash]?>">
                <?=$mw_cash[cf_cash_unit]?>
                <script> document.cf_form.cf_contents_shop_write.checked = '<?=$mw_basic[cf_contents_shop_write]?>'; </script>
            </div>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_not_membership_msg] value=1>&nbsp; <strong>멤버쉽 중지 대체 메시지</strong> </div>
	<div class="cf_content">
            안내 문구 :
            <input type="text" size="50" name="cf_not_membership_msg" class="ed" value="<?=$mw_basic[cf_not_membership_msg]?>"><br>
            이동 경로 :
            <input type="text" size="50" name="cf_not_membership_url" class="ed" value="<?=$mw_basic[cf_not_membership_url]?>">
	</div>
    </div>

    <?php if ($mw_cash['grade_table']) { ?>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; 등급별 권한 </div>
	<div class="cf_content" height=80>
            <input type="checkbox" name="cf_cash_grade_use" id="cf_cash_grade_use" value="1"> 사용
            <input type="button" value="등급설정" class="btn1" onclick="window.open('<?php
                echo $cash_admin_path?>/mw.grade.php')">
            <script>
            $("#cf_cash_grade_use").attr("checked", <?php echo $mw_basic['cf_cash_grade_use']?"true":"false"?>);
            </script>
            <style>
            .mw_grade { background-color:#ccc; }
            .mw_grade td { background-color:#eee; text-align:center; }
            </style>
            <table border="0" cellpadding="3" cellspacing="1" class="mw_grade">
            <tr>
                <td width="120">등급명</td>
                <td>목록</td>
                <td>읽기</td>
                <td>쓰기</td>
                <td>댓글</td>
            </tr>
            <?php
            $sql = " select * from {$mw['cash_grade_table']} where bo_table = '{$bo_table}' and gd_id = '0' ";
            $ro2 = sql_fetch($sql, false);
            if (!$ro2) {
                sql_query("insert into {$mw['cash_grade_table']} set bo_table = '{$bo_table}', gd_id = '0'  ", false);
                $ro2 = sql_fetch($sql, false);
            }
            ?>
                <tr>
                    <td>일반</td>
                    <td>
                        <input type="checkbox" name="gd_list_<?php echo $row['gd_id']?>"
                            id="gd_list_<?php echo $row['gd_id']?>" value="1">
                        <script> $("#gd_list_<?php echo $row['gd_id']?>").attr("checked", <?php
                            echo $ro2['gd_list']?"true":"false"?>); </script>
                    </td>
                    <td>
                        <input type="checkbox" name="gd_read_<?php echo $row['gd_id']?>"
                            id="gd_read_<?php echo $row['gd_id']?>" value="1">
                        <script> $("#gd_read_<?php echo $row['gd_id']?>").attr("checked", <?php
                            echo $ro2['gd_read']?"true":"false"?>); </script>
                    </td>
                    <td>
                        <input type="checkbox" name="gd_write_<?php echo $row['gd_id']?>"
                            id="gd_write_<?php echo $row['gd_id']?>" value="1">
                        <script> $("#gd_write_<?php echo $row['gd_id']?>").attr("checked", <?php
                            echo $ro2['gd_write']?"true":"false"?>); </script>
                    </td>
                    <td>
                        <input type="checkbox" name="gd_comment_<?php echo $row['gd_id']?>"
                            id="gd_comment_<?php echo $row['gd_id']?>" value="1">
                        <script> $("#gd_comment_<?php echo $row['gd_id']?>").attr("checked", <?php
                            echo $ro2['gd_comment']?"true":"false"?>); </script>
                    </td>
                </tr>
            <?php
            $sql = " select * from {$mw_cash['grade_table']} where gd_use = '1' order by gd_cash ";
            $qry = sql_query($sql, false);
            while ($row = sql_fetch_array($qry))
            {
                $sql = " select * from {$mw['cash_grade_table']} where bo_table = '{$bo_table}' and gd_id = '{$row['gd_id']}' ";
                $ro2 = sql_fetch($sql, false);
                if (!$ro2) {
                    sql_query("insert into {$mw['cash_grade_table']} set bo_table = '{$bo_table}', gd_id = '{$row['gd_id']}'  ", false);
                    $ro2 = sql_fetch($sql, false);
                }
            ?>
                <tr>
                    <td><?php echo $row['gd_name']?></td>
                    <td>
                        <input type="checkbox" name="gd_list_<?php echo $row['gd_id']?>"
                            id="gd_list_<?php echo $row['gd_id']?>" value="1">
                        <script> $("#gd_list_<?php echo $row['gd_id']?>").attr("checked", <?php
                            echo $ro2['gd_list']?"true":"false"?>); </script>
                    </td>
                    <td>
                        <input type="checkbox" name="gd_read_<?php echo $row['gd_id']?>"
                            id="gd_read_<?php echo $row['gd_id']?>" value="1">
                        <script> $("#gd_read_<?php echo $row['gd_id']?>").attr("checked", <?php
                            echo $ro2['gd_read']?"true":"false"?>); </script>
                    </td>
                    <td>
                        <input type="checkbox" name="gd_write_<?php echo $row['gd_id']?>"
                            id="gd_write_<?php echo $row['gd_id']?>" value="1">
                        <script> $("#gd_write_<?php echo $row['gd_id']?>").attr("checked", <?php
                            echo $ro2['gd_write']?"true":"false"?>); </script>
                    </td>
                    <td>
                        <input type="checkbox" name="gd_comment_<?php echo $row['gd_id']?>"
                            id="gd_comment_<?php echo $row['gd_id']?>" value="1">
                        <script> $("#gd_comment_<?php echo $row['gd_id']?>").attr("checked", <?php
                            echo $ro2['gd_comment']?"true":"false"?>); </script>
                    </td>
                </tr>
                <?php
            }
            ?>
            </table>
	</div>
    </div>
    <?php } ?>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox disabled>&nbsp; 분류별 구매 </div>
	<div class="cf_content" height=80>
            <label><input type="checkbox" name="cf_contents_shop_category" value="1">분류별 구매 사용</label>
            <script>$("input[name=cf_contents_shop_category]").prop("checked", "<?php echo $mw_basic['cf_contents_shop_category']?>");</script>
            <table border="0" cellpadding="3" cellspacing="1" class="mw_category">
            <tr>
                <td width="120">분류명</td>
                <td><?php echo $mw_cash['cf_cash_name']?></td>
                <td>사용</td>
            </tr>
            <?php
            $ca_list = array_filter(explode("|", $board['bo_category_list']), "trim");
            $db_list = implode("','", $ca_list);

            foreach ((array)$ca_list as $ca_name) {
                $row = mw_category_info($ca_name);
                ?>
                <tr>
                    <td><?php echo $ca_name?></td>
                    <td>
                        <input type="text" size="10" maxlength="7" id="ca_cash_<?php echo $row['ca_id']?>" name="ca_cash_<?php echo $row['ca_id']?>" value="<?php echo $row['ca_cash']?>">
                    </td>
                    <td>
                        <input type="checkbox" name="ca_cash_use_<?php echo $row['ca_id']?>" id="ca_cash_use_<?php echo $row['ca_id']?>" value="1">
                        <script>$("#ca_cash_use_<?php echo $row['ca_id']?>").prop("checked", "<?php echo $row['ca_cash_use']?>");</script>
                    </td>
                </tr>
                <?php
            }
            ?>
            </table>
	</div>
    </div>


    <div class="block"></div>
</div> <!-- tabs-8 -->

<div id="tabs-8-5" class="tabs"> <!-- 실명인증 -->

    <div class="cf_item" style="font-weight:bold; background-color:#fff; border:1px solid #ddd;
        padding:20px 0 20px 20px; margin:0 0 10px 0; color:#ff0000; line-height:20px;">
        i-PIN 인증은 신용평가 전문기관인 <a href="http://click.linkprice.com/click.php?m=allcredit&a=A100226477&l=0000"
        target="_blank">KCB</a> 를 통해 이루어집니다.<br/>
        계약 후 이용 가능합니다.
        [<a href="http://miwit.kr/b/g4_notice-1024" target="_blank">계약안내</a>]
    </div>

    <?
    $p32 = substr(sprintf('%o', fileperms("../mw.okname/okname")), -4);
    $p64 = substr(sprintf('%o', fileperms("../mw.okname/okname64")), -4);

    if (!($p32 == $p64 && $p64 == '0755')) {
    ?>
    <div class="cf_item" style="font-weight:bold; background-color:#fff; border:1px solid #ddd;
        padding:20px 0 20px 20px; margin:0 0 10px 0; color:#ff0000; line-height:20px;">
            아래 두 파일의 권한(permission)을 755 로 변경해주세요.
            <a href="http://miwit.kr/b/mw_tip-726" target="_blank">[변경방법 자세히보기]</a>
            <br/>
            skin/board/<?=$board[bo_skin]?>/mw.okname/okname<br/>
            skin/board/<?=$board[bo_skin]?>/mw.okname/okname64
    </div>
    <?} ?>

    <?
    $pkey = substr(sprintf('%o', @fileperms("../mw.okname/key")), -4);
    if ($pkey != '0707') {
    ?>
    <div class="cf_item" style="font-weight:bold; background-color:#fff; border:1px solid #ddd;
        padding:20px 0 20px 20px; margin:0 0 10px 0; color:#ff0000; line-height:20px;">
            아래 디렉토리 권한(permission)을 707 로 변경해주세요.
            <a href="http://miwit.kr/b/mw_tip-726" target="_blank">[변경방법 자세히보기]</a>
            <br/>
            skin/board/<?=$board[bo_skin]?>/mw.okname/key
    </div>
    <?} ?>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_kcb_type] value=1>&nbsp; <strong>인증종류</strong> </div>
	<div class="cf_content">
            <select name="cf_kcb_type">
            <option value=""> 사용안함 </option>
            <option value="okname"> i-PIN 인증 </option>
            <option value="19ban"> 성인인증 (19금) </option>
            </select>
	    <script>
            document.cf_form.cf_kcb_type.value = "<?=$mw_basic[cf_kcb_type]?>";
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_kcb_id] value=1>&nbsp; <strong>KCB 아이디</strong> </div>
	<div class="cf_content">
            <input type="text" size="15" maxlength="12" name="cf_kcb_id" class="ed" value="<?=$mw_basic[cf_kcb_id]?>">
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_kcb_list] value=1>&nbsp; <strong>적용</strong> </div>
	<div class="cf_content">
            <input type="checkbox" name="cf_kcb_list" value="1"> 목록 
            <input type="checkbox" name="cf_kcb_read" value="1"> 읽기 
            <input type="checkbox" name="cf_kcb_write" value="1"> 쓰기 
            <input type="checkbox" name="cf_kcb_comment" value="1"> 댓글
            <script>
            document.cf_form.cf_kcb_list.checked = '<?=$mw_basic[cf_kcb_list]?>';
            document.cf_form.cf_kcb_read.checked = '<?=$mw_basic[cf_kcb_read]?>';
            document.cf_form.cf_kcb_write.checked = '<?=$mw_basic[cf_kcb_write]?>';
            document.cf_form.cf_kcb_comment.checked = '<?=$mw_basic[cf_kcb_comment]?>';
            </script>
	</div>
    </div>

    <div class="cf_item">
	<div class="cf_title"> <input type=checkbox name=chk[cf_kcb_post] value=1>&nbsp; <strong>게시물별 설정</strong> </div>
	<div class="cf_content">
            <input type="checkbox" name="cf_kcb_post" value="1"> 사용,
            설정권한
            <select name="cf_kcb_post_level">
            <? for ($i=2; $i<=10; $i++) { ?>
            <option value="<?=$i?>"> <?=$i?> </option>
            <? } ?>
            </select> 레벨 이상
            <span class="cf_info">(실명인증을 게시물별로 설정할 수 있습니다.)</span>
	    <script>
            document.cf_form.cf_kcb_post.checked = "<?=$mw_basic[cf_kcb_post]?>";
            document.cf_form.cf_kcb_post_level.value = "<?=$mw_basic[cf_kcb_post_level]?>";
            </script>
	</div>
    </div>

    <div class="block"></div>
</div> <!-- tabs-8 -->


<div id="tabs-9" class="tabs"> <!-- 통계 -->

    <iframe width="720" height="300" style="margin:0 0 10px 0; border:1px solid #ccc;" src="mw.stats.php?bo_table=<?php echo $bo_table?>&time=<?php echo time()?>"></iframe>

</div> <!-- tabs-9 -->

<div id="tabs-10" class="tabs"> <!-- 버전확인 -->

    <div style="font-weight:bold; font-size:15px; margin:0 0 5px 0;"> -버전확인 </div>
    <div><textarea cols="130" rows="10" readonly><?php echo mw_read_file("../HISTORY")?></textarea></div>

    <div style="font-weight:bold; font-size:15px; margin:10px 0 5px 0;"> - 라이센스 </div>
    <div><textarea cols="130" rows="10" readonly><?php echo mw_read_file("../LICENSE")?></textarea></div>

</div> <!-- tabs-10 -->


</div> <!-- tabs -->

<p align=center>
    선택
    <select name="range">
        <option value=""></option>
        <option value="group">그룹적용</option>
        <option value="all">전체적용</option>
    </select>

    <input type=button class="bt" value="확     인" onclick="fsend()">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type=button class="bt" value="닫     기" onclick="self.close();">
</p>

</form>

<br/>
<br/>

</td></tr></table>

</div> <!-- load -->

<script type="text/javascript" src="<?=$g4['path']?>/js/wrest.js"></script>

<!-- 새창 대신 사용하는 iframe -->
<iframe width=0 height=0 name='hiddenframe' style='display:none;'></iframe>

</body>
</html>

