<?php
$sub_menu = "110100";
include_once('_common.php');
include_once('_upgrade.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

if ($is_admin != 'super')
    alert('최고관리자만 접근 가능합니다.');

$g5['title'] = '배추빌더5 기본설정';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_cf_theme">테마설정</a></li>
    <li><a href="#anc_cf_index">INDEX 설정</a></li>
    <li><a href="#anc_cf_head">HEAD 설정</a></li>
    <li><a href="#anc_cf_tail">TAIL 설정</a></li>
    <li><a href="#anc_cf_sidebar">Sidebar 설정</a></li>
</ul>';

$frm_submit = '<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
    <a href="'.G5_URL.'/">메인으로</a>
</div>';

?>

<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
<input type="hidden" name="token" value="<?php echo $token ?>" id="token">

<section id="anc_cf_theme">
    <h2 class="h2_frm">테마 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>테마 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_theme_color">컬러설정</label></th>
            <td colspan="3">
                <?php
                $list = mw_get_theme_color();
                echo "<select name='cf_theme_color' id='cf_theme_color'>".PHP_EOL;
                echo "<option value=''></option>".PHP_EOL;
                foreach ((array)$list as $row) {
                    echo "<option value='{$row}'>{$row}</option>".PHP_EOL;
                }
                echo "</select>".PHP_EOL;
                ?>
                <script>$("#cf_theme_color").val("<?php echo $mw['config']['cf_theme_color']?>");</script>
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4578" target="_blank">색상 미리보기</a>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_content_width">본문 가로사이즈</label></th>
            <td colspan="3">
                <input type="text" size="5" class="frm_input" name="cf_content_width" value="<?php echo $mw['config']['cf_content_width']?>">
                px
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_side_width">사이드뷰 가로사이즈</label></th>
            <td colspan="3">
                <input type="text" size="5" class="frm_input" name="cf_side_width" value="<?php echo $mw['config']['cf_side_width']?>">
                px
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_side_position">사이드뷰 위치</label></th>
            <td colspan="3">
                <select id="cf_side_position" name="cf_side_position">
                <option value=""></option>
                <option value="left">좌측</option>
                <option value="right">우측</option>
                </select>
                <script>$('#cf_side_position').val('<?php echo $mw['config']['cf_side_position']?>');</script>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_nav_no_scroll">메뉴 스크롤</label></th>
            <td colspan="3">
                <label><input type="checkbox" name="cf_nav_no_scroll" value="1"> 사용안함</label>
                <script>$('input[name=cf_nav_no_scroll]').prop('checked', '<?php echo $mw['config']['cf_nav_no_scroll']?>');</script>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_new">메뉴 새글표시</label></th>
            <td colspan="3">
                <select name="cf_new" id="cf_new">
                    <option value=""></option>
                    <option value="count">새글갯수 표시</option>
                    <option value="check">새글여부 표시</option>
                    <option value="no">표시 안함 </option>
                </select>
                <script>$('select[name=cf_new]').val('<?php echo $mw['config']['cf_new']?>');</script>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_no_popular">인기검색어</label></th>
            <td colspan="3">
                <label><input type="checkbox" name="cf_no_popular" value="1"> 사용안함</label>
                <script>$('input[name=cf_no_popular]').prop('checked', '<?php echo $mw['config']['cf_no_popular']?>');</script>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_css">사용자정의 CSS</label></th>
            <td colspan="3">
                <textarea name="cf_css" id="cf_css"><?php echo $mw['config']['cf_css'] ?></textarea>
                <div>
                    <label><input type="checkbox" name="cf_no_css" value="1"> 사용안함</label>
                    <script>$('input[name=cf_no_css]').prop('checked', '<?php echo $mw['config']['cf_no_css']?>');</script>
                </div>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_index">
    <h2 class="h2_frm">INDEX 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>INDEX 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_index_image_html">INDEX 스크롤이미지 편집</label></th>
            <td colspan="3">
                <textarea name="cf_index_image_html" id="cf_index_image_html"><?php echo $mw['config']['cf_index_image_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4600" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_index_image" value="1"> 사용안함</label>
                    <script>$('input[name=cf_no_index_image]').prop('checked', '<?php echo $mw['config']['cf_no_index_image']?>');</script>
                </div>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_head">
    <h2 class="h2_frm">HEAD 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>HEAD 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_search_html">검색창 편집</label></th>
            <td colspan="3">
                <?php echo help("검색창 코드를 직접 편집해주세요.")?>
                <textarea name="cf_search_html" id="cf_search_html"><?php echo $mw['config']['cf_search_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4599" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_search" value="1"> 사용안함</label>
                    <script>$('input[name=cf_no_search]').prop('checked', '<?php echo $mw['config']['cf_no_search']?>');</script>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_quick_link_html">퀵링크 편집</label></th>
            <td colspan="3">
                <?php echo help("검색창 하단의 퀵링크 코드를 직접 편집해주세요.")?>
                <textarea name="cf_quick_link_html" id="cf_quick_link_html"><?php echo $mw['config']['cf_quick_link_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4599" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_quick_link" value="1"> 사용안함</label> 
                    <script>$("input[name=cf_no_quick_link]").prop("checked", "<?php echo $mw['config']['cf_no_quick_link']?>");</script>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_head_html">HEAD 편집</label></th>
            <td colspan="3">
                <?php echo help("상단메뉴와 컨텐츠 사이")?>
                <textarea name="cf_head_html" id="cf_head_html"><?php echo $mw['config']['cf_head_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4599" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_head" value="1"> 사용안함</label> 
                    <script>$("input[name=cf_no_head]").prop("checked", "<?php echo $mw['config']['cf_no_head']?>");</script>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_content_head_html">Content HEAD 편집</label></th>
            <td colspan="3">
                <?php echo help("컨텐츠 상단")?>
                <textarea name="cf_content_head_html" id="cf_content_head_html"><?php echo $mw['config']['cf_content_head_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4599" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_content_head" value="1"> 사용안함</label> 
                    <script>$("input[name=cf_no_content_head]").prop("checked", "<?php echo $mw['config']['cf_no_content_head']?>");</script>
                </div>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_tail">
    <h2 class="h2_frm">TAIL 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>HEAD 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_content_tail_html">Content TAIL 편집</label></th>
            <td colspan="3">
                <?php echo help("컨텐츠 하단")?>
                <textarea name="cf_content_tail_html" id="cf_content_tail_html"><?php echo $mw['config']['cf_content_tail_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4601" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_content_tail" value="1"> 사용안함</label> 
                    <script>$("input[name=cf_no_content_tail]").prop("checked", "<?php echo $mw['config']['cf_no_content_tail']?>");</script>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_tail_html">TAIL 편집</label></th>
            <td colspan="3">
                <?php echo help("컨텐츠와 하단 사이")?>
                <textarea name="cf_tail_html" id="cf_tail_html"><?php echo $mw['config']['cf_tail_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4601" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_tail" value="1"> 사용안함</label> 
                    <script>$("input[name=cf_no_tail]").prop("checked", "<?php echo $mw['config']['cf_no_tail']?>");</script>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_tail_link_html">TAIL링크 편집</label></th>
            <td colspan="3">
                <?php echo help("사이트 하단부 TAIL 링크 코드를 직접 편집해주세요.")?>
                <textarea name="cf_tail_link_html" id="cf_tail_link_html"><?php echo $mw['config']['cf_tail_link_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4601" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_tail_link" value="1"> 사용안함</label> 
                    <script>$("input[name=cf_no_tail_link]").prop("checked", "<?php echo $mw['config']['cf_no_tail_link']?>");</script>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_info_html">사이트정보</label></th>
            <td colspan="3">
                <?php echo help("사이트 하단부 사이트정보 코드를 직접 편집해주세요.")?>
                <textarea name="cf_info_html" id="cf_info_html"><?php echo $mw['config']['cf_info_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4601" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_info" value="1"> 사용안함</label> 
                    <script>$("input[name=cf_no_info]").prop("checked", "<?php echo $mw['config']['cf_no_info']?>");</script>
                </div>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_sidebar">
    <h2 class="h2_frm">Sidebar 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>Sidebar 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_sidebar_head_html">사이드바 상단 편집</label></th>
            <td colspan="3">
                <?php echo help("사이드바 상단에 추가할 코드를 직접 편집해주세요.")?>
                <textarea name="cf_sidebar_head_html" id="cf_sidebar_head_html"><?php echo $mw['config']['cf_sidebar_head_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4602" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_sidebar_head" value="1"> 사용안함</label> 
                    <script>$("input[name=cf_no_sidebar_head]").prop("checked", "<?php echo $mw['config']['cf_no_sidebar_head']?>");</script>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_sidebar_tail_html">사이드바 하단 편집</label></th>
            <td colspan="3">
                <?php echo help("사이드바 하단에 추가할 코드를 직접 편집해주세요.")?>
                <textarea name="cf_sidebar_tail_html" id="cf_sidebar_tail_html"><?php echo $mw['config']['cf_sidebar_tail_html'] ?></textarea>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4602" target="_blank">설정방법보기</a>
                    <label><input type="checkbox" name="cf_no_sidebar_tail" value="1"> 사용안함</label> 
                    <script>$("input[name=cf_no_sidebar_tail]").prop("checked", "<?php echo $mw['config']['cf_no_sidebar_tail']?>");</script>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_sidebar">사이드바</label></th>
            <td colspan="3">
                <?php echo help("사이드바 기본항목 사용여부를 선택해주세요.")?>
                <ul class="sidebar-use">
                <li><label><input type="checkbox" name="cf_sidebar_outlogin" value="1"> 로그인</label></li>
                <li><label><input type="checkbox" name="cf_sidebar_social" value="1"> 소셜 로그인</label></li>
                <li><label><input type="checkbox" name="cf_sidebar_menu" value="1"> 메뉴</label></li>
                <li><label><input type="checkbox" name="cf_sidebar_cash" value="1"> 나의 cash (컨텐츠샵)</label></li>
                <li><label><input type="checkbox" name="cf_sidebar_notice" value="1"> 공지사항</label></li>
                <li><label><input type="checkbox" name="cf_sidebar_latest_write" value="1"> 최신글</label></li>
                <li><label><input type="checkbox" name="cf_sidebar_latest_comment" value="1"> 최신댓글</label></li>
                <li><label><input type="checkbox" name="cf_sidebar_visit" value="1"> 현재접속회원</label></li>
                <li><label><input type="checkbox" name="cf_sidebar_poll" value="1"> 설문</label></li>
                </ul>
                <div>
                    <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4602" target="_blank">설정방법보기</a>
                </div>
                <script>
                $("input[name=cf_sidebar_outlogin]").prop("checked", "<?php echo $mw['config']['cf_sidebar_outlogin']?>");
                $("input[name=cf_sidebar_social]").prop("checked", "<?php echo $mw['config']['cf_sidebar_social']?>");
                $("input[name=cf_sidebar_menu]").prop("checked", "<?php echo $mw['config']['cf_sidebar_menu']?>");
                $("input[name=cf_sidebar_cash]").prop("checked", "<?php echo $mw['config']['cf_sidebar_cash']?>");
                $("input[name=cf_sidebar_notice]").prop("checked", "<?php echo $mw['config']['cf_sidebar_notice']?>");
                $("input[name=cf_sidebar_latest_write]").prop("checked", "<?php echo $mw['config']['cf_sidebar_latest_write']?>");
                $("input[name=cf_sidebar_latest_comment]").prop("checked", "<?php echo $mw['config']['cf_sidebar_latest_comment']?>");
                $("input[name=cf_sidebar_visit]").prop("checked", "<?php echo $mw['config']['cf_sidebar_visit']?>");
                $("input[name=cf_sidebar_poll]").prop("checked", "<?php echo $mw['config']['cf_sidebar_poll']?>");
                </script>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>


</form>

<script>
function fconfigform_submit(f)
{
    f.action = "./config_form_update.php";
    return true;
}
</script>

<?php

include_once ('../admin.tail.php');
