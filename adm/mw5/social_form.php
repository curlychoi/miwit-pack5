<?php
$sub_menu = "110100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

if ($is_admin != 'super')
    alert('최고관리자만 접근 가능합니다.');

add_stylesheet('<link rel="stylesheet" href="'.$mw5['admin_url'].'/style.css'.'"/>');

$g5['title'] = '소셜로그인 설정';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_cf_social">소셜로그인 설정</a></li>
</ul>';

$frm_submit = '<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
    <a href="'.G5_URL.'/">메인으로</a>
</div>';

?>

<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
<input type="hidden" name="token" value="<?php echo $token ?>" id="token">

<section id="anc_cf_social">
    <h2 class="h2_frm">소셜로그인 설정</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>소셜로그인 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <!-- 페이스북 -->
        <tr>
            <th scope="row" rowspan="2"><label for="cf_title">페이스북</label></th>
            <td rowspan="2">
                <input type="checkbox" name="cf_facebook_use_login" value="1" id="cf_facebook_use_login" <?php echo $mw['config']['cf_facebook_use_login']?'checked':''; ?>>
                <label for="cf_facebook_use_login">사용</label>
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-1177" target="_blank">설정방법보기</a>
            </td>
            <th scope="row"><label for="cf_title">페이스북 App ID</label></th>
            <td><input type="text" name="cf_facebook_appid" value="<?php echo $mw['config']['cf_facebook_appid'] ?>" id="cf_facebook_appid" class="frm_input" size="40">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_title">페이스북 Secret</label></th>
            <td><input type="text" name="cf_facebook_secret" value="<?php echo $mw['config']['cf_facebook_secret'] ?>" id="cf_facebook_secret" class="frm_input" size="40"></td>
        </tr>

        <!-- 트위터 -->
        <tr>
            <th scope="row" rowspan="2"><label for="cf_title">트위터</label></th>
            <td rowspan="2">
                <input type="checkbox" name="cf_twitter_use_login" value="1" id="cf_twitter_use_login" <?php echo $mw['config']['cf_twitter_use_login']?'checked':''; ?>>
                <label for="cf_twitter_use_login">사용</label>
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-1176" target="_blank">설정방법보기</a>
            </td>
            <th scope="row"><label for="cf_title">트위터 Consumer Key</label></th>
            <td><input type="text" name="cf_twitter_consumer_key" value="<?php echo $mw['config']['cf_twitter_consumer_key'] ?>" id="cf_twitter_consumer_key" class="frm_input" size="40">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_title">트위터 Consumer Secret</label></th>
            <td><input type="text" name="cf_twitter_consumer_secret" value="<?php echo $mw['config']['cf_twitter_consumer_secret'] ?>" id="cf_twitter_consumer_secret" class="frm_input" size="40"></td>
        </tr>


        <!-- 구글 -->
        <tr>
            <th scope="row" rowspan="3"><label for="cf_title">구글</label></th>
            <td rowspan="3">
                <input type="checkbox" name="cf_google_use_login" value="1" id="cf_google_use_login" <?php echo $mw['config']['cf_google_use_login']?'checked':''; ?>>
                <label for="cf_google_use_login">사용</label>
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-1197" target="_blank">설정방법보기</a>
            </td>
            <th scope="row"><label for="cf_title">구글 Client ID</label></th>
            <td><input type="text" name="cf_google_client_id" value="<?php echo $mw['config']['cf_google_client_id'] ?>" id="cf_google_id" class="frm_input" size="40">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_title">구글 Client Secret</label></th>
            <td><input type="text" name="cf_google_client_secret" value="<?php echo $mw['config']['cf_google_client_secret'] ?>" id="cf_google_secret" class="frm_input" size="40"></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_title">구글 Client Domain</label></th>
            <td><input type="text" name="cf_google_client_domain" value="<?php echo $mw['config']['cf_google_client_domain'] ?>" id="cf_google_domain" class="frm_input" size="40"></td>
        </tr>

        <!-- 네이버 -->
        <tr>
            <th scope="row" rowspan="2"><label for="cf_title">네이버</label></th>
            <td rowspan="2">
                <input type="checkbox" name="cf_naver_use_login" value="1" id="cf_naver_use_login" <?php echo $mw['config']['cf_naver_use_login']?'checked':''; ?>>
                <label for="cf_naver_use_login">사용</label>
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-3832" target="_blank">설정방법보기</a>
            </td>
            <th scope="row"><label for="cf_title">네이버 Client ID</label></th>
            <td><input type="text" name="cf_naver_client_id" value="<?php echo $mw['config']['cf_naver_client_id'] ?>" id="cf_naver_id" class="frm_input" size="40">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_title">네이버 Client Secret</label></th>
            <td colspan="3"><input type="text" name="cf_naver_client_secret" value="<?php echo $mw['config']['cf_naver_client_secret'] ?>" id="cf_naver_secret" class="frm_input" size="40"></td>
        </tr>

        <!-- 카카오 -->
        <tr>
            <th scope="row" rowspan="2"><label for="cf_title">카카오</label></th>
            <td rowspan="2">
                <input type="checkbox" name="cf_kakao_use_login" value="1" id="cf_kakao_use_login" <?php echo $mw['config']['cf_kakao_use_login']?'checked':''; ?>>
                <label for="cf_kakao_use_login">사용</label>
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-3855" target="_blank">설정방법보기</a>
            </td>
            <th scope="row"><label for="cf_title">카카오 Client ID</label></th>
            <td><input type="text" name="cf_kakao_client_id" value="<?php echo $mw['config']['cf_kakao_client_id'] ?>" id="cf_kakao_id" class="frm_input" size="40">
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
    f.action = "./social_form_update.php";
    return true;
}
</script>

<?php

include_once ('../admin.tail.php');
