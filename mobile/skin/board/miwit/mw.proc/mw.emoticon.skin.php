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

$emoticon_path = "$g4[url]/skin/board/$board[bo_skin]/mw.emoticon";

$g4[title] = "이모티콘 입력";
$viewport = "<meta name=\"viewport\" content=\"width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0\">";
ob_start();
include_once("$g4[path]/head.sub.php");
$head = ob_get_clean();
$head = str_replace("<head>", "<head>\n{$viewport}", $head);
echo $head;
?>

<script type="text/javascript">
function add(img) {
    opener.document.getElementById("wr_content").value += "\n[" + img + "]\n";
    self.close();
}
</script>


<? for ($i=1; $i<=93; $i++) {?>
<? $img = "$emoticon_path/em{$i}.gif"; ?>

<span style="margin:10px;"> <a href="javascript:add('<?=$img?>');"><img src="<?=$img?>"></a> </span>

<? } ?>



<div style="margin:10px 0 0 10px;">
이모티콘 출처 : http://blog.roodo.com/onion_club
</div>

<?
include_once("$g4[path]/tail.sub.php");
?>
