<?php
/**
 * Bechu basic skin for gnuboard4
 *
 * copyright (c) 2008 Choi Jae-Young <www.miwit.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// gr_id 입력 안된것 보완 v.1.0.1
if (!$mw_basic[gr_id])
    sql_query("update $mw[basic_config_table] set gr_id = '$gr_id' where bo_table = '$bo_table'", false);

// gr_id 변경 체크 v.1.0.3
if ($mw_basic[gr_id] != $gr_id) {
    $sql = "update $mw[basic_config_table] set gr_id = '$gr_id' where bo_table = '$bo_table'";
    $res = sql_query($sql, false);
}

if (!$mw_basic)
{
    $sql = "insert into $mw[basic_config_table] set gr_id = '$gr_id', bo_table = '$bo_table'";
    $res = sql_query($sql, false);
    if (!$res)
    {
        // 스킨 설정 테이블 자동생성
        $sql = "create table $mw[basic_config_table] (
        gr_id varchar(20) default '' not null,
        bo_table varchar(20) default '' not null,
        cf_type varchar(5) default 'list' not null,
        cf_thumb_width smallint default '80' not null,
        cf_thumb_height smallint default '50' not null,
        cf_attribute varchar(10) default 'basic' not null,
        cf_ccl tinyint default '0' not null,
        cf_age varchar(5) not null,
        cf_gender varchar(1) not null,
        cf_hot tinyint default '0' not null,
        cf_hot_basis varchar(10) default 'hit' not null,
        cf_hot_limit tinyint default '10' not null,
        cf_related tinyint default '0' not null,
        cf_link_blank tinyint default '1' not null,
        cf_zzal tinyint default '0' not null,
        cf_zzal_must tinyint default '0' not null,
        cf_source_copy tinyint default '1' not null,
        cf_relation tinyint default '1' not null,
        cf_comment_editor tinyint  not null,
        cf_comment_emoticon tinyint default '0' not null,
        cf_comment_write tinyint default '1' not null,
        cf_singo tinyint default '1' not null,
        cf_singo_id text not null,
        cf_email text not null,
        cf_sms_id varchar(100) not null,
        cf_sms_pw varchar(100) not null,
        cf_hp text not null,
        cf_content_head text not null,
        cf_content_tail text not null,
        primary key (gr_id, bo_table)) $default_charset";
        sql_query($sql, false);

        $sql = "insert into $mw[basic_config_table] set gr_id = '$gr_id', bo_table = '$bo_table'";
        $res = sql_query($sql, false);
    }

    $sql = "select * from $mw[basic_config_table] where bo_table = '$bo_table'";
    $mw_basic = sql_fetch($sql, false);
}
// 게시판 테이블에 CCL 항목 자동 추가
if (is_null($write[wr_ccl])) {
    $sql = "alter table $write_table add wr_ccl varchar(10) default '' not null";
    sql_query($sql, false);
}

// 게시판 테이블에 신고 항목 자동 추가
if (is_null($write[wr_singo])) {
    $sql = "alter table $write_table add wr_singo tinyint default '0' not null";
    sql_query($sql, false);
}

// 게시판 테이블에 짤방 항목 자동 추가
if (is_null($write[wr_zzal])) {
    $sql = "alter table $write_table add wr_zzal varchar(255) default '짤방' not null";
    sql_query($sql, false);
}

// 게시판 테이블에 관련글 항목 자동 추가
if (is_null($write[wr_related])) {
    $sql = "alter table $write_table add wr_related varchar(255) default '' not null";
    sql_query($sql, false);
}

// 스킨환경정보에 글번호, 조회수등 컴마설정 자동추가 v.1.0.1
if (is_null($mw_basic[cf_comma])) {
    $sql = "alter table $mw[basic_config_table] add cf_comma tinyint default '0' not null";
    sql_query($sql, false);
}

// 코멘트 공지 자동추가 v.1.0.1
if (is_null($mw_basic[cf_comment_notice])) {
    $sql = "alter table $mw[basic_config_table] add cf_comment_notice text default '' not null";
    sql_query($sql, false);
}

// 다운로드 제한(코멘트 강제) 자동추가 v.1.0.1
if (is_null($mw_basic[cf_download_comment])) {
    $sql = "alter table $mw[basic_config_table] add cf_download_comment tinyint default '0' not null";
    sql_query($sql, false);
}

// 업로더 포인트 제공 자동추가 v.1.0.1
if (is_null($mw_basic[cf_uploader_point])) {
    $sql = "alter table $mw[basic_config_table] add cf_uploader_point tinyint default '0' not null";
    $sql .= ", add cf_uploader_day tinyint default '0' not null";
    sql_query($sql, false);
}

// 자동등록방지 코드 이미지 사용 - 그누보드4 최신버전과 이전버전의 호환성 v.1.0.1
if (is_null($mw_basic[cf_norobot_image])) {
    $sql = "alter table $mw[basic_config_table] add cf_norobot_image tinyint default '1' not null";
    sql_query($sql, false);
}

// 코멘트 입력시 비밀글 체크 기본설정기능 자동추가 v.1.0.1
if (is_null($mw_basic[cf_comment_secret])) {
    $sql = "alter table $mw[basic_config_table] add cf_comment_secret tinyint default '0' not null";
    sql_query($sql, false);
}

// 요약형 본문 글자수 설정 자동추가 v.1.0.1
if (is_null($mw_basic[cf_desc_len])) {
    $sql = "alter table $mw[basic_config_table] add cf_desc_len int default '150' not null";
    sql_query($sql, false);
}
    sql_query("alter table $mw[basic_config_table] add cf_desc_use tinyint default '0' not null", false);

// 권한에 따른 쓰기버튼 출력 옵션 v.1.0.2
if (is_null($mw_basic[cf_write_button])) {
    $sql = "alter table $mw[basic_config_table] add cf_write_button tinyint default '1' not null";
    sql_query($sql, false);
}

// 권한별 제목링크 v.1.0.2
if (is_null($mw_basic[cf_subject_link])) {
    $sql = "alter table $mw[basic_config_table] add cf_subject_link tinyint default '0' not null";
    sql_query($sql, false);
}

// 코멘트 금지 기능 v.1.0.2
if (is_null($mw_basic[cf_comment_ban])) {
    $sql = "alter table $mw[basic_config_table] add cf_comment_ban tinyint default '0' not null";
    sql_query($sql, false);
}
if (is_null($write[wr_comment_ban])) {
    $sql = "alter table $write_table add wr_comment_ban char(1) not null";
    sql_query($sql, false);
}

// 링크 게시판 
if (is_null($mw_basic[cf_link_board])) {
    $sql = "alter table $mw[basic_config_table] add cf_link_board tinyint default '0' not null";
    sql_query($sql, false);
}

// 링크 게시판 게시물별
//if (is_null($mw_basic[cf_link_write])) {
    $sql = "alter table $mw[basic_config_table] add cf_link_write tinyint default '0' not null";
    sql_query($sql, false);
    $sql = "alter table $write_table add wr_link_write varchar(1) default '' not null";
    sql_query($sql, false);
//}

// 공지사항 이름, 날짜, 조회수 출력 여부 
if (is_null($mw_basic[cf_notice_name])) {
    sql_query("alter table $mw[basic_config_table] add cf_notice_name tinyint default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_notice_date tinyint default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_notice_hit tinyint default '0' not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_notice_good varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] change cf_notice_good cf_notice_good varchar(1) not null", false);

// 일반게시물 이름, 날짜, 조회수 출력 여부 
if (is_null($mw_basic[cf_notice_name])) {
    sql_query("alter table $mw[basic_config_table] add cf_post_name tinyint default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_post_date tinyint default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_post_hit tinyint default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_post_num tinyint default '0' not null", false);
}

// 코멘트 금지 레벨설정 기능 v.1.0.2
if (is_null($mw_basic[cf_comment_ban_level])) {
    $sql = "alter table $mw[basic_config_table] add cf_comment_ban_level tinyint default '10' not null";
    sql_query($sql, false);
}

// 게시글 히스토리 v.1.0.2
if (is_null($mw_basic[cf_post_history])) {
    $sql = "alter table $mw[basic_config_table] add cf_post_history char(1) not null";
    sql_query($sql, false);

    $sql = "alter table $mw[basic_config_table] add cf_post_history_level tinyint default '10' not null";
    sql_query($sql, false);

    $sql = "alter table $mw[basic_config_table] add cf_delete_log char(1) not null";
    sql_query($sql, false);

    $sql = "create table $mw[post_history_table] (
            ph_id int unsigned auto_increment not null
            ,bo_table varchar(20) not null
            ,wr_id int not null
            ,wr_parent int not null
            ,mb_id varchar(20) not null
            ,ph_name varchar(255)
            ,ph_option set('html1', 'html2', 'secret', 'mail') not null
            ,ph_subject varchar(255) not null
            ,ph_content text not null
            ,ph_ip varchar(20) not null
            ,ph_datetime datetime not null
            ,primary key(ph_id)
            ,index(bo_table, wr_id, mb_id)) $default_charset";
    sql_query($sql, false);
}

// 다운로드 기록 v.1.0.2
if (is_null($mw_basic[cf_download_log])) {
    $sql = "alter table $mw[basic_config_table] add cf_download_log char(1) not null";
    sql_query($sql, false);

    $sql = "create table $mw[download_log_table] (
            dl_id int auto_increment not null
            ,bo_table varchar(20) not null
            ,wr_id int not null
            ,bf_no int not null
            ,mb_id varchar(20) not null
            ,dl_ip varchar(20) not null
            ,dl_datetime datetime not null
            ,primary key(dl_id)
            ,index(bo_table, wr_id, bf_no, mb_id)) $default_charset";
    sql_query($sql, false);
}

// 접근권한 v.1.0.2
if (is_null($mw_basic[cf_board_member])) {
    $sql = "alter table $mw[basic_config_table] add cf_board_member char(1) not null";
    sql_query($sql, false);

    // 게시판 접근권한 테이블 자동생성 v.1.0.2
    $sql = "create table $mw[board_member_table] (
    bo_table varchar(20) not null
    ,mb_id varchar(20) not null
    ,bm_datetime datetime not null
    ,bm_limit date not null
    ,primary key (bo_table)) $default_charset";
    sql_query($sql, false);
}

sql_query("alter table {$mw['board_member_table']} add bm_limit date not null", false);
sql_query("alter table {$mw['board_member_table']} change bm_limit bm_limit date not null", false);

// 접근권한 목록 v.1.0.2
if (is_null($mw_basic[cf_board_member_list])) {
    $sql = "alter table $mw[basic_config_table] add cf_board_member_list char(1) not null";
    sql_query($sql, false);
}

// 코멘트 기본 내용 v.1.0.2
if (is_null($mw_basic[cf_comment_default])) {
    $sql = "alter table $mw[basic_config_table] add cf_comment_default text not null";
    sql_query($sql, false);
}

// 게시물 목록 셔플 
if (is_null($mw_basic[cf_list_shuffle])) {
    $sql = "alter table $mw[basic_config_table] add cf_list_shuffle char(1) not null";
    sql_query($sql, false);
}

// 첫번째 첨부 이미지 본문 출력 안함 (썸네일용) 
if (is_null($mw_basic[cf_img_1_noview])) {
    $sql = "alter table $mw[basic_config_table] add cf_img_1_noview char(1) not null";
    sql_query($sql, false);
}

// 첨부파일 상단 
if (is_null($mw_basic[cf_file_head])) {
    $sql = "alter table $mw[basic_config_table] add cf_file_head text not null";
    sql_query($sql, false);
}

// 첨부파일 하단 
if (is_null($mw_basic[cf_file_tail])) {
    $sql = "alter table $mw[basic_config_table] add cf_file_tail text not null";
    sql_query($sql, false);
}

// 한사람당 글 한개만 
if (is_null($mw_basic[cf_only_one])) {
    $sql = "alter table $mw[basic_config_table] add cf_only_one char(1) not null";
    sql_query($sql, false);
}

// 배추컨텐츠샵 솔루션 사용
if (is_null($mw_basic[cf_contents_shop])) {
    $sql = "alter table $mw[basic_config_table] add cf_contents_shop char(1) not null";
    sql_query($sql, false);
}

// 컨텐츠 가격
if (is_null($write[wr_contents_price])) {
    $sql = "alter table $write_table add wr_contents_price int not null";
    sql_query($sql, false);
}

// 컨텐츠 사용 도메인 입력
if (is_null($write[wr_contents_domain])) {
    $sql = "alter table $write_table add wr_contents_domain char(1) not null";
    sql_query($sql, false);
}

// 관리자만 dhtml editor 사용
if (is_null($mw_basic[cf_admin_dhtml])) {
    $sql = "alter table $mw[basic_config_table] add cf_admin_dhtml char(1) not null";
    sql_query($sql, false);
}

// 관리자만 dhtml_comment editor 사용
if (is_null($mw_basic[cf_admin_dhtml_comment])) {
    $sql = "alter table $mw[basic_config_table] add cf_admin_dhtml_comment char(1) not null";
    sql_query($sql, false);
}

// 글쓰기 버튼 클릭시 공지
if (is_null($mw_basic[cf_write_notice])) {
    $sql = "alter table $mw[basic_config_table] add cf_write_notice text not null";
    sql_query($sql, false);
}

// 사용자 정의 css
if (is_null($mw_basic[cf_css])) {
    $sql = "alter table $mw[basic_config_table] add cf_css text not null";
    sql_query($sql, false);
}

// 썸네일 비율유지
if (is_null($mw_basic[cf_thumb_keep])) {
    $sql = "alter table $mw[basic_config_table] add cf_thumb_keep char(1) not null";
    sql_query($sql, false);
}

// 이미지 정보 
if (is_null($mw_basic[cf_exif])) {
    $sql = "alter table $mw[basic_config_table] add cf_exif char(1) not null";
    sql_query($sql, false);
}

// 인쇄 
if (is_null($mw_basic[cf_print])) {
    $sql = "alter table $mw[basic_config_table] add cf_print tinyint default '1' not null";
    sql_query($sql, false);
}

// 짧은글주소
if (is_null($mw_basic[cf_umz])) {
    $sql = "alter table $mw[basic_config_table] add cf_umz tinyint default '0' not null";
    sql_query($sql, false);
}
if (is_null($write[wr_umz])) {
    $sql = "alter table $write_table add wr_umz varchar(30) default '' not null";
    sql_query($sql, false);
}

// 짧은글주소 - 자체도메인
if (is_null($mw_basic[cf_shorten])) {
    $sql = "alter table $mw[basic_config_table] add cf_shorten tinyint default '0' not null";
    sql_query($sql, false);
}

// View 본문 상단 파일
if (is_null($mw_basic[cf_include_view_head])) {
    $sql = "alter table $mw[basic_config_table] add cf_include_view_head varchar(255) not null";
    sql_query($sql, false);
}

// View 본문 하단 파일
if (is_null($mw_basic[cf_include_view_tail])) {
    $sql = "alter table $mw[basic_config_table] add cf_include_view_tail varchar(255) not null";
    sql_query($sql, false);
}

// View 첨부파일 상단 파일
if (is_null($mw_basic[cf_include_file_head])) {
    $sql = "alter table $mw[basic_config_table] add cf_include_file_head varchar(255) not null";
    sql_query($sql, false);
}

// View 첨부파일 하단 파일
if (is_null($mw_basic[cf_include_file_tail])) {
    $sql = "alter table $mw[basic_config_table] add cf_include_file_tail varchar(255) not null";
    sql_query($sql, false);
}

// 목록 레코드
if (is_null($mw_basic[cf_include_list_main])) {
    $sql = "alter table $mw[basic_config_table] add cf_include_list_main varchar(255) not null";
    sql_query($sql, false);
}

// 코멘트 레코드
if (is_null($mw_basic[cf_include_comment_main])) {
    $sql = "alter table $mw[basic_config_table] add cf_include_comment_main varchar(255) not null";
    sql_query($sql, false);
}

// View 최상단
if (is_null($mw_basic[cf_include_view_top])) {
    $sql = "alter table $mw[basic_config_table] add cf_include_view_top varchar(255) not null";
    sql_query($sql, false);
}

// thumb 확장
if (is_null($mw_basic[cf_thumb2_width])) {
    sql_query("alter table $mw[basic_config_table] add cf_thumb2_width smallint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_thumb2_height smallint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_thumb3_width smallint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_thumb3_height smallint not null", false);
}

// 제목스타일
if (is_null($mw_basic[cf_subject_style])) {
    sql_query("alter table $mw[basic_config_table] add cf_subject_style tinyint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_subject_style_level tinyint not null", false);
}
    //sql_query("alter table $mw[basic_config_table] add cf_subject_style_color_default varchar(10) not null default '#3d5b7a'", false);
    sql_query("alter table $mw[basic_config_table] add cf_subject_style_color_default varchar(10) not null default '#555555'", false);
    sql_query("alter table $mw[basic_config_table] add cf_subject_style_color_picker varchar(1) not null default ''", false);

if (is_null($write[wr_subject_font])) {
    sql_query("alter table $write_table add wr_subject_font varchar(10) not null", false);
    sql_query("alter table $write_table add wr_subject_color varchar(10) not null", false);
}
    sql_query("alter table $write_table add wr_subject_bold varchar(1) not null", false);

//sql_query("alter table $mw[basic_config_table] change cf_uploader_point cf_uploader_point int not null", false);
//sql_query("alter table $mw[basic_config_table] change cf_uploader_day cf_uploader_day int not null", false);

// 썸네일2,3 비율유지
if (is_null($mw_basic[cf_thumb2_keep])) {
    sql_query("alter table $mw[basic_config_table] add cf_thumb2_keep char(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_thumb3_keep char(1) not null", false);
}

// 지업로더
if (is_null($mw_basic[cf_guploader])) {
    sql_query("alter table $mw[basic_config_table] add cf_guploader char(1) not null", false);
}

// 다운로드 팝업
if (is_null($mw_basic[cf_download_popup])) {
    sql_query("alter table $mw[basic_config_table] add cf_download_popup tinyint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_download_popup_msg text not null", false);
}

// 코멘트 기간
if (is_null($mw_basic[cf_comment_period])) {
    $sql = "alter table $mw[basic_config_table] add cf_comment_period int default '0' not null";
    sql_query($sql, false);
}

// 서비스 점검중 안내
if (is_null($mw_basic[cf_under_construction])) {
    $sql = "alter table $mw[basic_config_table] add cf_under_construction char(1) not null";
    sql_query($sql, false);
}

// 삭제금지
if (is_null($mw_basic[cf_no_delete])) {
    $sql = "alter table $mw[basic_config_table] add cf_no_delete char(1) not null";
    sql_query($sql, false);
}

// 글작성조건
if (is_null($mw_basic[cf_write_point])) {
    sql_query("alter table $mw[basic_config_table] add cf_write_point int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_write_register int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_write_day int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_write_day_count int not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_write_day_ip varchar(1) not null", false);

// 댓글작성조건
if (is_null($mw_basic[cf_comment_point])) {
    sql_query("alter table $mw[basic_config_table] add cf_comment_point int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_register int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_day int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_day_count int not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_comment_write_count int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_day_ip varchar(1) not null", false);

// 글작성자의 최신글
if (is_null($mw_basic[cf_latest])) {
    $sql = "alter table $mw[basic_config_table] add cf_latest tinyint not null";
    sql_query($sql, false);
}

// sns
if (is_null($mw_basic[cf_sns])) {
    $sql = "alter table $mw[basic_config_table] add cf_sns char(1) not null";
    sql_query($sql, false);
}

// 설문 
if (is_null($mw_basic[cf_vote])) {
    sql_query("alter table $mw[basic_config_table] add cf_vote char(1) not null", false);
}
    $sql = "create table if not exists $mw[vote_table] (
            vt_id int not null auto_increment,
            bo_table varchar(20) not null,
            wr_id int not null,
            vt_sdate datetime not null,
            vt_edate datetime not null,
            vt_total int not null,
            vt_point int not null,
            vt_multi int not null,
            primary key (vt_id),
            index (bo_table, wr_id)) $default_charset";
    sql_query($sql, false);
    sql_query("alter table $mw[vote_table] add vt_multi int not null", false);
    sql_query("alter table $mw[vote_table] add vt_sdate datetime not null after wr_id", false);
    sql_query("alter table $mw[vote_table] change vt_edate vt_edate datetime not null", false);
    sql_query("alter table $mw[vote_table] add vt_comment varchar(1) not null", false);
    $sql = "create table if not exists $mw[vote_item_table] (
            vt_id int not null,
            vt_num int not null,
            vt_item varchar(255) not null,
            vt_hit int not null,
            primary key (vt_id, vt_num)) $default_charset";
    sql_query($sql, false);
    $sql = "create table if not exists $mw[vote_log_table] (
            vt_id int not null,
            vt_num int not null,
            mb_id varchar(20) not null,
            vt_ip varchar(20) not null,
            vt_datetime datetime not null,
            index (vt_id, mb_id)) $default_charset";
    sql_query($sql,false);

// 설문등록가능 레벨 
if (is_null($mw_basic[cf_vote_level])) {
    $sql = "alter table $mw[basic_config_table] add cf_vote_level tinyint not null";
    sql_query($sql, false);
}

// 설문참여가능 레벨 
if (is_null($mw_basic[cf_vote_join_level])) {
    $sql = "alter table $mw[basic_config_table] add cf_vote_join_level tinyint not null";
    sql_query($sql, false);
}

// 리워드
if (is_null($mw_basic[cf_reward])) {
    sql_query("alter table $mw[basic_config_table] add cf_reward char(1) not null", false);
}
    $sql = "create table if not exists $mw[reward_log_table] (
        re_no int not null auto_increment,
        bo_table varchar(20) not null,
        wr_id int not null,
        mb_id varchar(20) not null,
        re_date date not null,
        re_time time not null,
        re_merchant_id varchar(255) not null,
        re_merchant_site varchar(255) not null,
        re_order_no varchar(255) not null,
        re_product_no varchar(255) not null,
        re_product_name varchar(255) not null,
        re_category varchar(255) not null,
        re_qty int not null,
        re_payment int not null,
        re_paytype varchar(255) not null,
        re_commission int not null,
        re_id varchar(255) not null,
        re_ip varchar(255) not null,
        primary key (re_no),
        index (bo_table, wr_id, mb_id)) $default_charset";
    sql_query($sql, false);

    $sql = "create table if not exists $mw[reward_table] (
        bo_table varchar(20) not null,
        wr_id int not null,
        re_company varchar(255) not null,
        re_site varchar(20) not null,
        re_point int not null,
        re_url varchar(255) not null,
        re_status char(1) not null,
        re_edate date not null,
        re_hit int not null,
        primary key (bo_table, wr_id)) $default_charset";
    sql_query($sql, false);

    sql_query("alter table $mw[reward_table] add re_company varchar(255) default '' not null after wr_id", false);

// 목록에서 추천,비추천 출력 여부
if (is_null($mw_basic[cf_list_good])) {
    sql_query("alter table $mw[basic_config_table] add cf_list_good tinyint default '0' not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_list_nogood tinyint default '0' not null", false);

// 추천,비추천 그래프 사용
if (is_null($mw_basic[cf_good_graph])) {
    sql_query("alter table $mw[basic_config_table] add cf_good_graph char(1) not null", false);
}

// 신고 삭제? 이동?
if (is_null($mw_basic[cf_singo_after])) {
    sql_query("alter table $mw[basic_config_table] add cf_singo_after varchar(20) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_singo_number tinyint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_singo_id_block char(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_singo_write_block char(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_singo_level tinyint default '2' not null", false);
}
    sql_query("create table if not exists $mw[singo_log_table] (
        si_id int not null auto_increment,
        bo_table varchar(20) not null,
        wr_id int not null,
        mb_id varchar(20) not null,
        si_type varchar(255) not null,
        si_memo text not null,
        si_datetime datetime not null,
        si_ip varchar(20) not null,
        primary key (si_id),
        index (bo_table, wr_id, mb_id)) $default_charset", false);

// 리워드 테이블 버그 수정
sql_query("alter table $mw[reward_table] change re_url re_url varchar(255) not null", false);
sql_query("alter table $mw[reward_log_table] add re_remote varchar(20) not null", false);

// 검색위
if (is_null($mw_basic[cf_search_top])) {
    sql_query("alter table $mw[basic_config_table] add cf_search_top char(1) not null", false);
}

// 공지읽어야 글쓰기 가능 
if (is_null($mw_basic[cf_must_notice])) {
    sql_query("alter table $mw[basic_config_table] add cf_must_notice char(1) not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_must_notice_read varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_must_notice_comment varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_must_notice_down varchar(1) not null", false);

    sql_query("create table if not exists $mw[must_notice_table] (
        bo_table varchar(20) not null,
        wr_id int not null,
        mb_id varchar(20) not null,
        mu_datetime datetime not null,
        primary key (bo_table, wr_id, mb_id)) $default_charset", false);

// 분류탭
if (is_null($mw_basic[cf_category_tab])) {
    sql_query("alter table $mw[basic_config_table] add cf_category_tab char(1) not null", false);
}

// 분류 선택 라디오버튼
if (is_null($mw_basic[cf_category_radio])) {
    sql_query("alter table $mw[basic_config_table] add cf_category_radio char(1) not null", false);
}

// 공지상단
if (is_null($mw_basic[cf_notice_top])) {
    sql_query("alter table $mw[basic_config_table] add cf_notice_top char(1) not null", false);
}

// 코멘트 추천,반대,베플
if (is_null($mw_basic[cf_comment_good])) {
    sql_query("alter table $mw[basic_config_table] add cf_comment_good char(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_nogood char(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_best char(1) not null", false);
}
    sql_query("create table if not exists $mw[comment_good_table] (
        bo_table varchar(20) not null,
        parent_id int not null,
        wr_id int not null,
        mb_id varchar(20) not null,
        bg_flag varchar(6) not null,
        bg_datetime datetime not null,
        primary key (bo_table, parent_id, wr_id, mb_id)) $default_charset",false);

// 신고 후 게시물 잠금
if (is_null($mw_basic[cf_write_secret])) {
    sql_query("alter table $mw[basic_config_table] add cf_singo_write_secret char(1) not null", false);
}

// 레벨 아이콘
if (is_null($mw_basic[cf_icon_level])) {
    sql_query("alter table $mw[basic_config_table] add cf_icon_level char(1) not null", false);
}
if (is_null($mw_basic[cf_icon_level_point])) {
    sql_query("alter table $mw[basic_config_table] add cf_icon_level_point int default '10000' not null", false);
}

// 게시판 상,하단
if (is_null($mw_basic[cf_include_head])) {
    sql_query("alter table $mw[basic_config_table] add cf_include_head varchar(255) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_include_tail varchar(255) not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_include_head_page varchar(20) default '/l//v//w/' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_include_tail_page varchar(20) default '/l//v//w/' not null", false);

// 본문추가
if (is_null($mw_basic[cf_include_view])) {
    sql_query("alter table $mw[basic_config_table] add cf_include_view varchar(255) not null", false);
}

// 코멘트 첨부파일
if (is_null($mw_basic[cf_comment_file])) {
    sql_query("alter table $mw[basic_config_table] add cf_comment_file char(1) not null", false);
}
    sql_query("alter table $mw[basic_config_table] change cf_comment_file cf_comment_file tinyint not null", false);

    sql_query(" create  table if not exists $mw[comment_file_table] (
     bo_table varchar(20)  not null default  '',
     wr_id int not null default '0',
     bf_no int not null default '0',
     bf_source varchar(255) not null default  '',
     bf_file varchar(255) not null default  '',
     bf_download varchar(255) not null default  '',
     bf_content text not null ,
     bf_filesize int not null default  '0',
     bf_width int not null default  '0',
     bf_height smallint not null default  '0',
     bf_type tinyint not null default  '0',
     bf_datetime datetime not null default '0000-00-00 00:00:00',
     primary  key (bo_table, wr_id, bf_no))", false);

// 코멘트 첨부파일
if (is_null($mw_basic[cf_contents_shop_download_count])) {
    sql_query("alter table $mw[basic_config_table] add cf_contents_shop_download_count int unsigned not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_contents_shop_download_day int unsigned not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_contents_shop_write char(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_contents_shop_write_cash int unsigned not null", false);
}

// 코멘트 페이징
if (is_null($mw_basic[cf_comment_page])) {
    sql_query("alter table $mw[basic_config_table] add cf_comment_page char(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_page_rows int not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_comment_page_first varchar(1) not null", false);

// 코멘트 html 
if (is_null($mw_basic[cf_comment_html])) {
    sql_query("alter table $mw[basic_config_table] add cf_comment_html char(1) not null", false);
}

// 닉네임 치환
if (is_null($mw_basic[cf_replace_word])) {
    sql_query("alter table $mw[basic_config_table] add cf_replace_word int default '10' not null", false);
}

// 호칭
if (is_null($mw_basic[cf_name_title])) {
    sql_query("alter table $mw[basic_config_table] add cf_name_title varchar(20) not null", false);
}

// 선택익명
if (is_null($mw_basic[cf_anonymous])) {
    sql_query("alter table $mw[basic_config_table] add cf_anonymous varchar(1) not null", false);
}

    sql_query("alter table $mw[basic_config_table] add cf_anonymous_nopoint varchar(1) not null", false);

if (is_null($write[wr_anonymous])) {
    sql_query("alter table $write_table add wr_anonymous char(1) not null", false);
}

// 댓글감춤
if (is_null($write[wr_comment_hide])) {
    sql_query("alter table $write_table add wr_comment_hide char(1) not null", false);
}

//  이미지 확대 사용 안함
if (is_null($mw_basic[cf_no_img_ext])) {
    sql_query("alter table $mw[basic_config_table] add cf_no_img_ext char(1) not null", false);
}

// 다운로드 팝업 크기
if (is_null($mw_basic[cf_download_popup_w])) {
    sql_query("alter table $mw[basic_config_table] add cf_download_popup_w int default '500' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_download_popup_h int default '300' not null", false);
}

// 링크로그
if (is_null($mw_basic[cf_link_log])) {
    $sql = "alter table $mw[basic_config_table] add cf_link_log char(1) not null";
    sql_query($sql, false);

    $sql = "create table if not exists $mw[link_log_table] (
            ll_id int auto_increment not null
            ,bo_table varchar(20) not null
            ,wr_id int not null
            ,ll_no int not null
            ,mb_id varchar(20) not null
            ,ll_name varchar(20) not null
            ,ll_ip varchar(20) not null
            ,ll_datetime datetime not null
            ,primary key(ll_id)
            ,index(bo_table, wr_id, ll_no, mb_id)) $default_charset";
    sql_query($sql, false);
}

// 에디터 종류
if (is_null($mw_basic[cf_editor])) {
    sql_query("alter table $mw[basic_config_table] add cf_editor varchar(10) not null", false);
}

// 워터마크
if (is_null($mw_basic[cf_watermark_path])) {
    sql_query("alter table $mw[basic_config_table] add cf_watermark_use varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_watermark_use_thumb varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_watermark_path varchar(255) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_watermark_position varchar(20) default 'center' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_watermark_transparency tinyint default 100 not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_watermark_type varchar(3) default 'jpg' not null", false);

// 업로더수익
if (is_null($mw_basic[cf_contents_shop_uploader])) {
    sql_query("alter table $mw[basic_config_table] add cf_contents_shop_uploader varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_contents_shop_uploader_cash int not null", false);
}

// 원본 강제 리사이징
/*
if (is_null($mw_basic[cf_original_width])) {
    sql_query("alter table $mw[basic_config_table] add cf_original_width smallint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_original_height smallint not null", false);
}
*/

