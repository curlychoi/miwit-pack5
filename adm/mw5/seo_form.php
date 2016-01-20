<?php
$sub_menu = "110200";
include_once('_common.php');
include_once('_upgrade.php');

auth_check($auth[$sub_menu], 'r');

add_javascript('<script src="'.G5_URL.'/asset/jquery-ui-1.11.4/jquery-ui.js"></script>');
add_stylesheet('<link rel="stylesheet" href="'.G5_URL.'/asset/jquery-ui-1.11.4/jquery-ui.css"/>');
add_stylesheet('<link rel="stylesheet" href="'.$mw5['admin_url'].'/style.css'.'"/>');

$token = get_token();

if ($is_admin != 'super')
    alert('최고관리자만 접근 가능합니다.');

if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest")
{
    $sql = " select * from {$g5['menu_table']} order by me_code ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $me_link = mw_url_style($row['me_link'], $_REQUEST['type'], $_REQUEST['cf_www'], $_REQUEST['cf_seo_except']);
        $sql = " update {$g5['menu_table']} set me_link = '{$me_link}' where me_id = '{$row['me_id']}' ";
        sql_query($sql);
    }
    exit;
}

$g5['title'] = 'SEO 설정';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$pg_anchor = '<ul class="anchor">
    <li><a href="#anc_cf_webmaster">Webmaster 설정</a></li>
    <li><a href="#anc_cf_analytics">Analytics 설정</a></li>
    <li><a href="#anc_cf_meta">SEO Meta 설정</a></li>
    <li><a href="#anc_cf_url">SEO URL 설정</a></li>
    <li><a href="#anc_cf_image">SEO Image 설정</a></li>
</ul>';

$frm_submit = '<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
    <a href="'.G5_URL.'/">메인으로</a>
</div>';

?>

<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);" enctype="multipart/form-data">
<input type="hidden" name="token" value="<?php echo $token ?>" id="token">

