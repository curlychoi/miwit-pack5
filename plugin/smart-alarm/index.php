<?php
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

include_once("_common.php");
include_once("_config.php");

if ($is_member) {
    $moa_config = sql_fetch("select * from $mw_moa_config_table where mb_id = '{$member['mb_id']}' ");
    if (!$moa_config)
        sql_query("insert into $mw_moa_config_table set mb_id = '{$member['mb_id']}' ");

    if (!$moa_config['cf_config'])
        $moa_config['cf_config'] = 'default';
}

if ($cf) {
    if (!$is_member)
        die("로그인 해주세요.");

    header("Content-Type: text/html; charset=$g4[charset]");
    $gmnow = gmdate("D, d M Y H:i:s") . " GMT";
    header("Expires: 0"); // rfc2616 - Section 14.21
    header("Last-Modified: " . $gmnow);
    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
    header("Cache-Control: pre-check=0, post-check=0, max-age=0"); // HTTP/1.1
    header("Pragma: no-cache"); // HTTP/1.0

    $moa_config = '?';
    switch ($cf) {
        case "default": $moa_config =''; break;
        case "only_reply": $moa_config = $cf; break;
        case "close": $moa_config = $cf; break;
    }
    if ($moa_config != '?') {
        sql_query("update $mw_moa_config_table set cf_config = '$cf' where mb_id = '{$member['mb_id']}' ");
    }
    echo "ok";
    exit;
}

$g4['title'] = "모아보기";
if ($_GET['is_mobile'])
    include_once("{$g4['path']}/plugin/mobile/_head.php");
else
    include_once("_head.php");

$now_path = $g4['path'];
$comment_image_path = $g4['path']."/data/mw.basic.comment.image"; 

sql_query(" update {$mw_moa_table} set mo_flag = '1' where mb_id = '{$member['mb_id']}' ");
sql_query(" delete from {$mw_moa_table} where mo_flag = '1' and mo_datetime < '".date("Y-m-d H:i:s", $g4['server_time'] - (86400*$mw_moa_date))."' ");

$sql_common = " from {$mw_moa_table} ";
$sql_order = " order by mo_datetime desc ";
$sql_search = " where mb_id = '{$member['mb_id']}' ";

$sql = "select count(*) as cnt
        $sql_common
        $sql_search";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "select *
        $sql_common
        $sql_search
        $sql_order
        limit $from_record, $rows ";
$qry = sql_query($sql);

$list = array();
for ($i=0; $row = sql_fetch_array($qry); ++$i)
{
    $list[$i] = mw_moa_row($row);

    $row2 = sql_fetch("select wr_subject from {$g4['write_prefix']}{$row['bo_table']} where wr_id = '{$row['wr_parent']}'", false);
    if (!$row2)
        $list[$i]['msg'] = "삭제되었습니다.";

    $list[$i]['subject'] = conv_subject($row2['wr_subject'], 30, "…");
    if (function_exists("mw_builder_reg_str")) {
        $list[$i]['subject'] = mw_builder_reg_str($list[$i]['subject']);
    } else {
        if ($member['mb_id']) {
            $list[$i]['subject'] = str_replace("{닉네임}", $member['mb_nick'], $list[$i]['subject']);
            $list[$i]['subject'] = str_replace("{별명}", $member['mb_nick'], $list[$i]['subject']);
        } else {
            $list[$i]['subject'] = str_replace("{닉네임}", "회원", $list[$i]['subject']);
            $list[$i]['subject'] = str_replace("{별명}", "회원", $list[$i]['subject']);
        }
    }
    $board = sql_fetch("select bo_subject from {$g4['board_table']} where bo_table = '{$row['bo_table']}' ");
    $list[$i]['msg'] = preg_replace("/^/", "<span class='board'>{$board['bo_subject']}</span> 게시판에 ", $list[$i]['msg']);
    $list[$i]['msg'] = preg_replace("/게시물에/", "게시물 '<span class='subject'>{$list[$i]['subject']}</span>' 에", $list[$i]['msg']);
}

