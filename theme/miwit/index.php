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

$latest = array();
for ($i=0, $c=0, $m=count($list); $i<$m; ++$i)
{
    $mw_skin_config = mw_skin_config($list[$i]);

    // 1:1 게시판 출력 안함
    if ($mw_skin_config['cf_attribute'] == '1:1') continue;

    $latest[$c]['bo_table'] = $list[$i];
    $latest[$c]['skin'] = 'theme/mw5';
    $latest[$c]['count'] = 5;
    $latest[$c]['length'] = 50;

    if ($mw_skin_config['cf_type'] == 'gall') {
        $latest[$c]['skin'] = 'theme/mw5-gallery';
        $latest[$c]['count'] = 2;
        $latest[$c]['length'] = 10;

        if ($i==($m-1) and $m%2!=0) {
            $latest[$c]['count'] = 4;
        }
    }

    $c++;
}

mw_script($theme_path."/js/mw.slider.js");

$img = array();
$img[] = G5_THEME_URL."/img/01.jpeg";
$img[] = G5_THEME_URL."/img/02.jpeg";
$img[] = G5_THEME_URL."/img/03.jpeg";
$img[] = G5_THEME_URL."/img/04.jpeg";
$img[] = G5_THEME_URL."/img/05.jpeg";
$img[] = G5_THEME_URL."/img/06.jpeg";
$img[] = G5_THEME_URL."/img/07.jpeg";

shuffle($img);
?>
<style>
.banner { width:728px; height:360px; position:relative; overflow:hidden; margin:0 0 10px 0; }
.banner ul { position:absolute; margin:0; padding:0; list-style:none; font-size:0; }
.banner ul li { margin:0; padding:0; list-style:none; }
</style>

<div class="banner">
<ul>
    <?php foreach ($img as $item) { ?>
    <li><a href="http://www.miwit.com" target="_blank"><img src="<?php echo $item?>"></a></li>
    <?php } ?>
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
foreach ((array)$latest as $row)
{
    echo '<div class="item">'.latest($row['skin'], $row['bo_table'], $row['count'], $row['length'], 0).'</div>';

    if (($i++)%2==0) echo '</div><div class="latest">';
}
?>
</div>

<?php

include_once(G5_THEME_PATH.'/tail.php');
