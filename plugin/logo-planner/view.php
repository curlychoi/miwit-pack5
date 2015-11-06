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


$row = sql_fetch("select * from $mw_logo_planner[logo_table] where ls_id = '$ls_id'");
if (!$row)
    alert("로고가 존재하지 않습니다.");

if (!$row[ls_target])
    $row[ls_target] = "_self";

switch ($row[ls_target]) {
    case "_self" : $row[ls_target] = "현재창"; break;
    case "_blank" : $row[ls_target] = "새창"; break;
    case "_top" : $row[ls_target] = "프레임 무시"; break;
}
?>

<link rel="stylesheet" href="./style.css" type="text/css">
<style type="text/css">
.tt { height:30px; }
</style>

<div class="f">
    <div class="fp">
        <a href="./list.php"><?=$row[ls_title]?> 로고</a>
    </div>
    <div class="fb">
        <input type="button" class="b" value="등록" onclick="location.href='write.php'">
    </div>
</div>

<table border="0" cellpadding="5" cellspacing="1" width="100%" class="w">
<tr>
    <td width="120" class="tt"> 로고 이름 </td>
    <td> <?=$row[ls_title]?> </td>
</tr>
<tr>
    <td width="120" class="tt"> 링크 </td>
    <td> <a href="<?=$row[ls_url]?>" target="_blank"><?=$row[ls_url]?></a> </td>
</tr>
<tr>
    <td width="120" class="tt"> 링크 타겟 </td>
    <td> <?=$row[ls_target]?> </td>
</tr>
<tr>
    <td width="120" class="tt"> 우선 순위 </td>
    <td> <?=$row[ls_order]?> </td>
</tr>
<tr>
    <td width="120" class="tt"> 사용 여부 </td>
    <td> <? if ($row[ls_use]) echo "√ 사용"; else "사용안함"; ?> </td>
</tr>
<tr>
    <td width="120" class="tt"> 대체휴일제 </td>
    <td> <? if ($row[ls_lieu]) echo "√ 사용"; else "사용안함"; ?> </td>
</tr>
<tr>
    <td class="tt"> 반복 </td>
    <td>
        <?
        if ($row[ls_sdate] == "0000-00-00") $row[ls_sdate] =  '';
        if ($row[ls_edate] == "0000-00-00") $row[ls_edate] =  '';

        $arr_yoil = array ("월", "화", "수", "목", "금", "토", "일");
        $ls_date = '';

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
        echo "[$ls_repeat] $ls_date";
        ?>
    </td>
</tr>
<tr>
    <td class="tt"> 로고 파일 </td>
    <td>
        <? if ($row[ls_logo_file] && file_exists("$mw_logo_planner[logo_path]/$row[ls_logo_file]")) { ?>
        <div style="padding:5px;"><img src="<?="$mw_logo_planner[logo_path]/$row[ls_logo_file]"?>" style="border:1px solid #ddd;"></div>
        <? } ?>
    </td>
</tr>
<tr>
    <td class="tt"> 메모 </td>
    <td>
        <?=nl2br($row[ls_memo])?>
    </td>
</tr>
</table>

<div style="text-align:right; margin:10px 0 0 0;">
    <input type="button" value="수정" class="b" onclick="location.href='write.php?w=u&ls_id=<?=$row[ls_id]?>'">
    <input type="button" value="삭제" class="b" onclick="del('write_update.php?w=d&ls_id=<?=$row[ls_id]?>')">
</div>


<p align="center">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" value="목     록" class="b" onclick="location.href='list.php'">
</p>

</form>

<?
include_once("$g4[path]/tail.sub.php");
