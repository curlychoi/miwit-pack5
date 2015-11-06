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

include_once('_common.php');
require_once('_config.php');
require_once('lib/twitter/twitteroauth.php');

$connection = new TwitterOAuth($mw_twitter_config[consumer_key], $mw_twitter_config[consumer_secret]);
$request_token = $connection->getRequestToken($mw_twitter_config[oauth_callback]);

$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
 
switch ($connection->http_code) {
  case 200:
    $url = $connection->getAuthorizeURL($token);
    header('Location: ' . $url); 
    break;
  default:
    echo '트위터에 접속할 수 없습니다. 새로고침 해보세요.';
}

