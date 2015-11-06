<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// 이메일등의 자동수집을 막기위해 자바스크립트로 한글자씩 잘라 출력함.
// 한글등의 2 byte 이상의 문자는 작동 안함.
function mw_nobot_slice($str) {
    $ret = "<script>";
    $ret.= "document.write(";
    for ($i=0; $i<strlen($str); $i++) {
	$ret .= "\"".substr($str, $i, 1)."\" + ";	
    }
    $ret.= "\"\")</script>";
    return $ret;
}

