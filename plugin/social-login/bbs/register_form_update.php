<?php
include_once("_common.php");

ob_start();
readfile($g4['bbs_path']."/register_form_update.php");
$file = ob_get_contents();
ob_end_clean();

//$str = "\$mb_id = trim(strip_tags(mysql_real_escape_string(\$_POST[mb_id])));
$str = "if (preg_match(\"/[^0-9a-z_]+/i\", \$mb_id)) {
    alert(\"회원아이디는 영문자, 숫자, _ 만 사용할수 있습니다.\");
}";

$file = str_replace("<?php", "", $file);
$file = str_replace("<?", "", $file);
$file = str_replace("?".">", "", $file);
$file = str_replace($str, "", $file);

//g5
$str = 'if ($msg = valid_mb_id($mb_id))';
$file = str_replace($str, "//", $file);

$str = <<<HEREDOC
        echo "
        <html><title>회원정보수정</title><meta http-equiv='Content-Type' content='text/html; charset=\$g4[charset]'></html><body> 
        <form name='fregisterupdate' method='post' action='{\$https_url}/register_form.php'>
        <input type='hidden' name='w' value='u'>
        <input type='hidden' name='mb_id' value='{\$mb_id}'>
        <input type='hidden' name='mb_password' value='{\$tmp_password}'>
        <input type='hidden' name='is_update' value='1'>
        </form>
        <script type='text/javascript'>
        alert('회원 정보가 수정 되었습니다.');
        document.fregisterupdate.submit();
        </script>
        </body>
        </html>";
HEREDOC;

$file = str_replace($str, "alert('회원정보가 수정되었습니다.', \$g4[path]);", $file);

if (!check_string($member['mb_name'], _G4_HANGUL_)) {
    $file = str_replace("set mb_nick", "set mb_name = '\$mb_name', mb_nick", $file);
}

eval($file);

exit;
