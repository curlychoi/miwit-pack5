<?php
include_once("_common.php");

header("Content-type: text/xml");

if (!$bo_table) { // group
    ob_start();
    echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'.PHP_EOL;
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

    $sql = " select bo_table from {$g5['board_table']} where bo_read_level = '1' ";
    $qry = sql_query($sql);

    while ($board = sql_fetch_array($qry))
{
        $write_table = $g5['write_prefix'].$board['bo_table'];
        $sql = " select wr_datetime from {$write_table} where wr_is_comment = 0 and !FIND_IN_SET('secret', wr_option) order by wr_num limit 1";
        $write = sql_fetch ($sql);

        $lastmod = strtotime($write['wr_datetime']);
        $lastmod = date("c", $lastmod);

        $loc = set_http(G5_URL.'/sitemap.php?bo_table='.$board['bo_table']);

        echo '<sitemap>'.PHP_EOL;
        echo '<loc>'.$loc.'</loc>'.PHP_EOL;
        echo '<lastmod>'.$lastmod.'</lastmod>'.PHP_EOL;
        echo '</sitemap>'.PHP_EOL;
    }
    echo '</sitemapindex>'.PHP_EOL;
    $xml = ob_get_clean();

    echo $xml;
    exit;
}

ob_start();
echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'.PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;

$sql = " select wr_id, wr_datetime from {$write_table} where wr_is_comment = 0 and !FIND_IN_SET('secret', wr_option) order by wr_num";
$qry = sql_query($sql);

while ($write = sql_fetch_array($qry))
{
    $loc = mw_seo_url($bo_table, $write['wr_id']);
    $loc = set_http($loc);

    $lastmod = strtotime($write['wr_datetime']);
    $lastmod = date("c", $lastmod);

    echo '<url>'.PHP_EOL;
    echo '<loc>'.$loc.'</loc>'.PHP_EOL;
    echo '<lastmod>'.$lastmod.'</lastmod>'.PHP_EOL;
    echo '<changefreq>monthly</changefreq>'.PHP_EOL;
    echo '<priority>0.8</priority>'.PHP_EOL;
    echo '</url>'.PHP_EOL;
}
echo '</urlset>'.PHP_EOL;
$xml = ob_get_clean();

echo $xml;
exit;

