<?
/**
 * 스마트알람 (Smart-Alarm for Gnuboard4)
 *
 * Copyright (c) 2011 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 
?>
<style type="text/css">
#moa  { margin:10px; color:#444; }
#moa ul { margin:0; padding:0; list-style:none; }
#moa ul li { margin:0; padding:5px 0 0 0; height:47px; *height:53px; clear:both; cursor:pointer; }
#moa ul li { background-color:#fff; color:#444; border-bottom:1px solid #e6e6e6; }
#moa ul li span.name { font-weight:bold; color:#3b5998; }
#moa ul li div {  }
#moa ul li div.msg { margin:5px 0 0 0; }
#moa ul li div.time { margin:5px 0 0 0; font-size:11px; }
#moa ul li div.image { float:left; width:42px; height:42px; margin:0 10px 0 5px; background-color:#f2f2f2; }
#moa ul li div.image img { float:left; width:38px; height:38px; margin:2px 0 0 2px; border:0; background-color:#fff; }
</style>

<div id="moa">
<ul>
<? for ($i=0; $i<$list_count; $i++) { ?>
<li class="item" onclick="location.href='<?=$list[$i][href]?>'">
    <div class="image"><img src="<?=$list[$i][comment_image]?>"></div>
    <div class="msg"><?=$list[$i][msg]?></div>
    <div class="time"><?=$list[$i][time]?></div>
</li>
<? } ?>
</ul>
</div>

<script type="text/javascript">
moa_font_color = '#444';
moa_name_color = '#3B5998';
moa_over_color = '#6d84b4';
moa_bg_color = '#fff';

$('#moa .item').mouseover(function () {
    $(this).css('background-color', moa_over_color);
    $(this).css('color', moa_bg_color);
    $(this).find('.name').css('color', moa_bg_color);
});
$('#moa .item').mouseout(function () {
    $(this).css('background-color', moa_bg_color);
    $(this).css('color', moa_font_color);
    $(this).find('.name').css('color', moa_name_color);
});
</script>

