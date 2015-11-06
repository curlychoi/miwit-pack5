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

$g4[title] = "분류이동";
include_once("$g4[path]/head.sub.php");

$wr_id_list = "";
if ($wr_id)
    $wr_id_list = $wr_id;
else {
    $comma = "";
    //for ($i=0; $i<count($_POST[chk_wr_id]); $i++) {
    for ($i=0; $i<count($chk_wr_id); $i++) {
        //$wr_id_list .= $comma . "'" . $_POST[chk_wr_id][$i] . "'";
        $wr_id_list .= $comma . "'" . $chk_wr_id[$i] . "'";
        $comma = ",";
    }
}

$category_list = explode("|", $board[bo_category_list]);

?>
<table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td>

<table width="100%" height="50" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td align="center" valign="middle" bgcolor="#EBEBEB" style="padding:5px;">
        <table width="100%" height="40" border="0" cellspacing="0" cellpadding="0">
        <tr> 
            <td width="25" align="center" bgcolor="#FFFFFF" ><img src="<?=$g4[bbs_img_path]?>/icon_01.gif" width="5" height="5"></td>
            <td width="" align="left" bgcolor="#FFFFFF" ><font color="#666666"><b>게시물 분류이동</b></font></td>
        </tr>
        </table></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr> 
    <td height="20" colspan="3"></td>
</tr>
<tr> 
    <td width="30" height="24"></td>
    <td width="" align="left" valign="middle">※ 이동할 분류를 한개만 선택하여 주십시오.</td>
    <td width="30" height="24"></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<!--<form name="fboardmoveall" method="post" action='javascript:fboardmoveall_submit(document.fboardmoveall);'>-->
<form name="fboardmoveall" method="post" action="mw.move.cate.update.php" onsubmit="return fboardmoveall_submit(document.fboardmoveall);">
<input type=hidden name=bo_table    value='<?=$bo_table?>'>
<input type=hidden name=wr_id_list  value="<?=$wr_id_list?>">
<tr> 
    <td height="20" align="center" valign="top">&nbsp;</td>
</tr>
<tr>
    <td align="center" valign="top">
        <table width="98%" border="0" cellspacing="0" cellpadding="0">
        
        <? for ($i=0; $i<count($category_list); $i++) { ?>
	<? if (!trim($category_list[$i])) continue; ?>
        <tr> 
            <td width="39" height="25" align="center"><input type=radio id='chk<?=$i?>' name='chk_category' value="<?=$category_list[$i]?>"></td>
            <td width="10" valign="bottom"><img src="<?=$g4[bbs_img_path]?>/l.gif" width="1" height="8"></td>
            <td width="490">
                <span style="cursor:pointer;" onclick="document.getElementById('chk<?=$i?>').checked=document.getElementById('chk<?=$i?>').checked?'':'checked';">
		    <?=$category_list[$i]?> </span>
            </td>
        </tr>
        <tr> 
            <td height="1" colspan="3" bgcolor="#E9E9E9"></td>
        </tr>
        <? } ?>
        </table></td>
</tr>
<tr> 
    <td height="40">&nbsp;</td>
</tr>
<tr> 
    <td height="2" bgcolor="#D5D5D5"></td>
</tr>
<tr> 
    <td height="2" bgcolor="#E6E6E6"></td>
</tr>
<tr> 
    <td height="40" align="center" valign="bottom"><input id="btn_submit" type=image src='<?=$g4[bbs_img_path]?>/ok_btn.gif' border=0>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="<?=$g4[bbs_img_path]?>/btn_close.gif" width="48" height="20" border="0"></a></td>
</tr>
</form>
</table>

<script type="text/javascript">
function fboardmoveall_submit(f)
{
    var check = false;

    if (typeof(f.elements['chk_category']) == 'undefined') 
        ;
    else {
        if (typeof(f.elements['chk_category'].length) == 'undefined') {
            if (f.elements['chk_category'].checked) 
                check = true;
        } else {
            for (i=0; i<f.elements['chk_category'].length; i++) {
                if (f.elements['chk_category'][i].checked) {
                    check = true;
                    break;
                }
            }
        }
    }

    if (!check) {
        alert('게시물 분류를 한개만 선택해 주십시오.');
        return false;
    }

    document.getElementById("btn_submit").disabled = true;

    //f.action = "./mw.move.cate.update.php";
    //f.submit();
    return true;
}
</script>

</td></tr></table>


<?
include_once("$g4[path]/tail.sub.php");
?>
