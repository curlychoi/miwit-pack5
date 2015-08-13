<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (!defined("G5_PATH")) return;

$g4 = $g5;

$g4['path'] = '';
$dir = dirname(str_replace(G5_PATH, '', $_SERVER['SCRIPT_FILENAME']));
if ($dir == '/')
    $g4['path'] = './';
else
    for ($i=0, $m=substr_count($dir, '/'); $i<$m; ++$i) {
        $g4['path'] .= '../';
    }

$g4['table_prefix']     = G5_TABLE_PREFIX;
$g4['path']             = substr($g4['path'], 0, strlen($g4['path'])-1);

$g4['url']              = G5_URL;

$g4['skin_path']        = $g4['path'].'/'.G5_SKIN_DIR;
$g4['mobile_skin_path'] = $g4['path'].'/'.G5_MOBILE_DIR.'/'.G5_SKIN_DIR;
$g4['bbs']              = G5_BBS_DIR;
$g4['bbs_path']         = $g4['path'].'/'.G5_BBS_DIR;

$g4['server_time']      = time();
$g4['time_ymd']         = date("Y-m-d", $g4['server_time']);
$g4['time_his']         = date("H:i:s", $g4['server_time']);
$g4['time_ymdhis']      = date("Y-m-d H:i:s", $g4['server_time']);
$g4['charset']          = "utf-8";

$g4['admin_path']       = $g4['path'].'/'.G5_ADMIN_DIR;
$g4['link_count']       = G5_LINK_COUNT;

if (G5_IS_MOBILE) {
    $board_skin_path    = $g4['mobile_skin_path'].'/board/'.$board['bo_mobile_skin'];
    $member_skin_path   = $g4['mobile_skin_path'].'/member/'.$config['cf_mobile_member_skin'];
    $new_skin_path      = $g4['mobile_skin_path'].'/new/'.$config['cf_mobile_new_skin'];
    $search_skin_path   = $g4['mobile_skin_path'].'/search/'.$config['cf_mobile_search_skin'];
    $connect_skin_path  = $g4['mobile_skin_path'].'/connect/'.$config['cf_mobile_connect_skin'];
    $faq_skin_path      = $g4['mobile_skin_path'].'/faq/'.$config['cf_mobile_faq_skin'];
}
else {
    $board_skin_path    = $g4['skin_path'].'/board/'.$board['bo_skin'];
    $member_skin_path   = $g4['skin_path'].'/member/'.$config['cf_member_skin'];
    $new_skin_path      = $g4['skin_path'].'/new/'.$config['cf_new_skin'];
    $search_skin_path   = $g4['skin_path'].'/search/'.$config['cf_search_skin'];
    $connect_skin_path  = $g4['skin_path'].'/connect/'.$config['cf_connect_skin'];
    $faq_skin_path      = $g4['skin_path'].'/faq/'.$config['cf_faq_skin'];
}

$g4['bbs_img_path']     = $board_skin_path."/bbs-img";

define('_G4_ALPHAUPPER_', G5_ALPHAUPPER);
define('_G4_ALPHALOWER_', G5_ALPHALOWER);
define('_G4_ALPHABETIC_', G5_ALPHABETIC);
define('_G4_NUMERIC_', G5_NUMERIC); 
define('_G4_HANGUL_', G5_HANGUL);
define('_G4_SPACE_', G5_SPACE);
define('_G4_SPECIAL_', G5_SPECIAL);


