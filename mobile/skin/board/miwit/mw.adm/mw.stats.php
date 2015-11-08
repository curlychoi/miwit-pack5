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
//include_once("$g4[path]/head.sub.php");

function week_begin($d) {
    $t = strtotime("$d 00:00:00");
    $w = date("w", $t);
    $n = $w - 1;
    return date("Y-m-d", strtotime("-$n day", $t));
}

function week_end($d) {
    $t = strtotime("$d 00:00:00");
    $w = date("w", $t);
    $n = 7 - $w;
    return date("Y-m-d", strtotime("$n day", $t));
}

if ($is_admin != "super")
    alert("접근 권한이 없습니다.");

if (!$sdate)
    $sdate = date("Y-m-01", $g4[server_time]);

if (!$edate)
    $edate = date("Y-m-t", $g4[server_time]);

if (!$limit)
    $limit = 10;

$colspan = 3;
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
<?php if (is_file($g4['path']."/js/sideview.js")) { ?>
<script src="<?php echo $g4['path']?>/js/sideview.js"></script>
<?php } ?>
</head>
<body topmargin="0" leftmargin="0" <?=isset($g4['body_script']) ? $g4['body_script'] : "";?>>
<a name="g4_head"></a>

<script type="text/javascript">
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

    $('#sdate').datepicker({
        showOn: 'button',
        buttonImage: '<?=$board_skin_path?>/img/calendar.gif',
        buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99'
    }); 

    $('#edate').datepicker({
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

function change_date(sd, ed) {
    fsearch.sdate.value = sd;
    fsearch.edate.value = ed;
    fsearch.submit();
}
</script>

<link rel="stylesheet" href="<?=$board_skin_path?>/style.common.css" type="text/css">
<link rel="stylesheet" href="<?=$g4['path']?>/style.css" type="text/css">

<style>
body { font-size:12px; }
input.ed { height:20px; border:1px solid #9A9A9A; border-right:1px solid #D8D8D8; border-bottom:1px solid #D8D8D8; padding:0 0 0 3px; }
textarea { border:1px solid #9A9A9A; border-right:1px solid #D8D8D8; border-bottom:1px solid #D8D8D8; padding:0 0 0 3px; }
input.bt { background-color:#efefef; height:20px; cursor:pointer; font-size:11px; font-family:dotum; }

.ui-datepicker { font:12px dotum; }
.ui-datepicker select.ui-datepicker-month, 
.ui-datepicker select.ui-datepicker-year { width: 70px;}
.ui-datepicker-trigger { margin:0 0 -5px 2px; }
</style>

<div style="padding:10px; background-color:#fff;">
<form name="fsearch" method=post action="mw.stats.php">
<input type="hidden" name="bo_table" value="<?=$bo_table?>"/>
<div style="height:30px;">
    기간 : <input type="text" class="ed" size="10" name="sdate" id="sdate" value="<?=$sdate?>" required itemname="기간">
    ~
    <input type="text" class="ed" size="10" name="edate" id="edate" value="<?=$edate?>" required itemname="기간">

    <input type="button" class="bt" value="지난달"
        onclick="change_date('<?=date("Y-m-01", strtotime("-1 month", strtotime("$sdate 00:00:00")))?>',
        '<?=date("Y-m-t", strtotime("-1 month", strtotime("$sdate 00:00:00")))?>')">
    <input type="button" class="bt" value="이번달"
        onclick="change_date('<?=date("Y-m-01", $g4[server_time])?>','<?=date("Y-m-t", $g4[server_time])?>')">
    <input type="button" class="bt" value="다음달"
        onclick="change_date('<?=date("Y-m-01", strtotime("+1 month", strtotime("$sdate 00:00:00")))?>',
        '<?=date("Y-m-t", strtotime("+1 month", strtotime("$sdate 00:00:00")))?>')">
    &nbsp;

    <? $d = date("Y-m-d", strtotime("$sdate 00:00:00")-(86400*7)); ?>
    <input type="button" class="bt" value="지난주"
        onclick="change_date('<?=week_begin($d)?>','<?=week_end($d)?>')">
    <input type="button" class="bt" value="이번주"
        onclick="change_date('<?=week_begin($g4[time_ymd])?>','<?=week_end($g4[time_ymd])?>')">
    <? $d = date("Y-m-d", strtotime("$sdate 00:00:00")+(86400*7)); ?>
    <input type="button" class="bt" value="다음주"
        onclick="change_date('<?=week_begin($d)?>','<?=week_end($d)?>')">
</div>
<div style="height:30px;">
    통계 : 
    <select name="stype" required itemname="통계">
    <option value=""> </option>
    <option value="1"> 최다 글+댓글 작성 </option>
    <option value="2"> 최다 글작성 </option>
    <option value="3"> 최다 댓글작성 (본인글 포함) </option>
    <option value="4"> 최다 댓글작성 (본인글 제외) </option>
    </select>
    <script type="text/javascript"> fsearch.stype.value = "<?=$stype?>"; </script>
    순위 : <input type="text" name="limit" size="5" class="ed" required itemname="순위" value="<?=$limit?>"> 까지
    <input type="submit" class="bt" value="검색">
</div>
</form>
</div>

<table border=0 width=100% align=center cellspacing=1 bgcolor="#dddddd" style="padding:10px;">
<colgroup width=60>
<colgroup width=''>
<colgroup width=200>
<tr align=center height=30 bgcolor="#efefef" style="font-weight:bold;">
    <td> 순위 </td>
    <td> 회원ID </td>
    <td> 횟수 </td>
</tr>
<?
$sql_common = " select count(*) as cnt, mb_id, wr_hit from $write_table as w ";

$sql_where = " where wr_datetime between '$sdate 00:00:00' and '$edate 23:59:59' ";


if ($stype == '1') ; // 최다 글+댓글 작성

if ($stype == '2') // 최다 글작성
    $sql_where.= " and wr_is_comment = '' ";

if ($stype == '3') // 최다 댓글작성 (본인글 포함)
    $sql_where.= " and wr_is_comment = '1' ";

if ($stype == '4') { // 최다 댓글작성 (본인글 제외)
    $sql_where.= " and wr_parent not in (select wr_id from $write_table $sql_where and wr_is_comment = '' and mb_id = w.mb_id)  ";
    $sql_where.= " and wr_is_comment = '1' ";
}

$sql_group = " group by mb_id ";
$sql_order = " order by cnt desc ";

$sql = " $sql_common $sql_where $sql_group $sql_order limit $limit ";

if ($stype)
    $qry = sql_query($sql);

for ($i=0; $row=sql_fetch_array($qry); $i++) {
    $mb = get_member($row[mb_id], "mb_id, mb_nick, mb_homepage, mb_email");
    $name = get_sideview($mb[mb_id], $mb[mb_nick], $mb[mb_homepage], $mb[mb_email]);
?>
<tr align=center height=30 bgcolor="#ffffff">
    <td> <?=($i+1)?> </td>
    <td> <?=$name?> </td>
    <td> <?=$row[cnt]?> </td>
</tr>
<? } ?>

<? if (!$i) { ?>
<tr><td colspan=<?=$colspan?> height=100 align=center bgcolor="#ffffff">자료가 없습니다.</td></tr>
<? } ?>
</table>

<div style="height:20px;"></div>
<script type="text/javascript" src="<?=$g4['path']?>/js/wrest.js"></script>

<!-- 새창 대신 사용하는 iframe -->
<iframe width=0 height=0 name='hiddenframe' style='display:none;'></iframe>

</body>
</html>

