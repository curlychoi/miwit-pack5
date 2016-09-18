<?php
if (!defined('_GNUBOARD_')) exit;

// 최신글 추출
// $cache_time 캐시 갱신시간
function mw_latest($skin_dir='', $bo_table, $rows=10, $subject_len=40, $cache_time=1, $options='')
{
    global $g5;

    if (!$skin_dir) $skin_dir = 'basic';

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

    $list = array();

    $is_rand = false;
    if (preg_match('/:rand$/i', $bo_table))
        $is_rand = true;

    $bo_table = str_replace(":rand", "", $bo_table);

    $sql = " select * from {$g5['board_table']} where bo_table = '{$bo_table}' ";
    $board = sql_fetch($sql);
    $bo_subject = get_text($board['bo_subject']);

    $tmp_write_table = $g5['write_prefix'] . $bo_table; // 게시판 테이블 전체이름
    $sql = " select * from {$tmp_write_table} where wr_is_comment = 0 order by wr_num limit 0, {$rows} ";
    if ($is_rand)
        $sql = preg_replace('/order by wr_num/i', 'order by rand()', $sql);
    $result = sql_query($sql);
    for ($i=0; $row = sql_fetch_array($result); $i++) {
        $list[$i] = get_list($row, $board, $latest_skin_url, $subject_len);
        $list[$i] = mw_get_list($list[$i], $board, $latest_skin_url, $subject_len);
    }

    ob_start();
    include $latest_skin_path.'/latest.skin.php';
    return ob_get_clean();
}