// 첨부파일 기본 갯수
if (is_null($mw_basic[cf_attach_count])) {
    sql_query("alter table $mw[basic_config_table] add cf_attach_count smallint default '1' not null", false);
}

// 관련글 타게시판
if (is_null($mw_basic[cf_related_table])) {
    sql_query("alter table $mw[basic_config_table] add cf_related_table varchar(255) not null", false);
}

// 최신글 타게시판
if (is_null($mw_basic[cf_latest_table])) {
    sql_query("alter table $mw[basic_config_table] add cf_latest_table varchar(20) not null", false);
}

// 게시물별 글읽기 레벨
if (is_null($write[wr_read_level])) {
    sql_query("alter table $mw[basic_config_table] add cf_read_level varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_read_level_own tinyint default '10' not null", false);
    sql_query("alter table $write_table add wr_read_level tinyint not null", false);
}

// 추천,비추천포인트
if (is_null($mw_basic[cf_good_point])) {
    sql_query("alter table $mw[basic_config_table] add cf_good_point int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_nogood_point int not null", false);
}

// 베플 기준
if (is_null($mw_basic[cf_comment_best_limit])) {
    sql_query("alter table $mw[basic_config_table] add cf_comment_best_limit int not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_comment_best_point int not null", false);

// 추천,비추천한 사람 포인트
if (is_null($mw_basic[cf_good_re_point])) {
    sql_query("alter table $mw[basic_config_table] add cf_good_re_point int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_nogood_re_point int not null", false);
}

// 본문 미리보기
/*if (is_null($mw_basic[cf_preview_level])) {
    sql_query("alter table $mw[basic_config_table] add cf_preview_level tinyint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_preview_size int not null", false);
}*/

// 코멘트 입력시 비밀글 사용안함
if (is_null($mw_basic[cf_comment_secret_no])) {
    sql_query("alter table $mw[basic_config_table] add cf_comment_secret_no char(1) not null", false);
}
    sql_query("alter table $mw[basic_config_table] change cf_comment_secret_no cf_comment_secret_no tinyint not null", false);
    sql_query("alter table $mw[basic_config_table] change cf_comment_secret_level cf_comment_secret_no tinyint not null", false);

// kcb 실명인증
if (is_null($write[wr_kcb_use])) {
    sql_query("alter table $mw[basic_config_table] add cf_kcb_id varchar(20) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_kcb_type varchar(6) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_kcb_list varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_kcb_read varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_kcb_write varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_kcb_post varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_kcb_post_level tinyint default 10 not null", false);
    sql_query("alter table $write_table add wr_kcb_use varchar(1) not null", false);

    $sql = "create table if not exists $mw[okname_table] (
    mb_id varchar(20) not null,
    ok_ip varchar(50) not null,
    ok_datetime datetime not null,
    primary key (mb_id)) $default_charset";
    sql_query($sql, false);
}

