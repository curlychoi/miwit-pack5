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

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if ($wr_id) return;
?>
    <span class="mw_basic_total">
    <button class="fa-button">총 게시물 <?=number_format($total_count)?>건,
    최근 <?=number_format($new_count)?> 건</button>
    <?php include($board_skin_path."/mw.proc/mw.smart-alarm-config.php") ?>
    </span>

    <?php if ($mw_basic[cf_social_commerce]) { ?>
    <button class="fa-button" onclick="window.open('<?=$social_commerce_path?>/order_list.php?bo_table=<?=$bo_table?>',
        'order_list', 'width=800,height=600,scrollbars=1');"><i class="fa fa-shopping-cart"></i> 주문내역</button>
    <?php } ?>

    <?php if ($mw_basic[cf_talent_market]) { ?>
    <button class="fa-button" onclick="window.open('<?=$talent_market_path?>/order_list.php?bo_table=<?=$bo_table?>',
        'order_list', 'width=800,height=600,scrollbars=1');"><i class="fa fa-shopping-cart"></i> 주문내역</button>
    <?php } ?>

    <?php if ($is_admin && $mw_basic[cf_collect] == 'rss' && file_exists("$g4[path]/plugin/rss-collect/_lib.php")) {?>
    <button class="fa-button" onclick="window.open('<?=$g4[path]?>/plugin/rss-collect/config.php?bo_table=<?=$bo_table?>',
        'rss_collect', 'width=800,height=600,scrollbars=1')"><i class="fa fa-wifi"></i> RSS수집</button>
    <?php } ?>

    <?php if ($is_admin && $mw_basic[cf_collect] == 'youtube' && file_exists("$g4[path]/plugin/youtube-collect/_lib.php")) {?>
    <button class="fa-button" onclick="window.open('<?=$g4[path]?>/plugin/youtube-collect/config.php?bo_table=<?=$bo_table?>',
        'youtube_collect', 'width=800,height=600,scrollbars=1')"><i class="fa fa-youtube"></i> 유투브</button>
    <?php } ?>

    <?php if ($is_admin && $mw_basic[cf_collect] == 'kakao' && file_exists("$g4[path]/plugin/kakao-collect/_lib.php")) {?>
    <button class="fa-button" onclick="window.open('<?=$g4[path]?>/plugin/kakao-collect/config.php?bo_table=<?=$bo_table?>',
        'kakao_collect', 'width=800,height=600,scrollbars=1')"><i class="fa fa-wifi"></i> 카카오</button>
    <?php } ?>

    <?php if ($is_admin && $mw_basic[cf_collect] == 'instagram' && file_exists("$g4[path]/plugin/instagram-collect/_lib.php")) {?>
    <button class="fa-button" onclick="window.open('<?=$g4[path]?>/plugin/instagram-collect/config.php?bo_table=<?=$bo_table?>',
        'instagram_collect', 'width=800,height=600,scrollbars=1')"><i class="fa fa-instagram"></i> 인스타그램</button>
    <?php } ?>

    <?php if ($mw_basic[cf_social_commerce] && $rss_href && file_exists("$social_commerce_path/img/xml.png")) { ?>
    <button onclick="location.href='<?=$social_commerce_path?>/xml.php?bo_table=<?=$bo_table?>'" class="fa-button">
    <i class="fa fa-rss"></i> XML</button>
    <?php } else if ($rss_href) { ?>
    <button onclick="location.href='<?=$rss_href?>'" class="fa-button">
    <i class="fa fa-rss"></i> RSS</button><?php }?>

    <?php if ($is_admin == "super") { ?>
    <button onclick="mw_config()" class="fa-button"><i class="fa fa-gear"></i> 설정</button><?php } ?>

    <?php if ($admin_href) { ?>
    <button onclick="location.href='<?=$admin_href?>'" class="fa-button">
    <i class="fa fa-wrench"></i> 관리자</button><?php } ?>

    <?php if (!$wr_id && $write_href) { ?>
    <button class="fa-button primary" onclick="location.href='<?php echo $write_href?>'">
    <i class="fa fa-pencil"></i> 글쓰기</button><?php } ?>

