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

$mobile_board = array();
$sql = " select *
           from {$g5['menu_table']}
          where me_use = '1'
            and length(me_code) = '2'
          order by me_order, me_id ";
$qry = sql_query($sql);
for ($i=0; $row=sql_fetch_array($qry); $i++) {
    preg_match("/bo_table=([0-9a-zA-Z-_]+)&/", $row['me_link'].'&', $match);
    if (!$match[1])
        preg_match("/\/b\/([0-9a-zA-Z-_]+)&/", $row['me_link'].'&', $match);
    if ($match[1])
        $mobile_board[] = $match[1];
    $sql2 = " select *
               from {$g5['menu_table']}
              where me_use = '1'
                and length(me_code) = '4'
                and substring(me_code, 1, 2) = '{$row['me_code']}'
              order by me_order, me_id ";
    $qry2 = sql_query($sql2);
    for ($j=0; $row2=sql_fetch_array($qry2); $j++) {
        preg_match("/bo_table=([0-9a-zA-Z-_]+)&/", $row2['me_link'].'&', $match);
        if (!$match[1])
            preg_match("/\/b\/([0-9a-zA-Z-_]+)&/", $row2['me_link'].'&', $match);
        if ($match[1])
            $mobile_board[] = $match[1];
    }
}
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

#mw_side_button {
    position:fixed;
    top:400px;
    left:10px;
    cursor:pointer;
    z-index:9998;
    background-color:#000;
    background-color:#428bca;
    font-weight:normal;
    font-size:25px;
    color:#fff;
    border-radius:30px;
    padding:5px 10px 5px 10px;
    -webkit-transform: translate3d(0,0,0);
    box-shadow:0px 0px 3px #999;
    border:3px solid #fff;
}

#mw_side_button .total_alarm {
    position:absolute;
    background-color:#000;
    background-color:#2368a3;
    background-color:red;
    color:#fff;
    padding:2px 7px 2px 7px;
    border-radius:10px;
    margin:-45px 0 0 20px;
    font:bold 12px 'dotum';
    opacity:0.8;
}

.mw_side_profile {
    background-color:#428bca;
    font:bold 15px 'gulim';
    color:#fff;
    height:55px;
}
.mw_side_profile a {
    font:bold 15px 'gulim';
    color:#fff;
}

.mw_side_profile #mw_side_image { 
    background-color:#fff;
    width:45px;
    height:45px;
    border:2px solid #fff;
    margin:5px 7px 0 5px;
    float:left;
}

.mw_side_profile .mw_side_name {
    float:left;
    margin:17px 0 0 10px;
}

.mw_side_profile #mw_side_alarm {
    float:right;
    cursor:pointer;
    font:normal 20px 'dotum';
    margin:15px 10px 9px 0;
    padding:0 10px 0px 0;
    border-right:1px solid #2d7abd;
}

.mw_side_profile #mw_side_alarm .new {
    position:absolute;
    background-color:#000;
    background-color:#2368a3;
    color:#fff;
    padding:2px 7px 2px 7px;
    border-radius:10px;
    margin:-30px 0 0 7px;
    font:bold 10px 'dotum';
    opacity:0.8;
}

.mw_side_profile #mw_side_close {
    float:right;
    cursor:pointer;
    font:normal 20px 'dotum';
    margin:15px 10px 0 0;
}

#mw_side .mw_side_func {
    height:71px;
    border-bottom:1px solid #ddd;
}

#mw_side .mw_side_func .item {
    border-right:1px solid #ddd;
    width:70px;
    height:70px;
    float:left;
    text-align:center;
    cursor:pointer;
    background-color:#efefef;
}

#mw_side .mw_side_func .item div {
    padding-top:13px;
}

#mw_side .mw_side_cash {
    font:normal 12px 'dotum';
    height:40px;
    padding:5px 10px 0px 10px;
    background-color:#dfdfdf;
    border-bottom:1px solid #ddd;
}

#mw_side .mw_side_cash .cash_name {
    float:left;
    line-height:30px;
}

#mw_side .mw_side_cash .my_cash {
    float:right;
    line-height:30px;
}

#mw_side .mw_side_cash .my_cash .nu {
    color:#ff6600;
}

