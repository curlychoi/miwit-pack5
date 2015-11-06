<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

$mw_syndi_path = $g4['path'].'/plugin/naver-syndi';

// 신디케이션 파일 없으면 제외
if (!@is_file($mw_syndi_path.'/_config.php')) return;

// 그룹접근 사용 제외
if ($group['gr_use_access']) return;

// 읽기 레벨 2이상 제외
if ($board['bo_read_level'] > 1) return;

// 1:1 게시판 제외
if ($mw_basic['cf_attribute'] == "1:1") return;

// 게시물별 레벨 설정시 2레벨 이상 제외
if ($mw_basic['cf_read_level'] && $wr_read_level > 1)  return;
if ($mw_basic['cf_read_level'] && $write['wr_read_level'] > 1)  return;

// 비밀글 제외
if ($secret) return;
if (strstr($write['wr_option'], $secret)) return;

// 컨텐츠샵 내용보기 결제 제외
if ($mw_basic['cf_contents_shop'] == '2' and $wr_contents_price) return;
if ($mw_basic['cf_contents_shop'] == '2' and $write['wr_contents_price']) return;

include($mw_syndi_path.'/_config.php');
include_once($mw_syndi_path.'/_lib.php');

if (!$wr_content)
    $wr_content = $write['wr_content'];

if ($mw_syndi['comment'] && $comment_id)
    mw_syndi_set_feed($bo_table, $comment_id, $wr_content, $w); // 코멘트
else
    mw_syndi_set_feed($bo_table, $wr_id, $wr_content, $w); // 원글
