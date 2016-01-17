<?php
include_once("../../common.php");
header("content-type: text/css; charset: utf-8");

//$wrap_width = $mw['config']['cf_width'];
$content_width = $mw['config']['cf_content_width']+40;
$side_width = $mw['config']['cf_side_width'];
$side_float = $mw['config']['cf_side_position'];
$side_margin_width = $side_width + 10;
$wrap_width = $content_width + $side_margin_width;
//$main_width = $wrap_width - $side_margin_width;

echo "#mw5 .wrapper { width:{$wrap_width}px }".PHP_EOL;
//echo " @media screen and (min-width:500px) { ";
echo "#mw5 .top { min-width:{$wrap_width}px }".PHP_EOL;
echo "#mw5 .head { min-width:{$wrap_width}px }".PHP_EOL;
echo "#mw5 .navbar { min-width:{$wrap_width}px }".PHP_EOL;
echo "#mw5 .footer { min-width:{$wrap_width}px }".PHP_EOL;
echo "#mw5 #device_change { min-width:{$wrap_width}px }".PHP_EOL;
//echo "}";
echo "#mw5 .menu_title { width:{$content_width}px; }".PHP_EOL;
echo "#mw5 .main { width:{$content_width}px; }".PHP_EOL;
echo "#mw5 .sidebar { width:{$side_width}px; }".PHP_EOL;
echo "#mw5 .sidebar { float:{$side_float}; }".PHP_EOL;

if ($side_float == 'right') {
    echo "#mw5 .main { width:-webkit-calc(100%-{$side_margin_width}px); }".PHP_EOL;
    echo "#mw5 .main { width:-moz-calc(100% - {$side_margin_width}px); }".PHP_EOL;
    echo "#mw5 .main { width:calc(100% - {$side_margin_width}px); }".PHP_EOL;

    echo "#mw5 .menu_title { width:-webkit-calc(100%-{$side_margin_width}px); }".PHP_EOL;
    echo "#mw5 .menu_title { width:-moz-calc(100% - {$side_margin_width}px); }".PHP_EOL;
    echo "#mw5 .menu_title { width:calc(100% - {$side_margin_width}px); }".PHP_EOL;
}
else if ($side_float == 'left') {
    echo "#mw5 .sidebar { margin-right:10px; }".PHP_EOL;
    echo "#mw5 .main { float:right; }".PHP_EOL;
    echo "#mw5 .menu_title { margin-left:{$side_margin_width}px; }".PHP_EOL;
}

