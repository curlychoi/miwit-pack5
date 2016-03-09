<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if (isset($prev['wr_id']) && $prev['wr_id']) {
    $prev_href = mw_seo_url($bo_table, $prev['wr_id'], $qstr);
}

if (isset($next['wr_id']) && $next['wr_id']) {
    $next_href = mw_seo_url($bo_table, $next['wr_id'], $qstr);
}

if ($list_href) $list_href = mw_bbs_path($list_href);
if ($search_href) $search_href = mw_bbs_path($search_href);
if ($write_href) $write_href = mw_bbs_path($write_href);
if ($update_href) $update_href = mw_bbs_path($update_href);
if ($reply_href) $reply_href = mw_bbs_path($reply_href);
if ($delete_href) $delete_href = mw_bbs_path($delete_href);
if ($prev_part_href) $prev_part_href = mw_bbs_path($prev_part_href);
if ($next_part_href) $next_part_href = mw_bbs_path($next_part_href);
if ($prev_href) $prev_href = mw_bbs_path($prev_href);
if ($next_href) $next_href = mw_bbs_path($next_href);

if ($mw['config']['cf_seo_url'])
{
    //$mw_basic['cf_umz'] = null;
    //$mw_basic['cf_shorten'] = 1;

    $list_href = mw_seo_url($bo_table, 0);
    if ($page)
        $list_href .= '?page='.$page;

    if ($search_href)
        $search_href = mw_seo_url($bo_table, 0, "&page={$page}".$qstr);

    if ($prev_part_href) $prev_part_href = mw_seo_bbs_path($prev_part_href);
    if ($next_part_href) $next_part_href = mw_seo_bbs_path($next_part_href);
    if ($prev_href) $prev_href = mw_seo_bbs_path($prev_href);
    if ($next_href) $next_href = mw_seo_bbs_path($next_href);
}

$rss_href = '';
if ($board['bo_use_rss_view']) $rss_href = $g4['bbs_path']."/rss.php?bo_table=$bo_table";

