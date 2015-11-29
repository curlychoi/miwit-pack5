<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

$mw_side_width = 280;

$member_thumb = null;
if ($is_member)
    $member_thumb = $g4['path']."/data/mw.basic.comment.image/".$member['mb_id'];

if (!$is_member or !is_file($member_thumb))
    $member_thumb = G5_THEME_URL."/mobile/img/anonymous.png";

$memo_not_read = 0;
// 읽지 않은 쪽지가 있다면
if ($member['mb_id']) {
    $sql = " select count(*) as cnt from {$g4['memo_table']} where me_recv_mb_id = '{$member['mb_id']}' and me_read_datetime = '0000-00-00 00:00:00' ";
    $row = sql_fetch($sql);
    $memo_not_read = $row['cnt'];
}

//$a = mw_mobile_total_alarm();
//extract($a);

$mw5_menu = mw_get_menu();
?>
<style>
#mw_side {
    width:<?php echo $mw_side_width?>px;
    height:200px;
    top:100px;
    left:-<?php echo $mw_side_width?>px;
    position:absolute;
    z-index:9999;
    background-color:#ddd;
    -webkit-overflow-scrolling: touch;
    <?php if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile")) echo "overflow-y:scroll"; ?>
}
</style>

<?php if (defined("MW_MOBILE_INDEX") && $mw_mobile['use_cloud']) { ?>
<div id="mw_side_button"><i class="fa fa-cloud"></i>
<?php if ($total_alarm) { ?><div class="total_alarm"><?php echo $total_alarm?></div><?php } ?></div>
<?php } ?>

<div id="mw_side">
    <div class="mw_side_profile">
        <img src="<?php echo $member_thumb?>" class="img-circle" id="mw_side_image">
        <span class="mw_side_name">
            <?php if ($is_member) { ?>
            <?php echo $member['mb_nick']?>
            <?php } else { ?>
            <a href="<?php echo G5_BBS_URL?>/login.php">로그인 해주세요.</a>
            <?php } ?>
        </span>
        <div id="mw_side_close"><i class="fa fa-close"></i></div>
        <?php if ($is_smart_alarm) { ?>
        <div id="mw_side_alarm"><i class="fa fa-bell"></i>
            <?php if ($moa_count) { ?>
            <div id="moa_count" class="new"><?php echo $moa_count?></div>
            <?php } // moa_count ?>
        </div>
        <?php } // is_smart_alarm ?>
    </div>

    <div class="mw_side_func">
        <?php if ($is_member) { ?>
        <a href="<?php echo G5_BBS_URL?>/member_confirm.php?url=register_form.php"><div class="item"><div><i class="fa fa-user"></i><br>정보수정</div></div></a>
        <?php } else { ?>
        <a href="<?php echo G5_BBS_URL?>/register.php"><div class="item"><div><i class="fa fa-user"></i><br>회원가입</div></div></a>
        <?php } ?>
        <a href="#;" onclick="win_memo()"><div class="item"><div><i class="fa fa-comment-o"></i><br>쪽지<?php
            if ($memo_not_read) echo "<span class='new'>$memo_not_read</span>";?></div></div></a>
        <a href="#;" onclick="win_point()"><div class="item"><div><i class="fa fa-gift"></i><br>포인트</div></div></a>
        <a href="#;" onclick="win_scrap()"><div class="item"><div><i class="fa fa-paperclip"></i><br>스크랩</div></div></a>
    </div>

    <?php if ($mw_cash['cf_cash_name']) { ?>
    <div class="mw_side_cash">
        <div class="cash_name"><i class="fa fa-database"></i> &nbsp;나의 <?php echo $mw_cash['cf_cash_name']?></div>
        <div class="my_cash"><span class="nu"><?php echo number_format($mw_cash['mb_cash'])?></span>
            <?php echo $mw_cash['cf_cash_unit']?></div>
    </div>
    <?php } ?>

    <?php if (is_file($g4['path']."/plugin/attendance/_config.php")) {
    $sql = "select count(*) as cnt from mw_attendance where at_datetime like '$g4[time_ymd]%'";
    $att = sql_fetch($sql);
    ?>
    <div class="mw_side_att">
        <i class="fa fa-map-marker"></i> &nbsp;
        <a href="<?php echo $g4['path']?>/plugin/attendance/?is_mobile=1">현재 출석
        <?php echo number_format($att['cnt'])?>명. 출석부 바로가기!</a>
    </div>
    <?php } ?>

    <div class="mw_side_menu">
    <?php
    for ($i=0; $row=$mw5_menu[$i]; ++$i)
    {
        $role = substr($row['me_code'], 0, 2);

        ob_start();
        echo "<div id=\"board-{$role}\" class=\"board\">\n";
        for ($j=0; $row2=$mw5_menu[$i]['sub'][$j]; ++$j) {
            $href = $row2['me_link'];
            $bo_new = '';
            if ($row2['bo_new'])
                $bo_new = "<span class=\"comment\">{$row2['bo_new']}</span>";

            echo "<a href=\"{$href}\" target=\"_{$row2['me_target']}\"><div>{$row2['me_name']} {$bo_new}</div></a>\n";
        }
        echo "</div>\n";
        $drop_menu = ob_get_clean();

        $nav_class = "item";
        if ($role == substr($menu['me_code'], 0, 2))
            $nav_class = "select";

        $me_name = $row['me_name'];

        $group_link_begin = '';
        $group_link_end = '';
        if ($j<2 and $row['me_link'] == $href) {
            $group_link_begin = "<a href=\"{$row['me_link']}\" target=\"_{$row['me_target']}\">";
            $group_link_end = "</a>";
        }
 
        echo "<div class=\"group\" id=\"group-{$role}\">{$group_link_begin}<div>{$me_name}";
        if ($row['new'])
            echo " <span class='comment2'>{$row['new']}</span>";
        echo "</div>{$group_link_end}</div>\n";

        if ($j>1 or $row['me_link'] != $href) echo $drop_menu;
    }
    ?>
    </div>

    <div class="mw_side_foot">
        <a href="<?php echo G5_URL?>/" class="btn btn-default btn-sm"><?php echo $config['cf_title']?></a>
        <a href="<?php echo $pc_url?>" class="btn btn-default btn-sm">PC버전</a>

        <? if ($is_member) { ?>
        <a href="<?php echo G5_BBS_URL?>/logout.php?url=login.php" class="btn btn-default btn-sm">로그아웃</a>
        <? } else { ?>
        <a href="<?php echo G5_BBS_URL?>/login.php?url=<?php echo urlencode($_SERVER['REQUEST_URI'])?>" class="btn btn-default btn-sm">로그인</a>
        <? } ?>
    </div>

    <div style="height:100px;">&nbsp;</div>
