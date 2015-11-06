<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

global $mwus;
global $theme_path;

if (function_exists("mw_css")) {
    //mw_css($popular_skin_url.'/style.php');
    add_stylesheet("<link rel=\"stylesheet\" href=\"{$popular_skin_url}/style.php\">");
    mw_script($theme_path.'/js/mw.slider.js');
}
else {
    add_stylesheet("<link rel=\"stylesheet\" href=\"{$popular_skin_url}/style.php\">");
    add_javascript("<script src=\"".G5_URL."/js/mw.slider.js\"></script>");
}

$href = G5_BBS_URL."/search.php?stx=";
if ($mwus['path'])
    $href = $mwus['path']."/?stx=";
?>
<div class="scroll">
<ul>
<?php
for ($i=0; $i<$pop_cnt; $i++) {
    if (!is_array($list[$i]))
        continue;

    $str = urlencode($list[$i]['pp_word']);

    $gap = '';
    if ($list[$i][icon] != "new" && $list[$i][icon] != "nogap")
        $gap = abs($list[$i]['rank_gap']);

    $r = $i + 1;

    echo "<li class=\"rank{$r}\">";
    echo "<a href=\"{$href}{$str}\">{$list[$i]['pp_word']}</a>";
    echo "<span class=\"arrow {$list[$i]['icon']}\">{$gap}</span>";
    echo "</li>\n";
}
?>
</ul>
</div>

<script>
$(document).ready(function () {
    var html = $(".scroll").html();
    $(".scroll").parent().append("<div class='popular-hover'></div>");

    var $hover = $(".scroll").parent().find(".popular-hover");
    $hover.html(html);
    $(".scroll").mouseover(function () {
        $hover.css("display", "block");
    });

    $hover.mouseleave(function () {
        $hover.css("display", "none");
    });

    $(".scroll").mw_slider();
});
</script>

