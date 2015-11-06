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
include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");

header("Content-Type: text/html; charset=".$g4['charset']);

if ($is_admin != 'super')
    die("로그인 해주세요.");

if (!$bo_table)
    die("bo_table 값이 없습니다.");

if (!$token or get_session("ss_config_token") != $token) 
    die("토큰 에러로 실행 불가합니다.");

//$su_old = urldecode($_POST['su_old']);
//$su_new = urldecode($_POST['su_new']);

$su_old = str_replace("\\\\", "\\", $su_old);

if ($su_regex) {
    $sql = "select wr_id, wr_subject from {$write_table} where wr_subject regexp '{$su_old}'";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $wr_subject = preg_replace("/{$su_old}/", $su_new, $row['wr_subject']);
        $wr_subject = addslashes($wr_subject);
        $sql = "update {$write_table} set wr_subject = '{$wr_subject}' where wr_id = '{$row['wr_id']}' ";
        sql_query($sql);
    }
}
else {
    $sql = "update {$write_table} set wr_content = replace (wr_content, '{$su_old}', '{$su_new}')";
    $qry = sql_query($sql);
}

echo "변경을 완료하였습니다.";
