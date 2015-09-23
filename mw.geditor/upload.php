<?php
include_once("_common.php");

header("Content-Type: text/html; charset=$g4[charset]");

function get_safe_filename($name)
{
    $pattern = '/["\'<>=#&!%\\\\(\)\*\+\?]/';
    $name = preg_replace($pattern, '', $name);

    return $name;
}
// --
// 첨부 이미지 저장 디렉토리 
// --

$path = "$g4[path]/data/$g4[geditor]";

$byte = 1024*1024 * 1; // 1메가

// --

make_dir($path);

$obj   = $_POST[obj];
$token = $_POST[token];
$work  = $_POST[work];

if (!$token) exit;

if (!$obj) 
    alert_only('오브젝트 변수가 없습니다.');

if ($work == 'delete') {
    delete_image($token, $path.'/'.date('ym'));
    exit;
}

$file = $_FILES[image];
$file[name] = get_safe_filename($file[name]);

$size = getImageSize($file[tmp_name]);
$mime = array('image/png', 'image/jpeg', 'image/gif');

if (!is_uploaded_file($file[tmp_name]))
    alert_only("첨부파일이 업로드되지 않았습니다.\\n\\n$file[error]");

if ($file[size] > $byte)
    alert_only("$byte byte 까지 업로드 가능합니다.");

if (!in_array($size['mime'], $mime))
    alert_only("PNG, GIF, JPG 형식의 이미지 파일만 업로드 가능합니다.");

if (!preg_match("/\.(jpg|png|gif)$/i", $file[name]))
    alert_only("PNG, GIF, JPG 형식의 이미지 파일만 업로드 가능합니다.");

if (!is_dir($path))
    alert_only("$path 디렉토리가 존재하지 않습니다.");

if (!is_writable($path))
    alert_only("$path 디렉토리의 퍼미션을 707로 변경해주세요.");

add_index($path);

$path .= '/'.date('ym');
make_dir($path);

delete_image($token, $path);

$dest_file = $path.'/'.abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr(md5(uniqid($g4[server_time])),0,8).'_'.str_replace('%', '', urlencode($file[name]));
$count = 0;

while (file_exists($dest_file))
    $dest_file = $path.'/['.$count++.']'.$file[name];

move_uploaded_file($file[tmp_name], $dest_file);

chmod($dest_file, 0606);

$file = dirname($_SERVER["SCRIPT_NAME"]).'/'.$dest_file;

setCookie('ge_token', $token);
setCookie('ge_file', $dest_file);

function add_index($path) {
    $indexfile = $path."/index.php";
    $f = @fopen($indexfile, "w");
    @fwrite($f, "");
    @fclose($f);
    @chmod($indexfile, 0606);
}

function delete_image($token, $path) {
    global $_COOKIE;
    if ($token==$_COOKIE[ge_token]) {
        if (substr($_COOKIE[ge_file], 0, strlen($path))==$path) {
            if (file_exists($_COOKIE[ge_file])) {
                @unlink($_COOKIE[ge_file]);
            }
        }
    }
}

function alert_only($msg='', $url='') {
    echo "<script language='javascript'>alert('$msg'); </script>";
    exit;
}

function make_dir($path)
{
    if (!is_dir($path)) 
    {
        @mkdir($path, 0707);
        @chmod($path, 0707);

        if (!is_dir($path))
            alert_only('디렉토리 생성에 실패하였습니다.');

        add_index($path);
    }
}

?>
<script language="javascript">
parent.<?=$obj?>.insert_image_preview("<?=$file?>");
</script>
