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

define("_MW_BOARD_", TRUE);

// 디렉토리 생성
function mw_mkdir($path, $permission=0707) {
    //if (is_dir($path)) return;
    if (is_mw_file($path)) @unlink($path);

    @mkdir($path, $permission);
    @chmod($path, $permission);

    // 디렉토리에 있는 파일의 목록을 보이지 않게 한다.
    $file = $path . "/index.php";
    $f = @fopen($file, "w");
    @fwrite($f, "");
    @fclose($f);
    @chmod($file, 0606);
}

// 관련글 얻기.. 080429, curlychoi
function mw_related($related, $field="wr_id, wr_subject, wr_content, wr_datetime, wr_comment")
{
    global $bo_table, $write_table, $g4, $wr_id, $mw_basic;

    if (!trim($related)) return;

    $bo_table2 = $bo_table;
    $write_table2 = $write_table;

    if (trim($mw_basic[cf_related_table])) {
        $bo_table2 = $mw_basic[cf_related_table];
        $write_table2 = "$g4[write_prefix]$bo_table2";
    }

    $sql_where = "";
    $related = explode(",", $related);
    foreach ($related as $rel) {
        $rel = trim($rel);
        if ($rel) {
            $rel = addslashes($rel);
            if ($sql_where) {
                $sql_where .= " or ";
            }
            $sql_where .= " (instr(wr_subject, '$rel') or instr(wr_content, '$rel')) ";
        }
    }
    if (!trim($mw_basic[cf_related_table]))
        $sql_where .= " and wr_id <> '$wr_id' ";

    $sql = "select $field from $write_table2 where wr_is_comment = 0 and ($sql_where) order by wr_num ";
    $qry = sql_query($sql, false);

    $list = array();
    $i = 0;
    while ($row = sql_fetch_array($qry)) {
        $row[href] = mw_seo_url($bo_table2, $row[wr_id]);
        if (!$row['wr_comment']) $row['wr_comment'] = '';
        $row[comment] = $row[wr_comment] ? "<span class='comment'>$row[wr_comment]</span>" : "";
        $row[subject] = get_text($row[wr_subject]);
        $row[subject] = mw_reg_str($row[subject]);
        $list[$i] = $row;
        if (++$i >= $mw_basic[cf_related]) {
            break;
        }
    }
    return $list;
}

function mw_related2($bo_table, $related, $related_skin, $field="wr_id, wr_subject, wr_content, wr_datetime, wr_comment")
{
    global $g4;
    global $wr_id;
    global $mw_basic;
    global $is_admin;

    if (!trim($related)) return;

    $keys = $related;
    $bo_table2 = $bo_table;

    $board = sql_fetch("select bo_subject from {$g4['board_table']} where bo_table = '{$bo_table2}' ");
    if (!$board) return;

//    if (trim($mw_basic['cf_related_table']) && !$mw_basic['cf_related_table_div'])
//        $bo_table2 = trim($mw_basic['cf_related_table']);

    $write_table2 = $g4['write_prefix'].$bo_table2;

    $sql_where = "";
    $related = explode(",", $related);
    foreach ($related as $rel) {
        $rel = trim($rel);
        if ($rel) {
            $rel = addslashes($rel);
            if ($sql_where) {
                $sql_where .= " or ";
            }
            $sql_where .= " ( ";
            if ($mw_basic['cf_related_subject'])
                $sql_where .= " instr(wr_subject, '{$rel}') ";

            if ($mw_basic['cf_related_content']) {
                if ($mw_basic['cf_related_subject']) {
                    $sql_where .= " or ";
                }
                $sql_where .= " instr(wr_content, '{$rel}') ";
            }
            $sql_where .= " ) ";
        }
    }
    if (!trim($mw_basic[cf_related_table]))
        $sql_where .= " and wr_id <> '$wr_id' ";

    $sql = "select $field from $write_table2 where wr_is_comment = 0 and ($sql_where) order by wr_num ";
    $qry = sql_query($sql, false);

    $list = array();
    $i = 0;
    while ($row = sql_fetch_array($qry)) {
        $row[href] = mw_seo_url($bo_table2, $row[wr_id]);
        if (!$row['wr_comment']) $row['wr_comment'] = '';
        $row[comment] = $row[wr_comment] ? "<span class='comment'>$row[wr_comment]</span>" : "";
        $row[subject] = get_text($row[wr_subject]);
        $row[subject] = mw_reg_str($row[subject]);
        $list[$i] = $row;
        if (++$i >= $mw_basic[cf_related]) {
            break;
        }
    }

    $etc = "&sfl=wr_subject||wr_content,1&sop=or&stx=".@urlencode(str_replace(",", " ", $keys));
    $board_url = mw_seo_url($bo_table2, 0, $etc);

    $skin = $related_skin;
    $skin = str_replace("{{board_subject}}", $board['bo_subject'], $skin);
    $skin = str_replace("{{board_url}}", $board_url, $skin);

    preg_match("/{{for}}(.*){{\/for}}/iUs", $related_skin, $match);
    $for_skin = $match[1];
    $for = '';
    for ($i=0, $m=count($list); $i<$m; ++$i) {
        $href = $list[$i]['href'];
        $date = substr($list[$i]['wr_datetime'], 0, 10);
        $subject = $list[$i]['subject'];
        $comment = $list[$i]['comment'];

        $row = $for_skin;
        $row = str_replace("{{href}}", $href, $row);
        $row = str_replace("{{date}}", $date, $row);
        $row = str_replace("{{subject}}", $subject, $row);
        $row = str_replace("{{comment}}", $comment, $row);

        $for.= $row;
    }
    $skin = preg_replace("/{{for}}.*{{\/for}}/iUs", $for, $skin);

    return $skin;
}

// 관련글 얻기.. 080429, curlychoi
function mw_view_latest($field="wr_id, wr_subject, wr_content, wr_datetime, wr_comment")
{
    global $bo_table, $write_table, $g4, $wr_id, $write, $mw_basic;

    if (!$write[mb_id]) return;

    $bo_table2 = $bo_table;
    $write_table2 = $write_table;

    if (trim($mw_basic[cf_latest_table])) {
        $bo_table2 = $mw_basic[cf_latest_table];
        $write_table2 = "$g4[write_prefix]$bo_table2";
    }

    $sql = "select $field from $write_table2 where wr_is_comment = 0 and wr_id <> '$wr_id' and mb_id = '$write[mb_id]' order by wr_num limit $mw_basic[cf_latest] ";
    $qry = sql_query($sql, false);

    $list = array();
    $i = 0;
    for ($i=0; $row=sql_fetch_array($qry); $i++) {
        $row[href] = mw_seo_url($bo_table2, $row[wr_id]);
        //$row[comment] = $row[wr_comment] ? "<span class='comment'>($row[wr_comment])</span>" : "";
        $row[comment] = $row[wr_comment] ? "<span class='comment'>+$row[wr_comment]</span>" : "";
        $row[subject] = get_text($row[wr_subject]);
        $row[subject] = mw_reg_str($row[subject]);
        $list[$i] = $row;
    }
    return $list;
}

function mw_thumbnail_keep($size, $set_width, $set_height) {
    global $mw_basic;

    if (!$mw_basic[cf_resize_base])
        $mw_basic[cf_resize_base] = 'long';

    if ($mw_basic[cf_resize_base] == 'long')
    {
        if ($size[0] > $size[1]) {
            @$rate = $set_width / $size[0];
            $get_width = $set_width;
            $get_height = (int)($size[1] * $rate);
        } else {
            @$rate = $set_width / $size[1];
            $get_height = $set_width;
            $get_width = (int)($size[0] * $rate);
        }
    }
    else if ($mw_basic[cf_resize_base] == 'width') {
        @$rate = $set_width / $size[0];
        $get_width = $set_width;
        $get_height = (int)($size[1] * $rate);
    }
    else if ($mw_basic[cf_resize_base] == 'height') {
        @$rate = $set_height / $size[1];
        $get_height = $set_height;
        $get_width = (int)($size[0] * $rate);
    }
    return array($get_width, $get_height);
}

function mw_image_auto_rotate($source_file)
{
    global $mw_basic;

    $size = @getimagesize($source_file);

    switch ($size[2]) {
        //case 1: $source = @imagecreatefromgif($source_file); break;
        case 2: $source = @imagecreatefromjpeg($source_file); break;
        //case 3: $source = @imagecreatefrompng($source_file); break;
        default: return false;
    }

    $exif = @exif_read_data($source_file);

    switch ($exif['Orientation']) {
        case 8:
            $source = imagerotate($source, 90, 0);
            break;
        case 3:
            $source = imagerotate($source, 180, 0);
            break;
        case 6:
            $source = imagerotate($source, -90, 0);
            break;
    }

    if (!$mw_basic[cf_resize_quality])
        $mw_basic[cf_resize_quality] = 100;

    @imagejpeg($source, $source_file, $mw_basic[cf_resize_quality]);
    @chmod($source_file, 0606);

    @imagedestroy($source);
}


// 썸네일 생성.. 080408, curlychoi
function mw_make_thumbnail($set_width, $set_height, $source_file, $thumbnail_file='', $keep=false, $time='')
{
    global $g4, $mw_basic;

    if (!$set_width && !$set_height) return;

    if (!$thumbnail_file)
        $source_file = $thumbnail_file;

    // 애니GIF 생성안함
    if ($mw_basic['cf_ani_nothumb'] && is_ani($source_file))
        return;

    $size = @getimagesize($source_file);

    switch ($size[2]) {
        case 1: $source = @imagecreatefromgif($source_file); break;
        case 2: $source = @imagecreatefromjpeg($source_file); break;
        case 3: $source = @imagecreatefrompng($source_file); break;
        default: return false;
    }

    if (!$mw_basic[cf_resize_base])
        $mw_basic[cf_resize_base] = 'long';

    // 원본이 설정 사이즈보다 작은 경우 변경하지 않음
    if ($source_file == $thumbnail_file) {
        if ($mw_basic[cf_resize_base] == 'long' && $size[0] < $set_width && $size[1] < $set_height) {
            return;
        }
        else if ($mw_basic[cf_resize_base] == 'width' && $size[0] < $set_width) {
            return;
        }
        else if ($mw_basic[cf_resize_base] == 'height' && $size[1] < $set_height) {
            return;
        }
    }

    if ($keep) // 비율 유지
    {
	$keep_size = mw_thumbnail_keep($size, $set_width, $set_height);
	$set_width = $get_width = $keep_size[0];
	$set_height = $get_height = $keep_size[1];
    }
    else
    {
        $rate = $set_width / $size[0];
        $get_width = $set_width;
        $get_height = (int)($size[1] * $rate); 

        $temp_h = (int)($set_height / $set_width * $size[0]);
        $src_y = (int)(($size[1] - $temp_h) / 2);

        if ($get_height < $set_height) {
            //$get_width = $set_width + $set_height - $get_height;
            //$get_height = $set_height;
            $rate = $set_height / $size[1];
            $get_height = $set_height;
            $get_width = (int)($size[0] * $rate); 

            $src_y = 0;
            $temp_w = (int)($set_width / $set_height * $size[1]);
            $src_x = (int)(($size[0] - $temp_w) / 2);
        }
    }

    $target = @imagecreatetruecolor($set_width, $set_height);
    $white = @imagecolorallocate($target, 255, 255, 255);
    @imagefilledrectangle($target, 0, 0, $set_width, $set_height, $white);
    @imagecopyresampled($target, $source, 0, 0, $src_x, $src_y, $get_width, $get_height, $size[0], $size[1]);

    if ($source_file != $thumbnail_file && $mw_basic[cf_watermark_use_thumb]
        && is_mw_file("$g4[bbs_path]/$mw_basic[cf_watermark_path]")) { // watermark
        mw_watermark($target, $set_width, $set_height
            , "$g4[bbs_path]/$mw_basic[cf_watermark_path]"
            , $mw_basic[cf_watermark_position]
            , $mw_basic[cf_watermark_transparency]);
    }

    if (!$mw_basic[cf_resize_quality])
        $mw_basic[cf_resize_quality] = 80;

    if ($mw_basic[cf_image_outline])
        mw_image_outline($target, $size, $mw_basic[cf_image_outline_color]);

    @imagejpeg($target, $thumbnail_file, $mw_basic[cf_resize_quality]);
    @chmod($thumbnail_file, 0606);

    @imagedestroy($target);
    @imagedestroy($source);

    global $write;
    if ($write['wr_datetime'] and !$time)
        $time = $write['wr_datetime'];

    if ($time)
        @touch($thumbnail_file, strtotime($time));

    thumb_log($thumbnail_file, 'finally');
}

function mw_hex_to_rgb($hex)
{
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   }
   else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);

   return $rgb;
}

function mw_image_outline($source, $size=null, $color="#cccccc")
{
    global $mw_basic;

    if (!@preg_match("/(jpe?g|gif|png)$/i", $source)) return;

    $source_file = $source;

    // 애니GIF 생성안함
    if (is_ani($source_file))
        return;

    $size = @getimagesize($source_file);
    switch ($size[2]) {
        case 1: $source = @imagecreatefromgif($source_file); break;
        case 2: $source = @imagecreatefromjpeg($source_file); break;
        case 3: $source = @imagecreatefrompng($source_file); break;
        default: return false;
    }

    $rgb = mw_hex_to_rgb($color); 
    $color = imagecolorallocate($source, $rgb[0], $rgb[1], $rgb[2]);
    $xy = array(0, 0, $size[0]-1, 0, $size[0]-1, $size[1]-1, 0, $size[1]-1);
    imagepolygon($source, $xy, 4, $color);

    switch ($size[2]) {
        case 1:
            imagegif($source, $source_file);
            break;
        case 2:
            imagejpeg($source, $source_file, $mw_basic[cf_resize_quality]);
            break;
        case 3:
            imagepng($source, $source_file);
            break;
    }
    @chmod($source_file, 0606);
    @imagedestroy($source);

    thumb_log($thumbnail_file, 'outline');
    return $source;
}

function mw_watermark($target, $tw, $th, $source, $position, $transparency=100)
{
    global $mw_basic;

    $wf = $source;
    $ws = @getimagesize($wf);

    $is_alpha_work = false;

    switch ($ws[2]) {
        case 1: $wi = @imagecreatefromgif($wf); break;
        case 2: $wi = @imagecreatefromjpeg($wf); break;
        case 3: $wi = @imagecreatefrompng($wf); break;
        default: $wi = "";
    }
    switch($position) {
        case "left_top":
            $wx = $wy = 0;
            break;
        case "left_bottom":
            $wx = 0;
            $wy = $th - $ws[1];
            break;
        case "right_top":
            $wx = $tw - $ws[0];
            $wy = 0;
            break;
        case "right_bottom":
            $wx = $tw - $ws[0];
            $wy = $th - $ws[1];
            break;
        case "center":
        default:
            if ($ws[0] > $tw || $ws[1] > $th) {
                $keep_size = mw_thumbnail_keep($ws, $tw, $th);
                $set_width = $get_width = $keep_size[0];
                $set_height = $get_height = $keep_size[1];

                $target2 = imagecreatetruecolor($set_width, $set_height);
                if ($ws[2] == 1 || $ws[2] == 3) { // 1:gif, 3:png
                    imagealphablending($target2, false);
                    imagesavealpha($target2, true);
                    $transparent = imagecolorallocatealpha($target2, 255, 255, 255, 127);
                    imagefilledrectangle($target2, 0, 0, $set_width, $set_height, $transparent);
                }
                else {
                    $white = imagecolorallocate($target2, 255, 255, 255);
                    imagefilledrectangle($target2, 0, 0, $set_width, $set_height, $white);
                }
                imagecopyresampled($target2, $wi, 0, 0, 0, 0, $get_width, $get_height, $ws[0], $ws[1]);

                imagedestroy($wi);
                $wi = $target2;

                $ws[0] = $set_width;
                $ws[1] = $set_height;

                $wx = (int)($tw/2 - $ws[0]/2);
                $wy = (int)($th/2 - $ws[1]/2);

                $is_alpha_work = true;
            }
            else {
                $wx = (int)($tw/2 - $ws[0]/2);
                $wy = (int)($th/2 - $ws[1]/2);
            }
            break;
    }
    if ($ws[2] == 1 && !$is_alpha_work) { // 1:gif
        $target2 = imagecreatetruecolor($ws[0], $ws[1]);
        imagealphablending($target2, false);
        imagesavealpha($target2, true);

        $transparent = imagecolorallocatealpha($target2, 255, 255, 255, 127);
        imagefilledrectangle($target2, 0, 0, $ws[0], $ws[1], $transparent);
        imagecopyresampled($target2, $wi, 0, 0, 0, 0, $ws[0], $ws[1], $ws[0], $ws[1]);
        imagedestroy($wi);
        $wi = $target2;
    }
    if ($ws[2] == 1 || $ws[2] == 3) { // 1:gif, 3:png
        //imagealphablending($wi, TRUE);
        //imagealphablending($target, TRUE);
        //imagecopy($target, $wi, $wx, $wy, 0, 0, $ws[0], $ws[1]);
        imagecopymerge_alpha($target, $wi, $wx, $wy, 0, 0, $ws[0], $ws[1], $transparency);
    }
    else {
        @imagecopymerge($target, $wi, $wx, $wy, 0, 0, $ws[0], $ws[1], $transparency);
    }
    @imagedestroy($wi);
}

// http://stackoverflow.com/questions/11291868/merge-transparent-images-in-php
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{ 
    if (!isset($pct)) return false; 

    $pct /= 100; 

    // Get image width and height 
    $w = imagesx($src_im); 
    $h = imagesy($src_im); 

    // Turn alpha blending off 
    imagealphablending ($src_im, false); 

    // Find the most opaque pixel in the image (the one with the smallest alpha value) 
    $minalpha = 127; 
    for ($x = 0; $x<$w; $x++) 
    for ($y = 0; $y<$h; $y++) { 
        $alpha = (imagecolorat($src_im, $x, $y) >> 24 ) & 0xFF; 
        if($alpha < $minalpha) { 
            $minalpha = $alpha; 
        } 
    } 

    //loop through image pixels and modify alpha for each 
    for ($x = 0; $x<$w; $x++) { 
        for ($y = 0; $y < $h; $y++) { 
            //get current alpha value (represents the TANSPARENCY!) 
            $colorxy = imagecolorat($src_im, $x, $y); 
            $alpha = ($colorxy >> 24) & 0xFF; 

            //calculate new alpha 
            if ($minalpha !== 127) { 
                $alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha); 
            }   
            else { 
                $alpha += 127 * $pct; 
            }

            //get the color index with new alpha 
            $alphacolorxy = imagecolorallocatealpha($src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
            //set pixel with the new color + opacity 
            if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) { 
                return false; 
            }
        }
    }

    // The image copy 
    imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
} 

function mw_watermark_file($source_file)
{
    global $watermark_path, $mw_basic, $g4;

    if (!is_mw_file($source_file)) return;

    $pathinfo = pathinfo($source_file);
    $basename = md5(basename($source_file)).'.'.$pathinfo[extension];
    $watermark_file = "$watermark_path/$basename";

    if (is_mw_file($watermark_file)) return $watermark_file;

    // 애니GIF 생성안함
    if ($mw_basic['cf_ani_nowatermark'] && is_ani($source_file))
        return;

    $size = @getimagesize($source_file);
    switch ($size[2]) {
        case 1: $source = @imagecreatefromgif($source_file); break;
        case 2: $source = @imagecreatefromjpeg($source_file); break;
        case 3: $source = @imagecreatefrompng($source_file); break;
        default: return;
    }

    $target = @imagecreatetruecolor($size[0], $size[1]);
    $white = @imagecolorallocate($target, 255, 255, 255);
    @imagefilledrectangle($target, 0, 0, $size[0], $size[1], $white);
    @imagecopyresampled($target, $source, 0, 0, 0, 0, $size[0], $size[1], $size[0], $size[1]);

    thumb_log($thumbnail_file, 'watermark_file');
    mw_watermark($target, $size[0], $size[1]
        , $mw_basic[cf_watermark_path]
        , $mw_basic[cf_watermark_position]
        , $mw_basic[cf_watermark_transparency]);

    if ($mw_basic['cf_watermark_type'] == 'png')
        @imagepng($target, $watermark_file);
    else 
        @imagejpeg($target, $watermark_file, $mw_basic[cf_resize_quality]);

    @chmod($watermark_file, 0606);
    @imagedestroy($source);
    @imagedestroy($target);

    return $watermark_file;
}

