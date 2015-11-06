<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (!defined("G5_PATH")) return;

function get_g4_skin_path($dir, $skin)
{
    global $g4;
    global $config;

    $cf_theme = trim($config['cf_theme']);
    $theme_path = $g4['path'].'/'.G5_THEME_DIR.'/'.$cf_theme;

    if (preg_match('#^theme/(.+)$#', $skin, $match)) { 
        if (G5_IS_MOBILE) {
            $skin_path = $theme_path.'/'.G5_MOBILE_DIR.'/'.G5_SKIN_DIR.'/'.$dir.'/'.$match[1];
            if (!is_dir($skin_path))
                $skin_path = $theme_path.'/'.G5_SKIN_DIR.'/'.$dir.'/'.$match[1];
        }
        else {
            $skin_path = $theme_path.'/'.G5_SKIN_DIR.'/'.$dir.'/'.$match[1];
        }
    }
    else {
        if (G5_IS_MOBILE) {
            $skin_path = $g4['mobile_skin_path'].'/'.$dir.'/'.$skin;
        }
        else {
            $skin_path = $g4['skin_path'].'/'.$dir.'/'.$skin;
        }
    }
    return $skin_path;
}

$g4 = $g5;

$g4['path'] = '';
$script_name = substr($_SERVER['SCRIPT_FILENAME'], strpos($_SERVER['SCRIPT_FILENAME'], G5_PATH)); 
$dir = dirname(str_replace(G5_PATH, '', $script_name));
if ($dir == '/')
    $g4['path'] = './';
else
    for ($i=0, $m=substr_count($dir, '/'); $i<$m; ++$i) {
        $g4['path'] .= '../';
    }

$g4['table_prefix']     = G5_TABLE_PREFIX;
$g4['cookie_domain']    = G5_COOKIE_DOMAIN; 
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

$board_skin_path    = get_g4_skin_path('board', $board['bo_mobile_skin']);
$member_skin_path   = get_g4_skin_path('member', $config['cf_mobile_member_skin']);
$new_skin_path      = get_g4_skin_path('new', $config['cf_mobile_new_skin']);
$search_skin_path   = get_g4_skin_path('search', $config['cf_mobile_search_skin']);
$connect_skin_path  = get_g4_skin_path('connect', $config['cf_mobile_connect_skin']);
$faq_skin_path      = get_g4_skin_path('faq', $config['cf_mobile_faq_skin']);

$g4['bbs_img_path']     = $board_skin_path."/bbs-img";

define('_G4_ALPHAUPPER_', G5_ALPHAUPPER);
define('_G4_ALPHALOWER_', G5_ALPHALOWER);
define('_G4_ALPHABETIC_', G5_ALPHABETIC);
define('_G4_NUMERIC_', G5_NUMERIC); 
define('_G4_HANGUL_', G5_HANGUL);
define('_G4_SPACE_', G5_SPACE);
define('_G4_SPECIAL_', G5_SPECIAL);