<section id="anc_cf_webmaster">
    <h2 class="h2_frm">Webmaster 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>Webmaster 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_google_webmaster">구글 웹마스터 태그</label></th>
            <td colspan="3">
                <?php echo help("구글 웹마스터에서 제공한 인증용 메타태그를 입력해주세요.")?>
                <input type="text" class="frm_input" name="cf_google_webmaster" id="cf_google_webmaster" style="width:90%" value="<?php echo htmlspecialchars($mw['config']['cf_google_webmaster'])?>">
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4594" target="_blank">설정방법보기</a>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_naver_webmaster">네이버 웹마스터 태그</label></th>
            <td colspan="3">
                <?php echo help("네이버 웹마스터에서 제공한 인증용 메타태그를 입력해주세요.")?>
                <input type="text" class="frm_input" name="cf_naver_webmaster" id="cf_naver_webmaster" style="width:90%" value="<?php echo htmlspecialchars($mw['config']['cf_naver_webmaster'])?>">
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4597" target="_blank">설정방법보기</a>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_bing_webmaster">빙 웹마스터 태그</label></th>
            <td colspan="3">
                <?php echo help("빙 웹마스터에서 제공한 인증용 메타태그를 입력해주세요.")?>
                <input type="text" class="frm_input" name="cf_bing_webmaster" id="cf_bing_webmaster" style="width:90%" value="<?php echo htmlspecialchars($mw['config']['cf_bing_webmaster'])?>">
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4598" target="_blank">설정방법보기</a>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_sitemap">사이트맵</label></th>
            <td colspan="3">
                <?php echo help("각 웹마스터 도구에서 사이트맵주소에 아래 경로를 입력해주세요.")?>
                <input type="text" class="frm_input" name="cf_sitemap" id="cf_sitemap" style="width:90%" value="<?php echo G5_URL.'/sitemap.php'?>" readonly>
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4618" target="_blank">설정방법보기</a>
                <script>
                $("#cf_sitemap").css("cursor", "pointer");
                $("#cf_sitemap").click(function () {
                    $(this).select();
                });
                </script>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_analytics">
    <h2 class="h2_frm">Analytics 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>Analytics 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_google_analytics">구글 애널리틱스</label></th>
            <td colspan="3">
                <?php echo help("구글 애널리틱스 추적코드를 입력해주세요.")?>
                <textarea name="cf_google_analytics" id="cf_google_analytics"><?php echo $mw['config']['cf_google_analytics'] ?></textarea>
                <div><a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4595" target="_blank">설정방법보기</a></div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_naver_analytics">네이버 애널리틱스</label></th>
            <td colspan="3">
                <?php echo help("네이버 애널리틱스 추적코드를 입력해주세요.")?>
                <textarea name="cf_naver_analytics" id="cf_naver_analytics"><?php echo $mw['config']['cf_naver_analytics'] ?></textarea>
                <div><a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4596" target="_blank">설정방법보기</a></div>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_meta">
    <h2 class="h2_frm">SEO Meta 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>SEO Meta 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_title">사이트 제목</label></th>
            <td colspan="3">
                <?php echo help("검색엔진에 알려줄 사이트 제목을 입력해주세요.")?>
                <input type="text" class="frm_input" name="cf_title" id="cf_title" style="width:90%" value="<?php echo $mw['config']['cf_title'] ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_author">사이트 운영자</label></th>
            <td colspan="3">
                <?php echo help("검색엔진에 알려줄 사이트 운영자 이름을 입력해주세요.")?>
                <input type="text" class="frm_input" name="cf_author" id="cf_author" size="20" value="<?php echo $mw['config']['cf_author'] ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_desc">사이트 설명</label></th>
            <td colspan="3">
                <?php echo help("검색엔진에 알려줄 사이트 설명을 입력해주세요.")?>
                <textarea name="cf_desc" id="cf_desc"><?php echo $mw['config']['cf_desc'] ?></textarea>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_url">
    <h2 class="h2_frm">SEO URL 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>SEO URL 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_www">www 로만 접속가능</label></th>
            <td colspan="3">
                <input type="checkbox" name="cf_www" value="1" id="cf_www" <?php echo $mw['config']['cf_www']?'checked':''; ?>>
                <label for="cf_www">사용</label>
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4622" target="_blank">주의!</a>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_seo_url">SEO URL (Permalink)</label></th>
            <td colspan="3">
                <input type="checkbox" name="cf_seo_url" value="1" id="cf_seo_url" <?php echo $mw['config']['cf_seo_url']?'checked':''; ?>>
                <label for="cf_seo_url">사용</label>
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4593" target="_blank">설정방법보기</a>
                <button class="btn_frmline" name="rewrite">나에게 맞는 Rewrite Rule 확인</button>
                <script>
                $("button[name=rewrite]").click(function () {
                    $("body").append('<div id="rewrite"></div>');
                    $("#rewrite").load("rewrite.php");
                    $("#rewrite").dialog({
                        width:800,
                        height:400,
                        modal:true,
                        buttons: {
                            'OK': function() {
                              $(this).dialog("close");
                            }
                        }
                    });
                    return false;
                });
                </script>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_seo_except">SEO URL 제외게시판</label></th>
            <td colspan="3">
                <?php echo help("제외할 게시판ID 를 컴마, 로 구분해서 입력해주세요.")?>
                <input type="text" class="frm_input" name="cf_seo_except" id="cf_seo_except" style="width:90%" value="<?php echo $mw['config']['cf_seo_except'] ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="">메뉴URL 자동변경</label></th>
            <td colspan="3">
                <button class="btn_frmline" name="url-seo">SEO URL 스타일로 변경</button>
                <button class="btn_frmline" name="url-parameter">파라메타 스타일로 변경</button>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<script>
function change_url(type)
{
    if (!confirm("변경하시겠습니까?")) return false;

    $.get("seo_form.php", {
        'cf_www':$("#cf_www").prop("checked"),
        'type':type,
        'cf_seo_except':$("#cf_seo_except").val()
    }, function (str) {
        alert("변경했습니다.");
    });
}
$("button[name=url-seo]").click(function () {
    change_url('seo');
    return false;
});

$("button[name=url-parameter]").click(function () {
    change_url('parameter');
    return false;
});
</script>

<?php echo $frm_submit; ?>

