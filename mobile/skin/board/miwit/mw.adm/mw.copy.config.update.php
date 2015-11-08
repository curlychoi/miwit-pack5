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
include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if ($is_admin != "super")
    alert_close("접근 권한이 없습니다.");

$sql = "desc $mw[basic_config_table] ";
$qry = sql_query($sql);
while ($row = sql_fetch_array($qry)) {
    if ($row[Field] == 'gr_id') continue;
    if ($row[Field] == 'bo_table') continue;
    $list[] = $row[Field];
}

$sql = "select * from $mw[basic_config_table] where bo_table = '$chk_bo_table'";
$row = sql_fetch($sql);

$sql = " update $mw[basic_config_table] set ";
for ($i=0, $m=count($list); $i<$m; $i++) {
    $sql .= " {$list[$i]} = '".addslashes($row[$list[$i]])."' ";
    if ($i<$m-1) $sql .= ", ";
}
$sql .= " where bo_table = '$bo_table' ";

sql_query($sql);

mw_basic_write_config_file();

?>
<script type="text/javascript">
alert("환경설정을 복사했습니다.");
opener.location.reload();
self.close();
</script>
