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

// 접근권한 목록 v.1.0.2 패치
$sql = "alter table $mw[board_member_table] drop primary key , add primary key (bo_table, mb_id)";
sql_query($sql, false);

if ($is_admin != "super")
    alert("접근 권한이 없습니다.");

$admin_menu[board_member] = "select";

$sfl = "mb_id";
$colspan = 6;

$sql_common = " from $mw[board_member_table] ";
$sql_order = " order by bm_datetime desc ";
$sql_search = " where bo_table = '$bo_table' ";

if ($sfl && $stx) {
    $tmp = sql_fetch("select mb_id from $g4[member_table] where mb_nick = '$stx' ");
    if ($tmp[mb_id])
        $stx = $tmp[mb_id];

    $sql_search .= " and mb_id like '%$stx%' ";
}

$sql = "select count(*) as cnt
        $sql_common
        $sql_search";
$row = sql_fetch($sql);
$total_count = $row[cnt];

$rows = $config[cf_write_pages];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "select *
        $sql_common
        $sql_search
        $sql_order
        limit $from_record, $rows ";
$qry = sql_query($sql);

$list = array();
for ($i=0; $row = sql_fetch_array($qry); ++$i) {
    $row[num] = $total_count - ($page - 1) * $rows - $i;

    $mb = get_member($row[mb_id], "mb_id, mb_nick, mb_homepage, mb_email");
    $row[name] = get_sideview($mb[mb_id], $mb[mb_nick], $mb[mb_homepage], $mb[mb_email]);

    if (!$row[bm_limit][0])
        $row[bm_limit] = '';

    $list[$i] = $row;
}

$write_pages = get_paging($rows, $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?bo_table={$bo_table}{$qstr}&page=");

//$g4[title] = "배추 BASIC SKIN 접근권한 설정";
//include_once("$g4[path]/head.sub.php");
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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?=$g4['path']?>/style.css" type="text/css">
<link rel="stylesheet" href="<?=$g4['path']?>/css/default.css" type="text/css">
<link rel="stylesheet" href="../style.common.css" type="text/css">
<link rel="stylesheet" href="../sideview.css" type="text/css">
<?php if (is_file($g4['path']."/js/sideview.js")) { ?>
<script src="<?php echo $g4['path']?>/js/sideview.js"></script>
<?php } ?>
<script src="<?php echo $g4['path']?>/js/common.js"></script>
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

    $('#bm_limit').datepicker({
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

</head>
<body topmargin="0" leftmargin="0" <?=isset($g4['body_script']) ? $g4['body_script'] : "";?>>
<a name="g4_head"></a>


<style>
body { font-size:12px; }
input.ed { height:20px; border:1px solid #9A9A9A; border-right:1px solid #D8D8D8; border-bottom:1px solid #D8D8D8; padding:0 0 0 3px; }
textarea { border:1px solid #9A9A9A; border-right:1px solid #D8D8D8; border-bottom:1px solid #D8D8D8; padding:0 0 0 3px; }
input.bt { background-color:#efefef; height:20px; cursor:pointer; font-size:11px; font-family:dotum; }
</style>

<div style="height:30px; background-color:#fff;">
    <form name="fwrite" id="fwrite" onsubmit="return mw_send();" style="margin:5px 0 5px 5px; float:left;">
    <input type=hidden name=bo_table value="<?=$bo_table?>">
    회원ID, 닉네임, IP : <input type=text size=15 class=ed name=mb_id required itemname="회원ID">,
    종료일 :
    <input type="text" size="10" maxlength="10" name="bm_limit" id="bm_limit" class="ed">
    <input type=submit value="등록" class="bt">
    </form>

    <form name="fsearch" method=get action="<?=$PHP_SELF?>" style="margin:5px 5px 5px 0; float:right;">
    <input type=hidden name=bo_table value="<?=$bo_table?>">
    검색 : <input type=text size=15 name=stx class=ed required itemname="검색어" value="<?=$stx?>">
    <input type=submit value="검색" class="bt">
    <input type=button value="처음" class="bt" onclick="location.href='mw.board.member.php?bo_table=<?=$bo_table?>'">
    </form>
</div>

<table border=0 width=100% align=center cellspacing=1 bgcolor="#dddddd">
<colgroup width=60>
<colgroup width=150>
<colgroup width=150>
<colgroup width=''>
<colgroup width='100'>
<colgroup width=50>
<tr align=center height=30 bgcolor="#efefef" style="font-weight:bold;">
    <td> 번호 </td>
    <td> 회원ID </td>
    <td> 회원정보 </td>
    <td> 처리일시 </td>
    <td> 종료일시 </td>
    <td> 삭제 </td>
</tr>
<? foreach ($list as $row) {?>
<tr align=center height=30 bgcolor="#ffffff">
    <td> <?=$row[num]?> </td>
    <td> <?=$row[mb_id]?> </td>
    <td> <?=$row[name]?> </td>
    <td> <?=$row[bm_datetime]?> </td>
    <td> <?=$row[bm_limit]?> </td>
    <td> <a href="javascript:mw_del('<?=$row[mb_id]?>');"><i class="fa fa-cut"></i></a> </td>
</tr>
<? } ?>
<? if (!$total_count) { ?>
<tr><td colspan=<?=$colspan?> height=100 align=center bgcolor="#ffffff">등록된 회원ID가 없습니다.</td></tr>
<? } ?>
</table>

<div style="margin:20px 0 0 0; text-align:center;"> <?=$write_pages?> </div>

<div style="height:50px;"></div>

<script type="text/javascript">
function mw_send() {
    $.post("mw.board.member.update.php", $("#fwrite").serialize(), function (str) {
        if (str) { alert(str); }
        location.reload();
    }); 
    return false;
}
function mw_del(mb_id) {
    if (confirm("정말 삭제하시겠습니까?")) {
        $.post("mw.board.member.update.php", { "w" : "d", "bo_table" : "<?=$bo_table?>", "mb_id" : mb_id }, function (str) {
            if (str) alert(str);
            location.reload();
        });
    }
}
</script>

<script type="text/javascript" src="<?=$g4['path']?>/js/wrest.js"></script>

<!-- 새창 대신 사용하는 iframe -->
<iframe width=0 height=0 name='hiddenframe' style='display:none;'></iframe>

</body>
</html>

