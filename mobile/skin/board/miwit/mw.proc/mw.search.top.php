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

if (!$is_admin && !$mw_basic['cf_search_level_view'] && (!$mw_basic['cf_search_level'] || $mw_basic['cf_search_level'] > $member['mb_level'])) return;

if (!$mw_basic['cf_search_top']) return;
?>
<div id="search_top">
    <form name=fsearchtop method=get action="<?php echo $g4['bbs_path']?>/board.php">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table?>">
        <input type="hidden" name="sca" value="<?php echo $sca?>">
        <input type="hidden" name="sfl" value="wr_subject||wr_content">
        <input type="hidden" name="sop" value="and">
        <div class="search_top_box">
            <input type="text" name="stx" maxlength="50" itemname="검색어" required
                value="<?php echo $stx?>" class="search_top_stx">
            <input type="submit" value="검색" class="search_top_button">
        </div>
    </form>
</div>

