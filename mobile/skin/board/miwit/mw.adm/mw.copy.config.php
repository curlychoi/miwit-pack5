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

$g4[title] = "배추 BASIC SKIN 관리자";
include_once("$g4[path]/head.sub.php");

$sql = " select * 
           from $g4[board_table] a, 
                $g4[group_table] b, 
                $mw[basic_config_table] c
          where c.gr_id = b.gr_id and a.bo_table = c.bo_table  ";
$sql .= " order by c.gr_id, c.bo_table ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) 
{
    $list[$i] = $row;
}

?>

<table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td>

<table width="100%" height="50" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td align="center" valign="middle" bgcolor="#EBEBEB" style="padding:5px;">
        <table width="100%" height="40" border="0" cellspacing="0" cellpadding="0">
        <tr> 
            <td width="25" align="center" bgcolor="#FFFFFF" ><img src="<?=$g4[bbs_img_path]?>/icon_01.gif" width="5" height="5"></td>
            <td width="" align="left" bgcolor="#FFFFFF" ><font color="#666666"><b>배추 베이직 스킨 환경설정 복사</b></font></td>
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
    <td width="" align="left" valign="middle">
        ※ 다른 게시판의 환경설정을 이 게시판으로 복사합니다.<br/>
        ※ 설정을 복사해 올 게시판을 한개만 선택하여 주십시오.
    </td>
    <td width="30" height="24"></td>
</tr>
</table>

<form name="fboardmoveall" method="post" onsubmit="return fboardmoveall_submit(this);">
<input type=hidden name=sw          value='<?=$sw?>'>
<input type=hidden name=bo_table    value='<?=$bo_table?>'>
<input type=hidden name=wr_id_list  value="<?=$wr_id_list?>">
<input type=hidden name=sfl         value='<?=$sfl?>'>
<input type=hidden name=stx         value='<?=$stx?>'>
<input type=hidden name=spt         value='<?=$spt?>'>
<input type=hidden name=page        value='<?=$page?>'>
<input type=hidden name=act         value='<?=$act?>'>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr> 
    <td height="20" align="center" valign="top">&nbsp;</td>
</tr>
<tr>
    <td align="center" valign="top">
        <table width="98%" border="0" cellspacing="0" cellpadding="0">
        
        <? for ($i=0; $i<count($list); $i++) { ?>
        <tr> 
            <td width="39" height="25" align="center"><input type=radio id='chk<?=$i?>' name='chk_bo_table' value="<?=$list[$i][bo_table]?>" style="cursor:pointer"></td>
            <td width="10" valign="bottom"><img src="<?=$g4[bbs_img_path]?>/l.gif" width="1" height="8"></td>
            <td width="490">
                <span style="cursor:pointer;" onclick="document.getElementById('chk<?=$i?>').checked=document.getElementById('chk<?=$i?>').checked?'':'checked';">
                    <?
                    if ($save_gr_subject==$list[$i][gr_subject])
                        echo "<span style='color:#cccccc;'>";
                    else
                        echo "<span>";
                    echo $list[$i][gr_subject] . " > ";
                    echo "</span>";
                    $save_gr_subject = $list[$i][gr_subject];
                    ?>
                    <?=$list[$i][bo_subject]?> (<?=$list[$i][bo_table]?>)</span>
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
</table>

</form>

<script type='text/javascript'>
function fboardmoveall_submit(f)
{
    var check = false;

    if (typeof(f.elements['chk_bo_table']) == 'undefined') 
        ;
    else {
        if (typeof(f.elements['chk_bo_table'].length) == 'undefined') {
            if (f.elements['chk_bo_table'].checked) 
                check = true;
        } else {
            for (i=0; i<f.elements['chk_bo_table'].length; i++) {
                if (f.elements['chk_bo_table'][i].checked) {
                    check = true;
                    break;
                }
            }
        }
    }

    if (!check) {
        alert('설정을 복사할 게시판을 선택해 주십시오.');
        return false;
    }

    document.getElementById("btn_submit").disabled = true;

    f.action = "mw.copy.config.update.php";
    return true;
}
</script>

</td></tr></table>

<?
include_once("$g4[path]/tail.sub.php");
?>
