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

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// 배추컨텐츠샵 맴버쉽
// 컨텐츠샵 멤버쉽
if (function_exists("mw_cash_is_membership"))
{
    $is_membership = @mw_cash_is_membership($member[mb_id], $bo_table);

    if ($is_membership != "no") {
?>

<script type="text/javascript" src="<?=$mw_cash[path]?>/cybercash.js"></script>
<script type="text/javascript">
var mw_cash_path = "<?=$mw_cash[path]?>";
</script>

<div id="membership-info">
    <?
    $edate = mw_cash_membership_end_date($bo_table, $member[mb_id]);
    if (!$edate) {
        $cash_button = '결 제';
        ?>
        <strong><?=$is_membership?></strong> 회원만 이용가능한 게시판 입니다.
        <?
    }
    else
    {
        $cash_button = '연장 결제';
        $end_time = strtotime($edate) - $g4[server_time];
        ?>
        <strong>이용 만기일</strong> : <?=date("Y년 m월 d일", strtotime($edate))?>,
        <span id=end_timer></span> 남음

        <script type="text/javascript">
        var end_time = <?=$end_time?>;
        function membership_end_timer()
        {
            var timer = document.getElementById("end_timer");

            dd = Math.floor(end_time/(60*60*24));
            hh = Math.floor((end_time%(60*60*24))/(60*60));
            mm = Math.floor(((end_time%(60*60*24))%(60*60))/60);
            ii = Math.floor((((end_time%(60*60*24))%(60*60))%60));

            var str = "";

            if (dd > 0) str += dd + "일 ";
            if (hh > 0) str += hh + "시간 ";
            if (mm > 0) str += mm + "분 ";
            str += ii + "초 ";

            timer.style.color = "#FF0000";
            timer.style.fontWeight = "bold";
            timer.innerHTML = str;

            end_time--;

            if (end_time < 0) {
                clearInterval(tid);
                timer.innerHTML = "종료되었습니다.";
            }
        }
        membership_end_timer();
        tid = setInterval('membership_end_timer()', 1000); 
        </script>
    <? } ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" value="<?=$cash_button?>" onclick="buy_membership('<?=$bo_table?>')" style="cursor:pointer; background-color:#efefef; font-size:11px; font-family:dotum;">
</div>

<? } } ?>