// 질문답변
if (is_null($mw_basic[wr_qna_status])) {
    sql_query("alter table $mw[basic_config_table] add cf_qna_point_use varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_qna_point_min int default '10' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_qna_point_max int default '1000' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_qna_point_add int default '100' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_qna_save tinyint default '70' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_qna_hold tinyint default '50' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_qna_count tinyint default '1' not null", false);
    sql_query("alter table $write_table drop cf_qna_status", false);
    sql_query("alter table $write_table drop cf_qna_point", false);
    sql_query("alter table $write_table add wr_qna_status varchar(1) default '1' not null", false);
    sql_query("alter table $write_table add wr_qna_point int not null", false);
    sql_query("alter table $write_table add wr_qna_id int not null", false);
}

// 럭키라이팅
if (is_null($mw_basic[cf_lucky_writing_chance])) {
    sql_query("alter table $mw[basic_config_table] add cf_lucky_writing_chance tinyint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_lucky_writing_point_start int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_lucky_writing_point_end int not null", false);
}

// 럭키라이팅
if (is_null($mw_basic[cf_lucky_writing_comment_chance])) {
    sql_query("alter table $mw[basic_config_table] add cf_lucky_writing_comment_ment varchar(255) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_lucky_writing_comment_chance tinyint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_lucky_writing_comment_point_start int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_lucky_writing_comment_point_end int not null", false);
}
if (is_null($mw_basic[cf_lucky_writing_ment])) {
    sql_query("alter table $mw[basic_config_table] add cf_lucky_writing_ment varchar(255) not null", false);
}
if (is_null($mw_basic[cf_lucky_writing_comment])) {
    sql_query("alter table $mw[basic_config_table] add cf_lucky_writing_comment varchar(255) not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_lucky_writing_no_admin varchar(1) not null", false);

// 임시저장
$sql = "create table if not exists $mw[temp_table] (
tp_id int not null auto_increment,
bo_table varchar(20) not null,
mb_id varchar(20) not null,
tp_subject varchar(255) not null,
tp_content text not null,
tp_datetime datetime not null,
tp_ip varchar(30) not null,
primary key (tp_id)) $default_charset";
sql_query($sql, false);

