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

$gmnow = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: 0"); // rfc2616 - Section 14.21
header("Last-Modified: " . $gmnow);
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="<?=$g4['charset']?>">
<title><?=$g4['title']?></title>
<script>
// 자바스크립트에서 사용하는 전역변수 선언
var g4_path      = "<?=$g4['path']?>";
var g4_bbs       = "<?=$g4['bbs']?>";
var g4_bbs_img   = "<?=$g4['bbs_img']?>";
var g4_url       = "<?=$g4['url']?>";
var g4_is_member = "<?=$is_member?>";
var g4_is_admin  = "<?=$is_admin?>";
var g4_bo_table  = "<?=isset($bo_table)?$bo_table:'';?>";
var g4_sca       = "<?=isset($sca)?$sca:'';?>";
var g4_charset   = "<?=$g4['charset']?>";
var g4_cookie_domain = "<?=$g4['cookie_domain']?>";
var g4_is_gecko  = navigator.userAgent.toLowerCase().indexOf("gecko") != -1;
var g4_is_ie     = navigator.userAgent.toLowerCase().indexOf("msie") != -1;
<? if ($is_admin) { echo "var g4_admin = '{$g4['admin']}';"; } ?>
</script>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<link href="//code.jquery.com/ui/1.11.2/themes/humanity/jquery-ui.css" rel="stylesheet" />
<style type="text/css">
.ci_title { font-size:15px; font-weight:bold; background-color:#efefef; padding:10px; }
.ci_info { margin:20px 0 0 20px; line-height:20px; font-size:13px; }
.ci_file { border-top:1px solid #9a9a9a; border-left:1px solid #9a9a9a; border-right:1px solid #d8d8d8; border-bottom:1px solid #d8d8d8; height:20px; font-size:12px; padding:2px; }
.ci_button { background-color:#efefef; cursor:pointer; }
.ci_buttons { text-align:center; margin:20px 0 0 0; height:50px; }
a { color:#000; }
</style>
</head>
<body topmargin="0" leftmargin="0" <?=isset($g4['body_script']) ? $g4['body_script'] : "";?>>
<a name="g4_head"></a>

<div class="ci_title"> ▶ 파일다운로드 </div>

<div class="ci_info">
    <?=$mw_basic[cf_download_popup_msg]?>

    <div class="ci_buttons">
        <input type="button" class="ci_button" value="파일 다운로드"
            onclick="location.href='<?=$g4[bbs_path]?>/download.php?bo_table=<?=$bo_table?>&wr_id=<?=$wr_id?>&no=<?=$no?>'">
    </div>

</div>

<!-- 새창 대신 사용하는 iframe -->
<iframe width=0 height=0 name='hiddenframe' style='display:none;'></iframe>

</body>
</html>

