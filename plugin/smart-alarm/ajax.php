<?
/**
 * 스마트알람 (Smart-Alarm for Gnuboard4)
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

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

$mw_moa_path_now = str_replace($g4[path], $now_path, $mw_moa_path);

sql_query(" update $mw_moa_table set mo_flag = '1' where mb_id = '$member[mb_id]' ");

$sql_common = " from $mw_moa_table ";
$sql_order = " order by mo_datetime desc ";
$sql_search = " where mb_id = '$member[mb_id]' ";

$sql = "select *
        $sql_common
        $sql_search
        $sql_order
        limit 5 ";
$qry = sql_query($sql);

$list = array();
for ($i=0; $row = sql_fetch_array($qry); ++$i)
{
    $list[$i] = mw_moa_row($row);
}

$list_count = count($list);
?>

<ul>
    <? for ($i=0; $i<$list_count; $i++) { ?>
    <li class="moa_item" onclick="location.href='<?=$list[$i][href]?>'">
        <div class="image"><img src="<?=$list[$i][comment_image]?>"></div>
        <div class="msg"><?=$list[$i][msg]?></div>
        <div class="time"><?=$list[$i][time]?></div>
    </li>
    <? } ?>
    <li class="moa_item" onclick="location.href='<?=$mw_moa_path_now?>/'" style="text-align:center; background-color:#F7F7F7; height:35px;">
        <div style="margin:7px 0 0 0;">모든 알림 보기</div>
    </li>
</ul>