// 첨부파일의 첫번째 파일을 가져온다.. 080408, curlychoi
// 이미지파일을 가져오는 인수 추가.. 080414, curlychoi
function mw_get_first_file($bo_table, $wr_id, $is_image=false)
{
    global $g4;
    $sql_image = "";
    if ($is_image) $sql_image = " and bf_width > 0 ";
    $sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' $sql_image order by bf_no limit 1";
    $row = sql_fetch($sql);
    return $row;
}

// 핸드폰번호 형식으로 return
function mw_get_hp($hp, $hyphen=1)
{
    if (!mw_is_hp($hp)) return '';

    if ($hyphen) $preg = "$1-$2-$3"; else $preg = "$1$2$3";

    $hp = str_replace('-', '', trim($hp));
    $hp = preg_replace("/^(01[016789])([0-9]{3,4})([0-9]{4})$/", $preg, $hp);

    return $hp;
}

// 핸드폰번호 여부
function mw_is_hp($hp)
{
    $hp = str_replace('-', '', trim($hp));
    if (preg_match("/^(0[17][016789])([0-9]{3,4})([0-9]{4})$/", $hp))
        return true;
    else
        return false;
}

// 분류 옵션을 얻음
function mw_get_category_option($bo_table='')
{
    global $g4;
    global $board;
    global $mw_basic;

    $arr = array_map("trim", explode("|", $board[bo_category_list]));
    if ($mw_basic['cf_ca_order']) {
        sort($arr);
    }

    $str = "";
    for ($i=0; $i<count($arr); $i++)
        if (trim($arr[$i]))
            $str .= "<option value='".urlencode($arr[$i])."'>{$arr[$i]}</option>\n";

    return $str;
}

function mw_set_sync_tag($content) {
    global $member;
    global $board;
    global $write;

    preg_match_all("/<([^>]*)</iUs", $content, $matchs);
    for ($i=0, $max=count($matchs[0]); $i<$max; $i++) {
	$pos = strpos($content, $matchs[0][$i]);
	$len = strlen($matchs[0][$i]);
	$content = substr($content, 0, $pos + $len - 1) . ">" . substr($content, $pos + $len - 1, strlen($content));
    }

    $content = mw_get_sync_tag($content, "div");
    $content = mw_get_sync_tag($content, "table");
    $content = mw_get_sync_tag($content, "font");

    // 그누보드 auto_link 에 괄호포함되는 문제 해결
    $content = preg_replace("/\(<a href=\"([^\)]+)\)\"([^>]*)>([^\)]+)\)<\/a>/i", "(<a href=\"$1\"$2>$3</a>)", $content);

    if ($board[bo_image_width]) {
        if (mw_is_mobile_builder()) {
            $board[bo_image_width] = 280;
        }

        preg_match_all("/width\s*:\s*([0-9]+)px/iUs", $content, $matchs);
        for ($i=0, $m=count($matchs[1]); $i<$m; $i++) {
            if ($matchs[1][$i] > $board[bo_image_width]) {
                $content = str_replace($matchs[0][$i], "width:{$board[bo_image_width]}px ", $content);
            }
        }

        preg_match_all("/width=[\"\']?([0-9]+)[\"\']?\s+height=[\"\']?([0-9]+)[\"\'\s>]/iUs", $content, $matchs);
        for ($i=0, $m=count($matchs[1]); $i<$m; $i++) {
            if ($matchs[1][$i] > $board[bo_image_width]) {
                $height = mw_width_ratio($matchs[1][$i], $matchs[2][$i], $board[bo_image_width]);
                $content = str_replace($matchs[0][$i], "width=\"{$board[bo_image_width]}\", height=\"{$height}\" ", $content);
            }
        }

        preg_match_all("/width=[\"\']?([0-9]+)[\"\'\s>]/iUs", $content, $matchs);
        for ($i=0, $m=count($matchs[1]); $i<$m; $i++) {
            if ($matchs[1][$i] > $board[bo_image_width]) {
                $content = str_replace($matchs[0][$i], "width=\"{$board[bo_image_width]}\" ", $content);
            }
        }
    }
    $content = mw_email_slice($content);

    return $content;
}

function mw_email_slice($content)
{
    $content = preg_replace("/<a href=\"mailto:([0-9a-z._-]+@[a-z0-9._-]{4,})\">([0-9a-z._-]+@[a-z0-9._-]{4,})<\/a>/i", "\\1", $content);
    preg_match_all("/([0-9a-z._-]+@[a-z0-9._-]{4,})/i", $content, $matches);
    for ($i=0, $m=count($matches[1]); $i<$m; ++$i) {
        $content = str_replace($matches[1][$i], mw_basic_nobot_slice($matches[1][$i]), $content);
    }

    return $content;
}

// 이메일의 자동수집을 막기위해 자바스크립트로 한글자씩 잘라 출력함.
// 한글등의 2 byte 이상의 문자는 작동 안함.
function mw_basic_nobot_slice($str) {
    $ret = "<script>";
    $ret.= "document.write(";
    for ($i=0; $i<strlen($str); $i++) {
	$ret .= "\"".substr($str, $i, 1)."\" + ";	
    }
    $ret.= "\"\")</script>";
    return $ret;
}

function mw_width_ratio($width, $height, $target)
{
    $ratio = $height / $width;
    $tmp = $target * $ratio;

    return (int)$tmp;
}

// html 태그 갯수 맞추기
function mw_get_sync_tag($content, $tag) {
    $tag = strtolower($tag);
    $res = strtolower($content);

    $open  = substr_count($res, "<$tag");
    $close = substr_count($res, "</$tag");

    if ($open > $close) {

        $gap = $open - $close;
        for($i=0; $i<$gap; $i++)
            $content .= "</$tag>";

    } else {

        $gap = $close - $open;
        for($i=0; $i<$gap; $i++)
            $content = "<$tag>".$content;
    }

    return $content;
}

// 엄지 짧은링크 얻기
function umz_get_url($url) {
    return;
    global $mw_basic;
    global $is_admin;

    $surl = $mw_basic[cf_umz2];
    if ($surl == 'mwt.so')
        $surl = 'umz.kr';

    if ($surl == 'my') {
        $surl = $mw_basic['cf_umz_domain'];
    }
    else if (!$surl) {
        $surl = "umz.kr";
    }
    $url2 = urlencode($url);
    $fp = @fsockopen ($surl, 80, $errno, $errstr, 30);
    if (!$fp) return false;

    fputs($fp, "POST /plugin/shorten/update.php?url=$url2 HTTP/1.0\r\n");
    fputs($fp, "Host: $surl\r\n");
    fputs($fp, "\r\n");
    while (trim($buffer = fgets($fp,1024)) != "") $header .= $buffer;
    while (!feof($fp)) $buffer .= fgets($fp,1024);
    fclose($fp);
    $ret = trim($buffer);
    if (substr($ret, 0, strlen($surl)+7) != "http://$surl") return '';
    return $ret;
}

// euckr -> utf8 
if (!function_exists("set_utf8")) {
function set_utf8($str)
{
    if (!is_utf8($str))
        $str = convert_charset('cp949', 'utf-8', $str);

    $str = trim($str);

    return $str;
}}

// utf8 -> euckr 
if (!function_exists("set_euckr")) {
function set_euckr($str)
{
    if (is_utf8($str))
        $str = convert_charset('utf-8', 'cp949', $str);

    $str = trim($str);

    return $str;
}}


// Charset 을 변환하는 함수 
if (!function_exists("convert_charset")) {
function convert_charset($from_charset, $to_charset, $str) {
    if( function_exists('iconv') )
        return iconv($from_charset, $to_charset, $str);
    elseif( function_exists('mb_convert_encoding') )
        return mb_convert_encoding($str, $to_charset, $from_charset);
    else
        die("Not found 'iconv' or 'mbstring' library in server.");
}}

// 텍스트가 utf-8 인지 검사하는 함수 
if (!function_exists("is_utf8")) {
function is_utf8($string) {

  // From http://w3.org/International/questions/qa-forms-utf-8.html
  return preg_match('%^(?:
        [\x09\x0A\x0D\x20-\x7E]            # ASCII
      | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
      |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
      | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
      |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
      |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
      | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
      |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
 )*$%xs', $string);
}}

// syntax highlight 
function _preg_callback($m)
{
    //$str = str_replace(array("<br/>", "&nbsp;"), array("\n", " "), $m[1]);

    $trans = get_html_translation_table();
    $trans = array_flip($trans);

    $str = $m[1];
    $str = preg_replace("/<br[^>]+>/i", "\n", $str);
    $str = preg_replace("/&nbsp;/i", " ", $str);
    $str = preg_replace("/<div>/i", "", $str);
    $str = preg_replace("/<\/div>/i", "\n", $str);

    $str = strtr($str, $trans);
    //$str = htmlspecialchars($str);
    $str = preg_replace("/</", "&lt;", $str);
    $str = preg_replace("/>/", "&gt;", $str);
    
    $str = preg_replace("/&lt;br\/&gt;/i", "\n", $str);

    return "<pre class='brush:php;' style='width:300px;'>$str</pre>";
}

function mw_get_level($mb_id) {
    global $icon_level_mb_id;
    global $icon_level_mb_point;
    global $mw_basic;
    $point = 0;
    if (!is_array($icon_level_mb_id)) $icon_level_mb_id = array();
    if (!is_array($icon_level_mb_point)) $icon_level_mb_point = array();
    if (!in_array($mb_id, $icon_level_mb_id)) {
        $icon_level_mb_id[] = $mb_id;
        $mb = get_member($mb_id, "mb_point");
        $icon_level_mb_point[$mb_id] = $mb[mb_point];
        $point = $mb[mb_point];
    } else {
        $point = $icon_level_mb_point[$mb_id];
    }
    $level = intval($point/$mw_basic[cf_icon_level_point]);
    if ($level > 98) $level = 98;
    if ($level < 0) $level = 0;
    return $level;
}

// 코멘트 첨부된 파일을 얻는다. (배열로 반환)
function get_comment_file($bo_table, $wr_id)
{
    global $g4, $mw, $qstr;

    $file["count"] = 0;
    $sql = " select * from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' order by bf_no ";
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result))
    {
        $no = $row[bf_no];
        $file[$no][href] = $g4['bbs_path']."/download.php?bo_table=$bo_table&wr_id=$wr_id&no=$no" . $qstr;
        $file[$no][download] = $row[bf_download];
        // 4.00.11 - 파일 path 추가
        $file[$no][path] = "$g4[path]/data/file/$bo_table";
        //$file[$no][size] = get_filesize("{$file[$no][path]}/$row[bf_file]");
        $file[$no][size] = get_filesize($row[bf_filesize]);
        //$file[$no][datetime] = date("Y-m-d H:i:s", @filemtime("$g4[path]/data/file/$bo_table/$row[bf_file]"));
        $file[$no][datetime] = $row[bf_datetime];
        $file[$no][source] = $row[bf_source];
        $file[$no][bf_content] = $row[bf_content];
        $file[$no][content] = get_text($row[bf_content]);
        //$file[$no][view] = view_file_link($row[bf_file], $file[$no][content]);
        $file[$no][view] = view_file_link($row[bf_file], $row[bf_width], $row[bf_height], $file[$no][content]);
        $file[$no][file] = $row[bf_file];
        // prosper 님 제안
        //$file[$no][imgsize] = @getimagesize("{$file[$no][path]}/$row[bf_file]");
        $file[$no][image_width] = $row[bf_width] ? $row[bf_width] : 640;
        $file[$no][image_height] = $row[bf_height] ? $row[bf_height] : 480;
        $file[$no][image_type] = $row[bf_type];
        $file["count"]++;
    }

    return $file;
}

// 호칭
function get_name_title($name, $wr_name) {
    global $mw_basic;
    if (strlen(trim($mw_basic[cf_name_title]))) {
        $name = str_replace("<span class='member'>{$wr_name}</span>", "<span class='member'>{$wr_name}{$mw_basic[cf_name_title]}</span>", $name);
    }
    return $name;
}

// 에디터 첨부 이미지 목록 가져오기
function mw_get_editor_image($data)
{
    global $g4, $watermark_path;

    $editor_image = $ret = array();

    $url = $g4[url];
    $url = preg_replace("(\/)", "\\\/", $url);
    $url = preg_replace("(\.)", "\.", $url);

    $ext = "<img.*src=\"(.*\/data\/geditor[^\"]+)\"";
    preg_match_all("/$ext/iUs", $data, $matchs);
    for ($j=0; $j<count($matchs[1]); $j++) {
        $editor_image[] = $matchs[1][$j];
    }

    $ext = "<img.*src=\"(.*\/data\/file[^\"]+)\"";
    preg_match_all("/$ext/iUs", $data, $matchs);
    for ($j=0; $j<count($matchs[1]); $j++) {
        $editor_image[] = $matchs[1][$j];
    }

    $ext = "<img.*src=\"(.*\/data\/mw\.cheditor[^\"]+)\"";
    preg_match_all("/$ext/iUs", $data, $matchs);
    for ($j=0; $j<count($matchs[1]); $j++) {
        $editor_image[] = $matchs[1][$j];
    }

    $ext = "<img.*src=\"(.*\/data\/{$g4[cheditor4]}[^\"]+)\"";
    preg_match_all("/$ext/iUs", $data, $matchs);
    for ($j=0; $j<count($matchs[1]); $j++) {
        $editor_image[] = $matchs[1][$j];
    }

    for ($j=0, $m=count($editor_image); $j<$m; $j++) {
        $match = $editor_image[$j];
        $path = $match;
        if (substr($match, 0, 7) == "http://") {
            $path = preg_replace("/http:\/\/[^\/]+\//iUs", "", $match);
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.$path;
            $path = str_replace("//", "/", $path);
            $path = str_replace("//", "/", $path);
        }
        else if (substr($match, 0, 1) == "/") {
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.$path;
            $path = str_replace("//", "/", $path);
            $path = str_replace("//", "/", $path);
        }
        else if (substr($match, 0, 3) == "../") {
            $path = str_replace("../", "", $path);
            for ($z=0, $zm=substr_count(dirname($_SERVER['SCRIPT_NAME']), "/"); $z<$zm; ++$z) {
                $path = '../'.$path;
            }
        }
       
        if (is_mw_file($path)) {
            $ret[http_path][] = $match;
            $ret[local_path][] = $path;
        }
    }
    return $ret;
}

// 에디터 이미지 워터마크 생성
function mw_create_editor_image_watermark($data)
{
    global $g4;
    global $watermark_path;
    global $mw_basic;

    $editor_image = mw_get_editor_image($data);

    for ($j=0, $m=count($editor_image[local_path]); $j<$m; $j++) {
        $match = $editor_image[http_path][$j];
        $path = $editor_image[local_path][$j];
        $size = @getimagesize($path);
        if ($size[0] > 0) {
            $watermark_file = mw_watermark_file($path);
            $data = str_replace($match, $watermark_file, $data);
            if ($mw_basic['cf_image_outline']) {
                mw_image_outline($watermark_file, null, $mw_basic['cf_image_outline_color']);
            }
        }
    }
    return $data;
}

// 에디터 이미지 및 워터마크 삭제
function mw_delete_editor_image($data)
{
    global $g4, $watermark_path;

    $editor_image = mw_get_editor_image($data);

    for ($j=0, $m=count($editor_image[local_path]); $j<$m; $j++) {
        $path = $editor_image[local_path][$j];
        $size = @getimagesize($path);
        if ($size[0] > 0) {
            $watermark_file = "$watermark_path/".basename($path);
            if (is_mw_file($path)) @unlink($path); // 에디터 이미지 삭제
            if (is_mw_file($watermark_file)) @unlink($watermark_file); // 에디터 워터마크 삭제
        }
    }
}

