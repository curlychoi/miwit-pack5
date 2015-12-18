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
?>

<!-- 메인화면 최신글 시작 -->
<div class="latest">
<?php
$i = 1;
$sql = " select * from {$g5['menu_table']} ";
$sql.= "  where (me_link like '%bo_table%' or me_link like '%/b/%') ";
$sql.= "    and me_code like '{$menu['me_code']}%' ";
$sql.= "  order by me_code, me_order ";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry)) {
    $latest_table = mw_get_board($row['me_link']);
    if (!$latest_table) continue;

    $mw_skin_config = mw_skin_config($latest_table);
    if ($mw_skin_config['cf_attribute'] == "1:1") continue;

    echo "<div class=\"item\">".latest("theme/mw5", $latest_table, 5, 50, 0)."</div>";

    if (($i++)%2==0) echo "</div><div class=\"latest\">";
}
?>
</div>
<!-- 메인화면 최신글 끝 -->

<?php
include_once(G5_THEME_PATH.'/tail.php');
