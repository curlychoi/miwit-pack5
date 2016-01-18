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

if (!$bo_table)
    die("bo_table 값이 없습니다.");

include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");

if ($is_admin != 'super')
    die("로그인 해주세요.");

if (!$token or get_session("ss_config_token") != $token) 
    die("토큰 에러로 실행 불가합니다.");

$table = '';
switch ($what) {
    case 'link': $table = $mw['link_log_table']; break;
    case 'down': $table = $mw['download_log_table']; break;
    case 'post': $table = $mw['post_history_table']; break;
    default:
        echo "what 값이 잘못되었습니다.";
        exit;
}

$sql = "delete from {$table} where bo_table = '{$bo_table}' ";
sql_query($sql);

echo "ok";

