<?php
/**
 * 배추 모바일 빌더 (Mobile for Gnuboard4)
 *
 * Copyright (c) 2010 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

$block = 5;

global $mw_latest_mobile_cnt;

if (!$mw_latest_mobile_cnt)
    $mw_latest_mobile_cnt = 0;

$mw_latest_mobile_cnt++;

$count = count($tab);
$style_name = "mw-latest-mobile-{$file_tables}-{$rows}-{$subject_len}-";

$page_gap = $rows/$block;
?>

<?php //if ($mw_latest_mobile_cnt == 1) { ?>
<style>
.<?php echo $style_name?>latest-swipe {
    margin:5px;
    padding:0px 0px 20px 0px;
    box-shadow: 0 1px #EBEBEB;
    border-radius: 3px;
    border: 1px solid;
    border-color: #E5E5E5 #D3D3D3 #B9C1C6;
    background-color:#fff;
}

@media screen and (min-width:500px) {
    .<?php echo $style_name?>latest-swipe {
        width:350px;
        float:left;
    }
}

.<?php echo $style_name?>latest-swipe .swipe {
    overflow: hidden;
    visibility: hidden;
    position: relative;
    display:block;
    font-weight:bold;
    color:#14ADE5;
    text-align:center;
    margin:7px 10px 0 10px;
    padding:0;
    box-shadow: 0 1px #EBEBEB;
    background: #fff;
    /*border-radius: 3px;
    border: 1px solid;
    border-color: #E5E5E5 #D3D3D3 #B9C1C6;*/
    color: #444;
    text-align:left;
    font-weight:normal;
}
.<?php echo $style_name?>latest-swipe .swipe a {
    color: #222;
    overflow:hidden;
    line-height:15px;
    text-decoration:none;
}
.<?php echo $style_name?>latest-swipe .swipe-wrap {
    overflow: hidden;
    position: relative;
}
.<?php echo $style_name?>latest-swipe .swipe-wrap > div {
    float:left;
    width:100%;
    position: relative;
}
.<?php echo $style_name?>latest-swipe .swipe-page div.item {
    display:block;
    font-size: 15px;
    line-height:30px;
    height:30px;
    border-bottom:1px solid #efefef;
    overflow:hidden;
    font-weight:normal;
}
.<?php echo $style_name?>latest-swipe .nav  {
    background-color:#fff;
    padding:10px 0 0 10px;
}
.<?php echo $style_name?>latest-swipe .nav > li.active > a {
    color:#000;
}
.<?php echo $style_name?>latest-swipe .nav > li > a {
    position: relative;
    display: block;
    padding:5px 10px 5px 10px;
    color:#000;
}
</style>
<?php //} ?>

<style>
.<?php echo $style_name?>latest-swipe .swipe-page div.thumb2 {
    padding:10px 0;
    height:200px;
    overflow:hidden;
    border:0;
    text-align:center;
}
.<?php echo $style_name?>latest-swipe .swipe-page div.thumb2 img {
    -webkit-border-radius:1em; -moz-border-radius:1em; border-radius:1em;
}
.<?php echo $style_name?>latest-swipe .swipe-page div.thumb2 div.img {
    display:inline;
    border:0;
    margin:0 auto 0 auto;
    width:85px;
    overflow:hidden;
    float:left;
    width:33%;
    text-align:center;
}
.<?php echo $style_name?>latest-swipe .swipe-page div.thumb2 div.cap {
    display:block;
    width:85px;
    height:30px;
    margin:0 auto 0 auto;
    overflow:hidden;
    font-size:11px;
    text-align:left;
    
}
.<?php echo $style_name?>latest-swipe .swipe-page div.thumb2 img {
    width:85px;
    height:60px;
    border:1px solid #ccc;
}
</style>

<script>
bo_url<?php echo $mw_latest_mobile_cnt?> = new Array();
</script>

<div class="<?php echo $style_name?>latest-swipe">

<ul id="swipe-menu<?php echo $mw_latest_mobile_cnt?>" class="nav nav-tabs">
    <?php
    for ($i=0; $i<$count; $i++) {
        echo "<li bo_table=\"{$tab[$bo_tables[$i]]['board']['bo_table']}\"";
        if ($i==0)
            echo " class=\"active\"";
        //echo "><a href=\"{$mw_mobile['path']}/board.php?bo_table={$tab[$bo_tables[$i]]['board']['bo_table']}\">";
        //$dbl = " ondblclick=\"location.href='".mw_seo_url($tab[$bo_tables[$i]]['board']['bo_table'])."'\"";
        $dbl = '';
        echo "><a href=\"javascript:mySwipe{$mw_latest_mobile_cnt}.slide(".($i*$page_gap).")\" {$dbl}>";
        echo "{$tab[$bo_tables[$i]]['board']['bo_subject']}</a></li>\n";
        echo "<script>bo_url{$mw_latest_mobile_cnt}['{$tab[$bo_tables[$i]]['board']['bo_table']}'] = '".mw_seo_url($tab[$bo_tables[$i]]['board']['bo_table'])."'</script>";
    }
    ?>
</ul>

