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
    for ($a=0; $a<$rows/5; $a++) {
        echo "<ul>";

        $s = $a*5;
        $e = ($a+1)*5;
        $r = rand($s, $e-1);
        for ($i=$s; $i<$e; $i++) {
            if ($r == $i)
                $list[$i]['subject'] = "<strong>{$list[$i]['subject']}</strong>";

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
            echo $list[$i]['subject'];
            echo "<span class=\"comment\">{$list[$i]['comment_cnt']}</span>";
            echo "</a>";
            echo "</li>\n";
        }
        echo "</ul>";
    }
    ?>
</div><!--<?php echo $css_name?>-->

