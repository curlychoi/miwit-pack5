<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

$board['bo_use_list_view'] = false;
?>
<style>
#mw_key { padding:10px; }
#mw_key legend { font:bold 14px dotum; }
#mw_key .ibox { padding:20px 0 0 0; text-align:center; }
#mw_key .title_img { margin:0; } 
#mw_key .fbox { padding:30px 0 30px 0; }
#mw_key .fbox div { margin:0 0 10px 0; color:#444; } 
#mw_key .fbox span { font:normal 11px dotum; color:#999; } 
#mw_key label { font:bold 12px dotum; width:80px; text-align:right; float:left; margin:5px 3px 0 0; }
#mw_key .btn { background-color:#efefef; cursor:pointer; font:normal 12px dotum; } 
</style>

<div id="mw_key">
    <form name="fkey" id="fkey" method="get" onsubmit="return check_key()">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id?>">

    <fieldset>
        <legend> 게시물 열람 </legend>
        <div class="ibox">
            <div class="title_img"><img src="<?=$board_skin_path?>/img/title_key.png"></div>
            <div class="fbox">
                <input type="password" size="20" name="wr_key_password" id="wr_key_password">
                <input type="submit" value="확인" class="btn">
                <input type="button" value="뒤로" class="btn" onclick="history.back()">
                <?php if ($is_admin) { ?>
                <input type="submit" value="skip (관리자)" class="btn">
                <?php } else if ($write['mb_id'] and $write['mb_id'] == $member['mb_id']) { ?>
                <input type="submit" value="skip (본인)" class="btn">
                <?php } ?>
            </div>
        </div>
    </fieldset>
    </form>
</div>

<script>
function check_key() {
    $.get("<?php echo $board_skin_path?>/mw.proc/mw.key.update.php", $("#fkey").serialize(), function (str) {
        if (str == "ok") {
            location.reload();
        }
        else if (str == "self") {
            alert("글쓴이 본인 통과");
            location.reload();
        }
        else if (str == "admin") {
            alert("관리자 통과");
            location.reload();
        }
        else {
            alert(str);
            $("#wr_key_password").val("");
            $("#wr_key_password").focus();
        }
    });
    return false;
}
</script>

