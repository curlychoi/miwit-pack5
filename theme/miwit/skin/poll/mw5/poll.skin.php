<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

add_stylesheet("<link rel=\"stylesheet\" href=\"{$poll_skin_url}/poll.css\">");
?>
<div class="poll">
    <form name="fpoll" action="<?php echo G5_BBS_URL ?>/poll_update.php" onsubmit="return fpoll_submit(this);" method="post">
    <input type="hidden" name="po_id" value="<?php echo $po_id?>">
    <input type="hidden" name="skin_dir" value="<?php echo $skin_dir?>">

    <h2><a>설문조사</a> <span class="point"><?php echo number_format($po['po_point'])?> 포인트 적립</span></h2>

    <ul>
        <li class="question">
            <?php echo $po['po_subject']?>
            <?php if ($is_admin) { ?>
            <a href="<?php echo G5_ADMIN_URL?>/poll_form.php?w=u&amp;po_id=<?php echo $po_id?>" target="_blank"><span class="fa fa-gear"></span></a>
            <?php } ?>
        </li>
        <?php for ($i=1; $i<=9 && $po["po_poll{$i}"]; $i++) { ?>
        <li>
            <input type="radio" name="gb_poll" value="<?php echo $i?>" id='gb_poll_<?php echo $i?>'>
            <label for='gb_poll_<?php echo $i?>'><?php echo $po['po_poll'.$i]?></label>
        </li>
        <?php } ?>
    </ul>

    <div class="button">
        <button type="submit"><i class="fa fa-check-circle-o"></i> 투표하기</button>
        <button type="button" onclick="poll_result();"><i class="fa fa-bar-chart"></i> 결과보기</button>
    </div>

    </form>
</div>

<script>
function fpoll_submit(f)
{
    <?php
    if ($member['mb_level'] < $po['po_level'])
        echo " alert('권한 {$po['po_level']} 이상의 회원만 투표에 참여하실 수 있습니다.'); return false; ";
     ?>

    var chk = false;
    for (i=0; i<f.gb_poll.length;i ++) {
        if (f.gb_poll[i].checked == true) {
            chk = f.gb_poll[i].value;
            break;
        }
    }

    if (!chk) {
        alert("투표하실 설문항목을 선택하세요");
        return false;
    }

    var new_win = window.open("about:blank", "win_poll", "width=616,height=500,scrollbars=yes,resizable=yes");
    f.target = "win_poll";

    return true;
}

function poll_result()
{
    <?php
    if ($member['mb_level'] < $po['po_level'])
        echo " alert('권한 {$po['po_level']} 이상의 회원만 결과를 보실 수 있습니다.'); return false; ";
     ?>

    win_poll("<?php echo G5_BBS_URL."/poll_result.php?po_id={$po_id}&skin_dir={$skin_dir}"?>");

    return false;
}
</script>

