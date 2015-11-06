<?php
define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/index.php');
    return;
}

include_once(G5_THEME_PATH.'/head.php');

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
$i = 1;
while ($latest_table = array_shift($list)) {
    $mw_skin_config = mw_skin_config($latest_table);
    if ($mw_skin_config['cf_attribute'] == "1:1") continue;

    echo "<div class=\"item\">".latest("theme/mw5", $latest_table, 5, 50, 0)."</div>";

    if (($i++)%2==0) echo "</div><div class=\"latest\">";
}
?>
</div>
<?php
include_once(G5_THEME_PATH.'/tail.php');
