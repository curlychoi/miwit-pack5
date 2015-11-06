<?php
/**
 * 소셜로그인 (Social-login for Gnuboard4)
 *
 * Copyright (c) 2012 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

// 페이스북 정보
$mw_facebook_config = array();
$mw_facebook_config['appId'] = '';
$mw_facebook_config['secret'] = '';

// 트위터 정보
$mw_twitter_config = array();
$mw_twitter_config['consumer_key'] = '';
$mw_twitter_config['consumer_secret'] = '';

// 트위터 관련 - 수정하지 마세요.
$tmp = parse_url($g4['url']);
$mw_twitter_config['oauth_callback'] = set_http($tmp['host'].dirname($_SERVER['SCRIPT_NAME'])."/twitter-login.php");

// 구글정보
$mw_google_config['client_id'] = '';
$mw_google_config['client_secret'] = '';
$mw_google_config['client_domain'] = '';

// 네이버 정보
$mw_naver_config = array();
$mw_naver_config['client_id'] = '';
$mw_naver_config['client_secret'] = '';
$mw_naver_config['callback'] = set_http($tmp['host'].dirname($_SERVER['SCRIPT_NAME'])."/naver-login.php");

// 카카오 정보
$mw_kakao_config = array();
$mw_kakao_config['client_id'] = '';
$mw_kakao_config['callback'] = set_http($tmp['host'].dirname($_SERVER['SCRIPT_NAME'])."/kakao-login.php");

