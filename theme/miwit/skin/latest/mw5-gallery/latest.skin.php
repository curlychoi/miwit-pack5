<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

$css_name = "gallery-{$bo_table}-{$rows}-{$subject_len}";

$css = "{$latest_skin_url}/style.php?bo_table={$bo_table}&rows={$rows}&subject_len={$subject_len}";
add_stylesheet("<link rel=\"stylesheet\" href=\"{$css}\">");

if (function_exists("mw_seo_url"))
    $bo_url = mw_seo_url($bo_table);
else 
    $bo_url = G5_BBS_URL."/board.php?bo_table=".$bo_table;

// $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);
// mw_get_thumb_path
// function mw_get_thumb_path($bo_table, $wr_id, $file=null, $thumb_number=null)

global $g4;
?>
<div class="<?php echo $css_name?>">
    <h2><a href="<?php echo $bo_url?>"><?php echo $bo_subject?></a></h2>
    <?php
    echo "<ul>";

    for ($i=0; $i<$rows; $i++) {

        $thumb = mw_get_thumb_path($bo_table, $list[$i]['wr_id']);
        $img = '<img src="'.$thumb.'">';

        if ($member['mb_id']) {
            $list[$i]['subject'] = str_replace("{닉네임}", $member['mb_nick'], $list[$i]['subject']);
            $list[$i]['subject'] = str_replace("{별명}", $member['mb_nick'], $list[$i]['subject']);
        }
        else {
            $list[$i]['subject'] = str_replace("{닉네임}", "회원", $list[$i]['subject']);
            $list[$i]['subject'] = str_replace("{별명}", "회원", $list[$i]['subject']);
        }

        $class = '';
        if ($list[$i]['icon_secret'])
            $class.= " secret";

        if ($list[$i]['icon_new'])
            $class.= " new";

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
</div><!--<?php echo $css_name?>-->
<div style="font-size:0; line-height:0; clear:both; height:10px;"></div>

