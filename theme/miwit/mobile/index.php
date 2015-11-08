<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_PATH.'/lib/mw.latest.mobile.lib.php');

include_once(G5_THEME_MOBILE_PATH.'/head.php');

$mw5_menu = mw_get_menu();
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
