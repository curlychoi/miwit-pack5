<?php
include_once("_common.php");

if (!$bo_table or !$wr_id)
    die("데이터가 없습니다.");

include_once("{$board_skin_path}/mw.lib/mw.skin.basic.lib.php");

$view = get_view($write, $board, $board_skin_path, 255);

include_once("{$board_skin_path}/view_head.skin.php");

echo str_replace("../../../../", "../", $file_viewer);

