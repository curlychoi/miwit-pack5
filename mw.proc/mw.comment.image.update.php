<?php
/**
 * Bechu-Basic Skin for Gnuboard4
 *
 * Copyright (c) 2008 Choi Jae-Young <www.miwit.com>
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

include_once("_common.php");

if (!$bo_table)
    alert_close("bo_table 이 없습니다.");

include_once($board_skin_path."/mw.lib/mw.skin.basic.lib.php");
include_once($board_skin_path."/mw.proc/mw.comment.image.config.php");

if (!$is_member)
    alert_close("회원만 이용 가능합니다.");

if (!$is_admin && $member['mb_id'] != $mb_id)
    alert_close("자신의 사진만 변경 가능합니다.");

if (!mb_id_check($mb_id))
    alert_close("존재하지 않는 회원입니다.");

$path = $comment_image_path;

$dest_file = $path.'/'.$mb_id;

@mkdir($path, 0707);
@chmod($path, 0707);

$indexfile = $path."/index.php";
$f = @fopen($indexfile, "w");
@fwrite($f, "");
@fclose($f);
@chmod($indexfile, 0606);

$file = $_FILES['comment_image'];
$file['name'] = get_safe_filename($file['name']);

$size = @getImageSize($file['tmp_name']);
$mime = array('image/png', 'image/jpeg', 'image/gif');
$exts = array('png', 'jpg', 'gif');

if ($size[0] > $cf_x)
    alert("가로사이즈가 {$cf_x}px 보다 큽니다.");
if ($size[1] > $cf_y)
    alert("세로사이즈가 {$cf_y}px 보다 큽니다.");
if ($file['size'] > $cf_size)
    alert("파일용량이 ".get_filesize($cf_size)." 보다 큽니다.");

$ext = substr($file['name'], strlen($file['name'])-3, 3);

if ($image_del)
    @unlink($dest_file);

if (is_uploaded_file($file['tmp_name'])) {
    if (!in_array($size['mime'], $mime))
        alert_close("PNG, GIF, JPG 형식의 이미지 파일만 업로드 가능합니다.");

    if (!in_array($ext, $exts))
        alert_close("PNG, GIF, JPG 형식의 이미지 파일만 업로드 가능합니다.");

    if (!is_dir($path))
        alert_close("$path 디렉토리가 존재하지 않습니다.");

    if (!is_writable($path))
        alert_close("$path 디렉토리의 퍼미션을 707로 변경해주세요.");

    move_uploaded_file($file['tmp_name'], $dest_file);

}

alert_close("완료되었습니다.");