// 팝업공지
function mw_board_popup($view, $html=0)
{
    global $is_admin, $bo_table, $g4, $board_skin_path, $mw_basic, $board, $board_skin_path;

    if (!$board_skin_path) $board_skin_path = $board_skin_path;

    $dialog_id = "mw_board_popup_$view[wr_id]";

    $board['bo_image_width'] = 550;
    $minWidth = 600;
    $minHeight = 300;

    $is_mobile = mw_is_mobile_builder();

    if ($is_mobile) {
        $board[bo_image_width] = 250;
        $minWidth = 250;
        $minHeight = 250;
    }

    /*
    // 파일 출력
    ob_start();
    $cf_img_1_noview = $mw_basic[cf_img_1_noview];
    for ($i=0; $i<=$view[file][count]; $i++) {
        if ($cf_img_1_noview && $view[file][$i][view]) {
            $cf_img_1_noview = false;
            continue;
        }
        if ($view[file][$i][view])
        {
            // 이미지 크기 조절
            if ($board[bo_image_width] < $view[file][$i][image_width]) {
                $img_width = $board[bo_image_width];
            } else {
                $img_width = $view[file][$i][image_width];
            }
            $view[file][$i][view] = str_replace("<img", "<img width=\"{$img_width}\"", $view[file][$i][view]);

            // 워터마크 이미지 출력
            if ($mw_basic[cf_watermark_use]) {
                preg_match("/src='([^']+)'/iUs", $view[file][$i][view], $match);
                $watermark_file = mw_watermark_file($match[1]);
                $view[file][$i][view] = str_replace($match[1], $watermark_file, $view[file][$i][view]);
            }

            echo $view[file][$i][view] . "<br/><br/>";
        }
    }
    $file_viewer = ob_get_contents();
    ob_end_clean();

    $html = 0;
    if (strstr($view['wr_option'], "html1"))
        $html = 1;
    else if (strstr($view['wr_option'], "html2"))
        $html = 2;

    $view[content] = conv_content($view[wr_content], $html);
    $view[rich_content] = preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/ie", "view_image(\$view, '\\1', '\\2')", $view[content]);
    $view[rich_content] = mw_reg_str($view[rich_content]);
    $view[rich_content] = bc_code($view[rich_content]);

    $subject = get_text($view[subject]);
    $subject = mw_reg_str($subject);
    $subject = bc_code($subject);
    $content = $file_viewer.$view[rich_content];
    */
    $subject = get_text($view[subject]);
    $subject = mw_reg_str($subject);
    $subject = bc_code($subject);

    global $write_table, $wr_id, $mw, $member;

    $html = 0;
    if (strstr($view['wr_option'], "html1"))
        $html = 1;
    else if (strstr($view['wr_option'], "html2"))
        $html = 2;

    $view[content] = conv_content($view[wr_content], $html);
    include("$board_skin_path/view_head.skin.php");

    set_session("ss_popup_token", $token = uniqid(time()));

    if ($_COOKIE[$dialog_id]) return false;

    $add_script = "";
    $add_button = "";

    if ($is_mobile) {
        // -----------------------------------------------------------
        // bootstrap modal
        // -----------------------------------------------------------
        if ($is_admin && $view[wr_id]) {
            $add_button = <<<HEREDOC
                <button type="button" class="btn btn-default" onclick="">내림</button>
                
HEREDOC;
            $add_script = <<<HEREDOC
                function mw_board_popup_del() {
                    var q = confirm("정말로 팝업공지를 내리시겠습니까?")
                    if (q) {
                        $.get("$board_skin_path/mw.proc/mw.popup.php?bo_table=$bo_table&wr_id=$view[wr_id]&token=$token", function (ret) {
                            alert(ret);
                        });
                    }
                }
HEREDOC;
        }

        echo <<<HEREDOC

        <div class="modal fade" id="dialog-message-$view[wr_id]" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">$subject</h4>
                    </div>
                    <div class="modal-body">
                    $view[rich_content]
                    </div>
                    <div class="modal-footer">
                        $add_button
                        <button type="button" class="btn btn-default" onclick="mw_board_popup_24()">24시간</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">확인</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        $add_script
        function mw_board_popup_24() {
            set_cookie("mw_board_popup_$view[wr_id]", "1", 24, "$g4[cookie_domain]");
            $("#dialog-message-$view[wr_id]").modal('hide');
        }
        $(document).ready(function () {
            $("#dialog-message-$view[wr_id]").modal('show');
        });
        </script>
HEREDOC;
    }
    else {
        // -----------------------------------------------------------
        // jquery modal
        // -----------------------------------------------------------
        if ($is_admin && $view[wr_id]) {
            $add_script = <<<HEREDOC
                "팝업내림": function () {
                    var q = confirm("정말로 팝업공지를 내리시겠습니까?")
                    if (q) {
                        $.get("$board_skin_path/mw.proc/mw.popup.php?bo_table=$bo_table&wr_id=$view[wr_id]&token=$token", function (ret) {
                            alert(ret);
                        });
                    }
                },
HEREDOC;
        }

        echo <<<HEREDOC
        <div id="dialog-message-$view[wr_id]" class="dialog-content" title="$subject">
            <div>$view[rich_content]</div>
        </div>

        <script>
        $(function() {
            $("#dialog-message-$view[wr_id]").dialog({
                modal: true,
                minWidth: $minWidth,
                minHeight: $minHeight,
                buttons: {
                    $add_script
                    "24시간 동안 창을 띄우지 않습니다.": function () {
                        set_cookie("mw_board_popup_$view[wr_id]", "1", 24, "$g4[cookie_domain]");
                        $(this).dialog("close");
                    },
                    "확인": function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
        </script>

        <style>
        .ui-dialog .ui-dialog-buttonpane button { font-size:.8em; }
        </style>
HEREDOC;
    }
}

function is_okname()
{
    global $g4, $mw, $member, $mw_basic, $is_admin;

    if ($is_admin == 'super') return true;

    set_session("ss_ipin_bo_table", "");

    if (!$mw_basic[cf_kcb_type]) return true;
    if (!$mw_basic[cf_kcb_id]) return true;

    if (get_session("ss_okname")) return true;

    if ($member[mb_id]) {
        $sql = "select * from $mw[okname_table] where mb_id = '$member[mb_id]'";
        $row = sql_fetch($sql, false);
        if ($row) {
            set_session("ss_okname", $row[ok_name]);
            return true;
        }
    }
    return false;
}

function check_okname()
{
    global $mw_basic, $g4, $member, $board_skin_path, $bo_table, $board;

    if (!$mw_basic[cf_kcb_id]) return false;

    echo "<link rel='stylesheet' href='$board_skin_path/style.common.css' type='text/css'>\n";
    echo "<style type='text/css'> #mw_basic { display:none; } </style>\n";

    $req_file = null;

    if ($mw_basic[cf_kcb_type] == "19ban")
        $req_file = "$board_skin_path/mw.proc/mw.19ban.php"; // 19금
    else
        $req_file = "$board_skin_path/mw.proc/mw.okname.php"; // 실명인증

    if (is_mw_file($req_file)) require($req_file);
}

// 자동치환
function mw_reg_str($str)
{
    global $member;

    if ($member[mb_id]) {
        $str = str_replace("{닉네임}", $member[mb_nick], $str);
        $str = str_replace("{별명}", $member[mb_nick], $str);
    } else {
        $str = str_replace("{닉네임}", "회원", $str);
        $str = str_replace("{별명}", "회원", $str);
    }

    return $str;
}

function mw_write_file($file, $contents)
{
    $fp = fopen($file, "w");
    ob_start();
    print_r($contents);
    $msg = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $msg);
    fclose($fp);
}

function mw_read_file($file)
{
    $content = '';
    if (is_mw_file($file)) {
        ob_start();
        readfile($file);
        $content = ob_get_clean();
    }
    return $content;
}

function mw_basic_read_config_file()
{
    global $g4, $mw_basic, $mw_basic_config_file;

    $contents = mw_read_file($mw_basic_config_file);
    $contents = base64_decode($contents);
    $contents = unserialize($contents);

    return $contents;
}

function mw_basic_write_config_file()
{
    global $g4, $mw, $bo_table, $mw_basic_config_file, $mw_basic_config_path;

    $sql = "select * from $mw[basic_config_table] where bo_table = '$bo_table'";
    $mw_basic = sql_fetch($sql, false);

    $contents = $mw_basic;
    $contents = serialize($contents);
    $contents = base64_encode($contents);

    $f = fopen($mw_basic_config_file, "w");
    fwrite($f, $contents);
    fclose($f);
    @chmod($mw_basic_config_file, 0600);

    if (!is_mw_file("$mw_basic_config_path/.htaccess")) {
        $f = fopen("$mw_basic_config_path/.htaccess", "w");
        fwrite($f, "Deny from All");
        fclose($f);
    }
}

function mw_basic_sns_date($datetime)
{
    global $g4;

    $timestamp = strtotime($datetime); // 글쓴날짜시간 Unix timestamp 형식 
    $current = $g4['server_time']; // 현재날짜시간 Unix timestamp 형식 

    // 1년전 
    if ($timestamp <= $current - 86400 * 365)
        $str = (int)(($current - $timestamp) / (86400 * 365)) . "년전"; 
    else if ($timestamp <= $current - 86400 * 31)
        $str = (int)(($current - $timestamp) / (86400 * 31)) . "개월전"; 
    else if ($timestamp <= $current - 86400 * 1)
        $str = (int)(($current - $timestamp) / 86400) . "일전"; 
    else if ($timestamp <= $current - 3600 * 1)
        $str = (int)(($current - $timestamp) / 3600) . "시간전"; 
    else if ($timestamp <= $current - 60 * 1)
        $str = (int)(($current - $timestamp) / 60) . "분전"; 
    else
        $str = (int)($current - $timestamp) . "초전"; 
    
    return $str; 
}

function mw_basic_counting_date($datetime, $endstr=" 남았습니다")
{
    global $g4;

    $timestamp = strtotime($datetime); // 글쓴날짜시간 Unix timestamp 형식 
    if (!$timestamp) return;

    $current = $g4['server_time']; // 현재날짜시간 Unix timestamp 형식 

    if ($current >= $timestamp)
        return "종료 되었습니다.";

    if ($current <= $timestamp - 86400 * 365)
        $str = (int)(($timestamp - $current) / (86400 * 365)) . "년"; 
    else if ($current <= $timestamp - 86400 * 31)
        $str = (int)(($timestamp - $current) / (86400 * 31)) . "개월"; 
    else if ($current <= $timestamp - 86400 * 1)
        $str = (int)(($timestamp - $current) / 86400) . "일"; 
    else if ($current <= $timestamp - 3600 * 1)
        $str = (int)(($timestamp - $current) / 3600) . "시간"; 
    else if ($current <= $timestamp - 60 * 1)
        $str = (int)(($timestamp - $current) / 60) . "분"; 
    else
        $str = (int)($timestamp - $current) . "초"; 
    
    return $str.$endstr; 
}

function bc_code($str, $is_content=1, $only_admin=0) {
    global $g4, $bo_table, $wr_id, $board_skin_path;

    if ($is_content) {
        $str = preg_replace("/\[url:\/\/(.*)\](.*)\[\/url\]/iU", "<a href='http://$1' target='_blank'>$2</a>", $str);
        $str = preg_replace("/\[s\](.*)\[\/s\]/iU", "<s>$1</s>", $str);
        $str = preg_replace("/\[b\](.*)\[\/b\]/iU", "<b>$1</b>", $str);
        $str = preg_replace("/\[u\](.*)\[\/u\]/iU", "<u>$1</u>", $str);
        $str = preg_replace("/\[(h[1-9])\](.*)\[\/h[1-9]\]/iU", "<$1>$2</$1>", $str);
        $str = preg_replace("/\[file([0-9])\](.*)\[\/file[0-9]\]/iU", "<img src=\"$board_skin_path/img/icon_file_down.gif\" align=absmiddle> <span style='cursor:pointer; text-decoration:underline;' onclick=\"file_download('$g4[bbs_path]/download.php?bo_table=$bo_table&wr_id=$wr_id&no=$1', '', '$1');\">$2</span>", $str);
        $str = preg_replace("/\[red\](.*)\[\/red\]/iU", "<span style='color:#ff0000;'>$1</span>", $str);
        $str = preg_replace("/\[grey\](.*)\[\/grey\]/iU", "<span style='color:#999;'>$1</span>", $str);
        $str = preg_replace("/\[gray\](.*)\[\/gray\]/iU", "<span style='color:#999;'>$1</span>", $str);
        //$str = preg_replace("/\[hide\](.*)\[\/hide\]/iU", "", $str);
        $str = preg_replace("/\[link([1-2])\](.*)\[\/link[1-2]\]/iU", "<a href=\"$g4[bbs_path]/link.php?bo_table=$bo_table&wr_id=$wr_id&no=$1\" target=\"_blank\">$2</a>", $str);
        $str = preg_replace("/\[(\/\/[^\s]+)\s+([^\]]+)\]/iUs", "<a href=\"$1\" target=\"_blank\">$2</a>", $str);
        $str = preg_replace("/\[img (\/\/[^\s]+)\]/iUs", "<img src=\"$1\"/>", $str);

        global $write, $config, $row;
        if ($write && $write[mb_id] == $config[cf_admin] && !$row[wr_is_comment]) {
            $callback = create_function ('$arg', '
                global $g4;

                if (mw_is_mobile_builder())
                    $arg[1] = preg_replace("/^\.\.\//", "../../", $arg[1]);
                $content = $arg[0];
                if (is_mw_file($arg[1])) {
                    ob_start();
                    include($arg[1]);
                    $content = ob_get_contents();
                    ob_end_clean();
                }
                return $content;'
            );
            $str = preg_replace_callback("/include\(\"([^\"]+)\"\)/i", $callback, $str);
        }

        $str = preg_replace('/\[soundcloud url="([^"]+)".*params="([^"]+)".*\]/ie', "mw_soundcloud('\\1', '\\2')", $str);
        $str = preg_replace('/\[soundcloud url=.*<A HREF="([^"]+)".*<\/A>.*params=&#034;([^;]+); [^\]]+\]/ie', "mw_soundcloud('\\1', '\\2')", $str);
    }
    if ($only_admin) {
        $callback = create_function('$arg', 'return mw_pay_banner($arg[1], $arg[2]);');
        $str = preg_replace_callback("/\<\?=mw_pay_banner\([\"\']?([^\"\']+)[\"\']?,[\s]*[\"\']?([^\"\']+)[\"\']?\)\?\>/i",
            $callback, $str);
    }

    $str = preg_replace("/\[line\]/iU", "<hr/>", $str);
    $str = preg_replace("/\[line\s+color=([^\]]+)\]/iU", "<hr style='border-color:\\1;'/>", $str);

    $str = preg_replace("/\[hr\]/iU", "<hr/>", $str);
    $str = preg_replace("/\[hr\s+color=([^\]]+)\]/iU", "<hr style='border-color:\\1;'/>", $str);

    $str = preg_replace("/\[month\]/iU", date('n', $g4[server_time]), $str);
    $str = preg_replace("/\[last_day\]/iU", date('t', $g4[server_time]), $str);

    $str = preg_replace("/\[today\]/iU", date('Y년 m월 d일', $g4['server_time']), $str);
    $str = preg_replace("/\[day of the week\]/iU", get_yoil($g4['time_ymdhis']), $str);

    $call_emoticon = create_function ('$arg', '
        global $board_skin_path;
        if (!preg_match("/^[0-9a-z-_\/]+$/i", $arg[1])) return $arg[0];
        $img = glob("{$board_skin_path}/mw.emoticon/{$arg[1]}.{gif,jpg,jpeg,png}", GLOB_BRACE);
        return sprintf("<img src=\"%s\" align=\"absmiddle\"/>", $img[0]);
    ');
    $str = preg_replace_callback("/\[e:([^\]]+)\]/i", $call_emoticon, $str);

    preg_match_all("/\[counting (.*)\]/iU", $str, $matches);
    for ($i=0, $m=count($matches[1]); $i<$m; $i++) {
        $t = mw_basic_counting_date($matches[1][$i]);
        if (!$t) continue;
        $str = preg_replace("/\[counting {$matches[1][$i]}\]/iU", $t, $str);
    }

    $str = mw_tag_debug($str);
    return $str;
}

function mw_spelling($str)
{
    global $g4, $board_skin_path;

    $str = str_replace("&#8238", "& #8238", $str);

    return $str;

    $path = "$board_skin_path/mw.lib/mw.spelling";
    if (is_mw_file($path)) {
        $tmp = mw_read_file($path);
        $list = explode(",", $tmp);
        for ($i=0, $m=count($list); $i<$m; $i++) {
            $spell = trim($list[$i]);
            if (!$spell) continue;
            $spell = explode("-", $spell);
            $str = preg_replace("/{$spell[0]}/", $spell[1], $str);
        }
    }

    if (strtolower(preg_replace('/-/', '', $g4[charset])) == 'euckr') {
        $str = convert_charset("euckr", "cp949//IGNORE", $str);
    }

    return $str;
}

function mw_get_ccl_info($ccl)
{
    $info = array();

    switch ($ccl)
    {
        case "by":
            $info[by] = "by";
            $info[nc] = "";
            $info[nd] = "";
            $info[kr] = "저작자표시";
            break;
        case "by-nc":
            $info[by] = "by";
            $info[nc] = "nc";
            $info[nd] = "";
            $info[kr] = "저작자표시-비영리";
            break;
        case "by-sa":
            $info[by] = "by";
            $info[nc] = "";
            $info[nd] = "sa";
            $info[kr] = "저작자표시-동일조건변경허락";
            break;
        case "by-nd":
            $info[by] = "by";
            $info[nc] = "";
            $info[nd] = "nd";
            $info[kr] = "저작자표시-변경금지";
            break;
        case "by-nc-nd":
            $info[by] = "by";
            $info[nc] = "nc";
            $info[nd] = "nd";
            $info[kr] = "저작자표시-비영리-변경금지";
            break;
        case "by-nc-sa":
            $info[by] = "by";
            $info[nc] = "nc";
            $info[nd] = "sa";
            $info[kr] = "저작자표시-비영리-동일조건변경허락";
            break;
        default :
            $info[by] = "";
            $info[nc] = "nc";
            $info[nd] = "nd";
            $info[kr] = "";
            break;
    }
    $info[ccl] = $ccl;
    $info[msg] = "크리에이티브 커먼즈 코리아 $info[kr] 2.0 대한민국 라이센스에 따라 이용하실 수 있습니다.";
    $info[link] = "http://creativecommons.org/licenses/{$ccl}/2.0/kr/";
    
    return $info;
}

function mw_delete_row($board, $write, $save_log=false, $save_message='삭제되었습니다.')
{
    global $g4;
    global $member;
    global $is_admin;
    global $board_skin_path;

    $write_table = "$g4[write_prefix]$board[bo_table]";

    $row = sql_fetch("select * from $write_table where wr_id = '$write[wr_id]'");
    if (!$row)
        return;

    //$board_skin_path = "$g4[path]/skin/board/$board[bo_skin]";
    $lib_file_path = "$board_skin_path/mw.lib/mw.skin.basic.lib.php";
    if (is_mw_file($lib_file_path)) include($lib_file_path);

    $delete_log = false;
    if (($write['wr_is_comment'] && $mw_basic['cf_comment_delete_log'])
        or (!$write['wr_is_comment'] && $mw_basic['cf_delete_log'])) {
        $delete_log = true;
    }

    if (trim($mw_basic['cf_trash']) && $mw_basic['cf_trash'] != $board['bo_table'] && !$write['wr_is_comment']) {
        mw_row_delete_point($board, $write);
        if ($delete_log or $save_log) {
            mw_move($board, $write['wr_id'], $mw_basic['cf_trash'], 'copy');
        }
        else {
            mw_move($board, $write['wr_id'], $mw_basic['cf_trash'], 'move');
            if (is_g5())
                delete_cache_latest($board['bo_table']);
            return;
        }
    }

    $count_write = 0;
    $count_comment = 0;

    // 썸네일 삭제
    if ($thumb_path) {
        $thumb_file = mw_thumb_jpg("$thumb_path/$write[wr_id]");
        if (is_mw_file($thumb_file)) @unlink($thumb_file);
    }

    if ($thumb2_path) {
        $thumb_file = mw_thumb_jpg("$thumb2_path/$write[wr_id]");
        if (is_mw_file($thumb_file)) @unlink($thumb_file);
    }

    if ($thumb3_path) {
        $thumb_file = mw_thumb_jpg("$thumb3_path/$write[wr_id]");
        if (is_mw_file($thumb_file)) @unlink($thumb_file);
    }

    if ($thumb4_path) {
        $thumb_file = mw_thumb_jpg("$thumb4_path/$write[wr_id]");
        if (is_mw_file($thumb_file)) @unlink($thumb_file);
    }

    if ($thumb5_path) {
        $thumb_file = mw_thumb_jpg("$thumb5_path/$write[wr_id]");
        if (is_mw_file($thumb_file)) @unlink($thumb_file);
    }

    if ($lightbox_path) {
        $files = glob("{$lightbox_path}/{$write['wr_id']}-*");
        @array_map('unlink', $files);
    }

    // 워터마크 삭제
    if ($watermark_path) {
        $sql = " select * from $g4[board_file_table] ";
        $sql.= " where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' and bf_width > 0  order by bf_no";
        $qry = sql_query($sql);
        while ($row = sql_fetch_array($qry)) {
            @unlink("$watermark_path/$row[bf_file]");
        }

        // 에디터 이미지 및 워터마크 삭제
        mw_delete_editor_image($write[wr_content]);
    }

    // 팝업공지 삭제
    sql_query("delete from $mw[popup_notice_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ", false);

    // 코멘트 삭제
    if ($write[wr_is_comment]) {

        // 코멘트 추천 삭제 
        if ($mw[comment_good_table]) 
            sql_query("delete from $mw[comment_good_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'");

        // 코멘트 포인트 삭제
        if (!delete_point($write[mb_id], $board[bo_table], $write[wr_id], '코멘트'))
            insert_point($write[mb_id], $board[bo_comment_point] * (-1), "$board[bo_subject] {$write[wr_parent]}-{$write[wr_id]} 코멘트삭제");

        if (!delete_point($write[mb_id], $board[bo_table], $write[wr_id], '댓글'))
            insert_point($write[mb_id], $board[bo_comment_point] * (-1), "$board[bo_subject] {$write[wr_parent]}-{$write[wr_id]} 코멘트삭제");


        // 업로드된 파일이 있다면 파일삭제
        if ($mw[comment_file_table]) {
            $sql = " select * from $mw[comment_file_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ";
            $qry = sql_query($sql);
            while ($row = sql_fetch_array($qry))
                @unlink("$g4[path]/data/file/$board[bo_table]/$row[bf_file]");

            // 파일테이블 행 삭제
            sql_query(" delete from $mw[comment_file_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");
        }

        // 럭키라이팅 삭제
        if (function_exists("mw_delete_lucky_writing")) mw_delete_lucky_writing($board, $write);

        $count_comment++;
    }
    // 원글삭제
    else { 
        $sql = " select wr_id, mb_id, wr_is_comment from $write_table where wr_parent = '$write[wr_id]' order by wr_id ";
        $result = sql_query($sql);
        while ($row = sql_fetch_array($result)) 
        {
            // 원글이라면
            if (!$row[wr_is_comment]) 
            {
                // 원글 포인트 삭제
                if (!delete_point($row[mb_id], $board[bo_table], $row[wr_id], '쓰기'))
                    insert_point($row[mb_id], $board[bo_write_point] * (-1), "$board[bo_subject] $row[wr_id] 글삭제");

                // qna 포인트 삭제
                delete_point($row[mb_id], $board[bo_table], $row[wr_id], '@qna');
                delete_point($row[mb_id], $board[bo_table], $row[wr_id], '@qna-hold');
                delete_point($row[mb_id], $board[bo_table], $row[wr_id], '@qna-choose');

                // 럭키라이팅 삭제
                if (function_exists("mw_delete_lucky_writing")) mw_delete_lucky_writing($board, $row);

                // 업로드된 파일이 있다면 파일삭제
                $sql2 = " select * from $g4[board_file_table] where bo_table = '$board[bo_table]' and wr_id = '$row[wr_id]' ";
                $result2 = sql_query($sql2);
                while ($row2 = sql_fetch_array($result2))
                    @unlink("$g4[path]/data/file/$board[bo_table]/$row2[bf_file]");
                    
                // 파일테이블 행 삭제
                sql_query(" delete from $g4[board_file_table] where bo_table = '$board[bo_table]' and wr_id = '$row[wr_id]' ");

                // 추천
                sql_query(" delete from $g4[board_good_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");

                $count_write++;
            } 
            // 코멘트라면
            else 
            {
                // 업로드된 파일이 있다면 파일삭제
                if ($mw[comment_file_table]) {
                    $sql2 = " select * from $mw[comment_file_table] where bo_table = '$board[bo_table]' and wr_id = '$row[wr_id]' ";
                    $qry2 = sql_query($sql2);
                    while ($row2 = sql_fetch_array($qry2))
                        @unlink("$g4[path]/data/file/$board[bo_table]/$row2[bf_file]");
                        
                    // 파일테이블 행 삭제
                    sql_query(" delete from $mw[comment_file_table] where bo_table = '$board[bo_table]' and wr_id = '$row[wr_id]' ");
                }

                // 코멘트 추천
                if ($mw[comment_good_table])
                    sql_query(" delete from $mw[comment_good_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");

                // 코멘트 포인트 삭제
                if (!delete_point($row[mb_id], $board[bo_table], $row[wr_id], '코멘트'))
                    insert_point($row[mb_id], $board[bo_comment_point] * (-1), "$board[bo_subject] {$write[wr_id]}-{$row[wr_id]} 코멘트삭제");

                // 코멘트 추천 포인트 삭제
                $sq2 = " select * from $g4[point_table] ";
                $sq2.= "  where po_rel_table = '$board[bo_table]' ";
                $sq2.= "    and po_rel_id = '$write[wr_id]' ";
                $sq2.= "    and (po_rel_action like '%@good%' or po_rel_action like '%@nogood%') ";
                $qr2 = sql_query($sq2);
                while ($ro2 = sql_fetch_array($qr2)) {
                    delete_point($row[mb_id], $board[bo_table], $row[wr_id], $ro2[mb_id].'@good');
                    delete_point($ro2[mb_id], $board[bo_table], $row[wr_id], $ro2[mb_id].'@good_re');
                    delete_point($row[mb_id], $board[bo_table], $row[wr_id], $ro2[mb_id].'@nogood');
                    delete_point($ro2[mb_id], $board[bo_table], $row[wr_id], $ro2[mb_id].'@nogood_re');
                }

                // 럭키라이팅 삭제
                if (function_exists("mw_delete_lucky_writing")) mw_delete_lucky_writing($board, $row);

                $count_comment++;
            }
        }
    }

    // 게시글 삭제
    if ($save_log != 'no' && ($delete_log || $save_log)) {
        if ($mw_basic[cf_post_history]) {
            //$wr_name2 = $board[bo_use_name] ? $member[mb_name] : $member[mb_nick];
            $sql = "insert into $mw[post_history_table]
                       set bo_table = '$board[bo_table]'
                           ,wr_id = '$write[wr_id]'
                           ,wr_parent = '$write[wr_parent]'
                           ,mb_id = '$write[mb_id]'
                           ,ph_name = '$write[wr_name]'
                           ,ph_option = '$write[wr_option]'
                           ,ph_subject = '".addslashes($write[wr_subject])."'
                           ,ph_content = '".addslashes($write[wr_content])."'
                           ,ph_ip = '$_SERVER[REMOTE_ADDR]'
                           ,ph_datetime = '$g4[time_ymdhis]'";
            sql_query($sql);
        }

        $sql = " update $write_table
                    set wr_subject = '$save_message'
                        ,wr_content = '$save_message'
                        ,wr_option = ''
                        ,wr_link1 = ''
                        ,wr_link2 = ''
                  where wr_id = '$write[wr_id]'";
        sql_query($sql);
    }
    else {
        // 원글삭제
        sql_query(" delete from $write_table where wr_parent = '$write[wr_id]' ");
        sql_query(" delete from $write_table where wr_id = '$write[wr_id]' ");

        // 추천 포인트 삭제
        $sql = " select * from $g4[point_table] ";
        $sql.= "  where po_rel_table = '$board[bo_table]' ";
        $sql.= "    and po_rel_id = '$write[wr_id]' ";
        $sql.= "    and (po_rel_action like '%@good%' or po_rel_action like '%@nogood%') ";
        $qry = sql_query($sql);
        while ($row = sql_fetch_array($qry)) {
            delete_point($write[mb_id], $board[bo_table], $write[wr_id], $row[mb_id].'@good');
            delete_point($row[mb_id], $board[bo_table], $write[wr_id], $row[mb_id].'@good_re');
            delete_point($write[mb_id], $board[bo_table], $write[wr_id], $row[mb_id].'@nogood');
            delete_point($row[mb_id], $board[bo_table], $write[wr_id], $row[mb_id].'@nogood_re');
        }

        // 리워드
        sql_query("delete from $mw[reward_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);
        sql_query("delete from $mw[reward_log_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);

        // 설문
        $sql = "select vt_id from $mw[vote_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'";
        $row = sql_fetch($sql, false);
        sql_query("delete from $mw[vote_item_table] where vt_id = '$row[vt_id]'", false);
        sql_query("delete from $mw[vote_log_table] where vt_id = '$row[vt_id]'", false);
        sql_query("delete from $mw[vote_table] where vt_id = '$row[vt_id]'", false);

        // 기타
        sql_query("delete from $mw[download_log_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);
        sql_query("delete from $mw[link_log_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);
        sql_query("delete from $mw[post_history_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);
        sql_query("delete from $mw[singo_log_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);
        sql_query("delete from $mw[must_notice_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]'", false);

        // 최근게시물 삭제
        sql_query(" delete from $g4[board_new_table] where bo_table = '$board[bo_table]' and wr_parent = '$write[wr_id]' ");
        sql_query(" delete from $g4[board_new_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");

        // 스크랩 삭제
        sql_query(" delete from $g4[scrap_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");

        // 퀴즈삭제
        if ($mw_basic[cf_quiz] && is_mw_file("$quiz_path/_config.php")) {
            include("$quiz_path/_config.php");
            $row = sql_fetch(" select * from $mw_quiz[quiz_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ", false);
            sql_query(" delete from $mw_quiz[quiz_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ", false);
            sql_query(" delete from $mw_quiz[log_table] where qz_id = '$row[qz_id]' ", false);
        }

        // 소셜커머스 삭제
        if (is_mw_file("$social_commerce_path/delete.skin.php")) @include("$social_commerce_path/delete.skin.php");

        // 마케팅DB 삭제
        if (is_mw_file("$marketdb_path/delete.skin.php")) @include("$marketdb_path/delete.skin.php");

        // 시험문제 삭제
        if (is_mw_file("$exam_path/delete.skin.php")) @include("$exam_path/delete.skin.php");

        // 게시판배너 삭제
        if (is_mw_file("$bbs_banner_path/delete.skin.php")) @include("$bbs_banner_path/delete.skin.php");

        // 재능마켓 삭제
        if (is_mw_file("$talent_market_path/delete.skin.php")) @include("$talent_market_path/delete.skin.php");

        // 모아보기 삭제
        if (function_exists('mw_moa_delete')) mw_moa_delete($write[wr_id]);

        // 통합검색
        if (function_exists('mw_del_united_search')) {
            if ($write['wr_is_comment'])
                mw_del_united_search($board['gr_id'], $board['bo_table'], $write['wr_id']);
            else
                mw_del_united_search($board['gr_id'], $board['bo_table'], $write['wr_id'], $write['wr_id']);
        }

        if ($write[wr_is_comment]) {
            // 원글의 코멘트 숫자를 감소(다시계산)
            $tmp = sql_fetch("select count(*) as cnt from $write_table where wr_parent = '$write[wr_parent]' and wr_is_comment = '1'");
            sql_query(" update $write_table set wr_comment = '$tmp[cnt]' where wr_id = '$write[wr_parent]' ");
        }
        // 글숫자 감소
        if ($count_write > 0 || $count_comment > 0) {
            sql_query(" update $g4[board_table] set bo_count_write = bo_count_write - '$count_write', bo_count_comment = bo_count_comment - '$count_comment' where bo_table = '$board[bo_table]' ");
        }
    }

    // 공지사항 삭제
    global $notice_div;

    $bo_notice = array_filter(explode($notice_div, trim($board['bo_notice'])), 'strlen');
    $bo_notice = implode($notice_div, array_diff((array)$bo_notice, (array)$write['wr_id']));

    sql_query(" update {$g4['board_table']} set bo_notice = '{$bo_notice}' where bo_table = '{$board['bo_table']}' ");

    if (is_g5())
        delete_cache_latest($board['bo_table']);
}

function mw_anonymous_nick($mb_id, $wr_ip='')
{
    global $mw_anonymous_list, $mw_anonymous_index, $write;

    if (!$mb_id)
        $mb_id = $wr_ip;

    if (!$mw_anonymous_list[$mb_id])
    {
        if (!$mw_anonymous_index)
            $mw_anonymous_index = 1;

        if ($write[mb_id] == $mb_id || $write[wr_ip] == $wr_ip) {
            $mw_anonymous_list[$mb_id] = "익명글쓴이";
        } else {
            $mw_anonymous_list[$mb_id] = "익명{$mw_anonymous_index}호";
            $mw_anonymous_index++;
        }
    }
    return $mw_anonymous_list[$mb_id];
}

// 19+ : 19세 이상
// 19- : 19세 미만 
// 19= : 19세만 
function mw_basic_age($value, $type='alert')
{
    global $g4;
    global $member;
    global $mw_is_list;
    global $mw_is_read;
    global $mw_is_write;
    global $mw_is_comment;
    global $is_admin;
    global $mw_basic;

    if (!$value) return;

    //if (!$member[mb_birth]) return;

    $member_age = floor((date("Ymd", $g4[server_time]) - $member[mb_birth]) / 10000);

    preg_match("/^([0-9]+)([\+\-\=])$/", $value, $match);

    $age = $match[1];
    $age_type = $match[2];

    $msg = '';

    switch ($age_type) {
        case "+" :
            if ($member_age < $age) $msg = "나이 {$age}세 이상만 접근 가능합니다.";
            break;
        case "-" :
            if ($member_age >= $age) $msg = "나이 {$age}세 미만만 접근 가능합니다.";
            break;
        case "=" :
            if ($member_age != $age) $msg = "나이 {$age}세만 접근 가능합니다.";
            break;
    }

    if ($msg && $type == 'alert') {
        alert($msg);
    }

    return $msg;
}

function mw_basic_move_cate($bo_table, $wr_id)
{
    global $g4, $mw_basic, $mw, $board, $write_table;
    global $notice_div;

    $sql = " select * from $mw[move_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and mv_datetime <= '$g4[time_ymdhis]' ";
    $row = sql_fetch($sql);

    if (!$row) return;

    $notice_array = explode($notice_div, trim($board[bo_notice]));
    if ($row[mv_notice] == "u") {
        
        if (!in_array((int)$wr_id, $notice_array))
        {
            $bo_notice = $wr_id . $notice_div . $board[bo_notice];
            sql_query(" update $g4[board_table] set bo_notice = '$bo_notice' where bo_table = '$bo_table' ");
        }
    }
    else if ($row[mv_notice] == "d") {
        $bo_notice = '';
        for ($i=0; $i<count($notice_array); $i++)
            if ((int)$wr_id != (int)$notice_array[$i])
                $bo_notice .= $notice_array[$i] . $notice_div;
        $bo_notice = trim($bo_notice);
        sql_query(" update $g4[board_table] set bo_notice = '$bo_notice' where bo_table = '$bo_table' ");

    }

    if ($row[mv_cate]) 
        sql_query( " update $write_table set ca_name = '$row[mv_cate]' where wr_id = '$wr_id' ");

    sql_query(" delete from $mw[move_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and mv_datetime <= '$g4[time_ymdhis]' ");
}

function mw_view_image($view, $number, $attribute)
{
    global $bo_table;
    global $wr_id;

    $ret = '';
    if (!$view['file']['count'])
        $view['file'] = get_file($bo_table, $wr_id);

    if ($view['file'][$number]['view']) {
        $ret = preg_replace("/>$/", " $attribute>", $view['file'][$number]['view']);
        if (trim($view['file'][$number][content]))
            $ret .= "<br/><br/>" . $view['file'][$number][content] . "<br/><br/>";
    }
    else {
        $ret = "{".$number."번 이미지 없음}";
    }
    return $ret;
}

function mw_view_movie($view, $number, $attribute)
{
    global $bo_table;
    global $wr_id;
    global $mw_basic;
    global $config;
    global $g4;
    global $jwplayer;
    global $jwplayer_count;

    $ret = '';
    if (!$view['file']['count'])
        $view['file'] = get_file($bo_table, $wr_id);

    $path = "{$g4['path']}/data/file/{$bo_table}/{$view['file'][$number]['file']}";

    $is_movie = false;
    if (strstr($mw_basic['cf_multimedia'], '/movie/') && preg_match("/\.($config[cf_movie_extension])$/i", $view['file'][$number]['file'])) {
        $is_movie = true;
    }

    if ($is_movie and is_mw_file($path)) {
        $ret = mw_jwplayer($path);
        if (trim($view['file'][$number]['content']))
            $ret.= "<br/><br/>".$view[file][$number]['content'] . "<br/><br/>";
    }
    else {
        $ret = "{".$number."번 동영상 없음}";
    }
    return $ret;
}

function mw_move($board, $wr_id_list, $chk_bo_table, $sw)
{
    global $g4, $member, $config, $is_admin;

    $bo_table = $board['bo_table'];
    $write_table = $g4['write_prefix'].$bo_table;
    $board_skin_path = "{$g4['path']}/skin/board/{$board['bo_skin']}";

    require("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

    if ($chk_bo_table && !is_array($chk_bo_table)) {
        $tmp = $chk_bo_table;
        $chk_bo_table = array();
        $chk_bo_table[] = $tmp;
    }

    $save = array();
    $save_count_write = 0;
    $save_count_comment = 0;
    $cnt = 0;

    // SQL Injection 으로 인한 코드 보완
    //$sql = " select distinct wr_num from $write_table where wr_id in (" . stripslashes($wr_id_list) . ") order by wr_id ";
    $sql = " select distinct wr_num from $write_table where wr_id in ($wr_id_list) order by wr_id ";
    $result = sql_query($sql);
    while ($row = sql_fetch_array($result)) 
    {
        $wr_num = $row[wr_num];
        for ($i=0; $i<count($chk_bo_table); $i++) 
        {
            $move_bo_table = $chk_bo_table[$i];
            $move_write_table = $g4['write_prefix'] . $move_bo_table;

            $src_dir = "$g4[path]/data/file/$bo_table"; // 원본 디렉토리
            $dst_dir = "$g4[path]/data/file/$move_bo_table"; // 복사본 디렉토리

            $count_write = 0;
            $count_comment = 0;

            $next_wr_num = get_next_num($move_write_table);

            //$sql2 = " select * from $write_table where wr_num = '$wr_num' order by wr_parent, wr_comment desc, wr_id ";
            $sql2 = " select * from $write_table where wr_num = '$wr_num' order by wr_parent, wr_is_comment, wr_comment desc, wr_id ";
            $result2 = sql_query($sql2);
            while ($row2 = sql_fetch_array($result2)) 
            {
                $nick = cut_str($member[mb_nick], $config[cf_cut_name]);
                if (!$row2[wr_is_comment] && $config[cf_use_copy_log]) {
                    $row2[wr_content] .= "\n\n[이 게시물은 {$nick}님에 의해 $g4[time_ymdhis] {$board[bo_subject]}에서 " . ($sw == 'copy' ? '복사' : '이동') ." 됨]";
                    if ($sw == 'copy') {
                        //$row2[wr_content] .= "\n\n".set_http($g4[url])."/$g4[bbs]/board.php?bo_table=$board[bo_table]&wr_id=$row2[wr_id]";
                        $row2[wr_content] .= "\n\n".mw_seo_url($board[bo_table], $row2[wr_id]);
                    }
                }

                if ($sw == 'copy') {
                    $row2['wr_content'] = mw_editor_image_copy($row2['wr_content']);
                }

                $sql = " insert into $move_write_table
                            set wr_num            = '$next_wr_num',
                                wr_reply          = '$row2[wr_reply]',
                                wr_is_comment     = '$row2[wr_is_comment]',
                                wr_comment        = '$row2[wr_comment]',
                                wr_comment_reply  = '$row2[wr_comment_reply]',
                                ca_name           = '".addslashes($row2[ca_name])."',
                                wr_option         = '$row2[wr_option]',
                                wr_subject        = '".addslashes($row2[wr_subject])."',
                                wr_content        = '".addslashes($row2[wr_content])."',
                                wr_link1          = '".addslashes($row2[wr_link1])."',
                                wr_link2          = '".addslashes($row2[wr_link2])."',
                                wr_link1_hit      = '$row2[wr_link1_hit]',
                                wr_link2_hit      = '$row2[wr_link2_hit]',
                                wr_hit            = '$row2[wr_hit]',
                                wr_good           = '$row2[wr_good]',
                                wr_nogood         = '$row2[wr_nogood]',
                                mb_id             = '$row2[mb_id]',
                                wr_password       = '$row2[wr_password]',
                                wr_name           = '".addslashes($row2[wr_name])."',
                                wr_email          = '".addslashes($row2[wr_email])."',
                                wr_homepage       = '".addslashes($row2[wr_homepage])."',
                                wr_datetime       = '$row2[wr_datetime]',
                                wr_last           = '$row2[wr_last]',
                                wr_ip             = '$row2[wr_ip]',
                                wr_1              = '".addslashes($row2[wr_1])."',
                                wr_2              = '".addslashes($row2[wr_2])."',
                                wr_3              = '".addslashes($row2[wr_3])."',
                                wr_4              = '".addslashes($row2[wr_4])."',
                                wr_5              = '".addslashes($row2[wr_5])."',
                                wr_6              = '".addslashes($row2[wr_6])."',
                                wr_7              = '".addslashes($row2[wr_7])."',
                                wr_8              = '".addslashes($row2[wr_8])."',
                                wr_9              = '".addslashes($row2[wr_9])."',
                                wr_10             = '".addslashes($row2[wr_10])."' ";
                sql_query("lock tables $move_write_table write", false);
                sql_query($sql);

                $insert_id = sql_insert_id();
                sql_query("unlock tables", false);

                sql_query(" update $move_write_table set wr_trackback = '".addslashes($row2[wr_trackback])."' where wr_id = '$insert_id' ", false);

                if (!$row2[wr_is_comment]) { // 원글
                    $save_parent = $insert_id;
                    $count_write++;
                } else { // 코멘트
                    $count_comment++;
                }

                sql_query(" update $move_write_table set wr_parent = '$save_parent' where wr_id = '$insert_id' ");

                // 배추스킨 확장 필드 복사/이동
                // 필드별로 업데이트 하는 이유 : 버전업 과정에서 누락된 필드 오류를 그냥 넘어가기 위해
                $flist = mw_table_desc($bo_table);
                foreach ((array)$flist as $key) {
                    $val = addslashes($row2[$key]);
                    sql_query(" update {$move_write_table} set {$key} = '{$val}' where wr_id = '{$insert_id}' ", false);
                }

                // 첨부파일 복사
                $sql3 = " select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by bf_no ";
                $result3 = sql_query($sql3);
                for ($k=0; $row3 = sql_fetch_array($result3); $k++) 
                {
                    $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
                    $filename = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $row3[bf_source]);
                    shuffle($chars_array);
                    $shuffle = implode("", $chars_array);
                    $filename = abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr($shuffle,0,8).'_'.str_replace('%', '', urlencode(str_replace(' ', '_', $filename))); 

                    if ($row3[bf_file]) { // 원본파일을 복사하고 퍼미션을 변경
                        @copy("$src_dir/$row3[bf_file]", "$dst_dir/$filename");
                        @chmod("$dst_dir/$filename", 0606);
                    }

                    $sql = " insert into $g4[board_file_table] 
                                set bo_table = '$move_bo_table', 
                                    wr_id = '$insert_id', 
                                    bf_no = '$row3[bf_no]', 
                                    bf_source = '".addslashes($row3[bf_source])."', 
                                    bf_file = '$filename', 
                                    bf_download = '$row3[bf_download]', 
                                    bf_content = '".addslashes($row3[bf_content])."',
                                    bf_filesize = '$row3[bf_filesize]',
                                    bf_width = '$row3[bf_width]',
                                    bf_height = '$row3[bf_height]',
                                    bf_type = '$row3[bf_type]',
                                    bf_datetime = '$row3[bf_datetime]' ";
                    sql_query($sql);

                    if ($sw == "move" && $row3[bf_file])
                        $save[$cnt][bf_file][$k] = "$src_dir/$row3[bf_file]";
                }

                // 코멘트 첨부파일 복사
                $sql3 = " select * from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by bf_no ";
                $result3 = sql_query($sql3);
                for ($k=0; $row3 = sql_fetch_array($result3); $k++) 
                {
                    $chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));
                    $filename = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $row3[bf_source]);
                    shuffle($chars_array);
                    $shuffle = implode("", $chars_array);
                    $filename = abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.substr($shuffle,0,8).'_'.str_replace('%', '', urlencode(str_replace(' ', '_', $filename))); 

                    if ($row3[bf_file]) { // 원본파일을 복사하고 퍼미션을 변경
                        @copy("$src_dir/$row3[bf_file]", "$dst_dir/$filename");
                        @chmod("$dst_dir/$filename", 0606);
                    }

                    $sql = " insert into $mw[comment_file_table] 
                                set bo_table = '$move_bo_table', 
                                    wr_id = '$insert_id', 
                                    bf_no = '$row3[bf_no]', 
                                    bf_source = '".addslashes($row3[bf_source])."', 
                                    bf_file = '$filename', 
                                    bf_download = '$row3[bf_download]', 
                                    bf_content = '".addslashes($row3[bf_content])."',
                                    bf_filesize = '$row3[bf_filesize]',
                                    bf_width = '$row3[bf_width]',
                                    bf_height = '$row3[bf_height]',
                                    bf_type = '$row3[bf_type]',
                                    bf_datetime = '$row3[bf_datetime]' ";
                    sql_query($sql);

                    if ($sw == "move" && $row3[bf_file])
                        $save[$cnt][bf_file][$k] = "$src_dir/$row3[bf_file]";
                }

                //////////////////////////////////////////////////////////////////////////////
                // 복사 스크립트
                //////////////////////////////////////////////////////////////////////////////
                if ($sw == "copy")
                {
                    // 최신글 등록
                    $sql = " insert into $g4[board_new_table] ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) ";
                    $sql.= " values ( '$move_bo_table', '$insert_id', '$save_parent', '$row2[wr_datetime]', '$row2[mb_id]' ) ";
                    sql_query($sql);

                    // 리워드
                    $tmp = sql_fetch("select * from $mw[reward_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'");
                    if ($tmp) {
                        $sql_common = "bo_table     = '$move_bo_table'";
                        $sql_common.= ", wr_id      = '$insert_id'";
                        $sql_common.= ", re_site    = '".addslashes($tmp[re_site])."'";
                        $sql_common.= ", re_point   = '$tmp[re_point]'";
                        $sql_common.= ", re_url     = '".addslashes($tmp[re_url])."'";
                        $sql_common.= ", re_edate   = '$tmp[re_edate]'";
                        sql_query("insert into $mw[reward_table] set $sql_common, re_status = '1'");
                    }

                    // 설문
                    $tmp = sql_fetch("select * from $mw[vote_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'");
                    if ($tmp) {
                        $vt_id = $tmp[vt_id];

                        $sql = "insert into $mw[vote_table] set bo_table = '$move_bo_table'";
                        $sql.= ", wr_id = '$insert_id' ";
                        $sql.= ", vt_edate = '$tmp[vt_edate]' ";
                        $sql.= ", vt_total = '$tmp[vt_total]' ";
                        $sql.= ", vt_point = '$tmp[vt_point]' ";
                        sql_query($sql);

                        $insert_vt_id = sql_insert_id();

                        $qry = sql_query("select * from $mw[vote_item_table] where vt_id = '$vt_id' order by vt_num");
                        while ($tmp = sql_fetch_array($qry)) {
                            sql_query("insert into $mw[vote_item_table] set vt_id = '$insert_vt_id', vt_num = '$tmp[vt_num]', vt_item = '$tmp[vt_item]', vt_hit = '$tmp[vt_hit]'");
                        }

                        $qry = sql_query("select * from $mw[vote_log_table] where vt_id = '$tmp[vt_id]' order by vt_num");
                        while ($tmp = sql_fetch_array($qry)) {
                            sql_query("insert into $mw[vote_log_table] set vt_id = '$insert_vt_id', vt_num = '$tmp[vt_num]', mb_id = '$tmp[mb_id]', vt_ip = '$tmp[vt_ip]', vt_datetime = '$tmp[vt_datetime]'");
                        }
                    }

                    // 글 변경로그
                    $qry = sql_query("select * from $mw[post_history_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by ph_id", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", wr_parent      = '$save_parent'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", ph_name        = '$tmp[ph_name]'";
                        $sql_common.= ", ph_option      = '$tmp[ph_option]'";
                        $sql_common.= ", ph_subject     = '".addslashes($tmp[ph_subject])."'";
                        $sql_common.= ", ph_content     = '".addslashes($tmp[ph_content])."'";
                        $sql_common.= ", ph_ip          = '$tmp[ph_ip]'";
                        $sql_common.= ", ph_datetime    = '$tmp[ph_datetime]'";
                        sql_query("insert into $mw[post_history_table] set $sql_common");
                    }

                    // 다운로드 로그
                    $qry = sql_query("select * from $mw[download_log_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by dl_id", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", bf_no          = '$tmp[bf_no]'";
                        $sql_common.= ", dl_name        = '$tmp[dl_name]'";
                        $sql_common.= ", dl_ip          = '$tmp[dl_ip]'";
                        $sql_common.= ", dl_datetime    = '$tmp[dl_datetime]'";
                        sql_query("insert into $mw[download_log_table] set $sql_common");
                    }

                    // 원글추천
                    $qry = sql_query("select * from $g4[board_good_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by bg_id ", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", bg_flag        = '$tmp[bg_flag]'";
                        $sql_common.= ", bg_datetime    = '$tmp[bg_datetime]'";
                        sql_query("insert into $g4[board_good_table] set $sql_common");                   
                    }

                    // 코멘트추천
                    $qry = sql_query("select * from $mw[comment_good_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' ", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", parent_id      = '$save_parent'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", bg_flag        = '$tmp[bg_flag]'";
                        $sql_common.= ", bg_datetime    = '$tmp[bg_datetime]'";
                        sql_query("insert into $mw[comment_good_table] set $sql_common");                   
                    }

                    // 신고로그
                    $qry = sql_query("select * from $mw[singo_log_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by si_id ", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", si_type        = '$tmp[si_type]'";
                        $sql_common.= ", si_memo        = '$tmp[si_memo]'";
                        $sql_common.= ", si_ip          = '$tmp[si_ip]'";
                        $sql_common.= ", si_datetime    = '$tmp[si_datetime]'";
                        sql_query("insert into $mw[singo_log_table] set $sql_common");                   
                    }

                    // 링크로그
                    $qry = sql_query("select * from $mw[link_log_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' order by ll_id ", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", ll_no          = '$tmp[ll_lo]'";
                        $sql_common.= ", ll_name        = '$tmp[ll_name]'";
                        $sql_common.= ", ll_ip          = '$tmp[ll_ip]'";
                        $sql_common.= ", ll_datetime    = '$tmp[ll_datetime]'";
                        sql_query("insert into $mw[link_log_table] set $sql_common");                   
                    }

                    // 공지필수
                    $qry = sql_query("select * from $mw[must_notice_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' ", false);
                    while ($tmp = sql_fetch_array($qry)) {
                        $sql_common = "bo_table         = '$move_bo_table'";
                        $sql_common.= ", wr_id          = '$insert_id'";
                        $sql_common.= ", mb_id          = '$tmp[mb_id]'";
                        $sql_common.= ", mu_datetime    = '$tmp[mu_datetime]'";
                        sql_query("insert into $mw[must_notice_table] set $sql_common");                   
                    }
                }

                //////////////////////////////////////////////////////////////////////////////
                // 이동 스크립트
                //////////////////////////////////////////////////////////////////////////////
                else if ($sw == "move")
                {
                    $save[$cnt][wr_id] = $row2[wr_parent];

                    // 썸네일 삭제
                    @unlink(mw_thumb_jpg("$thumb_path/$row2[wr_id]"));
                    @unlink(mw_thumb_jpg("$thumb2_path/$row2[wr_id]"));
                    @unlink(mw_thumb_jpg("$thumb3_path/$row2[wr_id]"));
                    @unlink(mw_thumb_jpg("$thumb4_path/$row2[wr_id]"));
                    @unlink(mw_thumb_jpg("$thumb5_path/$row2[wr_id]"));

                    // 워터마크 삭제
                    $sql = "select * from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' and bf_width > 0  order by bf_no";
                    $qry = sql_query($sql);
                    while ($file = sql_fetch_array($qry)) {
                        @unlink("$watermark_path/$row[bf_file]");
                    }

                    // 스크랩 이동
                    $sql = " update $g4[scrap_table] set bo_table = '$move_bo_table', wr_id = '$save_parent' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' ";
                    sql_query($sql);

                    // 최신글 이동
                    $sql = " update $g4[board_new_table] set bo_table = '$move_bo_table', wr_id = '$insert_id', wr_parent = '$save_parent' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' ";
                    sql_query($sql);

                    // 리워드
                    $sql = " update $mw[reward_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    $sql = " update from $mw[reward_log_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 설문
                    $sql = " update $mw[vote_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 글 변경로그
                    $sql = " update $mw[post_history_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 다운로드 로그
                    $sql = " update $mw[download_log_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 원글 추천
                    $sql = " update $g4[board_good_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 코멘트 추천
                    $sql = " update $mw[comment_good_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 코멘트 추천
                    $sql = " update $mw[comment_good_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 신고로그
                    $sql = " update $mw[singo_log_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 링크로그
                    $sql = " update $mw[link_log_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 공지필수
                    $sql = " update $mw[must_notice_table] set bo_table = '$move_bo_table', wr_id = '$insert_id' ";
                    $sql.= " where bo_table = '$bo_table' and wr_id = '$row2[wr_id]'";
                    sql_query($sql, false);

                    // 모아보기 삭제
                    if (function_exists('mw_moa_delete')) mw_moa_delete($row2[wr_id]);

                    // 팝업공지 삭제
                    sql_query("delete from $mw[popup_notice_table] where bo_table = '$bo_table' and wr_id = '$row2[wr_id]' ", false);
                }

                // 소셜커머스
                if (!$row2[wr_is_comment] && is_mw_file("$social_commerce_path/move_update.skin.php")) {
                    @include("$social_commerce_path/move_update.skin.php");
                }

                // 마케팅DB
                if (!$row2[wr_is_comment] && is_mw_file("$marketdb_path/move_update.skin.php")) {
                    @include("$marketdb_path/move_update.skin.php");
                }

                // 시험문제
                if (!$row2[wr_is_comment] && is_mw_file("$exam_path/move_update.skin.php")) {
                    @include("$exam_path/move_update.skin.php");
                }

                // 게시판배너
                if (!$row2[wr_is_comment] && is_mw_file("$bbs_banner_path/move_update.skin.php")) {
                    @include("$bbs_banner_path/move_update.skin.php");
                }

                // 재능마켓
                if (!$row2[wr_is_comment] && is_mw_file("$talent_market_path/move_update.skin.php")) {
                    @include("$talent_market_path/move_update.skin.php");
                }

                // 퀴즈
                if (!$row2[wr_is_comment] && is_mw_file("$quiz_path/move_update.skin.php")) {
                    @include("$quiz_path/_config.php");
                    @include("$quiz_path/move_update.skin.php");
                }

                $cnt++;
            }

            sql_query(" update $g4[board_table] set bo_count_write   = bo_count_write   + '$count_write'   where bo_table = '$move_bo_table' ");
            sql_query(" update $g4[board_table] set bo_count_comment = bo_count_comment + '$count_comment' where bo_table = '$move_bo_table' ");
        }

        $save_count_write += $count_write;
        $save_count_comment += $count_comment;
    }

    if ($sw == "move") 
    {
        for ($i=0; $i<count($save); $i++) 
        {
            //  파일삭제
            for ($k=0; $k<count($save[$i][bf_file]); $k++) {
                @unlink($save[$i][bf_file][$k]);    
            }

            sql_query(" delete from $write_table where wr_parent = '{$save[$i][wr_id]}' "); // 원글삭제
            sql_query(" delete from $g4[board_new_table] where bo_table = '$bo_table' and wr_id = '{$save[$i][wr_id]}' "); // 최신글 삭제
            sql_query(" delete from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '{$save[$i][wr_id]}' "); // 파일정보 삭제
            sql_query(" delete from $mw[comment_file_table] where bo_table = '$bo_table' and wr_id = '{$save[$i][wr_id]}' "); // 코멘트 파일정보 삭제
        }
        // 게시판 글수 카운터 조정
        $sql = " update $g4[board_table] set ";
        $sql.= "   bo_count_write = bo_count_write - '$save_count_write' ";
        $sql.= " , bo_count_comment = bo_count_comment - '$save_count_comment' ";
        $sql.= " where bo_table = '$bo_table' ";
        sql_query($sql);
    }

    // 공지사항에는 등록되어 있지만 실제 존재하지 않는 글 아이디는 삭제합니다.
    global $notice_div;
    $bo_notice = "";
    $lf = "";
    if ($board[bo_notice]) {
        $tmp_array = explode($notice_div, $board[bo_notice]);
        for ($i=0; $i<count($tmp_array); $i++) {
            $tmp_wr_id = trim($tmp_array[$i]);
            $row = sql_fetch(" select count(*) as cnt from $g4[write_prefix]$bo_table where wr_id = '$tmp_wr_id' ");
            if ($row[cnt]) 
            {
                $bo_notice .= $lf . $tmp_wr_id;
                $lf = $notice_div;
            }
        }
    }
    $sql = " update $g4[board_table] set bo_notice = '$bo_notice' where bo_table = '$bo_table' ";
    sql_query($sql);

    return $save_parent;
}

function mw_bomb()
{
    global $g4, $mw, $config;

    $is_bomb = false;

    sql_query("lock tables $mw[bomb_table] write", false);
    //$sql = " select * from $mw[bomb_table] where bo_table = '$board[bo_table]' and bm_datetime <= '$g4[time_ymdhis]' ";
    $sql = " select * from $mw[bomb_table] where bm_datetime <= '$g4[time_ymdhis]' ";
    $qry = sql_query(" select * from $mw[bomb_table] where bm_datetime <= '$g4[time_ymdhis]' ", false);
    sql_query("delete from $mw[bomb_table] where bm_datetime <= '$g4[time_ymdhis]'");
    sql_query("unlock tables", false);

    while ($row = sql_fetch_array($qry)) {
        $write_table = $g4[write_prefix].$row[bo_table];
        $write = sql_fetch("select * from $write_table where wr_id = '$row[wr_id]'");
        $board = sql_fetch("select * from $g4[board_table] where bo_table = '$row[bo_table]'");
        $mw_basic = sql_fetch("select cf_bomb_move_table, cf_bomb_move_time, cf_bomb_move_cate, cf_bomb_item from $mw[basic_config_table] where bo_table = '$row[bo_table]'");

        $move_table = trim($row[bm_move_table]);
        if (!$move_table)
            $move_table = trim($mw_basic[cf_bomb_move_table]);

        if ($move_table) {
            if ($row[bm_log]) {
                $wr_id = mw_move($board, $row[wr_id], $move_table, 'copy');
                mw_delete_row($board, $write, $row[bm_log], '폭파되었습니다.');
            }
            else
                $wr_id = mw_move($board, $row[wr_id], $move_table, 'move');

            if ($mw_basic['cf_bomb_move_time'] && $wr_id) {
                $sql = "update $g4[write_prefix]$move_table set wr_datetime = '$row[bm_datetime]' where wr_id = '$wr_id'";
                sql_query($sql);
            }
            if ($mw_basic['cf_bomb_move_cate'] && $wr_id) {
                $sql = "update $g4[write_prefix]$move_table set ca_name = '".addslashes($board[bo_subject])."' where wr_id = '$wr_id'";
                sql_query($sql);
            }
        } else {
            if (!$mw_basic[cf_bomb_item]) {
                mw_delete_row($board, $write, $row[bm_log], '폭파되었습니다.');
            }
            else {
                if (strstr($mw_basic[cf_bomb_item], "subject")) {
                    $sql = " update $write_table set wr_subject = '폭파되었습니다.' where wr_id = '$write[wr_id]' ";
                    sql_query($sql);
                }
                if (strstr($mw_basic[cf_bomb_item], "content")) {
                    $sql = " update $write_table set wr_content = '폭파되었습니다.' where wr_id = '$write[wr_id]' ";
                    sql_query($sql);
                }
                if (strstr($mw_basic[cf_bomb_item], "file")) {
                    // 썸네일 삭제
                    global $thumb_path, $thumb2_path, $thumb3_path, $thumb4_path, $thumb5_path, $lightbox_path, $watermark_path;
                    if ($thumb_path) {
                        $thumb_file = mw_thumb_jpg("$thumb_path/$write[wr_id]");
                        if (is_mw_file($thumb_file)) @unlink($thumb_file);
                    }

                    if ($thumb2_path) {
                        $thumb_file = mw_thumb_jpg("$thumb2_path/$write[wr_id]");
                        if (is_mw_file($thumb_file)) @unlink($thumb_file);
                    }

                    if ($thumb3_path) {
                        $thumb_file = mw_thumb_jpg("$thumb3_path/$write[wr_id]");
                        if (is_mw_file($thumb_file)) @unlink($thumb_file);
                    }

                    if ($thumb4_path) {
                        $thumb_file = mw_thumb_jpg("$thumb4_path/$write[wr_id]");
                        if (is_mw_file($thumb_file)) @unlink($thumb_file);
                    }

                    if ($thumb5_path) {
                        $thumb_file = mw_thumb_jpg("$thumb5_path/$write[wr_id]");
                        if (is_mw_file($thumb_file)) @unlink($thumb_file);
                    }

                    if ($lightbox_path) {
                        $files = glob("{$lightbox_path}/{$write['wr_id']}-*");
                        @array_map('unlink', $files);
                    }

                    $sql = " select * from $g4[board_file_table] ";
                    $sql.= " where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' order by bf_no";
                    $qry = sql_query($sql);
                    while ($row = sql_fetch_array($qry)) {
                        @unlink("$g4[path]/data/file/$board[bo_table]/$row[bf_file]");
                        @unlink("$watermark_path/$row[bf_file]");
                    }
                    sql_query("delete from $g4[board_file_table] where bo_table = '$board[bo_table]' and wr_id = '$write[wr_id]' ");

                    // 에디터 이미지 및 워터마크 삭제
                    mw_delete_editor_image($write[wr_content]);
                }
            }
        }
        //$sql = "delete from $mw[bomb_table] where bo_table = '$board[bo_table]' and wr_id = '$row[wr_id]'";
        //sql_query($sql, false);

        $is_bomb = true;
    }
    if ($is_bomb) {
        ?><script>location.reload();</script><?
        exit;
    }
}

function mw_tag_debug($str) // 잘못된 태그교정
{
    $str = preg_replace("/&lt;br\/>/i", "<br/>", $str);

    $tags = array('td', 'tr', 'table', 'div', 'ol', 'ul', 'span');

    foreach ($tags as $tag) {
        $sc = preg_match_all("/<$tag/i", $str, $matchs);
        $ec = preg_match_all("/<\/$tag/i", $str, $matchs);

        if ($sc > $ec) $str.= str_repeat("</$tag>", $sc-$ec);
        if ($sc < $ec) $str = str_repeat("<$tag>", $ec-$sc).$str;
    }
    return $str;
}

function mw_get_noimage()
{
    global $g4, $mw_basic, $board_skin_path;

    if (trim($mw_basic[cf_noimage_path]) && is_mw_file($mw_basic[cf_noimage_path]) and !is_dir($mw_basic[cf_noimage_path]))
        return $mw_basic[cf_noimage_path];

    return "$board_skin_path/img/noimage.gif";
}

function mw_jwplayer($url, $opt="")
{
    global $jwplayer;
    global $jwplayer_count;
    global $board_skin_path;
    global $board_skin_path;
    global $mw_basic;

    if (!$board_skin_path) $board_skin_path = $board_skin_path;
    if (!$jwplayer) $jwplayer = false;
    if (!$jwplayer_count) $jwplayer_count = 0;

    if (!$mw_basic['cf_jwplayer_version'])
        $mw_basic['cf_jwplayer_version'] = 'jwplayer6';

    $buffer = '';
    if (!$jwplayer) {
        $buffer .= "<script src='$board_skin_path/{$mw_basic['cf_jwplayer_version']}/jwplayer.js'></script>";
        $buffer .= "<script>jwplayer.key='';</script>";
        $jwplayer = true;
    }
    $buffer .= "<div id='jwplayer{$jwplayer_count}'>Loading the player...</div>";
    $buffer .= "<script> jwplayer('jwplayer{$jwplayer_count}').setup({ ";
    if ($mw_basic['cf_jwplayer_version'] == 'jwplayer5') {
        $buffer .= " flashplayer:'$board_skin_path/jwplayer5/player.swf', ";
        global $g4;
        $url = str_replace("../..", $g4[url], $url);
        $url = str_replace("..", $g4[url], $url);
    }
    if (mw_is_mobile_builder()) {
        $opt .= ", width:'100%' ";
    }
    elseif ($mw_basic['cf_player_size']) {
        $size = explode("x", $mw_basic['cf_player_size']);
        $opt .= ", width:'{$size[0]}', height:'{$size[1]}' ";
    }

    if ($mw_basic['cf_jwplayer_autostart']) {
        $opt = ', autostart:true ' . $opt;
    }
    $buffer .= " file:'{$url}' {$opt} }); </script>";

    $jwplayer_count++;

    return $buffer;
}

function mw_file_view($url, $write, $width=0, $height=0, $content="")
{
    global $g4, $config, $board, $mw_basic, $member, $jwplayer;
    static $ids;

    if (!$url) return;

    $ids++;

    // 파일의 폭이 게시판설정의 이미지폭 보다 크다면 게시판설정 폭으로 맞추고 비율에 따라 높이를 계산
    if ($width > $board[bo_image_width] && $board[bo_image_width])
    {
        $rate = $board[bo_image_width] / $width;
        $width = $board[bo_image_width];
        $height = (int)($height * $rate);
    }

    $mb = array();
    if ($write[mb_id])
        $mb = get_member($write[mb_id], "mb_level");

    if (preg_match("/\.($config[cf_image_extension])($|\?)/i", $url)) {
        // 이미지에 속성을 주지 않는 이유는 이미지 클릭시 원본 이미지를 보여주기 위한것임
        // 게시판설정 이미지보다 크다면 스킨의 자바스크립트에서 이미지를 줄여준다
        return "<img src='{$url}' name='target_resize_image[]' onclick='image_window(this);' style='cursor:pointer;' title='$content'>";
    }
    else if ($mw_basic[cf_iframe_level] and $mw_basic[cf_iframe_level] <= $mb[mb_level]) {
        if (!$width) {
            $width = 400;
            $height = 320;
        }
        if (!$jwplayer && preg_match("/\.($config[cf_movie_extension])$/i", $url)) {
            return "<script>doc_write(obj_movie('{$url}', '_g4_{$ids}', '$width', '$height'));</script>";
        }
        else if (preg_match("/\.($config[cf_flash_extension])$/i", $url)) {
            $size = @getimagesize($url);
            if ($size[0]) {
                $width = $size[0];
                $height = $size[1];
                if ($width > $board[bo_image_width] && $board[bo_image_width])
                {
                    $rate = $board[bo_image_width] / $width;
                    $width = $board[bo_image_width];
                    $height = (int)($height * $rate);
                }
            }
            return "<script>doc_write(obj_movie('{$url}', '_g4_{$ids}', '$width', '$height'));</script>";
        }
    }
}

function mw_get_youtube_thumb($wr_id, $url, $datetime='')
{
    global $g4, $mw_basic, $thumb_path;

    $file = mw_thumb_jpg("$thumb_path/{$wr_id}");
    //if (is_mw_file($file)) return;

    $p = parse_url($url);

    if (preg_match("/^https?:\/\/youtu.be\/(.*)$/i", $url, $mat)) {
        //$v = $mat[1];
        $v = basename($p['path']);
    }
    elseif (preg_match("/\/\/.*youtube\.com\/.*v[=\/]([a-zA-Z0-9-_]+)?/i", $url, $mat)) {
        parse_str($p['query']);
        //$v = $mat[1];
    }
    elseif (preg_match('/player.vimeo.com\/video\/(\d+)$/', $url, $mat)) {
        mw_get_vimeo_thumb($wr_id, $url, $datetime);
        return;
    }

    if (!$v) return;

    thumb_log($thumbnail_file, 'youtube-try');

    $fp = fsockopen ("img.youtube.com", 80, $errno, $errstr, 10);
    if (!$fp) return false;
    fputs($fp, "GET /vi/{$v}/mqdefault.jpg HTTP/1.0\r\n");
    fputs($fp, "Host: img.youtube.com:80\r\n");
    fputs($fp, "\r\n");
    while (trim($buffer = fgets($fp,1024)) != "") $header .= $buffer;
    while (!feof($fp)) $buffer .= fgets($fp,1024);
    fclose($fp);

    if ($buffer) {
        $fw = @fopen ($file, "wb");
        if ($fw) {
            fwrite($fw, trim($buffer));
            chmod ($file, 0777);
            fclose($fw);
        }

        // 이미지가 아니면 삭제
        $size = @getimagesize($file);
        if ($size[2] != 2) @unlink($file);
    }

    thumb_log($thumbnail_file, 'youtube');
    //mw_make_thumbnail($mw_basic[cf_thumb_width], $mw_basic[cf_thumb_height], $file, $file, true);
    mw_make_thumbnail_all($file);

    if (!$datetime) {
        global $write;
        if ($write['wr_datetime'])
            @touch($file, strtotime($write['wr_datetime']));
    }
    else if ($datetime) {
        @touch($file, strtotime($datetime));
    }
}

function mw_get_vimeo_thumb($wr_id, $url, $datetime='')
{
    global $g4, $mw_basic, $thumb_path;

    preg_match('/vimeo.com\/(\d+)$/', $url, $mat);
    $v = $mat[1];

    if (!$v) return;

    $file = mw_thumb_jpg("$thumb_path/{$wr_id}");
    if (is_mw_file($file)) return;

    $fp = fsockopen ("vimeo.com", 80, $errno, $errstr, 10);
    if (!$fp) return false;
    fputs($fp, "GET /api/v2/video/{$v}.php HTTP/1.0\r\n");
    fputs($fp, "Host: vimeo.com\r\n");
    fputs($fp, "\r\n");
    while (trim($buffer = fgets($fp,1024)) != "") $header .= $buffer;
    while (!feof($fp)) $buffer .= fgets($fp,1024);
    fclose($fp);

    $dat = unserialize(trim($buffer)); 
    $dat = $dat[0];

    if (!trim($dat[thumbnail_large])) return;

    $url = parse_url(trim($dat[thumbnail_large]));

    $fp = fsockopen ("$url[host]", 80, $errno, $errstr, 10);
    if (!$fp) return false;
    fputs($fp, "GET $url[path] HTTP/1.0\r\n");
    fputs($fp, "Host: $url[host]\r\n");
    fputs($fp, "\r\n");
    while (trim($buffer = fgets($fp,1024)) != "") $header .= $buffer;
    while (!feof($fp)) $buffer .= fgets($fp,1024);
    fclose($fp);

    if ($buffer) {
        $fw = @fopen ($file, "wb");
        if ($fw) {
            fwrite($fw, trim($buffer));
            chmod ($file, 0777);
            fclose($fw);
        }

        // 이미지가 아니면 삭제
        $size = @getimagesize($file);
        if ($size[2] != 2) @unlink($file);
    }

    thumb_log($thumbnail_file, 'vimeo');
    //mw_make_thumbnail($mw_basic[cf_thumb_width], $mw_basic[cf_thumb_height], $file, $file, true);
    mw_make_thumbnail_all($file);

    if (!$datetime) {
        global $write;
        if ($write['wr_datetime'])
            @touch($file, strtotime($write['wr_datetime']));
    }
    else if ($datetime) {
        @touch($file, strtotime($datetime));
    }
}

function mw_youtube($url, $q=0)
{
    global $g4, $board, $mw_basic;

    $v = '';
    $l = '';

    if (strstr($url, "youtube.com/feeds")) return;

    if (preg_match("/^https?:\/\/youtu.be\/([a-zA-Z0-9_-]+)?/i", $url, $mat)) {
        $v = $mat[1];
    }
    elseif (preg_match("/^https?:\/\/www\.youtube\.com\/watch\?v=([^&]+)&.*&list=([^&]+)&$/i", $url.'&', $mat)) {
        $v = $mat[1];
        $l = $mat[2];
    }
    //elseif (preg_match("/^http[s]{0,1}:\/\/www\.youtube\.com\/watch\?v=([^&]+)&/i", $url.'&', $mat)) {
    elseif (preg_match("/\/\/.*youtube\.com\/.*v[=\/]([a-zA-Z0-9_-]+)?/i", $url, $mat)) {
        $v = $mat[1];
    }
    elseif (preg_match("/\/\/.*youtube\.com\/embed\/([a-zA-Z0-9_-]+)?/i", $url, $mat)) {
        $v = $mat[1];
    }

    if (!$v) return;

    $t = null;
    preg_match("/t=([0-9ms]+)?/i", $url, $mat);
    if ($mat[1]) {
        $t = $mat[1];

        preg_match("/([0-9]+)m/", $t, $mat);
        $m = $mat[1];

        preg_match("/([0-9]+)s/", $t, $mat);
        $s = $mat[1];

        $t = $m*60+$s;
    }

    $v = trim($v);

    $src = "https://www.youtube.com/embed/{$v}?fs=1&hd=1";
    if ($t)
        $src .= "&start=".$t;
    if ($l)
        $src .= "&list=".$l;

    $tmp = parse_url($url);
    $param = $tmp['query'];

    if (!$mw_basic['cf_youtube_size'])
        $mw_basic['cf_youtube_size'] = 360;

    if ($q) {
        $mw_basic['cf_youtube_size'] = $q;
        $mw_basic['cf_player_size'] = null;
    }

    switch ($mw_basic['cf_youtube_size']) {
        case "144": $width = 320; $height = 180; break;
        case "240": $width = 560; $height = 315; break;
        case "360": $width = 640; $height = 394; break;
        case "480": $width = 854; $height = 516; break;
        case "720": $width = 1280; $height = 759; break;
        case "1080": $width = 1920; $height = 1123; break;
        default:
            $width = 640; $height = 394; break;
    }

    if ($mw_basic['cf_player_size']) {
        $size = explode("x", $mw_basic['cf_player_size']);
        $width = $size[0];
        $height = $size[1];
    }

    if ($width > $board['bo_image_width']) {
        $height = floor($board['bo_image_width']/$width*$height);
        $width = $board['bo_image_width'];
    }

    $you = "<iframe width='{$width}' height='{$height}' src='{$src}&wmode=transparent&{$param}' frameborder='0' ";
    $you.= "webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>";

    return $you;
}

function mw_youtube_content($content, $q='')
{
    $pt = mw_youtube_pattern($content);
    if ($pt)
        $content = preg_replace($pt, "mw_youtube('\\1', '$q')", $content);

    $pt = mw_vimeo_pattern($content);
    if ($pt)
        $content = preg_replace($pt, "mw_vimeo('\\1', '$q')", $content);

    return mw_video_wrapper($content);
}

function mw_soundcloud($src, $param)
{
    $src = str_replace('&#034;', '', $src);
    $src = str_replace('&#034', '', $src);
    $param = str_replace('&#034;', '', $param);
    $param = str_replace('&#034', '', $param);

    $s = sprintf('<iframe width="100%%" height="162" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=%s&%s"></iframe>', urlencode($src), urlencode($param));

    return $s;
}

function mw_video_wrapper($content)
{
    return preg_replace('/(<iframe[^>]+><\/iframe>)/i', "<div class='videoWrapper'>\\1</div>", $content);
}

function mw_youtube_pattern($content)
{
    $content = stripslashes($content);
    $pt = array();
    $pt[] = "/\[<a href=\"(https?:\/\/youtu\.be\/[^\"]+)\"[^>]*>[^<]+<\/a>\]/ie";
    $pt[] = "/\[<a href=\"(https?:\/\/www\.youtube\.com\/[^\"]+)\"[^>]*>[^<]+<\/a>\]/ie";
    $pt[] = "/\[(https?:\/\/youtu\.be\/[^\]]+)\]/ie";
    $pt[] = "/\[(https?:\/\/www\.youtube\.com\/[^\]]+)\]/ie";

    foreach ($pt as $p) {
        if (preg_match($p, $content)) {
            return $p;
        }
    }

    return false;
}

function mw_vimeo_pattern($content)
{
    $pt = array();
    $pt[] = "/\[(https?:\/\/vimeo\.com\/[^]]+)\]/ie"; 
    $pt[] = "/\[<a href=\"(https?:\/\/vimeo\.com\/[^\"]+)\"[^>]+>[^<]+<\/a>\]/ie"; 

    foreach ($pt as $p) {
        if (preg_match($p, $content)) {
            return $p;
        }
    }

    return false;
}

function mw_make_lightbox()
{
    global $g4, $mw_basic, $view, $lightbox_path, $file_start;

    $cf_img_1_noview = $mw_basic['cf_img_1_noview'];

    if (!$mw_basic['cf_lightbox_x']) $mw_basic['cf_lightbox_x'] = 100;
    if (!$mw_basic['cf_lightbox_y']) $mw_basic['cf_lightbox_y'] = 100;
    if (!$file_start) $file_start = 0;

    for ($i=$file_start; $i<=$view['file']['count']; $i++) {
        if (!$view[file][$i][view]) continue;
        if ($cf_img_1_noview) {
            $cf_img_1_noview = false;
            continue;
        }

        $lightbox_file = "{$lightbox_path}/{$view['wr_id']}-{$i}";

        if (!is_mw_file($lightbox_file)) {
            $source_file = "{$view['file'][$i]['path']}/{$view['file'][$i]['file']}";
            mw_make_thumbnail($mw_basic['cf_lightbox_x'], $mw_basic['cf_lightbox_y'], $source_file, $lightbox_file, 0);
        }
    }
}

function mw_special_tag($con)
{
    $con = preg_replace("/\&lt;([\/]?)(script|iframe)(.*)&gt;/iUs", "<$1$2$3>", $con);
    $con = str_replace("&#111;&#110;", "on", $con);
    $con = str_replace("&#115;&#99;", "sc", $con);
    return $con;
}

// Dae-Seok Kim님 제안
function mw_vimeo($url, $q=0)
{ 
    global $board, $mw_basic; 

    if (!$mw_basic['cf_youtube_size']) 
        $mw_basic['cf_youtube_size'] = 360; 

    if ($q) {
        $mw_basic['cf_youtube_size'] = $q;
        $mw_basic['cf_player_size'] = null;
    }

    switch ($mw_basic['cf_youtube_size']) { 
        case "144": $width = 320; $height = 180; break;
        case "240": $width = 560; $height = 315; break; 
        case "360": $width = 640; $height = 394; break; 
        case "480": $width = 854; $height = 516; break; 
        case "720": $width = 1280; $height = 759; break; 
        case "1080": $width = 1920; $height = 1123; break; 
        default: $width =640; $height = 394; break; 
    } 

    if ($mw_basic['cf_player_size']) {
        $size = explode("x", $mw_basic['cf_player_size']);
        $width = $size[0];
        $height = $size[1];
    }

    if ($width > $board['bo_image_width']) { 
        $height = floor($board['bo_image_width']/$width*$height); 
        $width = $board['bo_image_width']; 
    } 

    if (preg_match("/^http[s]{0,1}:\/\/vimeo\.com\/(.*)$/i", $url, $mat)) {
        $v = $mat[1];
    }

    $vimeo = "<iframe src='//player.vimeo.com/video/{$v}' width='{$width}' height='{$height}' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>"; 

    return $vimeo; 
} 

function mw_singo_admin($admin_id)
{
    global $g4, $mw_basic, $is_admin;

    if ($is_admin) return true;
    if (!$admin_id) return false;

    $singo_id = array();

    $tmp = explode(",", $mw_basic['cf_singo_id']);
    foreach ((array)$tmp as $mb_id) {
        $mb_id = trim($mb_id);
        if (!$mb_id) continue;
        $singo_id[] = $mb_id;
    }

    if (!in_array($admin_id, $singo_id)) return false;

    return true;
}


function mw_thumb_jpg($file)
{
    global $mw_basic;

    $jpg = $file.".jpg";

    if (!$mw_basic['cf_thumb_jpg']) return $file;
    if (preg_match("/\.jpg$/i", $file)) return $file;
    if (!is_mw_file($file)) return $jpg;

    rename($file, $jpg);
    return $jpg;
}

function mw_table_desc($bo_table)
{
    global $g4;

    $write_table = $g4['write_prefix'] . $bo_table;

    $f = array();
    $f[] = "wr_id";
    $f[] = "wr_num";
    $f[] = "wr_parent";
    $f[] = "wr_option";
    $f[] = "wr_reply";
    $f[] = "wr_is_comment";
    $f[] = "wr_comment";
    $f[] = "wr_comment_reply";
    $f[] = "ca_name";
    $f[] = "wr_option ";
    $f[] = "wr_subject";
    $f[] = "wr_content";
    $f[] = "wr_link1";
    $f[] = "wr_link2";
    $f[] = "wr_link1_hit";
    $f[] = "wr_link2_hit";
    $f[] = "wr_trackback";
    $f[] = "wr_hit";
    $f[] = "wr_good";
    $f[] = "wr_nogood";
    $f[] = "mb_id";
    $f[] = "wr_password";
    $f[] = "wr_name";
    $f[] = "wr_email";
    $f[] = "wr_homepage";
    $f[] = "wr_datetime";
    $f[] = "wr_last";
    $f[] = "wr_ip";
    $f[] = "wr_1";
    $f[] = "wr_2";
    $f[] = "wr_3";
    $f[] = "wr_4";
    $f[] = "wr_5";
    $f[] = "wr_6";
    $f[] = "wr_7";
    $f[] = "wr_8";
    $f[] = "wr_9";
    $f[] = "wr_10";
    $f[] = "wr_umz";

    $list = array();

    $sql = " desc {$write_table} ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        if (!in_array($row['Field'], $f)) {
            $list[] = $row['Field'];
        }
    }

    return $list;
}

function mw_kakao_str($content, $len=50)
{
    $content = strip_tags($content);
    $content = addslashes($content);
    $content = str_replace("\n", " ", $content);
    $content = preg_replace("/&#?[a-z0-9]+;/i", "", $content);
    $content = preg_replace("/\s+/", " ", $content);
    $content = trim($content);
    $content = cut_str($content, $len);

    return $content;
}

function mw_editor_image_copy($content)
{
    global $g4;

    preg_match_all("/<img src=\"http:\/\/.*\/(data\/cheditor[0-9]\/[^\"]+)\"[^>]+>/iUs", $content, $match1);
    preg_match_all("/<img src=\"http:\/\/.*\/(data\/geditor\/[^\"]+)\"[^>]+>/iUs", $content, $match2);

    $matchs = array();
    $matchs[0] = array_merge($match1[0], $match2[0]);
    $matchs[1] = array_merge($match1[1], $match2[1]);

    for ($i=0, $m=count($matchs[0]); $i<$m; ++$i)
    {
        $source = $matchs[1][$i];

        if (!$source) continue;

        preg_match("/^(.*)\.(jpe?g|png|gif)$/i", $source, $match);
        if ($match[2]) {
            $file = $match[1];
            $ext = $match[2];
        }
        else {
            continue;
        }

        $k = 1;
        while (1) {
            $copy = "{$file}-{$k}.{$ext}";

            if (!is_mw_file("{$g4['path']}/{$copy}"))
                break;
            else
                ++$k;

        }

        $res = @copy("{$g4['path']}/{$source}", "{$g4['path']}/{$copy}");
        if ($res)
            $content = str_replace($source, $copy, $content);
    }

    return $content;
}

function mw_write_icon($row)
{
    global $board_skin_path, $board_skin_path, $is_singo, $quiz_path;
    global $quiz_id, $bomb_id, $vote_id;

    $write_icon = '';
    $style =  "align=\"absmiddle\" style=\"border-bottom:2px solid #fff;\" class=\"write_icon\"";

    if ($row['wr_kcb_use'])
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_kcb.png\" {$style}>";
    elseif (in_array($row['wr_id'], $bomb_id))
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_bomb.gif\" {$style}>";
    elseif ($row['wr_key_password'])
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_key.png\" {$style} width=\"13\" height=\"12\">";
    elseif (strstr($row['wr_option'], 'secret'))
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_secret.gif\" {$style} width=\"13\" height=\"12\">";
    elseif (in_array($row['wr_id'], $quiz_id))
        $write_icon = "<img src=\"{$quiz_path}/img/icon_quiz.png\" {$style}>";
    elseif (in_array($row['wr_id'], $vote_id))
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_vote.png\" {$style}>";
    elseif (strstr($row['wr_link1'], "youtu"))
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_youtube.png\" {$style} width=\"13\" height=\"12\">";
    elseif ($row['wr_is_mobile'])
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_mobile.png\" {$style} width=\"13\" height=\"12\">";
    else {
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_subject.gif\" {$style} width=\"13\" height=\"12\">";
        if ($row['icon_new'])
            $write_icon = "<img src=\"{$board_skin_path}/img/icon_subject.gif\" {$style} width=\"13\" height=\"12\">";
    }

    // ---- font awesome

    $css = '';
    if ($row['icon_new']) {
        //$css = "color:#000";
    }

/*
    if ($row['wr_kcb_use'])
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_kcb.png\" {$style}>";
    elseif (in_array($row['wr_id'], $bomb_id))
        $write_icon = "<i class='fa fa-bomb fa-spin'></i>&nbsp;";
    elseif ($row['wr_key_password'])
        $write_icon = "<i class='fa fa-key'></i>&nbsp;";
    elseif (in_array($row['wr_id'], $quiz_id))
        $write_icon = "<i class='fa fa-question'></i>&nbsp;";
    elseif (in_array($row['wr_id'], $vote_id))
        $write_icon = "<i class='fa fa-bar-chart'></i>&nbsp;";
    elseif (strstr($row['wr_link1'], "youtu"))
        $write_icon = "<i class='fa fa-youtube'></i>&nbsp;";
    elseif ($row['wr_is_mobile'])
        $write_icon = "<i class='fa fa-mobile' style='font-size:15px; {$css};'></i>&nbsp;";
    else {
        $write_icon = "<i class='fa fa-file-text-o' style='font-size:10px; margin-top:5px; {$css};'></i>&nbsp;";
        if ($row['icon_new'])
            $write_icon = "<i class='fa fa-text-o' style='font-size:10px; margin-top:5px; {$css};'></i>&nbsp;";
    }
*/


    // ---- 

    if ($is_singo)
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_red.png\" {$style}>";

    if ($row['wr_view_block'])
        $write_icon = "<img src=\"{$board_skin_path}/img/icon_view_block.png\" {$style}>";

    return $write_icon;
}

function mw_list_link($row)
{
    global $g4; 
    global $mw; 
    global $board_skin_path; 
    global $board; 
    global $mw_basic; 
    global $member; 
    global $is_admin; 
    global $is_member; 
    global $write;
    global $bo_table;
    global $qstr;
    global $page;

    if (!strstr($qstr, "page=") && $page > 1)
        $qstr .= "&page=".$page;

    $sign = '&';
    //if ($mw['config']['cf_seo_url']) {
        $row['href'] = mw_seo_url($bo_table, $row['wr_id'], $qstr);
        if (!$qstr)
            $sign = '?';
    //}

    // 링크로그
    for ($j=1; $j<=$g4['link_count']; $j++)
    {
        //if ($mw_basic[cf_link_log])  {
            $row['link'][$j] = set_http(get_text($row["wr_link{$j}"]));
            $row['link_href'][$j] = "$board_skin_path/link.php?bo_table=$board[bo_table]&wr_id={$row[wr_id]}&no=$j" . $qstr;
            $row['link_hit'][$j] = (int)$row["wr_link{$j}_hit"];
        //}

        $row['link_target'][$j] = $row["wr_link{$j}_target"];
        if (!$row['link_target'][$j])
            $row['link_target'][$j] = '_blank';
    }

    $link_href = $row['link_href'][1];
    $link_point = 0;

    if ($mw_basic['cf_link_point']
        && $row['link_href'][1]
        && ($row['wr_link_write'] or $mw_basic['cf_link_board'])
        && !$is_admin
        && !($row['mb_id'] && $row['mb_id'] == $member['mb_id']))
    {
        $link_point = $mw_basic['cf_link_point'];

        if (!$is_member) {
            $link_href = "javascript:alert('글을 읽으시면 {$mw_basic['cf_link_point']} 포인트 차감됩니다.";
            $link_href.= "\\n\\n로그인해주세요.')";
        }
        else {
            $sql = " select * from {$g4['point_table']} ";
            $sql.= "  where mb_id = '{$member['mb_id']}' ";
            $sql.= "    and po_rel_table = '{$bo_table}' ";
            $sql.= "    and po_rel_id = '{$row['wr_id']}' ";
            $sql.= "    and po_rel_action = '링크'";
            $tmp = sql_fetch($sql);
            if (!$tmp) {
                if (!$is_admin && $mw_basic['cf_link_point'] + $member['mb_point'] < 0) {
                    $href = "javascript:alert('포인트가 부족합니다.\\n\\n";
                    $href.= "- 읽기 포인트: {$mw_basic['cf_link_point']}p\\n- 현재 포인트: {$member['mb_point']}p')";
                    $link_href = $href;
                }
                else {
                    $href = "#;\" onclick= \"if (confirm('글을 읽으시면 {$mw_basic['cf_link_point']} 포인트 차감됩니다.";
                    $href.= "\\n\\n현재 포인트: {$member['mb_point']}p\\n\\n')) {";
                    if ($row['link_target'][1] == '_blank')
                        $href.= "window.open('{$row['link_href'][1]}');";
                    else 
                        $href.= "location.href = '{$row['link_href'][1]}';";
                    $href.= "  }\"";
                    $link_href = $href;
                }
            }
        }
    }

    // 링크게시판
    if ($mw_basic['cf_link_board'] && $row['link_href'][1]) {
        //if (!$is_admin && $member['mb_id'] && $row['mb_id'] != $member['mb_id'])
        if (!$row['link'][1] || $is_admin || ($row['mb_id'] && $row['mb_id'] == $member['mb_id']))
            ;
        else if ($row['icon_secret'])
            ;
        else if ($member['mb_level'] >= $mw_basic['cf_link_board']) {
            if ($link_point)
                $row['href'] = $link_href;
            else if ($row['link_target'][1] == '_blank')
                $row['href'] = "javascript:void(window.open('{$row['link_href'][1]}'))";    
            else
                $row['href'] = $row['link_href'][1];
        }
        else
            $row['href'] = "javascript:void(alert('권한이 없습니다.'))";
        $row['wr_hit'] = $row['link_hit'][1];
    }

    // 게시물별 링크이동
    else if ($row['wr_link_write'] && $row['link_href'][1]) {
        if (!$row['link'][1] || $is_admin || ($row['mb_id'] && $row['mb_id'] == $member['mb_id']))
            ;
        else if ($row['icon_secret'])
            ;
        else if ($mw_basic['cf_read_level'] && $row['wr_read_level']) {
            if ($row['wr_read_level'] <= $member['mb_level']) {
                if ($link_point)
                    $row['href'] = $link_href;
                else if ($row['link_target'][1] == '_blank')
                    $row['href'] = "javascript:void(window.open('{$row['link_href'][1]}'))";    
                else
                    $row['href'] = $row['link_href'][1];
            }
            else
                $row['href'] = "javascript:void(alert('권한이 없습니다.'))";
        }
        else if ($member['mb_level'] >= $board['bo_read_level']) {
            if ($link_point)
                $row['href'] = $link_href;
            else if ($row['link_target'][1] == '_blank')
                $row['href'] = "javascript:void(window.open('{$row['link_href'][1]}'))";    
            else
                $row['href'] = $row['link_href'][1];
        }
        else
            $row['href'] = "javascript:void(alert('권한이 없습니다.'))";
        $row['wr_hit'] = $row['link_hit'][1];
    }

    // 글읽기 포인트 결제 안내
    else if ($board['bo_read_point'] < 0 && $row['mb_id'] != $member['mb_id'] && !$is_admin && $mw_basic['cf_read_point_message']) {
        if (!$is_member) {
            $href = "javascript:alert('글을 읽으시면 {$board['bo_read_point']} 포인트 차감됩니다.";
            $href.= "\\n\\n로그인해주세요.')";
            $row['href'] = $href;
        }
        else {
            $sql = " select * from {$g4['point_table']} ";
            $sql.= "  where mb_id = '{$member['mb_id']}' ";
            $sql.= "    and po_rel_table = '{$bo_table}' ";
            $sql.= "    and po_rel_id = '{$row['wr_id']}' ";
            $sql.= "    and po_rel_action = '읽기'";
            $tmp = sql_fetch($sql);
            if (!$tmp) {
                if (!$is_admin && $board['bo_read_point'] && $board['bo_read_point'] + $member['mb_point'] < 0) {
                    $href = "javascript:alert('포인트가 부족합니다.\\n\\n";
                    $href.= "- 읽기 포인트: {$board['bo_read_point']}p\\n- 현재 포인트: {$member['mb_point']}p')";
                    $row['href'] = $href;
                }
                else {
                    $href = "javascript:if (confirm('글을 읽으시면 {$board['bo_read_point']} 포인트 차감됩니다.";
                    $href.= "\\n\\n현재 포인트: {$member['mb_point']}p\\n\\n')) ";
                    $href.= "location.href = '{$row['href']}{$sign}point=1'";
                    $row['href'] = $href;
                }
            }
        }
    } 
    else if ($mw_basic['cf_read_level'] && $row['wr_read_level'] && $row['wr_read_level'] > $member['mb_level']) {
        $row['href'] = "javascript:void(alert('권한이 없습니다.'))";
    }

    return $row;
}

function mw_board_cache_read($cache_file, $cache_time) // 분단위
{
    global $g4;

    if (!is_mw_file($cache_file)) return false;
    if (!$cache_time) return false;

    $diff_time = $g4[server_time] - filemtime($cache_file);

    $cache_time *= 60; 

    if ($diff_time > $cache_time) return false;

    ob_start();
    readfile($cache_file);
    $content = ob_get_contents();
    ob_end_clean();

    $content = base64_decode($content);
    $content = unserialize($content);

    return $content;
}

function mw_board_cache_write($cache_file, $content)
{
    global $g4;

    $content = serialize($content);
    $content = base64_encode($content);

    //mw_mkdir($cache_file);

    $f = fopen($cache_file, "w");
    fwrite($f, $content);
    fclose($f);
}

function mw_get_date($datetime, $val)
{
    if (!$val)
        return $datetime;

    $init = date(str_replace("w", get_yoil($datetime), "Y-m-d (w) H:i:s"), strtotime($datetime));

    if ($val == "sns") {
        $date = "<span style='font-size:11px;' title='{$init}'>".mw_basic_sns_date($datetime)."</span>";
    }
    else {
        $date = date(str_replace("w", get_yoil($datetime), $val), strtotime($datetime));
        $date = "<span title='{$init}'>{$date}</span>";
    }
    return $date;
}

function mw_ie()
{
    $agent = $_SERVER['HTTP_USER_AGENT'];

    if (preg_match("/msie/i", $agent)) {
        return true;
    }
    else if (preg_match("/rv:1/i", $agent)) {
        return true;
    }
    else {
        return false;
    }
}

function mw_category_option($bo_table='')
{
    global $mw, $g4, $board, $member;

    $arr = array_filter(explode("|", $board['bo_category_list']), "trim");
    $str = "";
    for ($i=0; $i<count($arr); $i++) {
        $sql = " select * from {$mw['category_table']} where bo_table = '{$bo_table}' and ca_name = '{$arr[$i]}'";
        $row = sql_fetch($sql);
        if ($row['ca_level_write'] && $row['ca_level_write'] > $member['mb_level']) continue;
        if (trim($arr[$i])) {
            $str .= "<option value='{$arr[$i]}'>{$arr[$i]}</option>\n";
        }
    }

    return $str;
}

function mw_category_info($ca_name)
{
    global $bo_table, $g4, $mw;

    if (!$bo_table) return;
    if (!$ca_name) return;

    $sql = " select * from {$mw['category_table']} where bo_table = '{$bo_table}' and ca_name = '{$ca_name}' ";
    $row = sql_fetch($sql);

    return $row;
}

function mw_save_remote_image($url, $save_path)
{
    $url  = parse_url($url);

    $host = $url['host'];
    $path = $url['path'];
    $port = 80;

    if ($url['query']) $path .= '?'.$url['query'];
    if ($url['port']) $port = $url['port'];

    $fp = @fsockopen ($host, $port, $errno, $errstr, 10);
    if (!$fp) return false;
    else {
        fputs($fp, "GET $path HTTP/1.0\r\n");
        fputs($fp, "User-Agent: mw.basic\r\n");
        fputs($fp, "Host: $host:80\r\n");
        fputs($fp, "Accept: image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, */*\r\n");
        fputs($fp, "\r\n");

        while (trim($buffer = fgets($fp,1024)) != "") { $header .= $buffer; }
        while (!feof($fp)) { $buffer .= fgets($fp,1024); }
    }
    fclose($fp);
    if ($buffer) {
        $fw = fopen ($save_path, "wb");
        fwrite($fw, trim($buffer));
        chmod ($save_path, 0777);
        fclose($fw);
    }
    return is_mw_file($save_path);
}

// 게시물별 썸네일 생성
function mw_make_thumbnail_row ($bo_table, $wr_id, $wr_content, $remote=false, $time='')
{
    global $g4;
    global $mw_basic;
    global $file_path;
    global $thumb_path;
    global $thumb2_path;
    global $thumb3_path;
    global $thumb4_path;
    global $thumb5_path;

    global $thumb_file;
    global $thumb2_file;
    global $thumb3_file;
    global $thumb4_file;
    global $thumb5_file;

    global $is_admin;

    global $write;
    global $w;

    $is_thumb = false;

    if (!$thumb_file) {
        $thumb_file = mw_thumb_jpg($thumb_path.'/'.$wr_id);
        $thumb2_file = mw_thumb_jpg($thumb2_path.'/'.$wr_id);
        $thumb3_file = mw_thumb_jpg($thumb3_path.'/'.$wr_id);
        $thumb4_file = mw_thumb_jpg($thumb4_path.'/'.$wr_id);
        $thumb5_file = mw_thumb_jpg($thumb5_path.'/'.$wr_id);
    }

    if (is_mw_file($thumb_file)) unlink($thumb_file);
    if (is_mw_file($thumb2_file)) unlink($thumb2_file);
    if (is_mw_file($thumb3_file)) unlink($thumb3_file);
    if (is_mw_file($thumb4_file)) unlink($thumb4_file);
    if (is_mw_file($thumb5_file)) unlink($thumb5_file);

    $file = mw_get_first_file($bo_table, $wr_id, true);

    // 첨부파일 썸네일 생성
    if (!empty($file))
    {
        thumb_log($thumbnail_file, "file-{$bo_table}-{$wr_id}");
        mw_make_thumbnail_all($file_path.'/'.$file['bf_file']);

        $is_thumb = true;
    }
    // 컨텐츠내 이미지 썸네일 생성
    else
    {
        preg_match_all("/<img.*src=\\\"(.*)\\\"/iUs", stripslashes($wr_content), $matchs);
        preg_match_all("/<img.*src=\\\"(.*)\\\"/iUs", stripslashes($write['wr_content']), $matchs2);

        for ($i=0, $m=count($matchs[1]); $i<$m; ++$i)
        {
            $mat = $matchs[1][$i];
            $mat2 = $matchs2[1][$i];

            // 이모티콘 썸네일 생성 제외
            if (strstr($mat, "mw.basic.comment.image")) $mat = '';
            if (strstr($mat, "mw.emoticon")) $mat = '';
            if (preg_match("/cheditor[0-9]\/icon/i", $mat)) $mat = '';

            if (strstr($mat2, "mw.basic.comment.image")) $mat2 = '';
            if (strstr($mat2, "mw.emoticon")) $mat2 = '';
            if (preg_match("/cheditor[0-9]\/icon/i", $mat2)) $mat2 = '';

            if ($mat)
            {
                //$mat = str_replace($g4[url], "..", $mat);
                //$dat = preg_replace("/(http:\/\/.*)\/data\//i", "../data/", $mat);
                $dat = preg_replace("/(http:\/\/.*)\/data\//i", $g4['path']."/data/", $mat);
                if (!is_mw_file($dat) && (substr($mat, 0, 1) == '/' or substr($mat, 0, 1) == '.'))
                    $dat = str_replace("//", "/", $_SERVER['DOCUMENT_ROOT'].$mat);

                $dat2 = preg_replace("/(http:\/\/.*)\/data\//i", $g4['path']."/data/", $mat2);
                if (!is_mw_file($dat2) && (substr($mat2, 0, 1) == '/' or substr($mat2, 0, 1) == '.'))
                    $dat2 = str_replace("//", "/", $_SERVER['DOCUMENT_ROOT'].$mat2);

                if ($w == 'u' and $mat == $mat2) {
                    $remote = false;
                }

                // 서버내 이미지 썸네일 생성
                if (is_mw_file($dat))
                {
                    thumb_log($thumbnail_file, 'editor');
                    mw_make_thumbnail_all($dat);

                    $is_thumb = true;
                    break; // for (i)
                }

                // 외부 이미지 썸네일 생성
                else if ($remote)
                {
                    $ret = mw_save_remote_image($mat, $thumb_file);
                    if ($ret)
                    {
                        thumb_log($thumbnail_file, 'remote');
                        mw_make_thumbnail_all($thumb_file);

                        $is_thumb = true;
                        break; //for (i)
                    }
                }// if (is_mw_file)

            } // if (mat)
        } // for (i)

    }

    if (!$is_thumb) {
        if (is_mw_file($thumb_file)) unlink($thumb_file);
        if (is_mw_file($thumb2_file)) unlink($thumb2_file);
        if (is_mw_file($thumb3_file)) unlink($thumb3_file);
        if (is_mw_file($thumb4_file)) unlink($thumb4_file);
        if (is_mw_file($thumb5_file)) unlink($thumb5_file);
    }

    return $is_thumb;
}

function mw_make_thumbnail_all ($source_file)
{
    global $g4;
    global $mw_basic;
    global $thumb_file;
    global $thumb2_file;
    global $thumb3_file;
    global $thumb4_file;
    global $thumb5_file;
    global $is_admin;

    thumb_log($thumbnail_file, 'all-1');
    mw_make_thumbnail($mw_basic['cf_thumb_width'], $mw_basic['cf_thumb_height'], $source_file,
        $thumb_file, $mw_basic['cf_thumb_keep']);

    if ($mw_basic['cf_thumb2_width']) {
        thumb_log($thumbnail_file, 'all-2');
        @mw_make_thumbnail($mw_basic['cf_thumb2_width'], $mw_basic['cf_thumb2_height'], $source_file,
            $thumb2_file, $mw_basic['cf_thumb2_keep']);
    }

    if ($mw_basic['cf_thumb3_width']) {
        thumb_log($thumbnail_file, 'all-3');
        @mw_make_thumbnail($mw_basic['cf_thumb3_width'], $mw_basic['cf_thumb3_height'], $source_file,
            $thumb3_file, $mw_basic['cf_thumb3_keep']);
    }

    if ($mw_basic['cf_thumb4_width']) {
        thumb_log($thumbnail_file, 'all-4');
        @mw_make_thumbnail($mw_basic['cf_thumb4_width'], $mw_basic['cf_thumb4_height'], $source_file,
            $thumb4_file, $mw_basic['cf_thumb4_keep']);
    }

    if ($mw_basic['cf_thumb5_width']) {
        thumb_log($thumbnail_file, 'all-5');
        @mw_make_thumbnail($mw_basic['cf_thumb5_width'], $mw_basic['cf_thumb5_height'], $source_file,
            $thumb5_file, $mw_basic['cf_thumb5_keep']);
    }
}

if (!function_exists("mw_seo_url")) {
function mw_seo_url($bo_table, $wr_id=0, $qstr='', $mobile=1)
{
    global $g4;
    global $mw;
    global $mw_basic;
    global $mw_mobile;

    $url = $g4['url'];

    if (!$mobile && $mw_mobile['m_subdomain'])
        $url = preg_replace("/^http:\/\/m\./", "http://", $url);

    if (($mobile && mw_is_mobile_builder()) or ($mobile == 2))  {
        if ($mw_mobile['m_subdomain'] && !preg_match("/^http:\/\/m\./", $url)) {
            $url = mw_sub_domain_url("m", $url);
        }
        $seo_path = '/'.$mw['mobile_dir'];
    }
    else
        $seo_path = '/'.$g4['bbs'];

    if ($bo_table)
        $url .= $seo_path.'/board.php?bo_table='.$bo_table;

    if ($wr_id)
        $url .= "&wr_id=".$wr_id;

    if ($qstr)
        $url .= $qstr;

    return $url;
}}

if (!function_exists("mw_sub_domain_url")) {
function mw_sub_domain_url($sub, $url) {
    global $g4;
    if (strstr($url, "www.")) {
	$url = str_replace("www.", "$sub.", $url);
    } else {
	//echo "$url\n";
	$cookie_domain = str_replace(".", "\\.", $g4[cookie_domain]);
	$pattern = "/http:\/\/(.*)$cookie_domain(.*)/i";
	$change = "http://{$sub}{$g4[cookie_domain]}\$2";
	//echo "$pattern --- $change\n";
	$url = preg_replace($pattern, $change, $url);
	//echo $url;exit;
    }
    return $url;
}}

if (!function_exists("mw_bbs_path")) {
function mw_bbs_path($path)
{
    global $g4;
    global $mw;

    if (mw_is_mobile_builder()) {
        $path = preg_replace("/\.\//iUs", $g4['path'].'/'.$mw['mobile_dir'].'/', $path);
    }
    else {
        $path = preg_replace("/\.\//iUs", $g4['bbs_path'].'/', $path);
    }

    return $path;
}}

if (!function_exists("mw_seo_bbs_path")) {
function mw_seo_bbs_path($path)
{
    global $g4;
    global $bo_table;

    $wr_id = null;

    if (preg_match("/&wr_id=([0-9]+)&/iUs", $path, $mat)) {
        $wr_id = $mat[1];
        $path = str_replace('&wr_id='.$wr_id, '', $path);
    }

    $path = preg_replace("/&page=[01]?[&$]?/i", '', $path);

    if (mw_is_mobile_builder()) {
        $path = str_replace("../../{$mw['mobile_dir']}/board.php?bo_table=".$bo_table, mw_seo_url($bo_table, $wr_id).'?', $path);
    }
    else {
        $path = str_replace('../bbs/board.php?bo_table='.$bo_table, mw_seo_url($bo_table, $wr_id).'?', $path);
    }

    $path = preg_replace("/\?$/", "", $path);

    return $path;
}}

function mw_path_to_url($content)
{
    global $g4;

    $content = str_replace($g4['path'].'/data/', $g4['url'].'/data/', $content);
    $content = str_replace('../data/', $g4['url'].'/data/', $content);

    return $content;
}

if (!function_exists("mw_is_mobile_builder")) {
function mw_is_mobile_builder()
{
    global $mw;

    $is_mobile = false;

    if (strstr($_SERVER['SCRIPT_NAME'], "/".$mw['mobile_dir']))
        $is_mobile = true;
    else if (strstr($_SERVER['SCRIPT_NAME'], "/m/b/"))
        $is_mobile = true;

    return $is_mobile;
}}

function mw_is_rate($bo_table, $wr_id)
{
    global $g4;
    global $member;
    global $mw_basic;
    global $write;
    global $mw;
    global $is_admin;
    global $talent_market_path;
    global $social_commerce_path;

    // 사용안함
    if (!$mw_basic['cf_rate_level'])
        return "평가기능을 사용중이 아닙니다.";

    // 사용권한
    if (!$is_admin && $mw_basic['cf_rate_level'] > $member['mb_level'])
        return "평가할 권한이 없습니다.";

    if ($member['mb_id'])
        $mb_id = $member['mb_id'];
    else
        $mb_id = $_SERVER['REMOTE_ADDR'];

    // 글쓴이 평가 금지
    if (!$is_admin && !empty($write) && ($write['mb_id'] == $mb_id) || $write['wr_ip'] == $mb_id)
        return "글쓴이 본인은 평가할 수 없습니다.";

    $write_table = $g4['write_prefix'].$bo_table;

    // 이미 평가 했는지 검사
    $sql = " select * from {$write_table} ";
    $sql.= "  where wr_parent = '{$wr_id}' ";
    $sql.= "    and wr_rate > 0 ";
    $sql.= "    and (mb_id = '{$mb_id}' or wr_ip = '{$mb_id}') ";
    $row = sql_fetch($sql);

    if ($row)
        return "이미 평가하셨습니다.";

    // 다운로드 한사람만 평가가능
    if (!$is_admin && $mw_basic['cf_rate_down']) {
        $sql = " select * from {$mw['download_log_table']} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' and mb_id = '{$mb_id}' ";
        $row = sql_fetch($sql);

        if (!$row)
            return "다운로드 후 평가해주세요.";
    }

    // 구매자만 평가가능
    if (!$is_admin && $mw_basic['cf_rate_buy'])
    {
        // 컨텐츠샵 (다운로드 한 사람만 평가가능으로 대체)

        // 소셜커머스
        if ($mw_basic['cf_social_commerce']) {
            include_once($social_commerce_path."/_config.php");
            $sql = " select pr_id from {$mw_soc['product_table']} ";
            $sql.= "  where bo_table = '{$bo_table}' ";
            $sql.= "    and wr_id = '{$wr_id}' ";
            $product = sql_fetch($sql);

            $sql = " select or_id from {$mw_soc['order_table']} ";
            $sql.= "  where pr_id = '{$product['pr_id']}' ";
            $sql.= "    and mb_id = '{$mb_id}' ";
            $sql.= "    and (or_status = '1' or or_status = '2') ";
            $order = sql_fetch($sql);

            if (!$order)
                return "구매 하신분만 평가 가능합니다.";
        }

        // 재능마켓
        if ($mw_basic['cf_talent_market']) {
            include_once($talent_market_path."/_config.php");
            $sql = " select pr_id from {$mw_talent_market['product_table']} ";
            $sql.= "  where bo_table = '{$bo_table}' ";
            $sql.= "    and wr_id = '{$wr_id}' ";
            $product = sql_fetch($sql);

            $sql = " select or_id from {$mw_talent_market['order_table']} ";
            $sql.= "  where pr_id = '{$product['pr_id']}' ";
            $sql.= "    and buyer_id = '{$mb_id}' ";
            $sql.= "    and or_status = '4' ";
            $order = sql_fetch($sql);

            if (!$order)
                return "구매결정 하신분만 평가 가능합니다.";
        }
    }

    return '';
}

function mw_rate($bo_table, $wr_id)
{
    global $mw_basic;
    global $g4;
    global $is_admin;

    $write_table = $g4['write_prefix'].$bo_table;
    $sql = " select count(*) as cnt, sum(wr_rate) as rate ";
    $sql.= "   from {$write_table} ";
    $sql.= "  where wr_parent = '{$wr_id}' ";
    $sql.= "    and wr_is_comment = '1' ";
    $sql.= "    and wr_rate > 0 ";
    $row = sql_fetch($sql);

    $wr_rate = @round($row['rate'] / $row['cnt'], 2);
    $row['rate'] = $wr_rate;

    sql_query(" update {$write_table} set wr_rate = '{$wr_rate}' where wr_id = '{$wr_id}' ");

    return $row;
}

function mw_rate_count($bo_table, $wr_id)
{
    global $mw_basic;
    global $g4;

    $write_table = $g4['write_prefix'].$bo_table;
    $sql = " select count(*) as cnt ";
    $sql.= "   from {$write_table} ";
    $sql.= "  where wr_parent = '{$wr_id}' ";
    $sql.= "    and wr_is_comment = '1' ";
    $sql.= "    and wr_rate > 0 ";
    $row = sql_fetch($sql);

    return $row['cnt'];
}

if (!function_exists("is_g5")) {
function is_g5()
{
    if (defined('G5_PATH'))
        return true;

    return false;
}}

function is_ani($filename)
{
    $filecontents = @file_get_contents($filename);

    $str_loc=0;
    $count=0;
    while ($count < 2) # There is no point in continuing after we find a 2nd frame
    {
        $where1=strpos($filecontents,"\x00\x21\xF9\x04",$str_loc);
        if ($where1 === FALSE) {
            break;
        }
        else {
            $str_loc=$where1+1;
            $where2=strpos($filecontents,"\x00\x2C",$str_loc);
            if ($where2 === FALSE) {
                break;
            }
            else {
                if ($where1+8 == $where2) {
                    $count++;
                }
                $str_loc=$where2+1;
            }
        }
    }

    if ($count > 1)
        return(true);
    else
        return(false);
}

function mw_time_log($mw_run_time, $log)
{
    return;
    global $g4;
    global $bo_table;
    global $wr_id;

    $file = $g4['path']."/data/slow";

    if (!$mw_run_time)
        $mw_run_time = get_microtime();

    $run_time = number_format(get_microtime() - $mw_run_time, 5);

    if ($run_time < 0.2) return;

    $content = date("ymd H:i:s")." [{$bo_table}-{$wr_id}] [{$run_time}] {$log}\n";

    $fp = fopen($file, "a+");
    fwrite($fp, $content);
    fclose($fp);

    $mw_run_time = get_microtime();

    return $mw_run_time;
}

function thumb_log($thumb_file, $act)
{
    return;
    global $member;
    global $bo_table;
    global $wr_id;
    global $g4;
    global $w;

    if (strstr($_SERVER['SCRIPT_NAME'], "mw.adm.thumb.remake.php")) return;

    include_once($g4['path']."/lib/etc.lib.php");

    $file = $g4['path']."/data/thumb_log";
    $url = mw_seo_url($bo_table, $wr_id);

    $log = date("Y-m-d H:i:s")." {$act} [{$member['mb_id']}] {$_SERVER['REMOTE_ADDR']} {$_SERVER['SCRIPT_NAME']}?{$_SERVER["QUERY_STRING"]} [{$w}] {$thumb_file} {$url}\n";

    write_log($file, $log);
}

function is_mw_file($path)
{
    $path = str_replace(G5_URL, "../", $path);
    if (@is_file($path))
        return true;

    return false;
}

function is_notice($wr_id)
{
    global $g4;
    global $board;
    global $notice_div;

    $bo_notice = $board['bo_notice'].$notice_div;

    if (strstr($bo_notice, $wr_id.$notice_div)) {
        return true;
    }

    return false;
}

function is_reaction_test()
{
    return false;

    global $is_admin;

    if (!$is_admin)
        return false;

    //echo $_SERVER['HTTP_USER_AGENT'];

    if (strstr($_SERVER['HTTP_USER_AGENT'], 'Safari/600')) {
        return true;
    }

    return false;
}

function mw_list_icon($row)
{
    global $g4;
    global $board;

    $row['icon_update'] = "";
    if (!$row['icon_new'] && $row['wr_last'] != $row['wr_datetime'] && $row['wr_last'] >= date("Y-m-d H:i:s", $g4['server_time'] - ($board['bo_new'] * 3600))) {
        //$list[$i]['icon_update'] = "<img src='$board_skin_path/img/icon_update.gif' align='absmiddle'>";
        //$list[$i]['icon_new'] = '';
        $list[$i]['icon_update'] = "";//"&nbsp;&nbsp;<i class='fa fa-refresh fa-spin' style='font-size:9px;'></i>";
    }

    if ($row['icon_new'])
        $row['icon_new'] = " ";//"<span class='fa fa-plus-square'></span>";

    $row['is_secret'] = false;
    if ($row['icon_secret']) {
        $row['icon_secret'] = '';//"<span class='fa fa-lock'></span>";
        $row['is_secret'] = true;
    }

    if ($row['icon_link'])
        $row['icon_link'] = "";//"<span class='fa fa-external-link'></span>";

    if ($row['icon_file'])
        $row['icon_file'] = "";//"<span class='fa fa-save'></span>";

    if ($row['icon_hot'])
        $row['icon_hot'] = "";//"<span class='fa fa-fire'></span>";

    return $row;
}

function mw_sideview($name)
{
    global $mw_cash;
    global $is_admin;

    preg_match("/mb_id=([^\"]+)\"/iUs", $name, $match);
    $mb_id = $match[1];

    $name = preg_replace("/<a([^>]+)>(쪽지보내기)<\/a>/iUs", "<a $1 class='sideview_memo'>$2</a>", $name);
    $name = preg_replace("/<a([^>]+)>(메일보내기)<\/a>/iUs", "<a $1 class='sideview_mail'>$2</a>", $name);
    $name = preg_replace("/<a([^>]+)>(홈페이지)<\/a>/iUs", "<a $1 class='sideview_homepage'>$2</a>", $name);
    $name = preg_replace("/<a([^>]+)>(자기소개)<\/a>/iUs", "<a $1 class='sideview_profile'>$2</a>", $name);
    $name = preg_replace("/<a([^>]+)>(아이디로 검색)<\/a>/iUs", "<a $1 class='sideview_search'>$2</a>", $name);
    $name = preg_replace("/<a([^>]+)>(전체게시물)<\/a>/iUs", "<a $1 class='sideview_all'>$2</a>", $name);
    $name = preg_replace("/<a([^>]+)>(회원정보변경)<\/a>/iUs", "<a $1 class='sideview_member'>$2</a>", $name);
    $name = preg_replace("/<a([^>]+)>(포인트내역)<\/a>/iUs", "<a $1 class='sideview_point'>$2</a>", $name);
    if ($mw_cash['cf_cash_name'] and $is_admin)
        $name = str_replace("포인트내역</a>", "포인트내역</a><a href='".G5_ADMIN_URL."/mw.cash5/mw.cash.list.php?sfl=mb_id&stx={$mb_id}' target='_blank' class='sideview_cash'>캐쉬내역</a>", $name);
    return $name;
}

function mw_agent_mobile() {
    if (defined("G5_MOBILE_AGENT")) {
        if (preg_match("/(".G5_MOBILE_AGENT.")/i", $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
    }
    else if (preg_match("/(iphone|samsung|lgte|mobile|BlackBerry|android|windows ce|mot|SonyEricsson)/i", $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    }

    return false;
}

if (!function_exists("get_safe_filename")) { //g4 에 없음
function get_safe_filename($name)
{
    $pattern = '/["\'<>=#&!%\\\\(\)\*\+\?]/';
    $name = preg_replace($pattern, '', $name);

    return $name;
}}

function mb_id_check($mb_id)
{
    global $g4;
    if (preg_match("/^[a-z0-9-_@]+$/i", $mb_id)) {
        $row = sql_fetch("select mb_id from {$g4['member_table']} where mb_id = '{$mb_id}'");
        if ($row['mb_id'])
            return true;
        return false;
    }
    return false;
}

if (!function_exists("sql_insert_id")) {
function sql_insert_id($link=null)
{
    if (defined("G5_PATH")) {
        global $g5;

        if(!$link)
            $link = $g5['connect_db'];

        if(function_exists('mysqli_insert_id') && defined("G5_MYSQLI_USE") && G5_MYSQLI_USE)
            return mysqli_insert_id($link);
        else
            return mysql_insert_id($link);
    }
    else {
        global $connect_db;

        if(!$link)
            $link = $connect_db;

        return mysql_insert_id($link);
    }
}}

if (!function_exists("sql_num_rows")) {
function sql_num_rows($result)
{
    if (function_exists('mysqli_num_rows') && defined("G5_MYSQLI_USE") && G5_MYSQLI_USE)
        return mysqli_num_rows($result);
    else
        return mysql_num_rows($result);
}}

function mw_row_delete_point($board, $write)
{
    delete_point($write[mb_id], $board[bo_table], $write[wr_id], '코멘트');
    delete_point($write[mb_id], $board[bo_table], $write[wr_id], '댓글');
    delete_point($write[mb_id], $board[bo_table], $write[wr_id], '쓰기');

    delete_point($write[mb_id], $board[bo_table], $write[wr_id], '@qna');
    delete_point($write[mb_id], $board[bo_table], $write[wr_id], '@qna-hold');
    delete_point($write[mb_id], $board[bo_table], $write[wr_id], '@qna-choose');

    $sql = " select * from $g4[point_table] ";
    $sql.= "  where po_rel_table = '$board[bo_table]' ";
    $sql.= "    and po_rel_id = '$write[wr_id]' ";
    $sql.= "    and (po_rel_action like '%@good%' or po_rel_action like '%@nogood%') ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        delete_point($write[mb_id], $board[bo_table], $write[wr_id], $row[mb_id].'@good');
        delete_point($row[mb_id], $board[bo_table], $write[wr_id], $row[mb_id].'@good_re');
        delete_point($write[mb_id], $board[bo_table], $write[wr_id], $row[mb_id].'@nogood');
        delete_point($row[mb_id], $board[bo_table], $write[wr_id], $row[mb_id].'@nogood_re');
    }
}

