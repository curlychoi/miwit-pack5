<?php
// 이 파일은 새로운 파일 생성시 반드시 포함되어야 함
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$begin_time = get_microtime();

if (!isset($g5['title'])) {
    $g5['title'] = $config['cf_title'];
    $g5_head_title = $g5['title'];
}
else {
    $g5_head_title = $g5['title']; // 상태바에 표시될 제목
    $g5_head_title .= " | ".$config['cf_title'];
}

$meta_keywords = $g5['title'];
$meta_keywords = preg_replace("/\n/", " ", $meta_keywords);
$meta_keywords = preg_replace("/\"/", "", $meta_keywords);
$meta_keywords = preg_replace("/'/", "", $meta_keywords);
$meta_keywords = preg_replace("/,/", "", $meta_keywords);
$meta_keywords = preg_replace("/http:\/\/[^\s]+/", "", $meta_keywords);
$meta_keywords = cut_str($meta_keywords, 150);
$meta_keywords = trim($meta_keywords);

$meta_description = $write['wr_content'];

$meta_description = strip_tags($meta_description);
$meta_description = explode("\n", $meta_description);
for ($i=0, $m=count($meta_description); $i<$m; $i++) {
    $meta_description[$i] = trim($meta_description[$i]);
}
$meta_description = implode(" ", $meta_description);
$meta_description = preg_replace("/\n/", " ", $meta_description);
$meta_description = preg_replace("/\"/", "", $meta_description);
$meta_description = preg_replace("/'/", "", $meta_description);
$meta_description = preg_replace("/,/", "", $meta_description);
$meta_description = preg_replace("/http:\/\/[^\s]+/", "", $meta_description);
$meta_description = cut_str($meta_description, 150);
$meta_description = trim($meta_description);

$image_src = null;
if ($bo_table && $wr_id) {
    $image_src = G5_DATA_URL."/file/{$bo_table}/thumbnail/{$wr_id}.jpg";
}

if (!$image_src or !is_file($g4['path']."/data/file/{$bo_table}/thumbnail/{$wr_id}.jpg"))
    $image_src = G5_IMG_URL."/image_src.png";

