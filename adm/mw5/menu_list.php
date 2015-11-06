<?php
$sub_menu = "110300";
include_once('./_common.php');

if ($is_admin != 'super')
    alert('최고관리자만 접근 가능합니다.');

$token = get_token();

mw_css("/asset/font-awesome-4.3.0/css/font-awesome.min.css");

$sql = " select * ";
$sql.= "   from {$g5['menu_table']} ";
$sql.= "  order by convert(me_order, char), me_id ";
$qry = sql_query($sql);

$g5['title'] = "메뉴 추가 설정";
include_once('../admin.head.php');

$colspan = 7;
?>
<style>
.icon_preview {
    font-weight:normal;
    font-size:15px;
}
</style>

<div class="local_desc01 local_desc">
    <p><strong>주의!</strong> 메뉴설정 작업 후 반드시 <strong>확인</strong>을 누르셔야 저장됩니다.</p>
</div>

<form name="fmenulist" id="fmenulist" method="post" action="./menu_list_update.php" onsubmit="return fmenulist_submit(this);">
<input type="hidden" name="token" value="<?php echo $token ?>">

<div id="menulist" class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">메뉴순서</th>
        <th scope="col">출력권한</th>
        <th scope="col"><input type="checkbox" id="all_check" name="all_check">&nbsp; <label for="all_check">사이드뷰</label></th>
        <th scope="col">아이콘 선택</th>
        <th scope="col">아이콘</th>
        <th scope="col">메뉴</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($qry); $i++)
    {
        $row2 = sql_fetch("select * from {$mw5['menu_table']} where me_code = '{$row['me_code']}' ", false);
        $bg = 'bg'.($i%2);
        $sub_menu_class = '';
        if(strlen($row['me_code']) == 4) {
            $sub_menu_class = ' sub_menu_class';
            $sub_menu_info = '<span class="sound_only">'.$row['me_name'].'의 서브</span>';
            $sub_menu_ico = '<span class="sub_menu_ico"></span>';
        }

        $search  = array('"', "'");
        $replace = array('&#34;', '&#39;');
        $me_name = str_replace($search, $replace, $row['me_name']);
    ?>
    <tr class="<?php echo $bg; ?> menu_list menu_group_<?php echo substr($row['me_code'], 0, 2); ?>">
        <td class="td_category"><?php echo $row['me_order']?></td>
        <td class="td_category"><?php echo get_member_level_select('me_level[]', 1, 10, $row2['me_level'])?></td>
        <td class="td_category">
            <input type="checkbox" class="me_no_side" name="me_no_side[<?php echo $row['me_code']?>]" id="me_no_side_<?php echo $row['me_code']?>" value="1"<?php echo $row2['me_no_side']?' checked':''?>>
            <label for="me_no_side_<?php echo $row['me_code']?>">사용안함</label>
        </td>
        <td class="td_category">
            <input type="hidden" id="me_icon_<?php echo $row['me_code']?>" name="me_icon[]" value="<?php echo $row2['me_icon']?>">
            <a href="#;" class="btn_frmline" name="icon_select" data="<?php echo $row['me_code']?>">선택하기</a>
            <a href="#;" class="btn_frmline" name="icon_delete" data="<?php echo $row['me_code']?>">삭제하기</a>
        </td>
        <td class="td_category">
            <span class="icon_preview" id="icon_<?php echo $row['me_code']?>">
                <i class="fa fa-<?php echo $row2['me_icon']?>"></i>
            </span>
        </td>
        <td class="<?php echo $sub_menu_class; ?>" style="text-align:left;">
            <input type="hidden" name="me_code[]" value="<?php echo $row['me_code']?>">
            <?php echo $me_name; ?>
        </td>

    </tr>
    <?php
    }

    if ($i==0)
        echo '<tr id="empty_menu_list"><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" name="act_button" value="확인" class="btn_submit">
</div>

</form>

<script>
$(function() {
    $("a[name=icon_select]").click(function () {
        var me_code = $(this).attr("data");
        var f = window.open("fontawesome.php?me_code="+me_code, "fontawesome", "width=800,height=600,scrollbars=1");
        f.focus();
    });
    $("a[name=icon_delete]").click(function () {
        var me_code = $(this).attr("data");
        $("#icon_"+me_code).html(""); 
        $("#me_icon_"+me_code).val(""); 
    });
    $("input[name=all_check]").click(function () {
        var chk = $(this).prop("checked");
        $("input[class=me_no_side]").prop("checked", chk);
    });
});

function icon_select(me_code, icon)
{
    $("#icon_"+me_code).html("<i class='fa fa-"+icon+"'></i>"); 
    $("#me_icon_"+me_code).val(icon); 
}

function fmenulist_submit(f)
{
    return true;
}
</script>

<?php
include_once ('../admin.tail.php');