$list_count = count($list);

$write_pages = get_paging(10, $page, $total_page, "{$_SERVER['PHP_SELF']}?is_mobile={$_GET['is_mobile']}&page=");
?>
<style type="text/css">
.config { text-align:right; font:normal 12px 'gulim'; margin-right:5px; }
.config { }
.config select { font:normal 12px 'gulim'; }

#allmoa { margin:10px; color:#444; font-family:gulim; text-align:left; }
#allmoa ul { margin:0; padding:0; list-style:none; }
#allmoa ul li { margin:0; padding:5px 0 0 0; height:47px; *height:53px; clear:both; cursor:pointer; overflow:hidden; }
#allmoa ul li { background-color:#fff; color:#444; border-bottom:1px solid #e6e6e6; }
#allmoa ul li span.board { font-weight:bold; color:#3b5998; }
#allmoa ul li span.name { font-weight:bold; color:#3b5998; }
#allmoa ul li span.subject { font-weight:bold; color:#4C8BCA; }
#allmoa ul li div {  }
#allmoa ul li div.msg { margin:5px 0 0 0; }
#allmoa ul li div.time { margin:5px 0 0 0; font-size:11px; }
#allmoa ul li div.image { float:left; width:42px; height:42px; margin:0 10px 0 5px; }
#allmoa ul li div.image img { float:left; width:38px; height:38px; margin:2px 0 0 2px; border:2px solid #f2f2f2; background-color:#fff; }
#allmoa ul li div.del { float:right; margin:3px 5px 0 0; display:none; }
#allmoa .write_pages { margin:20px 0 50px 0; text-align:center; }
#allmoa .btn { background-color:#efefef; cursor:pointer; }
/* 페이지 번호 */
.page {
    padding:20px 0 40px 0;
    text-align:center;
}

/* 페이지 번호 링크 */
.page a {
    font-family:dotum;
    font-weight:bold;
    font-size:11px;
    color:#797979;
    text-decoration:none;
    border:1px solid #d4d4d4;
    background-color:#f4f4f4;
    padding:3px 5px 2px 5px;
    /*padding:5px 7px 4px 7px;*/
}

/* 페이지 번호 링크 */
.page a.img {
    border:0px solid #d4d4d4;
    background-color:transparent;
    padding:0;
    /*padding:5px 7px 4px 7px;*/
}


.page a:hover {
    background-color:#F1753E;
    color:#fff;
}

/* 페이지 번호 현재 */
.page b {
    font-weight:bold;
    font-size:11px;
    color:#fff;
    text-decoration:none;
    border:1px solid #5078B9;
    background-color:#5078B9;
    padding:3px 5px 2px 5px;
    /*padding:5px 7px 4px 7px;*/
}

<?php if ($_GET['is_mobile']) { ?>
#allmoa {
    list-style:none;
    margin:5px;
    padding:0;
    box-shadow: 0 1px #EBEBEB;
    border-radius: 3px;
    border: 1px solid;
    border-color: #E5E5E5 #D3D3D3 #B9C1C6;
    background-color:#fff;
    color:#444;
    font:normal 12px 'gulim';
    text-align:left;
}
#allmoa ul { font:normal 11px 'gulim'; line-height:15px; }
#allmoa ul li { height:65px; }
#allmoa ul li div.time { font:normal 10px 'gulim'; color:#777; float:left; }
.fa-button {
    border:1px solid #ddd;
    padding:7px 5px 7px 5px;
    padding:5px 7px 5px 7px;
    background-color:#fff;
    font:normal 11px 'dotum';
    text-decoration:none;
    color:#555;
    text-align:left;
    cursor:pointer;
    outline:none;
    word-break:keep-all;
    white-space: nowrap;
}
<?php } ?>
</style>

