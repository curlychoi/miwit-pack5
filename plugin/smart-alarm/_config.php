<?php
/**
 * 스마트알람 (Smart-Alarm for Gnuboard4)
 *
 * Copyright (c) 2011 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

$mw_moa_table = "mw_moa";
$mw_moa_config_table = "mw_moa_config";

$default_charset = '';
if (preg_match("/^utf/i", $g4['charset']))
    $default_charset = "default charset=utf8;";

$sql = "create table if not exists {$mw_moa_table} (
mo_id int unsigned not null auto_increment,
mb_id varchar(20) not null,
bo_table varchar(20) not null,
wr_parent int not null,
wr_id int not null,
wr_mb_id varchar(20) not null,
re_mb_id varchar(20) not null,
mo_work varchar(1) not null,
mo_flag varchar(1) not null,
mo_datetime datetime not null,
primary key (mo_id),
index (mb_id, mo_datetime)
) ".$default_charset;
sql_query($sql);

$sql = "create table if not exists {$mw_moa_config_table} (
mb_id varchar(20) not null,
cf_config varchar(10) not null,
cf_reject_board text not null,
primary key (mb_id)
) ".$default_charset;
sql_query($sql);

$comment_image_path = $g4['path']."/data/mw.basic.comment.image"; 

$mw_moa_path = $g4['path']."/plugin/smart-alarm";
$mw_moa_date = 7; // 지난 알람삭제 (기본 7일)

include_once($mw_moa_path."/_lib.php");

