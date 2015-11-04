<?php
// common.php 의 상대 경로
$g4_path = "../../../..";
if (is_file($g4_path."/common.php")) {
    // pc 스킨
    include_once($g4_path."/common.php");
}
else {
    $g4_path = "../../../../..";
    if (is_file($g4_path."/common.php")) {
        // g5 모바일 스킨
        include_once($g4_path."/common.php");
    }
    else {
        $g4_path = "../../../../../..";
        // 배추 모바일 스킨
        include_once($g4_path."/common.php");
    }
}
