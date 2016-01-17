<?php
$sub_menu = "110200";
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super')
    alert('최고관리자만 접근 가능합니다.');

check_token();

function mw_upload_seo_image($type, $upload)
{
    if (!is_uploaded_file($upload['tmp_name']))
        return;

    $image_path = G5_DATA_PATH.'/seo';

    @mkdir($image_path, 0707);
    @chmod($image_path, 0707);

    $indexfile = $image_path."/index.php";
    $f = @fopen($indexfile, "w");
    @fwrite($f, "");
    @fclose($f);
    @chmod($indexfile, 0606);

    $file_name = get_safe_filename($upload['name']);
    $dest_file = '';

    $size = @getimagesize($upload['tmp_name']);

    $mime = array('image/png', 'image/jpeg', 'image/gif');
    $exts = array('png', 'jpg', 'gif');

    switch ($type)
    {
        case 'favicon':
            $dest_file = $image_path.'/favicon.ico';
            if (!preg_match("/\.ico$/i",  $file_name) or $upload['type'] != "image/vnd.microsoft.icon")
                alert("Favicon 은 .ico 형식의 파일만 업로드 가능합니다.");
            break;

        case 'sns_image':
            $dest_file = $image_path.'/sns_image.png';
            if (!preg_match("/\.(jpg|png|gif)$/i", $file_name) or !in_array($size['mime'], $mime))
                alert("png, gif, jpg 형식의 이미지 파일만 업로드 가능합니다.");
            break;

        case 'phone_icon':
            $dest_file = $image_path.'/phone_icon.png';
            if (!preg_match("/\.(jpg|png|gif)$/i", $file_name) or !in_array($size['mime'], $mime))
                alert("png, gif, jpg 형식의 이미지 파일만 업로드 가능합니다.");
            break;
    }

    if (!$dest_file) return;

    @unlink($dest_file);

    move_uploaded_file($upload['tmp_name'], $dest_file);
}

$sql_common = "
     cf_title           = '{$_POST['cf_title']}'
    ,cf_google_webmaster= '{$_POST['cf_google_webmaster']}'
    ,cf_naver_webmaster = '{$_POST['cf_naver_webmaster']}'
    ,cf_bing_webmaster  = '{$_POST['cf_bing_webmaster']}'
    ,cf_google_analytics= '{$_POST['cf_google_analytics']}'
    ,cf_naver_analytics = '{$_POST['cf_naver_analytics']}'
    ,cf_author          = '{$_POST['cf_author']}'
    ,cf_desc            = '{$_POST['cf_desc']}'
    ,cf_www             = '{$_POST['cf_www']}' 
    ,cf_seo_url         = '{$_POST['cf_seo_url']}' 
    ,cf_seo_except      = '{$_POST['cf_seo_except']}' 
    ,cf_twitter         = '{$_POST['cf_twitter']}' 
    ,cf_facebook_appid  = '{$_POST['cf_facebook_appid']}' 
";

$row = sql_fetch("select * from {$mw5['config_table']}", false);
if ($row)
    $sql = " update {$mw5['config_table']} set {$sql_common} ";
else
    $sql = " insert into {$mw5['config_table']} set {$sql_common}";

sql_query($sql);

mw_upload_seo_image("favicon", $_FILES['cf_favicon']);
mw_upload_seo_image("sns_image", $_FILES['cf_sns_image']);
mw_upload_seo_image("phone_icon", $_FILES['cf_phone_icon']);

goto_url('./seo_form.php', false);

