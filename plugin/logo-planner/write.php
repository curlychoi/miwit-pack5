<?php
/**
 * 로고 플래너 (Logo Planner for Gnuboard4)
 *
 * Copyright (c) 2011 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

include_once("_common.php");
include_once("_config.php");
include_once("_lib.php");
include_once("$g4[path]/head.sub.php");

if ($is_admin != "super")
    alert_close("최고관리자만 접근할 수 있습니다.");

if ($w == "u" && $ls_id)
{
    $row = sql_fetch("select * from $mw_logo_planner[logo_table] where ls_id = '$ls_id'");
    if (!$row)
        alert("로고가 존재하지 않습니다.");

    if (!$row[ls_target])
        $row[ls_target] = "_self";
}
?>

<link rel="stylesheet" href="./style.css" type="text/css">

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

<link rel="stylesheet" href="//code.jquery.com/ui/1.8.4/themes/base/jquery-ui.css" />
<style>
.ui-datepicker { font:12px dotum; }
.ui-datepicker select.ui-datepicker-month, 
.ui-datepicker select.ui-datepicker-year { width: 70px;}
.ui-datepicker-trigger { margin:0 0 -5px 2px; }
</style>

<script src="//code.jquery.com/jquery-1.8.3.min.js"></script>
<script src="//code.jquery.com/ui/1.8.4/jquery-ui.js"></script>
<script type="text/javascript">
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

});
</script>
<?
//==============================================================================
?>

<script type="text/javascript">

function change_repeat() {
    var v = $("#ls_repeat").val()
    if (v == 'year' || v == 'month' || v == 'none') {
        $('#ls_date').css('display', 'inline');
        $('#ls_week').css('display', 'none');

        fwrite.ls_week.disabled = true;
        fwrite.ls_sdate.disabled = false;
        fwrite.ls_edate.disabled = false;
        fwrite.ls_lunar.disabled = false;

        $('#ls_sdate').datepicker({
            showOn: 'button',
            buttonImage: '<?=$g4[path]?>/img/calendar.gif',
            buttonImageOnly: true,
            buttonText: "달력",
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            yearRange: 'c-99:c+99'
        }); 
        $('#ls_edate').datepicker({
            showOn: 'button',
            buttonImage: '<?=$g4[path]?>/img/calendar.gif',
            buttonImageOnly: true,
            buttonText: "달력",
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            yearRange: 'c-99:c+99'
        }); 
    } else if (v == 'week') {
        $('#ls_date').css('display', 'none');
        $('#ls_week').css('display', 'inline');

        fwrite.ls_week.disabled = false;
        fwrite.ls_sdate.disabled = true;
        fwrite.ls_edate.disabled = true;
        fwrite.ls_lunar.disabled = true;
    } else {
        $('#ls_date').css('display', 'none');
        $('#ls_week').css('display', 'none');

        fwrite.ls_week.disabled = true;
        fwrite.ls_sdate.disabled = true;
        fwrite.ls_edate.disabled = true;
        fwrite.ls_lunar.disabled = true;
    }
}
$(document).ready(function () {
    fwrite.ls_use.checked = "<?=$row[ls_use]?>";
    fwrite.ls_lieu.checked = "<?=$row[ls_lieu]?>";
    fwrite.ls_lunar.checked = "<?=$row[ls_lunar]?>";
    fwrite.ls_repeat.value = "<?=$row[ls_repeat]?>";
    fwrite.ls_week.value = "<?=$row[ls_week]?>";
    fwrite.ls_target.value = "<?=$row[ls_target]?>";
    change_repeat();
});
</script>

<div class="f">
    <div class="fp">
        <a href="./list.php">로고<? if ($w=="u") echo "수정"; else echo "등록"; ?></a>
    </div>
    <div class="fb">
        &nbsp;
    </div>
</div>



<form name="fwrite" method="post" action="write_update.php" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?=$w?>">
<input type="hidden" name="ls_id" value="<?=$ls_id?>">

<table border="0" cellpadding="5" cellspacing="1" width="100%" class="w">
<tr>
    <td width="120" class="tt"> 로고 이름 </td>
    <td> <input type="text" name="ls_title" size="50" value="<?=$row[ls_title]?>" class="ed" required itemname="로고 이름"> </td>
</tr>
<tr>
    <td width="120" class="tt"> 링크 </td>
    <td> <input type="text" name="ls_url" size="50" value="<?=$row[ls_url]?>" class="ed" itemname="링크"> </td>
</tr>
<tr>
    <td width="120" class="tt"> 링크 타겟 </td>
    <td>
        <select name="ls_target">
            <option value=""> </option>
            <option value="_self"> 현재창 </option>
            <option value="_blank"> 새창 </option>
            <option value="_top"> 프레임 무시 </option>
        </select>
    </td>
</tr>
<tr>
    <td width="120" class="tt"> 우선 순위 </td>
    <td>
        <input type="text" name="ls_order" size="7" value="<?=$row[ls_order]?>" class="ed" numeric itemname="우선순위">
        <span class="i"> (일정이 중복될 경우 우선순위 숫자가 높은 로고가 출력됩니다.) </span>
    </td>
</tr>
<tr>
    <td width="120" class="tt"> 사용 여부 </td>
    <td>
        <input type="checkbox" name="ls_use" value="1"> 사용
    </td>
</tr>
<tr>
    <td width="120" class="tt"> 대체휴일제 </td>
    <td>
        <input type="checkbox" name="ls_lieu" value="1"> 사용
    </td>
</tr>
<tr>
    <td class="tt"> 반복 </td>
    <td>
        <select name="ls_repeat" id="ls_repeat" onchange="change_repeat()">
            <option value=""> </option>
            <option value="main"> 기본 로고 </option>
            <option value="none"> 반복 안함 </option>
            <option value="year"> 매년 반복 </option>
            <option value="month"> 매월 반복 </option>
            <option value="week"> 매주 반복 </option>
        </select>
        <div id="ls_date" style="display:none;">
            <span class="tt">날짜</span>
            <input type="text" id="ls_sdate" name="ls_sdate" class="ed" size="12" value="<?=$row[ls_sdate]?>" itemname="시작날짜" readonly> ~
            <input type="text" id="ls_edate" name="ls_edate" class="ed" size="12" value="<?=$row[ls_edate]?>" itemname="종료날짜" readonly>
            <input type="checkbox" name="ls_lunar" value="1"> 음력
        </div>
        <div id="ls_week" style="display:none;">
            <span class="tt">요일</span>
            <select name="ls_week" itename="요일">
                <option value=""> </option>
                <option value="1"> 월 </option>
                <option value="2"> 화 </option>
                <option value="3"> 수 </option>
                <option value="4"> 목 </option>
                <option value="5"> 금 </option>
                <option value="6"> 토 </option>
                <option value="7"> 일 </option>
            </select>
        </div>
    </td>
</tr>
<tr>
    <td class="tt"> 로고 파일 </td>
    <td>
        <input type="file" name="ls_logo_file" size="20" class="ed">
        <? if ($row[ls_logo_file] && file_exists("$mw_logo_planner[logo_path]/$row[ls_logo_file]")) { ?>
        <div style="padding:5px;"><img src="<?="$mw_logo_planner[logo_path]/$row[ls_logo_file]"?>" style="border:1px solid #ddd;"></div>
        <div> <input type="checkbox" name="ls_file_del" value="1"> 삭제 </div>
        <? } ?>
    </td>
</tr>
<tr>
    <td class="tt"> 메모 </td>
    <td>
        <textarea name="ls_memo" cols="50" rows="10"><?=$row[ls_memo]?></textarea>
    </td>
</tr>
</table>

<p align="center">
    <input type="submit" value="확     인" class="b">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" value="취     소" class="b" onclick="history.back()">
</p>

</form>

<?
include_once("$g4[path]/tail.sub.php");
