<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if (defined('G5_PATH') && @is_file(G5_EXTEND_PATH."/mw.g5.adapter.extend.php"))
    include_once(G5_EXTEND_PATH."/mw.g5.adapter.extend.php");

include_once("$g4[path]/plugin/logo-planner/_config.php");
include_once("$g4[path]/plugin/logo-planner/_lib.php");
