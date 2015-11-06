<?php
/**
 * MW Builder for Gnuboard4
 *
 * Copyright (c) 2008 Choi Jae-Young <www.miwit.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined('_GNUBOARD_')) exit;

// 지정한 서브도메인으로만 접근이 가능하도록한다.
// config.php 에 $g4[cookie_domain] 이 설정되어 있어야만 작동한다.
// 080326, 최재영
if (!function_exists("mw_sub_domain_only")) {
function mw_sub_domain_only($sub_domain="")
{
    global $g4, $_SERVER;

    if (!$g4[cookie_domain]) return false;
    if (!$_SERVER[HTTP_HOST]) return false;

    //$diff = substr($_SERVER[HTTP_HOST], 0, strlen($sub_domain)+1);
    //if ($diff != $sub_domain.".") {

    if (mw_get_sub_domain() == 'm') return;

    if ($_SERVER[HTTP_HOST] != $sub_domain.$g4[cookie_domain]) {
        goto_url2("http://".$sub_domain.$g4[cookie_domain].$_SERVER[REQUEST_URI]);
    }
}}

// 현재 서브도메인을 얻는다.
// config.php 에 $g4[cookie_domain] 이 설정되어 있어야만 작동한다.
// 080326, 최재영
if (!function_exists("mw_get_sub_domain")) {
function mw_get_sub_domain()
{
    global $g4, $_SERVER;

    if (!$g4[cookie_domain]) return false;
    if (!$_SERVER) return false;

    $sub_domain = str_replace($g4[cookie_domain], "", $_SERVER[HTTP_HOST]);

    // 서브도메인이 없는 경우 예외 처리 .. 080328, 최재영
    if ($sub_domain == $_SERVER[HTTP_HOST]) $sub_domain = "www";

    return $sub_domain;
}}

// 서브도메인을 적용한 url 리턴
if (!function_exists("mw_sub_domain_url")) {
function mw_sub_domain_url($sub, $url) {
    global $g4;
    if (strstr($url, "www.")) {
	$url = str_replace("www.", "$sub.", $url);
    } else {
	//echo "$url\n";
	$cookie_domain = str_replace(".", "\\.", $g4[cookie_domain]);
	$pattern = "/http:\/\/(.*)$cookie_domain(.*)/i";
	$change = "http://{$sub}{$g4[cookie_domain]}\$2";
	//echo "$pattern --- $change\n";
	$url = preg_replace($pattern, $change, $url);
	//echo $url;exit;
    }
    return $url;
}}

