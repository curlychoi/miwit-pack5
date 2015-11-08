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

header("Content-Type: text/html; charset=".$g4['charset']);

if ($is_admin != 'super')
    die("로그인 해주세요.");

if (!$bo_table && !$wr_id && !$num)
    die("파라메타가 잘못되었습니다.");

include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");

//echo $write['wr_link'.$num];exit;

mw_get_youtube_thumb($wr_id, $write['wr_link'.$num]);

echo "완료";
