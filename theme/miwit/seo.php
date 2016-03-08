<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$seo_title      = '';
$seo_desc       = '';
$seo_image      = '';
$seo_url        = '';
$seo_author     = '';
$seo_date       = '';
$seo_site_name  = '';
$seo_tag        = '';
$seo_robots     = "index, follow";

if ($g4['title'])
    $seo_title = $g5['title'];

if ($g5['title'])
    $seo_title = $g5['title'];

$seo_title = preg_replace("/\n/", " ", $seo_title);
$seo_title = preg_replace("/\"/", "", $seo_title);
$seo_title = preg_replace("/'/", "", $seo_title);
$seo_title = preg_replace("/,/", "", $seo_title);
$seo_title = preg_replace("/http:\/\/[^\s]+/", "", $seo_title);
$seo_title = trim($seo_title);
$seo_title = cut_str($seo_title, 255);

$seo_site_name = cut_str(trim(strip_tags($config['cf_title'])), 255);

if ($bo_table and $wr_id and $write)
{
    $sql = " select bf_file ";
    $sql.= "   from {$g5['board_file_table']} ";
    $sql.= "  where bo_table = '{$bo_table}' ";
    $sql.= "    and wr_id = '{$wr_id}' ";
    $sql.= "    and bf_width > 0 ";
    $sql.= "  order by bf_no ";
    $sql.= "  limit 1 ";

    $fb_file = sql_fetch($sql);

    if ($fb_file) {
        $seo_image = G5_URL."/data/file/".$bo_table."/".$fb_file['bf_file'];
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
                $seo_image = $mat;
                break;
            }
        }

        if (!$mat) {
            $seo_image = G5_PATH."/data/file/".$bo_table."/thumbnail/".$wr_id.".jpg";

            if (!@is_file($seo_image))
                $seo_image = G5_PATH."/data/file/".$bo_table."/thumbnail/".$wr_id;

            if (!@is_file($seo_image))
                $seo_image = G5_PATH."/data/file/".$bo_table."/thumb/".$wr_id;

            if (!@is_file($seo_image))
                $seo_image = '';
            else
                $seo_image = str_replace(G5_PATH, G5_URL, $seo_image);
        }
    }

    $seo_title = cut_str(trim(strip_tags($write['wr_subject'])), 255);

    $seo_desc = $write['wr_content'];
    $seo_desc = trim(preg_replace("/{이미지:[0-9]+}/iUs", "", $seo_desc));
    $seo_desc = strip_tags($seo_desc);
    $seo_desc = explode("\n", $seo_desc);
    for ($i=0, $m=count($seo_desc); $i<$m; $i++) {
        $seo_desc[$i] = trim($seo_desc[$i]);
    }
    $seo_desc = implode(" ", $seo_desc);
    $seo_desc = preg_replace("/\n/", " ", $seo_desc);
    $seo_desc = preg_replace("/\"/", "", $seo_desc);
    $seo_desc = preg_replace("/'/", "", $seo_desc);
    $seo_desc = preg_replace("/,/", "", $seo_desc);
    $seo_desc = preg_replace("/http:\/\/[^\s]+/", "", $seo_desc);
    $seo_desc = trim($seo_desc);
    $seo_desc = cut_str($seo_desc, 255);

    $seo_date = $write['wr_datetime'];

    $seo_author = $write['wr_name'];

    $seo_tag = $board['bo_subject'];


    if (strstr($write['wr_option'], 'secret'))
        $seo_robots = "noindex";

    $seo_url = mw_seo_url($bo_table, $wr_id);
}

if (!$seo_title)
    $seo_title = $mw['config']['cf_title'];

if (!$seo_image or !@is_file(str_replace(G5_URL, G5_PATH, $seo_image)))
    $seo_image = G5_DATA_URL."/seo/sns_image.png";

$seo_image_size = @getimagesize(str_replace(G5_URL, G5_PATH, $seo_image));
$seo_image_x = $seo_image_size[0];
$seo_image_y = $seo_image_size[1];

