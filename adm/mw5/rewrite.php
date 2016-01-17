<?php
$sub_menu = "110200";
include_once('_common.php');
include_once('_upgrade.php');

auth_check($auth[$sub_menu], 'r');

$token = get_token();

if ($is_admin != 'super')
    alert('최고관리자만 접근 가능합니다.');

$tmp = parse_url($_SERVER['SCRIPT_NAME']);
$tmp = str_replace(G5_ADMIN_DIR.'/mw5/rewrite.php', '', $tmp['path']);
$sub = trim(basename($tmp));

$root = G5_PATH;
if ($sub) {
    $root = preg_replace("/\/{$sub}$/i", "", $root);
    $sub = '/'.$sub;
}

if (!preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"])) {
    //apache
    if (!in_array('mod_rewrite', apache_get_modules())) {
        echo '서버에 mod_rewrite 모듈이 설치되어 있지 않습니다. 서버관리자에게 문의해주세요.';
        exit;
    }
    echo '<div>아래경로에 .htaccess 파일을 만들어 주세요.<div>'.PHP_EOL;
    echo '<div>&nbsp;<div>'.PHP_EOL;
    echo '<div>경로:<div>'.PHP_EOL;
    echo '<div>'.$root.'/.htaccess<div>'.PHP_EOL;
    echo '<div>&nbsp;<div>'.PHP_EOL;
    echo '<div>내용:<div>'.PHP_EOL;
    echo '<div style="white-space:pre">&lt;IfModule mod_rewrite.c&gt;
RewriteEngine On';
    if ($sub)
        echo '
RewriteBase '.$sub;
    echo '
RewriteRule ^b/([a-zA-Z0-9_]+)$ bbs/board.php?bo_table=$1 [QSA]
RewriteRule ^g/([a-zA-Z0-9_]+)$ bbs/group.php?gr_id=$1 [QSA]
RewriteRule ^c/([a-zA-Z0-9_]+)$ bbs/content.php?co_id=$1 [QSA]
RewriteRule ^b/([a-zA-Z0-9_]+)-([0-9]+)$ bbs/board.php?bo_table=$1&wr_id=$2 [QSA]
RewriteRule ^m/b/([a-zA-Z0-9_]+)$ plugin/mobile/board.php?bo_table=$1 [QSA]
RewriteRule ^m/b/([a-zA-Z0-9_]+)-([0-9]+)$ plugin/mobile/board.php?bo_table=$1&wr_id=$2 [QSA]
&lt;/IfModule&gt;
</div>
'.PHP_EOL;
}
else {
    //nginx
    echo '<div>Nginx 설정파일에 아래 Rewrite Rule 을 추가해주세요.<div>'.PHP_EOL;
    echo '<div>&nbsp;<div>'.PHP_EOL;
    echo '<div style="white-space:pre">
server {
    server_name mydomain.com;

    root '.$root.';
</div>
'.PHP_EOL;

    echo '
    <div style="white-space:pre; color:red;">
    rewrite ^'.$sub.'/b/([a-zA-Z0-9_]+)$ '.$sub.'/bbs/board.php?bo_table=$1;
    rewrite ^'.$sub.'/g/([a-zA-Z0-9_]+)$ '.$sub.'/bbs/group.php?gr_id=$1;
    rewrite ^'.$sub.'/c/([a-zA-Z0-9_]+)$ '.$sub.'/bbs/content.php?co_id=$1;
    rewrite ^'.$sub.'/b/([a-zA-Z0-9_]+)-([0-9]+)$ '.$sub.'/bbs/board.php?bo_table=$1&wr_id=$2;
    rewrite ^'.$sub.'/m/b/([a-zA-Z0-9_]+)$ '.$sub.'/plugin/mobile/board.php?bo_table=$1;
    rewrite ^'.$sub.'/m/b/([a-zA-Z0-9_]+)-([0-9]+)$ '.$sub.'/plugin/mobile/board.php?bo_table=$1&wr_id=$2;
    </div>
'.PHP_EOL;
    echo '}'.PHP_EOL;
}

