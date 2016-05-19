<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (strstr($_SERVER['PHP_SELF'], G5_BBS_DIR."/login.php")
    or strstr($_SERVER['PHP_SELF'], G5_BBS_DIR."/password.php")
    or strstr($_SERVER['PHP_SELF'], G5_BBS_DIR."/member_confirm.php")
)
    if (is_file(G5_PATH."/plugin/login-background/include.php"))
        include(G5_PATH."/plugin/login-background/include.php");

if ($config['cf_use_email_certify'] && strstr($_SERVER['PHP_SELF'], G5_BBS_DIR."/login.php")) {
    $re = G5_PLUGIN_URL."/email-re-certify/";
    ?><script>
    $('#login_password_lost').after('&nbsp;<a href="<?php echo $re?>" class="btn01">이메일재인증</a>');
    </script><?php
}

if (G5_IS_MOBILE) {
    echo "<style>.mbskin { padding:20px; }</style>".PHP_EOL;
}
?>

<?php if ($is_admin == 'super') {  ?><!-- <div style='float:left; text-align:center;'>RUN TIME : <?php echo get_microtime()-$begin_time; ?><br></div> --><?php }  ?>

<!-- ie6,7에서 사이드뷰가 게시판 목록에서 아래 사이드뷰에 가려지는 현상 수정 -->
<!--[if lte IE 7]>
<script>
$(function() {
    var $sv_use = $(".sv_use");
    var count = $sv_use.length;

    $sv_use.each(function() {
        $(this).css("z-index", count);
        $(this).css("position", "relative");
        count = count - 1;
    });
});
</script>
<![endif]-->
<?php
if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
echo $mw['config']['cf_google_analytics'].PHP_EOL;
echo $mw['config']['cf_naver_analytics'].PHP_EOL;
?>
</body>
</html>
<?php echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다. ?>