<?php if ($is_member) { ?>
<div class="config"> 설정 :
    <select id="moa_config">
        <option value="default"> 내가 참여한 글 전부알람</option>
        <option value="only_reply"> 내 글의 답글에만 알람</option>
        <option value="close"> 알람 사용안함</option>
    </select>
    <button type="button" class="fa-button" value="적용" onclick="moa_config_act()"><i class="fa fa-save"></i>
    적용</button>
    <button type="button" class="fa-button" onclick="moa_del(0)"><i class="fa fa-remove"></i>
    모두삭제</button>
    <script>
    $("#moa_config").val("<?php echo $moa_config['cf_config']?>");
    </script>
</div>
<?php } ?>

<div id="allmoa">
    <ul>
    <?php for ($i=0; $i<$list_count; $i++) { ?>
    <li class="item" id="item_<?php echo $list[$i]['mo_id']?>" url="<?php echo $list[$i]['href']?>">
        <div class="del" id="del_<?php echo $list[$i]['mo_id']?>"><a href="#;" onclick="moa_del(<?php echo $list[$i]['mo_id']?>)">x</a></div>
        <div class="image"><img src="<?php echo $list[$i]['comment_image']?>" class="img-circle"></div>
        <div class="msg"><?php echo $list[$i]['msg']?></div>
        <div class="time"><?php echo $list[$i]['time']?></div>
    </li>
    <?php } ?>
    <?php if (!$i) echo "<li style='padding:150px 0 150px 0; text-align:center;'>알려드릴 사항이 없어요 =O=</li>"; ?>
    </ul>

    <div class="page"><?php echo $write_pages?></div>
</div>


<script>
allmoa_font_color = '#444';
allmoa_name_color = '#3B5998';
allmoa_over_color = '#6d84b4';
allmoa_subject_color = '#4C8BCA';
allmoa_bg_color = '#fff';

<?php if (!$_GET['is_mobile']) { ?>
$('#allmoa .item').mouseover(function () {
    $(this).css('background-color', allmoa_over_color);
    $(this).css('color', allmoa_bg_color);
    $(this).find('.name').css('color', allmoa_bg_color);
    $(this).find('.subject').css('color', allmoa_bg_color);
});
$('#allmoa .item').mouseout(function () {
    $(this).css('background-color', allmoa_bg_color);
    $(this).css('color', allmoa_font_color);
    $(this).find('.name').css('color', allmoa_name_color);
    $(this).find('.subject').css('color', allmoa_subject_color);
});
$("#allmoa .item").mouseenter(function () {
    $("#del_"+$(this).attr("id").replace("item_", "")).css("display", "block");
});
$("#allmoa .item").mouseleave(function () {
    $("#del_"+$(this).attr("id").replace("item_", "")).css("display", "none");
});
<?php } ?>
$("#allmoa .item").click(function () {
    location.href = $(this).attr("url");
});


function moa_config_act() {
    if (!Date.now) {
        Date.now = function() { return new Date().getTime(); };
    }
    var t = Date.now() ;

    $.get('<?php echo $_SERVER['PHP_SELF']?>?cf='+$('#moa_config').val()+"&t="+t, function (ret) {
        if (ret == 'ok')
            alert("변경되었습니다.");
        else
            alert("변경중 오류가 발생했습니다.");
    });
}

function moa_del(mo_id) {
    var href = "delete.php?mo_id="+mo_id+"&page=<?php echo $page?>&is_mobile=<?php echo $_GET['is_mobile']?>";

    $("#item_"+mo_id).unbind("click");

    if (!confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
        $("#item_"+mo_id).mouseup(function () {
            location.href = $(this).attr("url");
        });
        return;
    }

    document.location.href = href;
}
</script>

<?php

if ($_GET['is_mobile'])
    include_once("{$g4['path']}/plugin/mobile/_tail.php");
else
    include_once("_tail.php");

