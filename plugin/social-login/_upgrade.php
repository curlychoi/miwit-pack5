<?php
/**
 * 소셜로그인 (Social-login for Gnuboard4)
 *
 * Copyright (c) 2012 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

$sql = " create table if not exists mw_facebook_login ( ";
$sql.= "   mb_id varchar(20) not null ";
$sql.= " , facebook_access_token varchar(255) not null ";
$sql.= " , facebook_id varchar(20) not null ";
$sql.= " , facebook_name varchar(50) not null ";
$sql.= " , facebook_username varchar(50) not null ";
$sql.= " , facebook_birthday varchar(12) not null ";
$sql.= " , facebook_email varchar(255) not null ";
$sql.= " , facebook_link varchar(255) not null ";
$sql.= " , facebook_gender varchar(6) not null ";
$sql.= " , facebook_locale varchar(10) not null ";
$sql.= " , facebook_datetime datetime not null ";
$sql.= " , primary key (facebook_id) ";
$sql.= " , index (mb_id) ";
$sql.= " )";
sql_query($sql);

sql_query("alter table mw_facebook_login add facebook_bio varchar(255) not null", false);

$sql = " create table if not exists mw_twitter_login ( ";
$sql.= "   mb_id varchar(20) not null ";
$sql.= " , twitter_id varchar(20) not null ";
$sql.= " , twitter_screen_name varchar(50) not null ";
$sql.= " , twitter_name varchar(50) not null ";
$sql.= " , twitter_url varchar(255) not null ";
$sql.= " , twitter_lang varchar(10) not null ";
$sql.= " , twitter_description varchar(255) not null ";
$sql.= " , twitter_datetime datetime not null ";
$sql.= " , primary key (twitter_id) ";
$sql.= " , index (mb_id) ";
$sql.= " )";
sql_query($sql);

$sql = " create table if not exists mw_google_login ( ";
$sql.= "   id int unsigned not null auto_increment";
$sql.= " , mb_id varchar(20) not null ";
$sql.= " , google_id varchar(30) not null ";
$sql.= " , google_name varchar(50) not null ";
$sql.= " , google_email varchar(255) not null ";
$sql.= " , google_link varchar(255) not null ";
$sql.= " , google_gender varchar(6) not null ";
$sql.= " , google_locale varchar(10) not null ";
$sql.= " , google_datetime datetime not null ";
$sql.= " , primary key (id) ";
$sql.= " , index (google_id) ";
$sql.= " , index (mb_id) ";
$sql.= " )";
sql_query($sql);

$sql = " create table if not exists mw_naver_login ( ";
$sql.= "   id int unsigned not null auto_increment";
$sql.= " , mb_id varchar(20) not null ";
$sql.= " , naver_id varchar(70) not null ";
$sql.= " , naver_nickname varchar(50) not null ";
$sql.= " , naver_email varchar(255) not null ";
$sql.= " , naver_gender varchar(1) not null ";
$sql.= " , naver_age varchar(6) not null ";
$sql.= " , naver_birthday varchar(5) not null ";
$sql.= " , naver_datetime datetime not null ";
$sql.= " , primary key (id) ";
$sql.= " , index (naver_id) ";
$sql.= " , index (mb_id) ";
$sql.= " )";
sql_query($sql);

$sql = " create table if not exists mw_kakao_login ( ";
$sql.= "   id int unsigned not null auto_increment";
$sql.= " , mb_id varchar(20) not null ";
$sql.= " , kakao_id varchar(70) not null ";
$sql.= " , kakao_nickname varchar(50) not null ";
$sql.= " , kakao_datetime datetime not null ";
$sql.= " , primary key (id) ";
$sql.= " , index (kakao_id) ";
$sql.= " , index (mb_id) ";
$sql.= " )";
sql_query($sql);

