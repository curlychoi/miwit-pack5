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

$default_charset = '';
if (preg_match("/^utf/i", $g4[charset]))
    $default_charset = "default charset=utf8;";

$sql = "create table if not exists {$mw_logo_planner['logo_table']} (
ls_id int not null auto_increment,
ls_use varchar(1) not null,
ls_repeat varchar(5) not null,
ls_order int not null,
ls_title varchar(255) not null,
ls_url varchar(255) not null,
ls_target varchar(5) not null,
ls_sdate date not null,
ls_edate date not null,
ls_week varchar(3) not null,
ls_lunar varchar(1) not null,
ls_lieu varchar(1) not null,
ls_logo_file varchar(100) not null,
ls_datetime datetime not null,
ls_memo text not null,
primary key (ls_id),
index (ls_use, ls_order, ls_repeat),
index (ls_sdate, ls_edate)
) {$default_charset} ";
sql_query($sql);

sql_query("alter table $mw_logo_planner[logo_table] add ls_memo text not null ", false);
sql_query("alter table $mw_logo_planner[logo_table] add ls_lieu varchar(1) not null ", false);

?>
<link rel="stylesheet" href="style.css" type="text/css"/>

<div class="f">
    <div class="fp">
        <a href="./list.php">로고 플래너</a>
    </div>
    <div class="fb">
        <input type="button" class="b" value="등록" onclick="location.href='write.php'">
    </div>
</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="t">
<tr>
    <td class="tt" width="150"> 제목 </td>
    <td class="tt" width="50"> 사용 </td>
    <td class="tt" width="70"> 우선순위 </td>
    <td class="tt" width="60"> 반복 </td>
    <td class="tt" width="200"> 일시 </td>
    <td class="tt" width=""> 로고 </td>
</tr>
<?
$cnt = 0;
$sql = " select * from $mw_logo_planner[logo_table] order by ls_order desc ";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry))
{
    if ($row[ls_sdate] == "0000-00-00") $row[ls_sdate] =  '';
    if ($row[ls_edate] == "0000-00-00") $row[ls_edate] =  '';

    $arr_yoil = array ("월", "화", "수", "목", "금", "토", "일");
    $ls_date = '&nbsp;';

    switch ($row[ls_repeat]) {
        case "week":
            $ls_repeat = "매주";
            $ls_date = $arr_yoil[$row[ls_week]-1];
            break;
        case "none":
            $ls_repeat = "반복안함";
            $ls_sdate = date("Y년 n월 j일", strtotime("$row[ls_sdate] 00:00:00"));
            $ls_edate = date("Y년 n월 j일", strtotime("$row[ls_edate] 00:00:00"));
            if ($ls_sdate == $ls_edate)
                $ls_date = $ls_edate;
            else
                $ls_date = "{$ls_sdate}∼{$ls_edate}";
            if ($row[ls_lunar]) $ls_date .= " (음)";
            break;
        case "month":
            $ls_repeat = "매월";
            $ls_sdate = date("j일", strtotime("$row[ls_sdate] 00:00:00"));
            $ls_edate = date("j일", strtotime("$row[ls_edate] 00:00:00"));
            if ($ls_sdate == $ls_edate)
                $ls_date = $ls_edate;
            else
                $ls_date = "{$ls_sdate}∼{$ls_edate}";
            if ($row[ls_lunar]) $ls_date .= " (음)";
            break;
        case "year":
            $ls_repeat = "매년";
            $ls_sdate = date("n월 j일", strtotime("$row[ls_sdate] 00:00:00"));
            $ls_edate = date("n월 j일", strtotime("$row[ls_edate] 00:00:00"));
            if ($ls_sdate == $ls_edate)
                $ls_date = $ls_edate;
            else
                $ls_date = "{$ls_sdate}∼{$ls_edate}";
            if ($row[ls_lunar]) $ls_date .= " (음)";
            break;
        case "main":
            $ls_repeat = "기본";
            break;
    }

    $ls_logo = '&nbsp;';
    if ($row[ls_logo_file]) {
        $logo_file = "$mw_logo_planner[logo_path]/$row[ls_logo_file]";
        if (file_exists($logo_file)) {
            $ls_logo = "<div style='margin:5px;'><img src='$logo_file' style='border:1px solid #ddd;'></div>";
        }
    }
?>
<tr>
    <td class="tl"> <a href="view.php?ls_id=<?=$row[ls_id]?>"><?=$row[ls_title]?></a> </td>
    <td class="tl"> <? if ($row[ls_use]) echo "√"; else "&nbsp;"; ?> </td>
    <td class="tl"> <?=$row[ls_order]?> </td>
    <td class="tl"> <?=$ls_repeat?> </td>
    <td class="tl"> <?=$ls_date?> </td>
    <td class="tl"> <?=$ls_logo?> </td>
</tr>
<? $cnt++; } ?>

<? if (!$cnt) { ?>
<tr>
    <td class="tn" colspan="6"> 등록된 로고가 없습니다. </td>
</tr>
<? } ?>
</table>

<?
include_once("$g4[path]/tail.sub.php");
