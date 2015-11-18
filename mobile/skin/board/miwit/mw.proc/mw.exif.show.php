<?
include_once("_common.php");
header("Content-Type: text/html; charset=$g4[charset]");

$sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$bf_no'";
$row = sql_fetch($sql);

if (!$row) exit;

$file = "$g4[path]/data/file/$bo_table/$row[bf_file]";
$exif = @exif_read_data($file);

$em = "";
switch ($exif[ExposureMode]) {
    case "0": $em = "자동노출"; break;
    case "1": $em = "수동노출"; break;
    case "2": $em = "브라켓노출"; break;
}

$fl = explode("/", $exif[FocalLength]);
$fl = @($fl[0]/$fl[1]);

if ($fl) $fl .= " mm";

$et = "";
if ($exif[ExposureTime]) $et = $exif[ExposureTime] . " 초"; 
//print_r($exif);
?>
<table border=0 cellpadding=0 cellspacing=0>
<tr><td width="80">모델명</td><td><?=$exif[Model]?></td></tr>
<!--<tr><td>소프트웨어</td><td><?=$exif[Software]?></td></tr>-->
<tr><td>촬영일자</td><td><?=$exif[DateTimeOriginal]?></td></tr>
<tr><td>감도</td><td><?=$exif[ISOSpeedRatings]?></td></tr>
<tr><td>노출모드</td><td><?=$em?></td></tr>
<tr><td>노출시간</td><td><?=$et?></td></tr>
<!--<tr><td>노출보정</td><td><?=$exif[ExposureBiasValue]?></td></tr>-->
<tr><td>조리개값</td><td><?=$exif[COMPUTED][ApertureFNumber]?></td></tr>
<tr><td>초점거리</td><td><?=$fl?></td></tr>
</table>