</div>

<script>
$(document).ready(function ()
{
    mw_side_toggle = false;

    $("#mw_side_button").click(function ()
    {
        if (mw_side_toggle) {
            mw_side_off();
        }
        else {
            mw_side_on();
        }
    });

    $("#mw_toggle_button").click(function ()
    {
        if (mw_side_toggle) {
            mw_side_off();
        }
        else {
            mw_side_on();
        }
    });

    $("#mw_side_close").click(function () {
        //$("#mw_side_button").click();
        $("#mw_toggle_button").click();
    });

    $(".mw_side_menu .group").click(function () {
        var gr_id = $(this).attr("id").split("-")[1];
        $("#board-"+gr_id).toggle("fast");
    });

    $("#mw_side_alarm").click(function () {
        location.href = "<?php echo $g4['path']?>/plugin/smart-alarm/";
    });

    //$(".total_alarm").clone().appendTo("#mw_toggle_button").addClass("mw_total_alarm");
});

function mw_side_on()
{
    mw_modal();

    $("#mw_side").css("top", $(window).scrollTop());
    //$("#mw_side").css("height", $(document).height());
    $("#mw_side").css("height", window.innerHeight);

    $("#mw_side").animate({ "left": "+=<?php echo $mw_side_width?>px" }, "slow");
    $("#mw_side_button").css("display", "none");

    <?php if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile")) { ?>
    //$("html, body").css({ "height": "100%", "overflow": "hidden" });

    document.ontouchmove = function(e) {
        if(!$('#mw_side').has($(e.target)).length) {
            e.preventDefault();
        }
    };
    <?php } ?>

    mw_side_toggle = true;
}

function mw_side_off()
{
    $("#mw_side").animate({ "left": "-=<?php echo $mw_side_width?>px" }, "fast");
    $("#mw_side_button").css("display", "block");

    mw_modal_close();

    //$("html, body").css({ "height": "auto", "overflow": "auto" });

    document.ontouchmove = function(e) { return true; };

    $(".mw_side_menu .board").css("display", "none");

    mw_side_toggle = false;
}

function mw_modal()
{
    $("<div id='mw_modal_mask'/>").appendTo("body");
    $("#mw_modal_mask").css({
        "width": $(window).width(),
        "height": $(document).height(),
        "position": "fixed",
        "z-index": 9000,
        "background-color": "#000",
        "display": "none",
        "opacity": 0.6,
        "left": 0,
        "top": 0
    });

    //$("#mw_modal_mask").fadeTo("fast", 0.8);
    $("#mw_modal_mask").show();

    $(window).one("resize", function() { 
        mw_modal();
    });

    $("#mw_modal_mask").click(function () {
        mw_side_off();
    });
}

function mw_modal_close() {
    $("#mw_modal_mask").remove();
    //$("#"+mw_modal_obj).hide();
    $(window).unbind("resize");
}

$(window).bind("load scroll resize", function ()
{
    $("#mw_side_button").css("top", window.innerHeight - $("#mw_side_button").outerHeight() - 10);
});

// 쪽지 창
function win_memo(url)
{
    if (!url)
        url = g5_bbs_url + "/memo.php?is_mobile=1";
    window.open(url, "winMemo", "left=50,top=50,width=620,height=460,scrollbars=1");
}

// 포인트 창
function win_point(url)
{
    window.open(g5_bbs_url + "/point.php?is_mobile=1", "winPoint", "left=20, top=20, width=616, height=635, scrollbars=1");
}

// 스크랩 창
function win_scrap(url)
{
    if (!url)
        url = g5_bbs_url + "/scrap.php?is_mobile=1";
    window.open(url, "scrap", "left=20, top=20, width=616, height=500, scrollbars=1");
}
</script>