<div id="swipe-box<?php echo $mw_latest_mobile_cnt?>" class="swipe">
<div class="swipe-wrap">
    <div class="swipe-page" id="swipe<?php echo $mw_latest_mobile_cnt?>-0" bo_table="<?php echo $tab[$bo_tables[0]]['board']['bo_table']?>">
        <?php
        $page = 1;
        $bo_count = 0;
        foreach ($tab as $bo_table => $list) {
            if ($bo_table == 'main') continue;
            $file = $tab[$bo_table]['file'];
            $board = $tab[$bo_tables[$j]]['board'];
            for ($i=0; $i<$rows; ++$i) {
                $row = $list[$i];
                $row[subject] = mw_builder_reg_str($row[subject]);
                //echo "<span class=\"glyphicon glyphicon-th-list\"></span> ";
                /*
                echo "<a href=\"./board.php?bo_table={$bo_table}&wr_id={$row['wr_id']}\">";
                echo "<div class=\"item\">";
                //echo "[{$board['bo_subject']}] ";
                echo "{$row['subject']}";
                if ($row['wr_comment'])
                    echo "&nbsp;<span class=\"comment\">+{$row['wr_comment']}</span>";
                echo "</div>\n";
                echo "</a>";*/
                if (($i+1)%$block==0) {
                    if ($is_img) {
                        echo "<div class=\"thumb2\">";
                        for ($j=floor($i/$block)*6, $m=count($file)/($rows/$block)*(($i+1)/$block); $j<$m; ++$j) {
                            $file[$j][subject] = mw_builder_reg_str($file[$j][subject]);
                            $href = mw_seo_url($bo_table, $file[$j]['wr_id']);
                            echo "<div class='img'><a href=\"{$href}\"><img src=\"{$file[$j]['path']}\" alt=\"\"></a>";
                            echo "<div class='cap'>{$file[$j]['subject']}";
                            if ($file[$j]['wr_comment'])
                                echo "&nbsp;<span class=\"comment\">+{$file[$j]['wr_comment']}</span>";
                            echo "</div></div>";

                        }
                        echo "</div>";
                    }
                    if ($page%($rows/$block)==0) $bo_count++;
                    if ($page<($count*($rows/$block))) {
                        echo "</div>";
                        echo "<div class=\"swipe-page\" id=\"swipe{$mw_latest_mobile_cnt}-{$page}\" ";
                        echo " bo_table=\"{$bo_tables[$bo_count]}\">";
                    }
                    $page++;
                }
            }
        }
        ?>
    </div> <!-- swipe-page -->
</div>
</div> <!-- swipe -->

<div style="text-align:center; padding:10px 10px 0 10px; clear:both;">
    <button onclick="mySwipe<?php echo $mw_latest_mobile_cnt?>.prev()" class="btn btn-default btn-sm" style="float:left;">이전</button> 
    <button onclick="mySwipe<?php echo $mw_latest_mobile_cnt?>.next()" class="btn btn-default btn-sm" style="float:right;">다음</button>
    <span id="swipe_number<?php echo $mw_latest_mobile_cnt?>" style="margin:30px 0 0 0;"></span> 
</div>

</div> <!-- latest-swipe -->

<? if ($mw_latest_mobile_cnt==1) { ?><script src="<?php echo G5_JS_URL?>/swipe.js"></script><? } ?>
<script>
var elem<?php echo $mw_latest_mobile_cnt?> = document.getElementById('swipe-box<?php echo $mw_latest_mobile_cnt?>');
window.mySwipe<?php echo $mw_latest_mobile_cnt?> = Swipe(elem<?php echo $mw_latest_mobile_cnt?>, {
    // startSlide: 4,
    // auto: 3000,
    continuous: true,
    // disableScroll: true,
    // stopPropagation: true,
    callback: function(index, element) {
        print_swipe_number<?php echo $mw_latest_mobile_cnt?>();
        $("#swipe-menu<?php echo $mw_latest_mobile_cnt?> > li").removeClass();
        $("#swipe-menu<?php echo $mw_latest_mobile_cnt?> > li").off("click");
        $("#swipe-menu<?php echo $mw_latest_mobile_cnt?> > li").each(function (i) {
            if ($(this).attr("bo_table") == $("#swipe<?php echo $mw_latest_mobile_cnt?>-"+index).attr("bo_table")) {
                $(this).addClass("active");
                $(this).click(function () {
                    var bo_table = $(this).attr("bo_table");
                    location.href = bo_url<?php echo $mw_latest_mobile_cnt?>[$(this).attr("bo_table")];
                });

            }
        });
    },
    transitionEnd: function(index, element) {}
});

$(document).ready(function () {
    $("#swipe-menu<?php echo $mw_latest_mobile_cnt?> > li").off("click");
    $("#swipe-menu<?php echo $mw_latest_mobile_cnt?> > li").each(function (i) {
        if (i == 0) {
            $(this).click(function () {
                var bo_table = $(this).attr("bo_table");
                location.href = bo_url<?php echo $mw_latest_mobile_cnt?>[$(this).attr("bo_table")];
            });
        }
    });
});

function move_swipe<?php echo $mw_latest_mobile_cnt?>(index) 
{
    mySwipe<?php echo $mw_latest_mobile_cnt?>.getPos();
}

function print_swipe_number<?php echo $mw_latest_mobile_cnt?>()
{
    $("#swipe_number<?php echo $mw_latest_mobile_cnt?>").text((mySwipe<?php echo $mw_latest_mobile_cnt?>.getPos()+1) + '/' + mySwipe<?php echo $mw_latest_mobile_cnt?>.getNumSlides());
}
print_swipe_number<?php echo $mw_latest_mobile_cnt?>();

</script>

