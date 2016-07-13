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

include_once("$g4[path]/head.sub.php");
?>
<script type="text/javascript" src="<?=$board_skin_path?>/mw.js/prototype-1.6.0.2.js"></script>

<link rel="stylesheet" href="<?=$board_skin_path?>/style.common.css" type="text/css">

<style type="text/css">
#mw_basic a { color:#000; text-decoration:none; }
#mw_basic .cf_head { padding:10px; background-color:#bed8e4; }
#mw_basic .cf_sub_menu { height:30px; background-color:#6B95BD; }
#mw_basic .cf_sub_menu .item { padding:5px 10px 0 10px; } 
#mw_basic .cf_sub_menu .tab { float:left; height:25px; background-color:#6B95BD; margin:5px 0 0 0; } 
#mw_basic .cf_sub_menu .tab a { text-decoration:none; }
#mw_basic .cf_sub_menu .select { float:left; height:25px; background-color:#EFEFEF; margin:5px 0 0 0; }
#mw_basic .cf_sub_menu .select a { text-decoration:none; color:#4B4B4B; font-weight:bold; }
#mw_basic .cf_title { width:140px; height:30px; padding:0 0 0 10px; background-color:#efefef; }
#mw_basic .cf_content { padding:0 0 0 10px; line-height:20px; color:#747474; } 
#mw_basic .cf_content a { color:#747474; } 
#mw_basic .cf_content td { color:#747474; }
</style>

<div id=mw_basic>

<div class=cf_head>
    <a href="http://miwit.kr" target=_blank><img src="<?=$board_skin_path?>/img/logo_curlychoi.gif" align=absmiddle></a>
    &nbsp;&nbsp;
    <strong>배추 BASIC 스킨 설정</strong> : <?=$board[bo_subject]?> (<?=$bo_table?>)
</div>

<div class=cf_sub_menu>
<div class=<?=$admin_menu[config]?>> <div class=item><a href="mw.config.php?bo_table=<?=$bo_table?>" >기본환경설정</a></div> </div>
<div class=<?=$admin_menu[board_member]?>> <div class=item><a href="mw.board.member.php?bo_table=<?=$bo_table?>">접근권한설정</a></div> </div>
</div>

