<?php
if (!defined("_GNUBOARD_")) exit;

if (is_null($html_process)) {
    $html_process = new html_process();
}

function mw_script($src)
{
    global $html_process;

    if (!@is_file(G5_PATH.$src)) return;

    $mtime = filemtime(G5_PATH.$src);
    $src = G5_URL.$src;//."?".$mtime;

    add_javascript("<script src=\"{$src}\"></script>");
}

function mw_css($href)
{
    global $html_process;

    if (!@is_file(G5_PATH.$href)) return;

    $mtime = filemtime(G5_PATH.$href);
    $href = G5_URL.$href;//."?".$mtime;

    add_stylesheet("<link rel=\"stylesheet\" href=\"{$href}\">");
}

function mw_theme_path()
{
    global $mw5;

    return G5_PATH.$mw5['theme_url'];
}

function mw_theme_url()
{
    global $mw5;

    return G5_URL.$mw5['theme_url'];
}

function mw_get_theme_color()
{
    global $mw5;

    $list = array();

    $theme_path = G5_THEME_PATH.'/color';
    if ($dh = opendir($theme_path)) {
        while (($dir = readdir($dh)) !== false) {
            if ($dir == "." || $dir == "..")
                continue;

            if (is_dir($theme_path.'/'.$dir))
                $list[] = $dir;
        }
        closedir($dh);
    }
    return $list;
}

function mw_builder_reg_str($str)
{
    global $g4;
    global $member;

    if ($member['mb_id']) {
        $str = str_replace("{닉네임}", $member['mb_nick'], $str);
        $str = str_replace("{별명}", $member['mb_nick'], $str);
    }
    else {
        $str = str_replace("{닉네임}", "회원", $str);
        $str = str_replace("{별명}", "회원", $str);
    }

    $str = preg_replace("/\[month\]/iU", date('n', $g4['server_time']), $str);
    $str = preg_replace("/\[last_day\]/iU", date('t', $g4['server_time']), $str);

    $str = preg_replace("/\[today\]/iU", date('Y년 m월 d일', $g4['server_time']), $str);
    $str = preg_replace("/\[day of the week\]/iU", get_yoil($g4['time_ymdhis']), $str);

    preg_match_all("/\[counting (.*)\]/iU", $str, $matches);
    for ($i=0, $m=count($matches[1]); $i<$m; $i++) {
        $str = preg_replace("/\[counting {$matches[1][$i]}\]/iU", mw_builder_counting_date($matches[1][$i]), $str);
    }

    return $str;
}

function mw_builder_counting_date($datetime, $endstr=" 남았습니다")
{
    global $g4;

    $timestamp = strtotime($datetime); // 글쓴날짜시간 Unix timestamp 형식 
    $current = $g4['server_time']; // 현재날짜시간 Unix timestamp 형식 

    if ($current >= $timestamp)
        return "종료 되었습니다.";

    if ($current <= $timestamp - 86400 * 365)
        $str = (int)(($timestamp - $current) / (86400 * 365)) . "년"; 
    else if ($current <= $timestamp - 86400 * 31)
        $str = (int)(($timestamp - $current) / (86400 * 31)) . "개월"; 
    else if ($current <= $timestamp - 86400 * 1)
        $str = (int)(($timestamp - $current) / 86400) . "일"; 
    else if ($current <= $timestamp - 3600 * 1)
        $str = (int)(($timestamp - $current) / 3600) . "시간"; 
    else if ($current <= $timestamp - 60 * 1)
        $str = (int)(($timestamp - $current) / 60) . "분"; 
    else
        $str = (int)($timestamp - $current) . "초"; 
    
    return $str.$endstr; 
}

function mw_get_thumb_path($bo_table, $wr_id, $file=null, $thumb_number=null)
{
    global $g4;

    $thumb = null;

    if (!$bo_table or !$wr_id) return $thumb;

    $img1 = "{$g4['path']}/data/file/{$bo_table}/thumbnail{$thumb_number}/{$wr_id}";
    $img2 = "{$g4['path']}/data/file/{$bo_table}/thumb/{$wr_id}";

    $jpg1 = "{$img1}.jpg";
    $jpg2 = "{$img2}.jpg";

    $thumb = $jpg1;

    if (!file_exists($thumb)) $thumb = $img1;
    if (!file_exists($thumb)) $thumb = $jpg2;
    if (!file_exists($thumb)) $thumb = $img2;
    if (!file_exists($thumb)) {
        if (!$file)
            $file = mw_builder_get_first_file($bo_table, $wr_id, true);
        $thumb = "{$g4['path']}/data/file/{$bo_table}}/{$file['bf_file']}";
        if (!preg_match("/\.(jpg|gif|png)$/i", $thumb)) {
            $thumb = null;
        }
    }
    if (is_dir($thumb)) $thumb = null;

    return $thumb;
}

function mw_builder_get_first_file($bo_table, $wr_id, $is_image=false)
{
    global $g4;

    $sql = "select * from $g4[board_file_table] ";
    $sql.= " where bo_table = '$bo_table' ";
    $sql.= "   and wr_id = '$wr_id' ";
    if ($is_image) 
        $sql.= " and bf_width > 0 ";
    $sql.= " order by bf_no ";
    $sql.= " limit 1";
    $row = sql_fetch($sql);

    return $row;
}

function mw_html_entities($str)
{
    $str = str_replace("\"", "&quot;", $str);
    //$str = str_replace("&", "&amp;", $str);
    $str = str_replace("<", "&lt;", $str);
    $str = str_replace(">", "&gt;", $str);
    $str = str_replace("'", "&prime;", $str);

    return $str;
}

function mw_skin_config($bo_table)
{
    $file = G5_DATA_PATH."/mw.basic.config/".$bo_table;

    $content = '';
    if (@is_file($file)) {
        ob_start();
        readfile($file);
        $content = ob_get_clean();
    }

    $content = base64_decode($content);
    $content = unserialize($content);

    return $content;
}

function goto_url2($url) {
    global $g4;

    header("location: {$url}");
    exit;
}

function mw_get_board($url)
{
    preg_match("/bo_table=([0-9a-zA-Z_]+)&/", $url.'&', $match);
    if (!$match[1])
        preg_match("/\/b\/([0-9a-zA-Z-_]+)&/", $url.'&', $match);

    return $match[1];
}