// 추천,비추천포인트
if (is_null($mw_basic[cf_comment_good_point])) {
    sql_query("alter table $mw[basic_config_table] add cf_comment_good_point int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_good_re_point int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_nogood_point int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_nogood_re_point int not null", false);
}

// 글작성시 이모티콘 사용
if (is_null($mw_basic[cf_post_emoticon])) {
    sql_query("alter table $mw[basic_config_table] add cf_post_emoticon varchar(1) not null", false);
}

// 시간에 인덱스
sql_query("alter table $write_table add index wr_datetime (wr_datetime)", false);

// 이미지크기 사용자정의
//if (is_null($mw_basic[cf_change_image_size])) {
    sql_query("alter table $mw[basic_config_table] add cf_change_image_size varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_change_image_size_level tinyint not null", false);
//}


sql_query("alter table $mw[basic_config_table] add cf_singo_writer varchar(1) not null", false);

// iframe 사용권한
if (is_null($mw_basic[cf_iframe_level])) {
    sql_query("alter table $mw[basic_config_table] add cf_iframe_level int default 10 not null", false);
}

// 추천/비추 기간
if (is_null($mw_basic[cf_good_days])) {
    sql_query("alter table $mw[basic_config_table] add cf_good_days int not null", false);
}

sql_query("alter table $mw_cash[membership_board_table] add mp_comment char(1) not null ", false);

