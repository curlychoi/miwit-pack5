<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (!$mw['config']['cf_follow']) return;

$plugin_url = G5_PLUGIN_URL.'/'.basename(dirname(__FILE__));

mw_css('/'.G5_PLUGIN_DIR.'/follow/style.css');
mw_script('/'.G5_PLUGIN_DIR.'/follow/script.js');

$cf_twitter = $mw['config']['cf_twitter'];
$cf_facebook = $mw['config']['cf_facebook'];
$cf_instagram = $mw['config']['cf_instagram'];
$cf_google = $mw['config']['cf_google'];
$cf_youtube = $mw['config']['cf_youtube'];
$cf_github = $mw['config']['cf_github'];

echo '<div id="sns-follow">'.PHP_EOL;

if ($cf_facebook)
    printf('<div class="facebook"><a href="https://www.facebook.com/%s" target="_blank"><img src="%s/svg/follow_facebook.svg"></div>', $cf_facebook, $plugin_url);

if ($cf_twitter)
    printf('<div class="twitter"><a href="https://twitter.com/%s" target="_blank"><img src="%s/svg/follow_twitter.svg"></div>', $cf_twitter, $plugin_url);

if ($cf_instagram)
    printf('<div class="instagram"><a href="https://www.instagram.com/%s/" target="_blank"><img src="%s/svg/follow_instagram.svg"></div>', $cf_instagram, $plugin_url);

if ($cf_google)
    printf('<div class="google"><a href="https://plus.google.com/+%s" target="_blank"><img src="%s/svg/follow_google.svg"></div>', $cf_google, $plugin_url);

if ($cf_youtube)
    printf('<div class="youtube"><a href="https://www.youtube.com/user/%s" target="_blank"><img src="%s/svg/follow_youtube.svg"></div>', $cf_youtube, $plugin_url);

if ($cf_github)
    printf('<div class="github"><a href="https://www.github.com/%s" target="_blank"><img src="%s/svg/follow_github.svg"></div>', $cf_github, $plugin_url);

echo '</div>';

