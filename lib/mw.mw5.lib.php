<?php
if (!defined("_GNUBOARD_")) exit;

include_once(G5_LIB_PATH.'/mw.permalink.lib.php');
include_once(G5_LIB_PATH.'/mw.common.lib.php');
include_once(G5_LIB_PATH.'/mw.popular.lib.php');
include_once(G5_LIB_PATH.'/mw.widget.lib.php');
include_once(G5_LIB_PATH.'/mw.host.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

// www 로만 접속
if ($mw['config']['cf_www']) mw_sub_domain_only("www");

if ($mw['config']['cf_seo_url'] && $bo_table && !mw_seo_except($bo_table)) {
    if (strstr($_SERVER['REQUEST_URI'], $g4['bbs'].'/board.php')) {
        $seo_etc = $qstr;
        $seo_etc.= mw_seo_query();
        //if ($cwin) $seo_etc .= '&cwin='.$cwin;

        if ($write['wr_is_comment']) {
            $wr_id = $write['wr_parent'];
            $seo_etc = '#c_'.$write['wr_id'];
        }

        goto_url2(mw_builder_seo_url($bo_table, $wr_id, $seo_etc));
    }
}