sql_query("alter table $mw[basic_config_table] add cf_kcb_comment varchar(1) not null", false);

// 접근권한 내용
if (is_null($mw_basic[cf_board_member_view])) {
    $sql = "alter table $mw[basic_config_table] add cf_board_member_view char(1) not null";
    sql_query($sql, false);
}
    sql_query("alter table $mw[basic_config_table] add cf_board_member_comment varchar(1) not null", false);

sql_query("alter table $write_table add wr_is_mobile varchar(1) not null", false);

// sns 식 날짜표시
if (is_null($mw_basic[cf_sns_datetime])) {
    sql_query("alter table $mw[basic_config_table] add cf_sns_datetime varchar(1) not null", false);
}

// 소셜커머스
if (is_null($mw_basic[cf_social_commerce])) {
    sql_query("alter table $mw[basic_config_table] add cf_social_commerce varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_social_commerce_level int not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_social_commerce_hp varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_social_commerce_begin int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_social_commerce_limit int not null", false);

// 마케팅DB
if (is_null($mw_basic[cf_marketdb])) {
    sql_query("alter table $mw[basic_config_table] add cf_marketdb tinyint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_marketdb_hp varchar(1) not null", false);
}
    sql_query("alter table $mw[basic_config_table] change cf_marketdb cf_marketdb tinyint not null", false);

// 구글맵
if (is_null($mw_basic[cf_google_map])) {
    sql_query("alter table $mw[basic_config_table] add cf_google_map varchar(1) not null", false);
}
sql_query("alter table $write_table add wr_google_map varchar(255) not null", false);

// 원본강제 리사이징 오류 수정
if (is_null($mw_basic[cf_resize_original])) {
    sql_query("alter table $mw[basic_config_table] change cf_original_width cf_resize_original smallint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_resize_original smallint not null", false);
    sql_query("alter table $mw[basic_config_table] drop cf_original_height", false);
}

// 리사이징 퀄리티
if (is_null($mw_basic[cf_resize_quality])) {
    sql_query("alter table $mw[basic_config_table] add cf_resize_quality smallint not null default 100", false);
}

// 리사이징 기준
if (is_null($mw_basic[cf_resize_base])) {
    sql_query("alter table $mw[basic_config_table] add cf_resize_base varchar(6) not null default 'long'", false);
}

// 공지상단 제목길이
if (is_null($mw_basic[cf_notice_top_length])) {
    sql_query("alter table $mw[basic_config_table] add cf_notice_top_length int not null default 100", false);
}

// 글읽을조건
if (is_null($mw_basic[cf_read_point])) {
    sql_query("alter table $mw[basic_config_table] add cf_read_point int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_read_register int not null", false);
}

// 글쓰기 기본 제목
if (is_null($mw_basic[cf_insert_subject])) {
    sql_query("alter table $mw[basic_config_table] add cf_insert_subject varchar(255) not null", false);
}

if (is_null($mw_basic[cf_umz2])) {
    sql_query("alter table $mw[basic_config_table] add cf_umz2 varchar(30) not null", false);
}

if (is_null($mw_basic[cf_hot_limit])) {
    sql_query("alter table $mw[basic_config_table] add cf_hot_limit tinyint default '10' not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_hot_len int default '90' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_hot_cache int default '0' not null", false);

// 퀴즈 플러그인 
if (is_null($mw_basic[cf_quiz])) {
    sql_query("alter table $mw[basic_config_table] add cf_quiz varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_quiz_level tinyint not null default '2'", false);
    sql_query("alter table $mw[basic_config_table] add cf_quiz_join_level tinyint not null default '2'", false);
}

// 게시물 자동 폭파
if (is_null($mw_basic[cf_bomb_level])) {
    sql_query("alter table $mw[basic_config_table] add cf_bomb_level tinyint default '0' not null", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_bomb_days_max int default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] change cf_bomb_days cf_bomb_days_max int default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_bomb_days_min int default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] change cf_bomb_days_min cf_bomb_days_min int default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_bomb_time int default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] change cf_bomb_time cf_bomb_time int default '0' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_bomb_move_table varchar(20) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_bomb_move_time varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_bomb_move_cate varchar(1) not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_bomb_item varchar(50) not null", false);

$sql = "create table if not exists $mw[bomb_table] (
        bo_table varchar(20) not null
        ,wr_id int not null
        ,bm_datetime datetime not null
        ,primary key(bo_table, wr_id)) $default_charset";
