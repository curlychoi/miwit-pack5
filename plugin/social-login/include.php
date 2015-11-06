<?php
/**
 * 소셜로그인 (Social-login for Gnuboard4)
 *
 * Copyright (c) 2012 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

include_once("$g4[path]/plugin/social-login/_config.php");
?>

<div id="fb-root"></div>                                
<script src="http://connect.facebook.net/ko_KR/all.js"></script>  
<script>
window.fbAsyncInit = function () {
    FB.init({
        appId  : "<?=$mw_facebook_config[appId]?>",
        status : true, // check login status
        cookie : true, // enable cookies to allow the server to access the session
        xfbml  : true  // parse XFBML
    });
}
function facebook_login(){
    FB.getLoginStatus(function(response) {
        if (response.session) {
            LoginSuccessGo();
        }else{
            FB.login(function(response) { facebook_login_success(); }, {perms:'public_profile,email'});
        }
    });
}

function facebook_login_success(){
    location.href = '<?=$g4[path]?>/plugin/social-login/facebook-login.php';
}

function twitter_login() {
    window.open('<?=$g4[path]?>/plugin/social-login/twitter-redirect.php','twitterlogin', 'width=600,height=500');
}

function google_login() {
    window.open('<?=$g4[path]?>/plugin/social-login/google-login.php','googlelogin', 'width=600,height=500');
}

function naver_login() {
    window.open('<?=$g4[path]?>/plugin/social-login/naver-login.php','naverlogin', 'width=600,height=500');
}

function kakao_login() {
    window.open('<?=$g4[path]?>/plugin/social-login/kakao-login.php','kakaologin', 'width=600,height=500');
}
</script>