#mw_side .comment {
/*
    font:normal 11px 'gulim';
    color:#ff6600;
    background-color:#6e969a;
*/
    float:right;
    margin:3px 7px 0 0;
    padding:0;
    background-color:#dfdfdf;
    color:#ff6600;
    width:25px;
    height:25px;
    text-align:center;
    font-weight:bold;
    font-size:11px;
    line-height:25px;
    border:1px solid #ccc;
    border-radius:15px;

}
#mw_side .comment2 {
    float:right;
    margin:3px 7px 0 0;
    padding:0;
    background-color:translate;
    color:#ff6600;
    width:25px;
    height:25px;
    text-align:center;
    font-weight:bold;
    font-size:11px;
    line-height:25px;
    border:1px solid #ddd;
    border-radius:15px;
}

#mw_side .mw_side_att {
    font:normal 12px 'dotum';
    height:40px;
    padding:5px 10px 0px 10px;
    background-color:#efefef;
    border-bottom:1px solid #ddd;
    line-height:30px;
}

#mw_side .mw_side_foot {
    margin-top:20px;
    text-align:center;
}

#mw_side .mw_side_foot a {
    font:normal 11px 'dotum';
    color:#777;
}

#mw_side .mw_side_menu div {
}

#mw_side .mw_side_menu .group {
    line-height:30px;
    padding:5px 0 5px 7px;
    background-color:#eee;
    border-bottom:1px solid #ddd;
    cursor:pointer;
    background-color:#fff;
}

#mw_side .mw_side_menu .board {
    display:none;
    background-color:#efefef;
    background-color:#dfdfdf;
    font-size:12px;
}

#mw_side .mw_side_menu .board div {
    padding:5px 0 5px 12px;
    line-height:30px;
    border-bottom:1px solid #ccc;
}

#mw_side .mw_side_func i {
    font-size:20px;
}

#mw_side .mw_side_func .new {
    position:absolute;
    background-color:#000;
    background-color:#428bca;
    color:#fff;
    padding:2px 7px 2px 7px;
    border-radius:10px;
    margin:-25px 0 0 -10px;
    font:bold 10px 'dotum';
    opacity:0.8;
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
    $list = array();
    $group_count = array();
    for ($i=0, $m=count($mobile_board); $i<$m; $i++) {
        $board = sql_fetch("select gr_id, bo_table, bo_subject, bo_new from $g4[board_table] where bo_table = '{$mobile_board[$i]}'");
        if (!$board) continue;

        $sql = " select count(*) as cnt from $g4[write_prefix]$board[bo_table] ";
        $sql.= " where wr_is_comment = '' ";
        $sql.= " and wr_datetime >= '".date("Y-m-d H:i:s", $g4['server_time'] - ($board['bo_new'] * 3600))."'" ;
        $row = sql_fetch($sql);

        $cnt = '';
        if ($row[cnt])
            $cnt = "<span class='comment'>$row[cnt]</span>";

        $href = mw_seo_url($board['bo_table']);
        $list[$board['gr_id']][$board['bo_table']]['bo_subject'] = $board['bo_subject'];
        $list[$board['gr_id']][$board['bo_table']]['bo_new'] = $row['cnt'];
        $group_count[$board['gr_id']] += $row['cnt'];
    }

    foreach ((array)$list as $gr_id => $row) {
        $group = sql_fetch("select * from {$g4['group_table']} where gr_id = '{$gr_id}' ");
        echo "<div class=\"group\" id=\"group-{$gr_id}\"><i class=\"fa fa-book\"></i>&nbsp; {$group['gr_subject']}";
        if ($group_count[$gr_id])
            echo " <span class='comment2'>{$group_count[$gr_id]}</span>";
        echo "</div>\n";
        echo "<div id=\"board-{$gr_id}\" class=\"board\">\n";
        foreach ((array)$row as $bo_table => $item) {
            $href = mw_seo_url($bo_table);
            $new = "<span class='comment'>{$item['bo_new']}</span>";
            if (!$item['bo_new'])
                $new = '';
            echo "<a href=\"{$href}\"><div><i class=\"fa fa-file-o\"></i> &nbsp;{$item['bo_subject']} {$new}</div></a>\n";
        }
        echo "</div>\n";
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