// 현재 접속자
// 게시판 제목에 ' 포함되면 오류 발생
$g5['lo_location'] = addslashes($g5['title']);
if (!$g5['lo_location'])
    $g5['lo_location'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
$g5['lo_url'] = addslashes(clean_xss_tags($_SERVER['REQUEST_URI']));
if (strstr($g5['lo_url'], '/'.G5_ADMIN_DIR.'/') || $is_admin == 'super') $g5['lo_url'] = '';

/*
// 만료된 페이지로 사용하시는 경우
header("Cache-Control: no-cache"); // HTTP/1.1
header("Expires: 0"); // rfc2616 - Section 14.21
header("Pragma: no-cache"); // HTTP/1.0
*/
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<?php
if (G5_IS_MOBILE) {
    echo '<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">'.PHP_EOL;
    echo '<meta name="HandheldFriendly" content="true">'.PHP_EOL;
    echo '<meta name="format-detection" content="telephone=no">'.PHP_EOL;
} else {
    echo '<meta http-equiv="imagetoolbar" content="no">'.PHP_EOL;
    echo '<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">'.PHP_EOL;
    echo '<meta name="viewport" content="width='.$mw['config']['cf_width'].'">'.PHP_EOL;
}

if($config['cf_add_meta'])
    echo $config['cf_add_meta'].PHP_EOL;

if ($bo_table && $wr_id)
{
    $fb_file = sql_fetch("select bf_file from {$g4['board_file_table']}
                           where bo_table = '{$bo_table}'
                             and wr_id = '{$wr_id}'
                             and bf_width > 0
                           order by bf_no
                           limit 1");

    if ($fb_file) {
        $ogp_thumb = $g4['url']."/data/file/".$bo_table."/".$fb_file['bf_file'];
    }
    else {
        preg_match_all("/<img.*src=\\\"(.*)\\\"/iUs", stripslashes($write['wr_content']), $matchs);

        $mat = '';
        for ($i=0, $m=count($matchs[1]); $i<$m; ++$i)
        {
            $mat = $matchs[1][$i];

            // 이모티콘 제외
            if (strstr($mat, "mw.basic.comment.image")) $mat = '';
            if (strstr($mat, "mw.emoticon")) $mat = '';
            if (preg_match("/cheditor[0-9]\/icon/i", $mat)) $mat = '';

            if ($mat) {
                $ogp_thumb = $mat;
                break;
            }
        }

        if (!$mat) {
            $ogp_thumb = $g4['path']."/data/file/".$bo_table."/thumbnail/".$wr_id.".jpg";

            if (!@is_file($ogp_thumb))
                $ogp_thumb = $g4['path']."/data/file/".$bo_table."/thumbnail/".$wr_id;

            if (!@is_file($ogp_thumb))
                $ogp_thumb = $g4['path']."/data/file/".$bo_table."/thumb/".$wr_id;

            if (!@is_file($ogp_thumb))
                $ogp_thumb = '';
            else
                $ogp_thumb = str_replace($g4['path'], $g4['url'], $ogp_thumb);
        }
    }

    if (!$ogp_thumb or !is_file(str_replace($g4['url'], $g4['path'], $ogp_thumb)))
        $ogp_thumb = $g4['url']."/img/image_src.png";

    $ogp_title = trim(cut_str(strip_tags($write['wr_subject']), 255));
    $ogp_site_name = trim(cut_str(strip_tags($config['cf_title']), 255));

    $ogp_url = $g4['url']."/".$g4['bbs']."/board.php?bo_table=".$bo_table."&wr_id=".$wr_id;
    if (function_exists("mw_seo_url"))
        $ogp_url = mw_seo_url($bo_table, $wr_id);

    $ogp_description = $write['wr_content'];
    $ogp_description = trim(preg_replace("/{이미지:[0-9]+}/iUs", "", $ogp_description));
    $ogp_description = strip_tags($ogp_description);
    $ogp_description = explode("\n", $ogp_description);
    for ($i=0, $m=count($ogp_description); $i<$m; $i++) {
        $ogp_description[$i] = trim($ogp_description[$i]);
    }
    $ogp_description = implode(" ", $ogp_description);
    $ogp_description = preg_replace("/\n/", " ", $ogp_description);
    $ogp_description = preg_replace("/\"/", "", $ogp_description);
    $ogp_description = preg_replace("/'/", "", $ogp_description);
    $ogp_description = preg_replace("/,/", "", $ogp_description);
    $ogp_description = preg_replace("/http:\/\/[^\s]+/", "", $ogp_description);
    $ogp_description = cut_str($ogp_description, 150);
    $ogp_description = trim($ogp_description);

    $ogp = "<meta property=\"og:image\" content=\"{$ogp_thumb}\"/>\n";
    $ogp.= "<meta property=\"og:title\" content=\"{$ogp_title}\"/>\n";
    $ogp.= "<meta property=\"og:site_name\" content=\"{$ogp_site_name}\"/>\n";
    $ogp.= "<meta property=\"og:url\" content=\"{$ogp_url}\"/>\n";
    $ogp.= "<meta property=\"og:description\" content=\"{$ogp_description}\"/>\n";
    echo $ogp;
}
?>
<?php if ($bo_table) { ?>
<link rel="canonical" href="<?php echo mw_seo_url($bo_table, $wr_id)?>" />
<link rel="alternate" type="application/rss+xml" title="<?=$config[cf_title]?>, <?=$board[bo_subject]?>" href="<?="$g4[url]/$g4[bbs]/rss.php?bo_table=$bo_table"?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?="$g4[url]/$g4[bbs]/rss.php?bo_table=$bo_table"?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?="$g4[url]/$g4[bbs]/rss.php?bo_table=$bo_table"?>" />
<?php } ?>
<?php if ($bo_table && $wr_id && strstr($write['wr_option'], 'secret')) echo '<meta name="robots" content="noindex">'.PHP_EOL; ?>
<link rel="image_src" href="<?php echo $image_src?>" />
<link rel="shortcut icon" href="<?php echo G5_IMG_URL?>/shortcut.ico">
<title><?php echo $g5_head_title; ?></title>
<link rel="stylesheet" href="<?php echo G5_THEME_CSS_URL; ?>/<?php echo G5_IS_MOBILE ? 'mobile' : 'default'; ?>.css">
<!--[if lte IE 8]>
<script src="<?php echo G5_JS_URL ?>/html5.js"></script>
<![endif]-->
<script>
// 자바스크립트에서 사용하는 전역변수 선언
var g5_url       = "<?php echo G5_URL ?>";
var g5_bbs_url   = "<?php echo G5_BBS_URL ?>";
var g5_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
var g5_is_admin  = "<?php echo isset($is_admin)?$is_admin:''; ?>";
var g5_is_mobile = "<?php echo G5_IS_MOBILE ?>";
var g5_bo_table  = "<?php echo isset($bo_table)?$bo_table:''; ?>";
var g5_sca       = "<?php echo isset($sca)?$sca:''; ?>";
var g5_editor    = "<?php echo ($config['cf_editor'] && $board['bo_use_dhtml_editor'])?$config['cf_editor']:''; ?>";
var g5_cookie_domain = "<?php echo G5_COOKIE_DOMAIN ?>";
var g4_path      = "<?php echo $g4['path']?>";
var g4_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
<?php
if ($is_admin) {
    echo 'var g5_admin_url = "'.G5_ADMIN_URL.'";'.PHP_EOL;
}
mw_script('/'.G5_JS_DIR.'/jquery-1.8.3.min.js');
mw_script('/'.G5_JS_DIR.'/common.js');
mw_script('/'.G5_JS_DIR.'/wrest.js');
?>
</script>
<?php
if(G5_IS_MOBILE) {
    mw_script('/'.G5_JS_DIR.'/modernizr.custom.70111.js');
}
if(!defined('G5_IS_ADMIN'))
    echo $config['cf_add_script'];
?>
</head>
<body>
<?php
if ($is_member) { // 회원이라면 로그인 중이라는 메세지를 출력해준다.
    $sr_admin_msg = '';
    if ($is_admin == 'super') $sr_admin_msg = "최고관리자 ";
    else if ($is_admin == 'group') $sr_admin_msg = "그룹관리자 ";
    else if ($is_admin == 'board') $sr_admin_msg = "게시판관리자 ";

    echo '<div id="hd_login_msg">'.$sr_admin_msg.get_text($member['mb_nick']).'님 로그인 중 ';
    echo '<a href="'.G5_BBS_URL.'/logout.php">로그아웃</a></div>';
}
?>
