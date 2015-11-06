<?
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

// 게시판 관리자 이상 복사, 이동 가능
if (!($member[mb_id] && ($is_admin == "super" || $group[gr_admin] == $member[mb_id] || $board[bo_admin] == $member[mb_id]))) 
    alert_close("게시판 관리자 이상 접근이 가능합니다.");

if (!$chk_category) alert("분류를 선택해주세요.");

$sql = "update $write_table set ca_name = '$chk_category' where wr_parent in (".stripslashes($wr_id_list).")";
$qry = sql_query($sql);

$g4['title'] = "분류이동";
include_once("$g4[path]/head.sub.php");
?>
<script type="text/javascript">
alert("분류이동 하였습니다.");
opener.location.reload();
self.close();
</script>
<?
include_once("$g4[path]/tail.sub.php");
?>