<section id="anc_cf_sns">
    <h2 class="h2_frm">SEO SNS 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>SEO SNS 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_follow">팔로우 링크</label></th>
            <td colspan="3">
                <?php echo help("팔로우 링크 사용여부를 선택해주세요.")?>
                <label>
                <input type="checkbox" class="frm_input" name="cf_follow" value="1" <?php echo $mw['config']['cf_follow']?'checked':'';?>>
                사용</label>
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4623" target="_blank">팔로우 링크란?</a>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_facebook_appid">페이스북 앱ID</label></th>
            <td colspan="3">
                <?php echo help("검색엔진에 알려줄 페이스북 앱ID를 입력해주세요.")?>
                <input type="text" size="20" class="frm_input" name="cf_facebook_appid" value="<?php echo $mw['config']['cf_facebook_appid']?>">
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4579" target="_blank">만드는 방법 보기</a>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_facebook">페이스북 주소ID</label></th>
            <td colspan="3">
                <?php echo help("페이스북 주소 아이디를 입력해주세요. https://www.facebook.com/[이부분]")?>
                <input type="text" size="20" class="frm_input" name="cf_facebook" value="<?php echo $mw['config']['cf_facebook']?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_twitter">트위터ID</label></th>
            <td colspan="3">
                <?php echo help("트위터 계정 아이디를 입력해주세요. https://twitter.com/[이부분]")?>
                <input type="text" size="20" class="frm_input" name="cf_twitter" value="<?php echo $mw['config']['cf_twitter']?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_google">구글 플러스</label></th>
            <td colspan="3">
                <?php echo help("구글플러스 계정 아이디를 입력해주세요. https://plus.google.com/+[이부분]")?>
                <input type="text" size="20" class="frm_input" name="cf_google" value="<?php echo $mw['config']['cf_google']?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_instagram">인스타그램</label></th>
            <td colspan="3">
                <?php echo help("인스타그램 계정 아이디를 입력해주세요. https://www.instagram.com/[이부분]")?>
                <input type="text" size="20" class="frm_input" name="cf_instagram" value="<?php echo $mw['config']['cf_instagram']?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_youtube">유튜브</label></th>
            <td colspan="3">
                <?php echo help("인스타그램 계정 아이디를 입력해주세요. https://www.youtube.com/user/[이부분]")?>
                <input type="text" size="20" class="frm_input" name="cf_youtube" value="<?php echo $mw['config']['cf_youtube']?>">
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_cf_image">
    <h2 class="h2_frm">SEO Image 설정</h2>
    <?php echo $pg_anchor?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>SEO Image 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_favicon">Favicon</label></th>
            <td colspan="3">
                <?php echo help("브라우져 상단과 즐겨찾기에 사용될 파비콘(ico 파일)을 첨부해주세요.")?>
                <input type="file" class="frm_input" name="cf_favicon" />
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4590" target="_blank">자세히 보기</a>
                <?php
                if (is_file(G5_DATA_PATH.'/seo/favicon.ico') and filesize(G5_DATA_PATH.'/seo/favicon.ico')) {
                    echo '<div class="favicon-preview">';
                    echo '파일경로 : <a href="'.G5_DATA_URL.'/seo/favicon.ico" target="_blank"/>'.G5_DATA_URL.'/seo/favicon.ico</a>';
                    echo '<div class="i">(ico 파일은 미리보기가 되지 않습니다.)</div></div>'.PHP_EOL;
                }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_sns_image">SNS용 기본 이미지</label></th>
            <td colspan="3">
                <?php echo help("SNS 공유시 표시될 이미지입니다. 게시물 첨부 이미지가 없는 경우 사용됩니다. 1200x630 사이즈 png 파일을 추천합니다.")?>
                <input type="file" class="frm_input" name="cf_sns_image" />
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4591" target="_blank">자세히 보기</a>
                <?php
                if (is_file(G5_DATA_PATH.'/seo/sns_image.png') and filesize(G5_DATA_PATH.'/seo/sns_image.png')) {
                    $size = @getimagesize(G5_DATA_PATH.'/seo/sns_image.png');
                    echo '<div class="sns-image-preview"><img src="'.G5_DATA_URL.'/seo/sns_image.png"/></div>'.PHP_EOL;
                    echo '<div>업로드된 이미지 사이즈 : '.$size[0].' x '.$size[1].'</div>';
                }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_phone_icon">스마트폰 이미지</label></th>
            <td colspan="3">
                <?php echo help("스마트폰 바탕화면 추가시 사용할 아이콘 이미지를 첨부해주세요.")?>
                <input type="file" class="frm_input" name="cf_phone_icon" />
                <a class="btn_frmline" href="http://www.miwit.com/b/mw_tip-4592" target="_blank">자세히 보기</a>
                <?php
                if (is_file(G5_DATA_PATH.'/seo/phone_icon.png') and filesize(G5_DATA_PATH.'/seo/phone_icon.png'))  {
                    $size = @getimagesize(G5_DATA_PATH.'/seo/phone_icon.png');
                    echo '<div class="phone-icon-preview"><img src="'.G5_DATA_URL.'/seo/phone_icon.png"/></div>';
                    echo '<div>업로드된 이미지 사이즈 : '.$size[0].' x '.$size[1].'</div>';
                }
                ?>
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
    f.action = "./seo_form_update.php";
    return true;
}
</script>

<?php

include_once ('../admin.tail.php');
