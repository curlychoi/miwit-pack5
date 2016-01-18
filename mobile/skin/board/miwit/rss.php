<?php
include_once("_common.php");

if (!$bo_table)
    die("bo_table 이 없습니다.");

$lib_permalink = $g4['path'].'/lib/mw.permalink.lib.php';
if (@is_file($lib_permalink))
    include_once($lib_permalink);

$lib_mw_basic = $board_skin_path.'/mw.lib/mw.skin.basic.lib.php';
if (@is_file($lib_mw_basic))
    include_once($lib_mw_basic);
else
    die("배추스킨 설정 파일이 없습니다.");

if ($board['bo_read_level'] >= 2)
    die("비회원 읽기가 가능한 게시판만 RSS 지원합니다.");

if (!$board['bo_use_rss_view'])
    die("RSS 보기가 금지되어 있습니다.");

$cf_rss_limit = $mw_basic['cf_rss_limit'];
if (!$cf_rss_limit)
    $cf_rss_limit = 10;

ob_start();
echo "<?xml version=\"1.0\" encoding=\"{$g4['charset']}\"?".">\n";
echo "<rss version=\"2.0\">\n";
echo "<channel>\n";
echo "<title>".addslashes($config['cf_title'])."</title>\n";
echo "<link>".set_http($g4['url'])."</link>\n";

$sql = " select * from {$write_table} where wr_is_comment = 0 order by wr_num limit {$cf_rss_limit}";
$qry = sql_query($sql);

while ($write = sql_fetch_array($qry))
{
    $mw_basic = array();

    if ($mw_basic['cf_attribute'] == '1:1')
        break; // 1:1게시판이면 끝!

    if (strstr($write['wr_option'], "secret"))
        continue; // 비밀글이면 끝!

    if ($write['wr_singo'] && $write['wr_singo'] >= $mw_basic['cf_singo_number'] && $mw_basic['cf_singo_write_block'])
        continue; // 신고게시물 끝!

    if ($write['wr_view_block'])
        continue; // 보기차단 게시물 끝!

    $is_anonymous = false;
    if ($mw_basic['cf_attribute'] == 'anonymous')
        $is_anonymous = true;

    if ($mw_basic['cf_anonymous'] && $write['wr_anonymous'])
        $is_anonymous = true;

    if ($is_anonymous)
        $write['wr_name'] = '익명';

    $bo_subject = htmlspecialchars($board['bo_subject']);

    $html = 0;
    if (strstr($write['wr_option'], "html1"))
        $html = 1;
    else if (strstr($write['wr_option'], "html2"))
        $html = 2;

    $view = get_view($write, $board, $board_skin_path, 255);
    $view['content'] = conv_content($view['wr_content'], $html);

    $file_viewer = $board_skin_path.'/mw.proc/mw.file.viewer.php';
    if (is_mw_file($file_viewer)) include($file_viewer);

    if (function_exists("mw_path_to_url"))
        $view['rich_content'] = mw_path_to_url($view['rich_content']);

    $find = array('&amp;', '&nbsp;');
    $replace = array('&', ' '); 

    $content = str_replace($find, $replace, $view['rich_content']);

    $row['id'] = $write['wr_id'];
    $row['title'] = htmlspecialchars($write['wr_subject']);
    $row['author'] = htmlspecialchars($write['wr_name']);
    $row['link'] = mw_seo_url($bo_table, $write['wr_id']);
    $row['link'] = str_replace("&", "&amp;", $row['link']);
    
    $row['pubdate'] = date("c", strtotime($write['wr_datetime']));
    if ($write['wr_last'])
        $row['pubdate'] = date("c", strtotime($write['wr_last']));
    $row['content'] = $content;

    echo "<item>\n";
    echo "<title><![CDATA[{$row['title']}]]></title>\n";
    echo "<link>".$row['link']."</link>\n";
    echo "<author>{$row['author']}</author>\n";
    echo "<pubDate>".$row['pubdate']."</pubDate>\n";
    echo "<description><![CDATA[".$row['content']."]]></description>\n";
    echo "<category>".addslashes($write['ca_name'])."</category>\n";
    echo "</item>\n";
}
echo "</channel>\n";
echo "</rss>\n";

$xml = ob_get_clean();

header("content-type: text/xml"); 
header("cache-control: no-cache, must-revalidate"); 
header("pragma: no-cache");   

echo $xml;


