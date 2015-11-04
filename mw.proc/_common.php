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

if (defined('G5_PATH'))
    header("Content-Type: text/html; charset=utf-8");
else
    header("Content-Type: text/html; charset=".$g4['charset']);

if (defined("G5_PATH"))
    include_once("../mw.proc/mw.g5.adapter.extend.php");


