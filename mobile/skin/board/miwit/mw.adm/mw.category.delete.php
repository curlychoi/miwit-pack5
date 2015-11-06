<?php
include_once("_common.php");

if (!$bo_table)
    die("데이터가 없습니다.");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if ($is_admin != "super")
    die("접근 권한이 없습니다.");

$sql = "select * from {$write_table} where ca_name = '{$ca_name}' and wr_is_comment = 0 ";
$qry = sql_query($sql);

$cnt = 0;
while ($write = sql_fetch_array($qry)) {
    mw_delete_row($board, $write);
    //echo "$row[wr_subject]\n";
    $cnt++;
}


echo "{$ca_name}분류의 게시물 {$cnt}건을 삭제했습니다.";
exit;
