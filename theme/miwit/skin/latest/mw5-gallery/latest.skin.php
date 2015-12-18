<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

global $member;

$css_name = "gallery-{$bo_table}-{$rows}-{$subject_len}";

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

    for ($i=0; $i<$rows; $i++) {

        $thumb = mw_get_thumb_path($bo_table, $list[$i]['wr_id']);
        $img = '<img src="'.$thumb.'">';

        $list[$i] = mw_get_list($list[$i]);

        if (!$thumb)
            $img = '<div class="noimage"></div>';

        if ($member['mb_id']) {
            $list[$i]['subject'] = str_replace("{닉네임}", $member['mb_nick'], $list[$i]['subject']);
            $list[$i]['subject'] = str_replace("{별명}", $member['mb_nick'], $list[$i]['subject']);
        }
        else {
            $list[$i]['subject'] = str_replace("{닉네임}", "회원", $list[$i]['subject']);
            $list[$i]['subject'] = str_replace("{별명}", "회원", $list[$i]['subject']);
        }

        $class = '';
        if ($list[$i]['icon_secret'] or $list[$i]['wr_singo_lock'] or $list[$i]['wr_view_lock']) {
            $class.= " secret";
            $img = '<div class="noimage"><i class="fa fa-lock"></i></div>';
        }

        if ($list[$i]['icon_new']) {
            $class.= " new";
        }

        if (function_exists("mw_seo_url"))
            $href = mw_seo_url($bo_table, $list[$i]['wr_id']);
        else 
            $href = $list[$i]['href'];

        echo "<li class=\"{$class}\">";
        echo "<a href=\"{$href}\">";
        echo '<div class="thumb">'.$img.'</div>';
        echo '<div class="title">'.$list[$i]['subject']."";
            echo "<span class=\"comment\">{$list[$i]['comment_cnt']}</span>";
            echo '</div>';
        echo "</a>";
        echo "</li>\n";
    }
    echo "</ul>";
    ?>
    <div class="clear"></div>
</div><!--<?php echo $css_name?>-->
<div class="clear"></div>

