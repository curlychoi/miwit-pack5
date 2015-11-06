<?php
define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/index.php');
    return;
}

include_once(G5_THEME_PATH.'/head.php');

$list = array();
$sql = " select * from {$g5['menu_table']} ";
$sql.= "  where me_use = '1' ";
$sql.= "  order by me_order, me_code ";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry)) {
    preg_match("/bo_table=([0-9a-zA-Z_]+)&/", $row['me_link'].'&', $match);
    if (!$match[1])
        preg_match("/\/b\/([0-9a-zA-Z-_]+)&/", $row['me_link'].'&', $match);

    $latest_table = $match[1];

    if (!$latest_table) continue;
    if (in_array($latest_table, $list)) continue;

    $list[] = $latest_table;
}

mw_script($theme_path."/js/mw.slider.js");

$img = array();
$img[] = G5_THEME_URL."/img/01.png";
$img[] = G5_THEME_URL."/img/02.png";
$img[] = G5_THEME_URL."/img/03.png";
$img[] = G5_THEME_URL."/img/04.png";
$img[] = G5_THEME_URL."/img/05.png";
$img[] = G5_THEME_URL."/img/06.png";
$img[] = G5_THEME_URL."/img/07.png";
$img[] = G5_THEME_URL."/img/08.png";

shuffle($img);
?>
<style>
.banner { width:720px; height:360px; position:relative; overflow:hidden; margin:0 0 10px 0; }
.banner ul { position:absolute; margin:0; padding:0; list-style:none; font-size:0; }
.banner ul li { margin:0; padding:0; list-style:none; }
</style>

<div class="banner">
<ul>
    <?php foreach ($img as $item) { ?>
    <li><a href="http://www.miwit.com" target="_blank"><img src="<?php echo $item?>"></a></li>
    <?php } ?>
    <!--
    <li><a href="http://www.miwit.com" target="_blank"><img src="http://gnuboard5.com/01.png"></a></li>
    <li><a href="http://www.miwit.com" target="_blank"><img src="http://gnuboard5.com/02.png"></a></li>
    <li><a href="http://www.miwit.com" target="_blank"><img src="http://gnuboard5.com/03.png"></a></li>
    <li><a href="http://www.miwit.com" target="_blank"><img src="http://gnuboard5.com/04.png"></a></li>
    <li><a href="http://www.miwit.com" target="_blank"><img src="http://gnuboard5.com/05.png"></a></li>
    <li><a href="http://www.miwit.com" target="_blank"><img src="http://gnuboard5.com/06.png"></a></li>
    <li><a href="http://www.miwit.com" target="_blank"><img src="http://gnuboard5.com/07.png"></a></li>
    <li><a href="http://www.miwit.com" target="_blank"><img src="http://gnuboard5.com/08.png"></a></li>
    -->
</ul>
</div>

<script>
$(document).ready(function() {
    $('.banner').mw_slider({
        way:'left',
        delay:3000,
    });
});
</script>

<div class="latest">
<?php
for ($i=0, $m=count($list); $i<$m; ++$i) {
    echo "<div class=\"item\">".latest("theme/mw5", $list[$i], 5, 50, 0)."</div>";

    if (($i+1)%2==0) echo "</div><div class=\"latest\">";
}
?>
</div>
<?php
include_once(G5_THEME_PATH.'/tail.php');
