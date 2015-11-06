<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_PATH.'/lib/mw.latest.mobile.lib.php');

include_once(G5_THEME_MOBILE_PATH.'/head.php');

$mw5_menu = array();

$sql = " select *
           from {$g5['menu_table']}
          where me_use = '1'
            and length(me_code) = '2'
          order by me_order, me_id ";
$qry = sql_query($sql);
//for ($i=0; $row=sql_fetch_array($qry); $i++) {
$i = 0;
while ($row=sql_fetch_array($qry)) {
    $extend = sql_fetch("select * from {$mw5['menu_table']} where me_code = '{$row['me_code']}' ", false);
    if ($extend) {
        if ($extend['me_level'] > $member['mb_level']) {
            continue;
        }
        if ($extend['me_icon']) {
            $row['me_name'] = "<i class='fa fa-{$extend['me_icon']}'></i> ".$row['me_name'];
        }
    }

    $mw5_menu[$i] = $row;
    $mw5_menu[$i]['sub'] = array();

    $g_new = 0;
    $g_count = 0;
    $g_table = '';

    preg_match("/bo_table=([0-9a-zA-Z-_]+)&/", $row['me_link'].'&', $match);
    if (!$match[1])
        preg_match("/\/b\/([0-9a-zA-Z-_]+)&/", $row['me_link'].'&', $match);

    if ($match[1]) {
        $mw_skin_config = mw_skin_config($match[1]);
        if ($mw_skin_config['cf_attribute'] != "1:1" or $is_admin) {
            $b = sql_fetch(" select bo_count_write, bo_new from {$g5['board_table']} where bo_table = '{$match[1]}' ");
            $t = sql_fetch(" select count(*) as cnt from {$g5['write_prefix']}{$match[1]} where wr_is_comment = '' and wr_datetime >= DATE_SUB(NOW(), INTERVAL {$b['bo_new']} HOUR) ");

            $g_new += $t['cnt'];
            $g_count += $b['bo_count_write'];
            $g_table = $match[1];
        }
    }

    $j = 0;
    $sql2 = " select *
               from {$g5['menu_table']}
              where me_use = '1'
                and length(me_code) = '4'
                and substring(me_code, 1, 2) = '{$row['me_code']}'
              order by me_order, me_id ";
    $qry2 = sql_query($sql2);
    //for ($j=0; $row2=sql_fetch_array($qry2); $j++) {
    while ($row2=sql_fetch_array($qry2)) {
        $extend = sql_fetch("select * from {$mw5['menu_table']} where me_code = '{$row2['me_code']}' ", false);
        if ($extend) {
            if ($extend['me_level'] > $member['mb_level']) {
                continue;
            }
            if ($extend['me_icon']) {
                $row2['me_name'] = "<i class='fa fa-{$extend['me_icon']}'></i> ".$row2['me_name'];
            }
        }

        preg_match("/bo_table=([0-9a-zA-Z-_]+)&/", $row2['me_link'].'&', $match);
        if (!$match[1])
            preg_match("/\/b\/([0-9a-zA-Z-_]+)&/", $row2['me_link'].'&', $match);

        if ($match[1] && $match[1] == $g_table) {
            $row2['bo_new'] = $g_new;
            $row2['bo_count'] = $g_count;
        }
        else if ($match[1]) {
            $mw_skin_config = mw_skin_config($match[1]);
            if ($mw_skin_config['cf_attribute'] != "1:1" or $is_admin) {
                $b = sql_fetch(" select bo_count_write, bo_new from {$g5['board_table']} where bo_table = '{$match[1]}' ");
                $t = sql_fetch(" select count(*) as cnt from {$g5['write_prefix']}{$match[1]} where wr_is_comment = '' and wr_datetime >= DATE_SUB(NOW(), INTERVAL {$b['bo_new']} HOUR) ");

                $row2['bo_new'] = $t['cnt'];
                $row2['bo_count'] = $b['bo_count_write'];

                $g_new += $t['cnt'];
                $g_count += $b['bo_count_write'];
            }
        }

        $mw5_menu[$i]['sub'][$j] = $row2;
        ++$j;
    }
    if (!$j)
        $mw5_menu[$i]['sub'][0] = $row;

    $mw5_menu[$i]['new'] = $g_new;
    $mw5_menu[$i]['count'] = $g_count;

    ++$i;
}

$mw5_menu_count = count($mw5_menu);

$list = array();
for ($i=0; $row=$mw5_menu[$i]; ++$i) {
    $latest_table = mw_get_board($row['me_link']);
    if ($latest_table and !in_array($latest_table, $list))
        $list[] = $latest_table;

    for ($j=0; $row2=$mw5_menu[$i]['sub'][$j]; $j++) {
        $latest_table = mw_get_board($row2['me_link']);
        if ($latest_table and !in_array($latest_table, $list))
            $list[] = $latest_table;
    }

}

$i = 1;
while ($latest_table = array_shift($list)) {
    $mw_skin_config = mw_skin_config($latest_table);
    if ($mw_skin_config['cf_attribute'] == "1:1") continue;

    echo "<div class=\"item\">".mw_latest_mobile("theme/mobile", $latest_table, 15, 50, 9, 0)."</div>";
}

include_once(G5_THEME_MOBILE_PATH.'/tail.php');
