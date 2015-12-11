<?php
/**
 * 소셜로그인 (Social-login for Gnuboard)
 *
 * Copyright (c) 2012 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

function list_social_login()
{
    return array(
        '@fb'=>'페이스북',
        '@gl'=>'구글',
        '@tw'=>'트위터',
        '@nv'=>'네이버',
        '@ka'=>'카카오톡'
    );
}

function verify_social_login($mb_id=null)
{
    global $member;


    if (!$mb_id)
        $mb_id = trim($member['mb_id']);

    if (!$mb_id) return false;

    $sns = substr(strtolower($mb_id), 0, 3);
    $id = substr($mb_id, 3);

    $id = substr(preg_replace('#[^a-z0-9_]#i', '', $id), 0, 20);

    if (!array_key_exists($sns, list_social_login())) return false;

    if ("{$sns}-{$id}" == $mb_id) return true;

    return false;
}

function is_social_login($mb_id=null)
{
    global $member;

    if (!$mb_id)
        $mb_id = trim($member['mb_id']);

    if (!$mb_id) return;

    $sns = substr(strtolower($mb_id), 0, 3);

    $arr = list_social_login();
    if (array_key_exists($sns, $arr)) {
        return $arr[$sns];
    }

    return false;
}

// euckr -> utf8 
if (!function_exists("set_utf8")) {
function set_utf8($str)
{
    if (!is_utf8($str))
        $str = convert_charset('cp949', 'utf-8', $str);

    $str = trim($str);

    return $str;
}}

// utf8 -> euckr 
if (!function_exists("set_euckr")) {
function set_euckr($str)
{
    if (is_utf8($str))
        $str = convert_charset('utf-8', 'cp949', $str);

    $str = trim($str);

    return $str;
}}


// Charset 을 변환하는 함수 
if (!function_exists("convert_charset")) {
function convert_charset($from_charset, $to_charset, $str) {
    if( function_exists('iconv') )
        return iconv($from_charset, $to_charset, $str);
    elseif( function_exists('mb_convert_encoding') )
        return mb_convert_encoding($str, $to_charset, $from_charset);
    else
        die("Not found 'iconv' or 'mbstring' library in server.");
}}

// 텍스트가 utf-8 인지 검사하는 함수 
if (!function_exists("is_utf8")) {
function is_utf8($string) {

  // From http://w3.org/International/questions/qa-forms-utf-8.html
  return preg_match('%^(?:
        [\x09\x0A\x0D\x20-\x7E]            # ASCII
      | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
      |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
      | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
      |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
      |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
      | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
      |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
 )*$%xs', $string);
}}

function mw_naver_generate_state() {
        $mt = microtime();
        $rand = mt_rand();
        return md5($mt . $rand); 
}

if (!function_exists("mwp_is_social")) { 
function mwp_is_social() { 
}}
