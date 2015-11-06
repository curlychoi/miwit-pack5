<?php
/**
 * Bechu-Basic Skin for Gnuboard4
 *
 * Copyright (c) 2008 Choi Jae-Young <www.miwit.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

include_once("_common.php");

$mw_is_view = true;

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

header("Content-Type: text/html; charset=$g4[charset]");
$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0

$img_path = "$g4[url]/skin/board/$board[bo_skin]/img/";

if ($mw_basic[cf_good_graph])
{
    $good_box_width = $_GET['width'];
    if (!$good_box_width)
        $good_box_width = 270;
    $wgg = @(($good_box_width-20)/($write[wr_good]+$write[wr_nogood]))*$write[wr_good]+10;
    $wgn = @(($good_box_width-20)/($write[wr_good]+$write[wr_nogood]))*$write[wr_nogood]+10;
    if ($wgg == 10 && $wgn == 10) {
        $wgg = $good_box_width/2;
        $wgn = $good_box_width/2;
    }

?>
    <style type="text/css">
    .good-box { text-align:center; font-size:11px; font-weight:bold; color:#fff; margin:50px 0 20px 0; }
    .good-box .in { text-align:center; width:<?=$good_box_width+130?>px; height:30px; margin:0 auto; }
    .good-box .bg { float:left; }
    .good-box .bn { float:right; }
    .good-box .gh { float:left; width:<?=$good_box_width+2?>px; margin:8px 0 0 4px; }
    .good-box .gh .gg { background:url(<?=$img_path?>/graph_good.gif); width:<?=$wgg?>px; height:14px; float:left; }
    .good-box .gh .gn { background:url(<?=$img_path?>/graph_nogood.gif); width:<?=$wgn?>px; height:14px; float:left; margin:0 0 0 2px; }
    .good-box .gh span { display:block; padding:0; margin:2px 0 0 0; height:12px; line-height:12px; }
    </style>
    <div class="good-box">
    <div class="in">
        <div class="bg"><img src="<?=$img_path?>/btn_good2.gif" border="0" onclick="mw_good_act('good')" style="cursor:pointer;"></a></div>
        <div class="gh">
            <div class="gg"><span><?=number_format($write[wr_good])?></span></div>
            <div class="gn"><span><?=number_format($write[wr_nogood])?></span></div>
        </div>
        <div class="bn"><img src="<?=$img_path?>/btn_nogood2.gif" border="0" onclick="mw_good_act('nogood')" style="cursor:pointer;"></div>
    </div>
    </div>

<?php
}
else {
    if (!$board[bo_use_good] || !$board[bo_use_nogood])
        $good_box_width = 100;
    else
        $good_box_width = 260;
?>

    <style type="text/css">
    .good-box { margin:0 auto 0 auto; text-align:center; }
    .good-box .in { text-align:center; height:30px; margin:0 auto 0 auto; }
    .good-box span { float:left; margin:5px 0 0 27px; font-size:12px; font-weight:bold; color:#fff; }
    .good-box .gg { width:100px; height:30px; display:inline-block; cursor:pointer; background:url(<?=$img_path?>/btn_good.gif); }
    .good-box button { margin-right:7px; font-size:12px; }
    .good-box button div { padding:5px; }
    .good-box button i { font-size:15px; margin-right:10px;}
    .good-box .gn { width:100px; height:30px; display:inline-block; cursor:pointer; background:url(<?=$img_path?>/btn_nogood.gif); margin:0 0 0 10px; }
    </style>
    <div class="good-box"><div class="in">
        <?php if ($board[bo_use_good]) { ?>
            <?php if (!$_GET['width']) { ?>
            <button class="fa-button" onclick="mw_good_act('good')">
                <div><i class="fa fa-thumbs-up"></i> 추천하기 : <?=number_format($write[wr_good])?></div></button>
            <?php } else { ?>
            <div class="gg" onclick="mw_good_act('good')"><span>추천 : <?=number_format($write[wr_good])?></span></div>
            <?php } ?>
        <?php } ?>
        <?php if ($board[bo_use_nogood]) { ?>
            <?php if (!$_GET['width']) { ?>
            <button class="fa-button" onclick="mw_good_act('nogood')">
                <div><i class="fa fa-thumbs-down"></i> 다른의견 : <?=number_format($write[wr_nogood])?></div></button>
            <?php } else { ?>
            <div class="gn" onclick="mw_good_act('nogood')"><span>비추 : <?=number_format($write[wr_nogood])?></span></div>
            <?php } ?>
        <?php } ?>
    </div></div>

<? } ?>

