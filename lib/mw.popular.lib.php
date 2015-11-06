<?php
/**
 * MW5 for Gnuboard5
 *
 * Copyright (c) 2015 Choi Jae-Young <www.miwit.com>
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

// 인기검색어 출력
// $skin_dir : 스킨 디렉토리
// $pop_cnt : 검색어 몇개
// $date_cnt : 몇일 동안
function mw_popular($skin_dir='basic', $pop_cnt=10, $date_cnt=7, $minute=0)
{
    global $config;
    global $g5;
    global $mwp;
    global $is_admin;

    //$cache_file_list = G5_PATH."/data/mw.cache/popular-".G5_TIME_YMD."-{$pop_cnt}-{$date_cnt}";
    $list = array();

    $popular_config = null;
    if ($mwp['popular_config_table'])
        $popular_config = sql_fetch("select * from {$mwp['popular_config_table']}", false);

    $sql_except = '';
    if ($popular_config['cf_except_word']) {
        $wrd = explode(",", $popular_config['cf_except_word']);
        for ($i=0, $m=count($wrd); $i<$m; $i++) {
            $key = trim($wrd[$i]);
            if (!trim($key)) continue;
            $key = addslashes($key);
            $sql_except .= " and pp_word <> '{$key}' ";
        }
    }

    if (!$list) {
	$date_gap = date("Y-m-d", G5_SERVER_TIME - ($date_cnt * 86400));
	$date_gap_old = date("Y-m-d", strtotime($date_gap) - ($date_cnt * 86400));
	$sql = " select pp_word, count(*) as cnt from {$g5['popular_table']}
		  where pp_date between '{$date_gap}' and '".G5_TIME_YMD."'
                    and pp_word not in (select mb_id from {$g5['member_table']})
                    and pp_word not in (1,2,3,4,5,6,7,8,9,10)
                    and trim(pp_word) <> ''
                    {$sql_except}
		  group by pp_word
		  order by cnt desc, pp_word
		  limit 0, {$pop_cnt} ";
	$result = sql_query($sql, false);
        if (!$result) {
            $sql = " select pp_word, count(*) as cnt from {$g5['popular_table']}
                      where pp_date between '{$date_gap}' and '".G5_TIME_YMD."'
                        and trim(pp_word) <> ''
                        {$sql_except}
                      group by pp_word
                      order by cnt desc, pp_word
                      limit 0, {$pop_cnt} ";
            $result = sql_query($sql);
        }

	$old = array();
	$sql2 = " select pp_word, count(*) as cnt from {$g5['popular_table']}
		  where pp_date between '{$date_gap_old}' and '{$date_gap}'
                    and trim(pp_word) <> ''
                    {$sql_except}
		  group by pp_word
		  order by cnt desc, pp_word
		  limit 0, 100 ";
	$qry2 = sql_query($sql2);
	$count = sql_num_rows($qry2);
	for ($j=0; $row2=sql_fetch_array($qry2); $j++) {
	    $old[$j] = $row2;
	    $old[$old[$j]['pp_word']] = $j;
	}

	for ($i=0; $row=sql_fetch_array($result); $i++) 
	{
	    $j = $old[$row['pp_word']];
	    if (!$j || !is_numeric($j)) $j = 0;

	    $list[$i] = $row;
	    $list[$i]['pp_word'] = get_text($list[$i]['pp_word']); // 스크립트등의 실행금지
	    $list[$i]['pp_word'] = urldecode($list[$i]['pp_word']);
	    $list[$i]['pp_rank'] = $i + 1;
	    if ($count == $j) {
		$list[$i]['old_pp_rank'] = 0;
		$list[$i]['rank_gap'] = 0;
	    }
            else {
		$list[$i]['old_pp_rank'] = $j + 1;
		$list[$i]['rank_gap'] = $list[$i]['old_pp_rank'] - $list[$i]['pp_rank'];
	    }
	    if ($list[$i]['rank_gap'] > 0)
		$list[$i]['icon'] = "up";
	    else if ($list[$i]['rank_gap'] < 0)
		$list[$i]['icon'] = "down";
	    else if ($list[$i]['old_pp_rank'] == 0)
		$list[$i]['icon'] = "anew";
	    else if ($list[$i]['rank_gap'] == 0)
		$list[$i]['icon'] = "nogap";
	}
    }

    if (empty($list)) {
        $list[0]['pp_word'] = "배추빌더"; $list[0]['icon'] = "up";
        $list[1]['pp_word'] = "너무멋져"; $list[1]['icon'] = "down";
        $list[2]['pp_word'] = "배추스킨"; $list[2]['icon'] = "up";
        $list[3]['pp_word'] = "최고에요"; $list[3]['icon'] = "down";
        $list[4]['pp_word'] = "곱슬최씨"; $list[4]['icon'] = "up";
        $list[5]['pp_word'] = "사랑해요"; $list[5]['icon'] = "down";
        $list[6]['pp_word'] = "이문장은"; $list[6]['icon'] = "up";
        $list[7]['pp_word'] = "다른것을"; $list[7]['icon'] = "down";
        $list[8]['pp_word'] = "검색하면"; $list[8]['icon'] = "up";
        $list[9]['pp_word'] = "사라져요"; $list[9]['icon'] = "down";
    }

    ob_start();
    if (preg_match('#^theme/(.+)$#', $skin_dir, $match)) {
        if (G5_IS_MOBILE) {
            $popular_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/popular/'.$match[1];
            if(!is_dir($popular_skin_path))
                $popular_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/popular/'.$match[1];
            $popular_skin_url = str_replace(G5_PATH, G5_URL, $popular_skin_path);
        } else {
            $popular_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/popular/'.$match[1];
            $popular_skin_url = str_replace(G5_PATH, G5_URL, $popular_skin_path);
        }
        $skin_dir = $match[1];
    }
    else {
 
        if (G5_IS_MOBILE) {
            $popular_skin_path = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/popular/'.$skin_dir;
            $popular_skin_url = G5_MOBILE_URL.'/'.G5_SKIN_DIR.'/popular/'.$skin_dir;
        }
        else {
            $popular_skin_path = G5_SKIN_PATH.'/popular/'.$skin_dir;
            $popular_skin_url = G5_SKIN_URL.'/popular/'.$skin_dir;
        }
    }
    include_once ($popular_skin_path.'/popular.skin.php');
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

