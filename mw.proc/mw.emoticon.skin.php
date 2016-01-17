<?php
include_once("_common.php");
include_once($board_skin_path.'/mw.lib/mw.skin.basic.lib.php');

$dirs = array();
foreach (glob(dirname(__FILE__).'/../mw.emoticon/*') as $row) {
    if (is_dir($row)) {
        $dirs[] = basename($row);
    }
}

echo '<ul class="menu">';
echo '<li><a href="#;" onclick="win_emoticon(\'default\')">default</a></li>';
foreach ((array)$dirs as $dir) {
    printf('<li><a href="#;" onclick="win_emoticon(\'%s\')">%s</a></li>', $dir, $dir);
}
echo '</ul>';

unset($dir);

$dir = $mw_basic['cf_emoticon'];
if (preg_match('/^[0-9a-z-_]+$/i', $_REQUEST['dir'])) {
    $dir = $_REQUEST['dir'];
}
else if ($_REQUEST['dir'] == 'default') {
    $dir = '';
}
?>
<div class="clear"></div>
<ul class="emo">
<?php
$path = '../mw.emoticon';
$emo_dir = '';
if (preg_match("/[0-9a-z-_]+/i", $dir) and is_dir($path.'/'.$dir)) {
    $emo_dir = $dir;
    $path .= '/'.$emo_dir;
}

$emo = glob($path."/*.{gif,jpg,jpeg,png}", GLOB_BRACE);
sort($emo);

foreach ((array)$emo as $item) :
    if (!preg_match("/^[0-9a-z-_]+\.(png|jpe?g|gif)$/i", basename($item))) continue;

    preg_match("/^([0-9a-z-_]+)\./i", basename($item), $match);
    $add = $emo_dir ? $emo_dir.'/'.$match[1] : $match[1];

    $path = str_replace('..', $board_skin_path, $item);

    printf('<li><img src="%s" onclick="emoticon_add(\'%s\')"></li>', $path, $add);
endforeach;
?>
</ul>


