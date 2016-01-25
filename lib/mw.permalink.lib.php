<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if (@is_file($g4['path'].'/lib/mw.host.lib.php'))
    include_once($g4['path'].'/lib/mw.host.lib.php');

function mw_seo_except($bo_table)
{
    global $mw;

    if (!$bo_table) return false;

    $list = explode(',', $mw['config']['cf_seo_except']);
    $list = array_filter($list, 'trim');

    if (in_array($bo_table, $list))
        return true;

    return false;
}

function mw_seo_url($bo_table, $wr_id=0, $qstr='', $mobile=1)
{
    global $g4;
    global $mw;
    global $mw_basic;
    global $mw_mobile;
    global $is_admin;

    return mw_builder_seo_url($bo_table, $wr_id, $qstr, $mobile);
}

function mw_builder_seo_url($bo_table, $wr_id=0, $qstr='', $mobile=1)
{
    global $g4;
    global $mw;
    global $mw_basic;
    global $mw_mobile;
    global $is_admin;

    $url = G5_URL;

    if (!$mobile && $mw_mobile['m_subdomain'])
        $url = preg_replace("/^http:\/\/m\./", "http://", $url);

    if (($mobile && mw_is_mobile_builder()) or ($mobile == 2))  {
        if ($mw_mobile['m_subdomain'] && !preg_match("/^http:\/\/m\./", $url)) {
            $url = mw_sub_domain_url("m", $url);
        }
        $seo_path = '/plugin/mobile';
    }
    else
        $seo_path = '/'.G5_BBS_DIR;

    if ($bo_table)
        $url .= $seo_path.'/board.php?bo_table='.$bo_table;

    if ($wr_id)
        $url .= "&wr_id=".$wr_id;

    if ($qstr == '?') $qstr = '';


    if ($qstr)
        $url .= $qstr;

    if ($mw['config']['cf_seo_url'])
    {
        if (mw_seo_except($bo_table))
            return $url;

        $url = G5_URL;

        if (!$mobile && $mw_mobile['m_subdomain'])
            $url = preg_replace("/^http:\/\/m\./", "http://", $url);

        $seo_path = '/b/';

        if (($mobile && mw_is_mobile_builder()) or ($mobile == 2))  {
            if ($mw_mobile['m_subdomain'] && !preg_match("/^http:\/\/m\./", $url)) {
                $url = mw_sub_domain_url("m", $url);
            }
            $url.= '/m/';
            $seo_path = 'b/';
        }

        if ($bo_table)
            $url .= $seo_path.$bo_table;

        if ($wr_id)
            $url .= '-'.$wr_id;

        if ($qstr)
            $url .= '?'.$qstr;
    }


    $url = str_replace("&amp;", "&", $url);
    $url = preg_replace("/&page=0(&)/", "$1", $url);
    $url = preg_replace("/&page=0$/", '', $url);
    $url = preg_replace("/&page=1(&)/", "$1", $url);
    $url = preg_replace("/&page=1$/", '', $url);
    //$url = preg_replace("/&page=(&)/", "$1", $url);
    //$url = preg_replace("/&page=$/", '', $url);
    $url = str_replace("?&", '?', $url);
    $url = preg_replace("/\?$/", "", $url);

    return $url;
}

function mw_is_mobile_builder()
{
    $is_mobile = false;

    if (strstr($_SERVER['SCRIPT_NAME'], "/plugin/mobile/"))
        $is_mobile = true;
    else if (strstr($_SERVER['SCRIPT_NAME'], "/m/b/"))
        $is_mobile = true;

    return $is_mobile;
}


function mw_builder_seo_page($pg_id)
{
    global $g4;
    global $mw;

    $url = $g4['url'].'/page.php?pg_id='.$pg_id;

    if ($mw['config']['cf_seo_url']) {
        $url = $g4['url'].'/page_'.$pg_id;
    }

    return $url;
}


function mw_builder_seo_main($gr_id)
{
    global $g4;
    global $mw;

    $url = $g4['url'].'/?mw_main='.$gr_id;

    if ($mw['config']['cf_seo_url']) {
        $url = $g4['url'].'/main_'.$gr_id;
    }

    return $url;
}

function mw_builder_seo_sign()
{
    global $mw;

    if ($mw['config']['cf_seo_url'])
        return '?';
    else
        return '&';
}

function mw_bbs_path($path)
{
    global $g4;

    if (mw_is_mobile_builder()) {
        $path = preg_replace("/\.\//iUs", $g4['path'].'/plugin/mobile/', $path);
    }
    else {
        $path = preg_replace("/\.\//iUs", $g4['bbs_path'].'/', $path);
    }

    return $path;
}

