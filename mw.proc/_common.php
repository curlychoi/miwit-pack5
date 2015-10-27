<?php
$g4_path = "../../../.."; // common.php 의 상대 경로
if (is_file("$g4_path/common.php"))
    include_once("$g4_path/common.php");
else {
    $g4_path = "../../../../..";
    include_once("$g4_path/common.php");
}

if (defined('G5_PATH'))
    header("Content-Type: text/html; charset=utf-8");
else
    header("Content-Type: text/html; charset=".$g4['charset']);

if (defined("G5_PATH"))
    include_once("../mw.proc/mw.g5.adapter.extend.php");


