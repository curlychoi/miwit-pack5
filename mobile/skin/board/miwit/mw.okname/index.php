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
include_once("$g4[path]/head.sub.php");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if ($mw_basic[cf_kcb_comment] && is_okname()) {
    ?>
    <script type="text/javascript">
    opener.location.reload();
    self.close();
    </script>
    <?
}

if (!$mw_basic[cf_kcb_id]) die("KCB ID 를 입력해주세요.");

echo "<link rel='stylesheet' href='$board_skin_path/style.common.css' type='text/css'>\n";
echo "<style type='text/css'> #mw_basic { display:none; } </style>\n";

$req_file = null;

if ($mw_basic[cf_kcb_type] == "19ban")
    $req_file = "$board_skin_path/mw.proc/mw.19ban.php"; // 19금
else
    $req_file = "$board_skin_path/mw.proc/mw.okname.php"; // 실명인증

if (file_exists($req_file)) require($req_file);