function mw_seo_bbs_path($path)
{
    global $g4;
    global $bo_table;

    $wr_id = null;

    if (preg_match("/&wr_id=([0-9]+)&/iUs", $path, $mat)) {
        $wr_id = $mat[1];
        $path = str_replace('&wr_id='.$wr_id, '', $path);
    }

    $path = preg_replace("/&page=[01]?[&$]?/i", '', $path);

    if (mw_is_mobile_builder()) {
        $path = str_replace('../../plugin/mobile/board.php?bo_table='.$bo_table, mw_seo_url($bo_table, $wr_id).'?', $path);
    }
    else {
        $path = str_replace('../bbs/board.php?bo_table='.$bo_table, mw_seo_url($bo_table, $wr_id).'?', $path);
    }

    $path = preg_replace("/\?$/", "", $path);

    return $path;
}

function mw_seo_query()
{
    global $_SERVER;

    $ret = null;
    $rev = array('bo_table', 'wr_id', 'page', 'stx', 'sfl', 'sca', 'sst', 'sod', 'sop', 'spt');

    $qry = $_SERVER["QUERY_STRING"];
    $qry = explode("&", $qry);
    foreach ((array)$qry as $item) {
        $var = explode("=", $item);

        if (!in_array($var[0], $rev)) {
            $ret .= '&'.$var[0] . '=' . $var[1];
        }
    }
    //$ret = '&'.$ret;
    return $ret;
}

function mw_url_style($url, $type='seo', $cf_www='', $cf_seo_except='')
{
    //$cf_www = filter_var($cf_www, FILTER_VALIDATE_BOOLEAN);
    if ($cf_www == 'true')
        $cf_www = true;
    else
        $cf_www = false;

    $parse_url = parse_url($url.'&');

    $bbs = G5_BBS_DIR;

    if (G5_COOKIE_DOMAIN)
        $cookie_domain = G5_COOKIE_DOMAIN;

    if (!$cookie_domain)
        $cookie_domain = get_top_domain();

    $cookie_domain = preg_replace("/^\./", "", $cookie_domain);

    parse_str($parse_url['query'], $param);
    $except = array_filter(array_map('trim', explode(",", $cf_seo_except)), 'strlen');

    if (in_array($param['bo_table'], $except) and $type == 'seo') return $url;

    if ($cf_www) {
        if ($parse_url['host'] == $cookie_domain) {
            $parse_url['host'] =  'www.'.$parse_url['host'];
        }
    }
    else {
        $parse_url['host'] = preg_replace("/^www\./iUs", "", $parse_url['host']);
    }
    $parse_url['host'] = preg_replace("/\/\//iUs", "", $parse_url['host']);

    if ($type == 'seo') {
        $parse_url['path'] = preg_replace("/{$bbs}\/board\.php/iUs", "b/", $parse_url['path']);
        $parse_url['path'] = preg_replace("/{$bbs}\/group\.php/iUs", "g/", $parse_url['path']);
        $parse_url['path'] = preg_replace("/{$bbs}\/content\.php/iUs", "c/", $parse_url['path']);

        $c = 0;
        $parse_url['query'] = preg_replace("/bo_table=([0-9a-zA-Z-_]+)&wr_id=([0-9]+)&/iUs", "$1-$2?", $parse_url['query'], 1, $a); $c += $a;
        $parse_url['query'] = preg_replace("/bo_table=([0-9a-zA-Z-_]+)&/iUs", "$1?", $parse_url['query'], 1, $a); $c += $a;
        $parse_url['query'] = preg_replace("/gr_id=([0-9a-zA-Z-_]+)&/iUs", "$1?", $parse_url['query'], 1, $a); $c += $a;
        $parse_url['query'] = preg_replace("/co_id=([0-9a-zA-Z-_]+)&/iUs", "$1?", $parse_url['query'], 1, $a); $c += $a;

        if (!$c)
            $parse_url['query'] = '?'.$parse_url['query'];
    }
    else if ($type == 'parameter') {
        $c = 0;
        $parse_url['path'].= '&';
        $parse_url['path'] = preg_replace("/b\/([0-9a-zA-Z-_]+)-([0-9]+)&/iUs", "{$bbs}/board.php?bo_table=$1&wr_id=$2&", $parse_url['path'], 1, $a); $c += $a;
        $parse_url['path'] = preg_replace("/b\/([0-9a-zA-Z-_]+)&/iUs", "{$bbs}/board.php?bo_table=$1&", $parse_url['path'], 1, $a); $c += $a;
        $parse_url['path'] = preg_replace("/g\/([0-9a-zA-Z-_]+)&/iUs", "{$bbs}/group.php?gr_id=$1&", $parse_url['path'], 1, $a); $c += $a;
        $parse_url['path'] = preg_replace("/c\/([0-9a-zA-Z-_]+)&/iUs", "{$bbs}/content.php?co_id=$1&", $parse_url['path'], 1, $a); $c += $a;
        if (!$c)
            $parse_url['path'] = preg_replace("/\&$/", "?", $parse_url['path']);
    }

    $res = trim("//".$parse_url['host'].$parse_url['path'].$parse_url['query']);
    $res = preg_replace("/\?$/", "", $res);
    $res = preg_replace("/\&$/", "", $res);
    $res = preg_replace("/\?$/", "", $res);
    $res = preg_replace("/\&$/", "", $res);

    return $res;
}