if (!$seo_url)
    $seo_url  = set_http($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

if (!$seo_desc)
    $seo_desc = addslashes($mw['config']['cf_desc']);

if (!$seo_author)
    $seo_author = addslashes($mw['config']['cf_author']);

if (!$seo_author) {
    $tmp = get_member($config['cf_admin'], "mb_nick");
    $seo_author = $tmp['mb_nick'];
}

$seo_favicon = '';
if (is_file(G5_DATA_PATH.'/seo/favicon.ico') and filesize(G5_DATA_PATH.'/seo/favicon.ico'))
    $seo_favicon = G5_DATA_URL.'/seo/favicon.ico';

$seo_phone_icon = '';
if (is_file(G5_DATA_PATH.'/seo/phone_icon.png') and filesize(G5_DATA_PATH.'/seo/phone_icon.png'))
    $seo_phone_icon = G5_DATA_URL.'/seo/phone_icon.png';

ob_start();
?>
<!-- default -->
<meta name="keywords" content="<?php echo $seo_tag?>" />
<meta name="author" content="<?php echo $seo_author?>" />
<meta name="robots" content="<?php echo $seo_robots?>" />
<meta name="description" content="<?php echo $seo_desc?>" />
<link rel="canonical" href="<?php echo $seo_url?>" />
<link rel="alternate" href="<?php echo $seo_url?>" />
<?php if ($seo_favicon) { ?>
<link rel="shortcut icon" href="<?php echo $seo_favicon?>" />
<?php } ?>
<link rel="apple-touch-icon" href="<?php echo $seo_phone_icon?>" />
<link rel="image_src" href="<?php echo $seo_image?>" />

<!-- facebook-->
<meta property="fb:app_id" content="<?php echo $mw['config']['cf_facebook_appid']?>" />
<meta property="og:locale" content="ko_KR" />
<meta property="og:type" content="article" />
<meta property="og:site_name" content="<?php echo $seo_site_name?>" />
<meta property="og:title" content="<?php echo $seo_title?>" />
<meta property="og:url" content="<?php echo $seo_url?>" />
<meta property="og:image" content="<?php echo $seo_image?>" />
<meta property="og:description" content="<?php echo $seo_desc?>" />

<meta property="article:tag" content="<?php echo $seo_tag?>" />
<meta property="article:section" content="<?php echo $seo_tag?>" />
<meta property="article:publisher" content="<?php echo $seo_author?>" />
<meta property="article:author" content="<?php echo $seo_author?>" />

<!-- google -->
<meta itemprop="headline" content="<?php echo $seo_title?>" />
<meta itemprop="alternativeHeadline" content="<?php echo $seo_title?>" />
<meta itemprop="name" content="<?php echo $seo_title?>" />
<meta itemprop="description" content="<?php echo $seo_desc?>" />

<meta itemprop="image" content="<?php echo $seo_image?>" />
<meta itemprop="url" content="<?php echo $seo_url?>" />
<meta itemprop="thumbnailUrl" content="<?php echo $seo_image?>" />

<meta itemprop="publisher" content="<?php echo $seo_author?>" />
<meta itemprop="genre" content="blog" />
<meta itemprop="inLanguage" content="ko-kr" />

<!-- twitter -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="<?php echo $mw['config']['cf_twitter']?>" />
<meta name="twitter:creator" content="<?php echo $mw['config']['cf_twitter']?>" />
<meta name="twitter:url" content="<?php echo $seo_url?>" />
<meta name="twitter:image" content="<?php echo $seo_image?>" />
<meta name="twitter:title" content="<?php echo $seo_title?>" />
<meta name="twitter:description" content="<?php echo $seo_desc?>" />

<!-- nateon -->
<meta name="nate:title" content="<?php echo $seo_title?>" />
<meta name="nate:site_name" content="<?php echo $seo_site_name?>" />
<meta name="nate:url" content="<?php echo $seo_url?>" />
<meta name="nate:image" content="<?php echo $seo_image?>" />
<meta name="nate:description" content="<?php echo $seo_desc?>" />

<?php if ($bo_table and $board['bo_use_rss_view'] and $board['bo_read_level'] == 1) : ?>
<link rel="alternate" type="application/rss+xml" title="<?php echo $config['cf_title']?>, <?php echo $board['bo_subject']?>" href="<?php echo G5_BBS_URL."/rss.php?bo_table=".$bo_table?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php echo G5_BBS_URL."/rss.php?bo_table=".$bo_table?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php echo G5_BBS_URL."/rss.php?bo_table=".$bo_table?>" />
<?php endif; ?>


<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "BlogPosting",
    "mainEntityOfPage":{
        "@type":"WebPage",
        "@id":"<?php echo $seo_url?>"
    },
    "headline": "<?php echo $seo_title?>",
    "image": {
        "@type": "ImageObject",
        "url": "<?php echo $seo_image?>",
        "height": '<?php echo $seo_image_y?>',
        "width": '<?php echo $seo_image_x?>'
    },
    "datePublished": "<?php echo date('c', strtotime($seo_date))?>",
    "dateModified": "<?php echo date('c', strtotime($seo_date))?>",
    "author": {
        "@type": "Person",
        "name": "<?php echo $seo_author?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "<?php echo $seo_author?>",
    },
    "description": "<?php echo $seo_desc?>"
}
</script>

<?php
$seo = ob_get_clean();

return $seo;
