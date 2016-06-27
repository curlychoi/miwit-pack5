<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/group.php');
    return;
}

if(!$is_admin && $group['gr_device'] == 'mobile')
    alert($group['gr_subject'].' 그룹은 모바일에서만 접근할 수 있습니다.');

$g5['title'] = $group['gr_subject'];
include_once(G5_THEME_PATH.'/head.php');

$loop_index = 0;
$sql = sprintf("select bo_table from %s where bo_list_level <= '%d' and bo_device <> 'mobile' and gr_id = '%s'", $g5['board_table'], $member['mb_level'], $gr_id);
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry)) {

    $latest_table = $row['bo_table'];
    $mw_skin_config = mw_skin_config($latest_table);

    // 1:1 게시판 출력 안함
    if ($mw_skin_config['cf_attribute'] == '1:1') {
        $real_max--;
        continue;
    }

    $latest[$loop_index]['bo_table'] = $latest_table;
    $latest[$loop_index]['skin'] = 'theme/mw5';
    $latest[$loop_index]['count'] = 6;
    $latest[$loop_index]['length'] = 50;

    if ($mw_skin_config['cf_type'] == 'gall') {
        $latest[$loop_index]['skin'] = 'theme/mw5-gallery';
        $latest[$loop_index]['count'] = 2;
        $latest[$loop_index]['length'] = 10;

        if ($loop_index==$real_max and $loop_index%2!=0) {
            $latest[$loop_index]['count'] = 4;
        }
    }

    $loop_index++;
}

?>
<div class="latest">
<?php
global $latest;
$i = 1;
foreach ((array)$latest as $row)
{
    echo '<div class="item">'.mw_latest($row['skin'], $row['bo_table'], $row['count'], $row['length'], 0).'</div>';

    if (($i++)%2==0) echo '</div><div class="latest">';
}
?>
</div>
<?php
include_once(G5_THEME_PATH.'/tail.php');
