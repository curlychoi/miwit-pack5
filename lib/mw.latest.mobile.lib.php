<?php
/**
 * 배추 모바일 빌더 (Mobile for Gnuboard4)
 *
 * Copyright (c) 2010 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

@include_once($g4['path']."/lib/mw.cache.lib.php");

if (is_file($g4['path']."/lib/mw.permalink.lib.php")) {
    include_once($g4['path']."/lib/mw.permalink.lib.php");
    include_once($g4['path']."/lib/mw.common.lib.php");
}

function mw_latest_mobile($skin_dir="", $bo_tables, $rows=10, $subject_len=50, $is_img=0, $minute=0)
{
    global $g4, $mw, $mw_mobile;

    if(preg_match('#^theme/(.+)$#', $skin_dir, $match)) {
        if (G5_IS_MOBILE) {
            $latest_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
            if(!is_dir($latest_skin_path))
                $latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
            $latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
        } else {
            $latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
            $latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
        }
        $skin_dir = $match[1];
    } else {
        if(G5_IS_MOBILE) {
            $latest_skin_path = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
            $latest_skin_url  = G5_MOBILE_URL.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
        } else {
            $latest_skin_path = G5_SKIN_PATH.'/latest/'.$skin_dir;
            $latest_skin_url  = G5_SKIN_URL.'/latest/'.$skin_dir;
        }
    }

    $tmp_tables = explode(",", $bo_tables);
    $bo_tables = array();
    for ($i=0, $j=0; $i<count($tmp_tables); $i++) {
	$tmp_tables[$i] = trim($tmp_tables[$i]);
	if (!$tmp_tables[$i]) continue;
	$bo_tables[$j++] = $tmp_tables[$i];
    }
    $sql_tables = implode("','", $bo_tables);
    $file_tables = implode("-", $bo_tables);
    $file_tables = str_replace(":rand", "", $file_tables);

    $tab = null;

    $cache_file_tab = $g4['path']."/data/mw.cache/latest-mobile-{$file_tables}-list-{$rows}-{$is_img}-{$subject_len}";

    if (function_exists("mw_cache_read")) {
        //$tab = mw_cache_read($cache_file_tab, $minute);
    }

    $table_list = $bo_tables;
    $tmp_list = array();

    for ($i=0; $i<count($bo_tables); $i++) {
        $tmp = explode(":", $bo_tables[$i]);
        $bo_table = $tmp[0];

        $tmp_list[] = $bo_table;
    }
    $bo_tables = $tmp_list;

    if (!$tab) {
	$tab = array();
	for ($i=0; $i<count($table_list); $i++)
        {
	    $list = array();
	    $file = array();
            $is_rand = false;

            $tmp = explode(":", $table_list[$i]);
	    $bo_table = $tmp[0];

            if ($tmp[1] == "rand")
                $is_rand = true;

	    $tmp_write_table = $g4['write_prefix'] . $bo_table;
	    $board = sql_fetch("select * from {$g4['board_table']} where bo_table = '{$bo_table}'");

            if ($is_img) {
                $file = mw_mobile_get_thumb($bo_table, $is_img, $is_rand);

                for ($k=0, $m=count($file); $k<$m; ++$k) {
                    $sql = "select * ";
                    $sql.= "  from {$g4['write_prefix']}{$file[$k]['bo_table']} ";
                    $sql.= " where wr_id = '{$file[$k]['wr_id']}'";
                    $row = sql_fetch($sql);

                    $row['wr_subject'] = mw_counting_str($row['wr_subject']);

                    if (function_exists("mw_get_list")) {
                        $row = mw_get_list($row, $board, $latest_skin_path, $subject_len);
                    }
                    else {
                        $row = get_list($row, $board, $latest_skin_path, $subject_len);
                    }

                    if ($row['wr_view_block'])
                        $row['wr_subject'] = "보기가 차단된 게시물입니다.";

                    if ($row['wr_view_block'])
                        $file[$k]['path'] = $mw_mobile['path']."/img/lock.png";

                    if ($row['icon_secret'])
                        $file[$k]['path'] = $mw_mobile['path']."/img/lock.png";

                    if ($row['wr_key_password'])
                        $file[$k]['path'] = $mw_mobile['path']."/img/lock.png";

                    if ($row['wr_singo_lock'])
                        $file[$k]['path'] = $mw_mobile['path']."/img/lock.png";

                    $file[$k]['subject'] = conv_subject($row['wr_subject'], $subject_len, "…");
                    $file[$k]['wr_comment'] = $row['wr_comment'];
                    $file[$k]['wr_link1'] = $row['wr_link1'];
                }

                if (count($file) < $is_img) {
                    for ($j=count($file); $j<$is_img; $j++) {
                        $file[$j]['path'] = "$latest_skin_path/img/noimage.gif";
                        $file[$j]['subject'] = "...";
                        $file[$j]['href'] = "#";
                    }   
                }
            }

            $noids = array();
            for ($k=0; $k<count($file); $k++) {
                $noids[] = $file[$k]['wr_id'];
            }
            $noids = implode("','", $noids);

	    $sql = " select * from {$tmp_write_table} ";
            $sql.= "  where wr_is_comment = 0 ";
            $sql.= "    and wr_id not in ('{$noids}') ";
            if ($is_rand)
                $sql.= "  order by rand() ";
            else
                $sql.= "  order by wr_num asc ";
            $sql.= "  limit ".$rows;

	    $qry = sql_query($sql);
	    for ($j=0; $row=sql_fetch_array($qry); $j++) {
                $row['wr_subject'] = mw_counting_str($row['wr_subject']);
                if (function_exists("mw_get_list")) {
                    $list[$j] = mw_get_list($row, $board, $latest_skin_path, $subject_len);
                }
                else {
                    if ($row['wr_view_block'])
                        $row['wr_subject'] = "보기가 차단된 게시물입니다.";

                    $list[$j] = get_list($row, $board, $latest_skin_path, $subject_len);
                }
		$list[$j]['content'] = $list[$i]['wr_content'] = "";
                $list[$j]['href'] = $mw_mobile['path']."/board.php?bo_table={$bo_table}&wr_id={$list[$j]['wr_id']}";
	    }
	    if (!$j) {
		for ($j=0; $j<$rows; $j++) {
		    if (!$board) {
			$board = array();
			$board['bo_subject'] = "none";
		    }
		    $list[$j]['bo_subject'] = $board['bo_subject'];
		    $list[$j]['subject'] = cut_str("게시물이 없어요.", $subject_len);
		    $list[$j]['href'] = "#";
		}
	    }
	    $tab[$bo_table] = $list;
	    $tab[$bo_table]['board'] = $board;
            $tab[$bo_table]['file'] = $file;
	}
        if (function_exists("mw_cache_write"))
            mw_cache_write($cache_file_tab, $tab);
    }

    ob_start();
    include $latest_skin_path."/latest.skin.php";
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function mw_mobile_get_thumb($bo_tables, $cnt=1, $is_rand)
{
    global $g4, $mw_mobile;

    $files = array();

    foreach ((array)$bo_tables as $bo_table)
    {
        $row = sql_fetch("select max(wr_id) as max_id from {$g4['write_prefix']}{$bo_table} where wr_is_comment = 0", false);
        $max = $row['max_id'];

        if (!$max) continue;

        $max_id = $max;

        $path = $g4['path']."/data/file/{$bo_table}/thumbnail"; 
        if (!is_dir($path))
            $path = $g4['path']."/data/file/{$bo_table}/thumb"; 

        $fnd = 0;

        for($i=0, $m=$max_id; $i<$m; $i++) {
            if ($is_rand)
                $max = rand(1, $max_id);
            else if ($i)
                --$max;

            $file_path = "{$path}/{$max}.jpg";
            if (!file_exists($file_path))
                $file_path = "{$path}/{$max}";

            if (file_exists($file_path)) {
                $file = array();
                $file['bo_table'] = $bo_table;
                $file['wr_id'] = $max;
                $file['path'] = $file_path;
                $file['href'] = "{$mw_mobile['path']}/board.php?bo_table={$bo_table}&wr_id={$max}";

                //$filemtime = filemtime($file_path);
                if (!$files[$max]['wr_id']) {
                    $files[$max] = $file;
                    ++$fnd;
                }
            }
            //if ($i>100) break;
            if ($fnd >= $cnt) break;
            if (!$max) break;
        }
    }

    if (!count($files)) return;

    krsort($files);

    $list = array();
    for ($i=1; $i<=$cnt; $i++) {
        $list[] = array_shift($files);
        if (!$files) break;
    }

    $files = null;
    unset($files);

    return $list;
}

// 자동치환
if (!function_exists("mw_builder_reg_str")) {
function mw_builder_reg_str($str)
{
    global $member;

    if ($member['mb_id']) {
        $str = str_replace("{닉네임}", $member['mb_nick'], $str);
        $str = str_replace("{별명}", $member['mb_nick'], $str);
    } else {
        $str = str_replace("{닉네임}", "회원", $str);
        $str = str_replace("{별명}", "회원", $str);
    }

    return $str;
}}

if (!function_exists("mw_counting_date")) {
function mw_counting_date($datetime, $endstr=" 남았습니다")
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
}}

if (!function_exists("mw_counting_str")) {
function mw_counting_str($str)
{
    preg_match_all("/\[counting (.*)\]/iU", $str, $matches);
    for ($j=0, $jm=count($matches[1]); $j<$jm; $j++) {
        $str = preg_replace("/\[counting {$matches[1][$j]}\]/iU", mw_counting_date($matches[1][$j]), $str);
    }

    return $str;
}}

