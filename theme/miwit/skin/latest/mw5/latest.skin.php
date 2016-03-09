<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

global $member;

$css_name = "mw5-{$bo_table}-{$rows}-{$subject_len}";

$css = "{$latest_skin_url}/style.php?bo_table={$bo_table}&rows={$rows}&subject_len={$subject_len}";
add_stylesheet("<link rel=\"stylesheet\" href=\"{$css}\">");

if (function_exists("mw_seo_url"))
    $bo_url = mw_seo_url($bo_table);
else 
    $bo_url = G5_BBS_URL."/board.php?bo_table=".$bo_table;
?>
<div class="<?php echo $css_name?>">
    <h2><a href="<?php echo $bo_url?>"><?php echo $bo_subject?></a></h2>
    <?php
    echo "<ul>";
    for ($i=0; $i<$rows; ++$i) {
        //if (rand(1,$rows) == 1) $list[$i]['subject'] = "<strong>{$list[$i]['subject']}</strong>";

        $class = '';
        if ($list[$i]['icon_secret'] or $list[$i]['wr_singo_lock'] or $list[$i]['wr_view_lock']) {
            $class.= " secret";
        }

        if ($list[$i]['icon_new'])
            $class.= " new";

        if (function_exists("mw_seo_url"))
            $href = mw_seo_url($bo_table, $list[$i]['wr_id']);
        else 
            $href = $list[$i]['href'];

        echo "<li class=\"{$class}\">";
        echo "<a href=\"{$href}\">";
        echo $list[$i]['subject'];
        echo "<span class=\"comment\">{$list[$i]['comment_cnt']}</span>";
        echo "</a>";
        echo "</li>\n";
    }
    echo "</ul>";
    ?>
</div><!--<?php echo $css_name?>-->