sql_query($sql, false);
sql_query("alter table $mw[bomb_table] add bm_log varchar(1) not null", false);
sql_query("alter table $mw[bomb_table] add bm_move_table varchar(20) not null", false);

if (is_null($mw_basic[cf_age])) {
    sql_query("alter table $mw[basic_config_table] add cf_age varchar(5) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_gender varchar(1) not null", false);
}

// 분류 예약이동
if (is_null($mw_basic[cf_move_level])) {
    sql_query("alter table $mw[basic_config_table] add cf_move_level tinyint default '0' not null", false);
}

$sql = "create table if not exists $mw[move_table] (
        bo_table varchar(20) not null
        ,wr_id int not null
        ,mv_cate varchar(255) not null
        ,mv_notice varchar(1) not null
        ,mv_datetime datetime not null
        ,primary key(bo_table, wr_id)) $default_charset";
sql_query($sql, false);

// 수집기
if (is_null($mw_basic[cf_collect])) {
    sql_query("alter table $mw[basic_config_table] add cf_collect varchar(10) not null", false);
}

if (is_null($mw_basic[cf_good_cancel_days])) {
    sql_query("alter table $mw[basic_config_table] add cf_good_cancel_days int not null default '3'", false);
}

if (is_null($mw_basic[cf_talent_market])) {
    sql_query("alter table $mw[basic_config_table] add cf_talent_market varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_talent_market_commission tinyint not null default '30'", false);
    sql_query("alter table $mw[basic_config_table] add cf_talent_market_min int not null default '5'", false);
    sql_query("alter table $mw[basic_config_table] add cf_talent_market_max int not null default '100'", false);
}
    sql_query("alter table $mw[basic_config_table] add cf_talent_market_min_point int not null default '5000'", false);
    sql_query("alter table $mw[basic_config_table] add cf_talent_market_max_point int not null default '10000'", false);

    sql_query("alter table $mw[basic_config_table] add cf_talent_market_app varchar(1) not null default ''", false);
    sql_query("alter table $mw[basic_config_table] add cf_talent_market_hp varchar(1) not null default ''", false);
    sql_query("alter table $mw[basic_config_table] add cf_talent_market_auto int not null default 7", false);


    sql_query("alter table $mw[basic_config_table] add cf_good_count int not null default '0'", false);
    sql_query("alter table $mw[basic_config_table] add cf_good_cancel int not null default '1'", false);

    sql_query("alter table $mw[basic_config_table] add cf_read_point_message varchar(1) not null default ''", false);

    sql_query("alter table $write_table add wr_view_block varchar(1) not null default ''", false);

    sql_query("alter table $mw[basic_config_table] add cf_view_good tinyint not null default '10'", false);

    sql_query("alter table $mw[basic_config_table] add cf_board_sdate varchar(10) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_board_edate varchar(10) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_board_stime varchar(8) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_board_etime varchar(8) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_board_week varchar(13) not null default '1,1,1,1,1,1,1'", false);

    sql_query("alter table $mw[basic_config_table] add cf_contents_shop_fix varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_contents_shop_max int not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_contents_shop_min int not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_download_date varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] change cf_sns cf_sns varchar(255) not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_auto_move varchar(255) not null", false);
    sql_query("alter table $write_table add wr_auto_move varchar(1) not null default ''", false);

    sql_query("alter table $mw[basic_config_table] add cf_link_target_level tinyint  not null", false);
    sql_query("alter table $write_table add wr_link1_target varchar(10) not null default '_blank'", false);
    sql_query("alter table $write_table add wr_link2_target varchar(10) not null default '_blank'", false);

    sql_query("alter table $mw[basic_config_table] add cf_thumb4_width smallint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_thumb4_height smallint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_thumb4_keep char(1) not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_thumb5_width smallint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_thumb5_height smallint not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_thumb5_keep char(1) not null", false);

    sql_query("alter table $write_table add wr_contents_price int not null", false);
    sql_query("alter table $write_table add wr_contents_domain char(1) not null", false);
    sql_query("alter table $write_table add wr_contents_preview text not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_memo_id varchar(255) not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_download_good varchar(1) default '' not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_default_category varchar(20) default '' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_noimage_path varchar(255) default '' not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_comment_level tinyint default '1' not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_image_auto_rotate varchar(1) not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_youtube_size int not null default '360'", false);

    sql_query("alter table $mw[basic_config_table] add cf_lightbox tinyint not null default '0'", false);
    sql_query("alter table $mw[basic_config_table] add cf_lightbox_x int not null default '100'", false);
    sql_query("alter table $mw[basic_config_table] add cf_lightbox_y int not null default '100'", false);

    sql_query("alter table $write_table add wr_lightbox varchar(1) not null default ''", false);

    sql_query("alter table $mw[basic_config_table] add cf_multimedia varchar(100) default '/movie//image//flash//youtube//link_movie//link_image//link_flash/' not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_search_level tinyint default '1' not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_content_add text not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_hp_reply varchar(20) not null after cf_hp", false);
    sql_query("alter table $mw[basic_config_table] add cf_jwplayer_version varchar(15) not null default 'jwplayer6'", false);
    sql_query("alter table $mw[basic_config_table] add cf_jwplayer_autostart varchar(1) not null default ''", false);

    sql_query("alter table $mw[basic_config_table] add cf_ban_subject varchar(1) not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_content_align varchar(1) not null", false);
    sql_query("alter table $write_table add wr_align varchar(6) default 'left' not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_comment_write_notice text not null", false);

    sql_query("alter table $write_table add wr_to_id varchar(20) default '' not null", false);

    sql_query("alter table $write_table add wr_marketdb varchar(1) not null", false);
    sql_query("alter table $write_table change wr_marketdb wr_marketdb varchar(1) not null", false);

    sql_query("alter table {$mw[basic_config_table]} add cf_thumb_jpg varchar(1) not null", false);
    sql_query("alter table {$mw[basic_config_table]} add cf_image_save_close varchar(1) not null", false);

    sql_query("alter table {$mw[basic_config_table]} add cf_link_point int not null", false);

    sql_query("alter table {$mw[basic_config_table]} add cf_not_membership_msg varchar(255) not null", false);
    sql_query("alter table {$mw[basic_config_table]} add cf_not_membership_url varchar(255) not null", false);

    // 시험문제 플러그인 
    sql_query("alter table $mw[basic_config_table] add cf_exam varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_exam_level tinyint not null default '2'", false);
    sql_query("alter table $mw[basic_config_table] add cf_exam_notice varchar(1) not null default ''", false);
    sql_query("alter table $mw[basic_config_table] add cf_exam_download varchar(1) not null default ''", false);

    sql_query("alter table $mw[basic_config_table] add cf_player_size varchar(10) not null default ''", false);

    sql_query("alter table $mw[basic_config_table] add cf_hot_print varchar(3) default 'lvw' not null", false);

    // 2.3.8
    sql_query("alter table $mw[basic_config_table] add cf_write_width varchar(6) default 'normal' not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_write_height int default '10' not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_youtube_only varchar(1) not null default ''", false);

    sql_query("alter table $mw[basic_config_table] add cf_good_level tinyint not null default '2'", false);
    sql_query("alter table $mw[basic_config_table] add cf_nogood_level tinyint not null default '2'", false);

    sql_query("alter table $mw[basic_config_table] add cf_qna_enough varchar(1) not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_comment_head varchar(255) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_tail varchar(255) not null", false);

    sql_query("alter table $mw[basic_config_table] change cf_comment_head cf_comment_head text not null", false);
    sql_query("alter table $mw[basic_config_table] change cf_comment_tail cf_comment_tail text not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_bbs_banner varchar(1) not null", false);
    sql_query("alter table $mw[basic_config_table] add cf_bbs_banner_page varchar(20) default '/l//v//w/' not null", false);

    sql_query("alter table $mw[basic_config_table] add cf_key_level tinyint not null", false);
    sql_query("alter table $write_table add wr_key_password varchar(50) not null", false);
    sql_query("alter table $write_table change wr_key_password wr_key_password varchar(50) not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_jump_level tinyint not null default 0", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_jump_point int not null default 0", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_jump_days int not null default 1", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_jump_count int not null default 1", false);

    $sql = "create table if not exists {$mw['jump_log_table']} (
        jp_id int not null auto_increment,
        bo_table varchar(20) not null,
        wr_id int not null,
        mb_id varchar(20) not null,
        jp_datetime datetime not null,
        primary key (jp_id)) ".$default_charset;
    sql_query($sql, false);

    sql_query("alter table {$mw['basic_config_table']} add cf_time_list varchar(20) not null default '' ", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_time_view varchar(20) not null default '' ", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_time_comment varchar(20) not null default '' ", false);

    sql_query("alter table $mw[basic_config_table] add cf_post_specialchars varchar(1) not null default '1'", false);
    sql_query("alter table $mw[basic_config_table] add cf_comment_specialchars varchar(1) not null default '1'", false);

    sql_query("alter table {$mw[basic_config_table]} add cf_image_outline varchar(1) not null", false);
    sql_query("alter table {$mw[basic_config_table]} add cf_image_outline_color varchar(7) not null default '#cccccc'", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_include_write_head varchar(255) not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_include_write_main varchar(255) not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_include_write_tail varchar(255) not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_include_write_update_head varchar(255) not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_include_write_update varchar(255) not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_include_write_update_tail varchar(255) not null", false);

    $sql = "create table if not exists {$mw['category_table']} (
        bo_table varchar(20) not null,
        ca_id int not null auto_increment,
        ca_name varchar(50) not null,
        ca_type varchar(10) not null,
        ca_level_list tinyint not null,
        ca_level_view tinyint not null,
        ca_level_write tinyint not null,
        ca_color varchar(6) not null,
        ca_cash int unsigned not null,
        ca_cash_use varchar(1) not null,
        primary key (ca_id)) ".$default_charset;
    sql_query($sql, false);

    sql_query("alter table {$mw['category_table']} add ca_cash int unsigned not null", false);
    sql_query("alter table {$mw['category_table']} add ca_cash_use varchar(1) not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_contents_shop_category varchar(1) not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_seo_url varchar(1) not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_comment_mention varchar(1) not null", false);

    $sql = "create table if not exists {$mw['level_table']} (
        bo_table varchar(20) not null,
        mb_level tinyint not null,
        cf_use varchar(1) not null,
        cf_write_day int not null,
        cf_write_day_count int not null,
        cf_qna_count tinyint not null,
        primary key (bo_table, mb_level)) ".$default_charset;
    sql_query($sql, false);

    sql_query("alter table {$mw['level_table']} add cf_qna_count tinyint not null default '0'", false);

    sql_query("alter table $mw[basic_config_table] add cf_gender_m varchar(4) not null default 'lvwc'", false);
    sql_query("alter table $mw[basic_config_table] add cf_gender_w varchar(4) not null default 'lvwc'", false);

    sql_query("alter table $mw[basic_config_table] add cf_rss varchar(1) not null default ''", false);
    sql_query("alter table $mw[basic_config_table] add cf_rss_limit int not null default '10'", false);

    sql_query("alter table $mw[basic_config_table] add cf_hidden_link tinyint not null", false);

    sql_query("alter table {$write_table} add wr_hidden_link1 varchar(255) not null default ''", false);
    sql_query("alter table {$write_table} add wr_hidden_link2 varchar(255) not null default ''", false);

    sql_query("alter table $mw[basic_config_table] add cf_cash_grade_use varchar(1) not null", false);

    if ($mw_cash['grade_table']) {
        $sql = "create table if not exists {$mw['cash_grade_table']} (
            bo_table varchar(20) not null,
            gd_id int not null,
            gd_list varchar(1) not null default '1',
            gd_read varchar(1) not null default '1',
            gd_write varchar(1) not null default '1',
            gd_comment varchar(1) not null default '1',
            primary key (bo_table, gd_id)
        )";
        sql_query($sql, false);
    }

    sql_query("alter table {$mw[basic_config_table]} add cf_image_remote_save varchar(1) not null default '1'", false);
    sql_query("alter table {$mw[basic_config_table]} add cf_comment_delete_log varchar(1) not null default ''", false);

    sql_query("alter table {$mw[basic_config_table]} add cf_age_opt varchar(4) not null default 'lvwc'", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_rate_level tinyint not null default 0", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_rate_point int not null default 0", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_rate_down varchar(1) not null default ''", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_rate_buy varchar(1) not null default ''", false);

    sql_query("alter table {$write_table} add wr_rate decimal(3,2) not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_umz_domain varchar(100) not null default ''", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_list_cate varchar(1) not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_search_name varchar(1) default '' not null", false);
    sql_query("alter table {$mw['basic_config_table']} change cf_search_name cf_search_name tinyint default '1' not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_ani_nothumb varchar(1) not null default ''", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_ani_nowatermark varchar(1) not null default ''", false);

    sql_query("alter table {$mw['basic_config_table']} change cf_related_table cf_related_table varchar(255) not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_related_table_div varchar(1) not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_related_subject varchar(1) not null default '1'", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_related_content varchar(1) not null default '1'", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_ca_order varchar(1) not null default ''", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_kakao_key varchar(255) not null default ''", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_name_location varchar(1) not null default ''", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_search_level_view varchar(1) not null default ''", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_trash varchar(50) not null default ''", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_download_day int default 0 not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_download_count int default 0 not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_link_level tinyint default 1 not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_link_level_view varchar(1) default '' not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_lucky_writing_comment_first varchar(255) not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_lucky_writing_comment_first_chance tinyint not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_lucky_writing_comment_first_point_start int not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_lucky_writing_comment_first_point_end int not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_thumb_round varchar(1) not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_emoticon varchar(50) not null", false);

    sql_query("alter table {$mw['basic_config_table']} add cf_prev_next varchar(1) not null", false);
    sql_query("alter table {$mw['basic_config_table']} add cf_comment_image_no varchar(1) not null", false);

