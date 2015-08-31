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

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// 파일 출력
if ($mw_basic['cf_social_commerce'] or $mw_basic['cf_talent_market']) {
    $file_start = 2;
}
else if ($mw_basic[cf_talent_market]) {
    $file_start = 1;
}
else {
    $file_start = 0;
}

$jwplayer = false;
$jwplayer_count = 0;

$movie_viewer = '';

ob_start();
$cf_img_1_noview = $mw_basic[cf_img_1_noview];
for ($i=$file_start; $i<=$view[file][count]; $i++) {
    if ($cf_img_1_noview && $view[file][$i][view]) {
        $cf_img_1_noview = false;
        $file_start = 1;
        continue;
    }

    if (strstr($mw_basic['cf_multimedia'], '/movie/')
        && preg_match("/\.($config[cf_movie_extension])$/i", $view[file][$i][file])) {

        $view[file][$i][movie] = true;

        if (strstr($view[content], "{동영상:")) continue;

        $tmp = '';
        $m = mw_jwplayer("{$g4[path]}/data/file/{$board[bo_table]}/{$view[file][$i][file]}");
        $m.= "<br/><br/>";
        if (trim($view[file][$i][content]))
            $m.= $view[file][$i][content] . "<br/><br/>";

        echo $m;

        $movie_viewer .= $m;
    } 
    else if ($view[file][$i][view])
    {
        if (is_g5()) {
            $view[file][$i][view] = preg_replace("/<a[^>]+>/i", "", $view[file][$i][view]);
            $view[file][$i][view] = preg_replace("/<\/a>/", "", $view[file][$i][view]);
        }

        // 원본 강제 리사이징
        if ($mw_basic[cf_resize_original]) {
            if ($view[file][$i][image_width] > $mw_basic[cf_resize_original] || $view[file][$i][image_height] > $mw_basic[cf_resize_original]) {
                $file = "$file_path/{$view[file][$i][file]}";
                thumb_log($file, 'resize-original');
                mw_make_thumbnail($mw_basic[cf_resize_original], $mw_basic[cf_resize_original], $file, $file, true);
                if ($mw_basic[cf_watermark_use] && is_mw_file($mw_basic[cf_watermark_path])) mw_watermark_file($file);
                $size = getimagesize($file);
                $view[file][$i][image_width] = $size[0];
                $view[file][$i][image_height] = $size[1];
                sql_query("update $g4[board_file_table] set bf_width = '$size[0]', bf_height = '$size[1]',
                    bf_filesize = '".filesize($file)."'
                    where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_file = '{$view[file][$i][file]}'");
            }
        }
        // 이미지 크기 조절
        if ($board[bo_image_width] < $view[file][$i][image_width]) {
            $img_width = $board[bo_image_width];
            $img_class = " class=\"content-image\" ";
        } else {
            $img_width = $view[file][$i][image_width];
            $img_class = "";
        }
        $view[file][$i][view] = str_replace("<img", "<img {$img_class} width=\"{$img_width}\"", $view[file][$i][view]);

        // 이미지 저장 방지
        if ($mw_basic[cf_image_save_close])
            $view[file][$i][view] = str_replace("<img", "<img oncontextmenu=\"return false\" style=\"-webkit-touch-callout:none\" ", $view[file][$i][view]);

        // 워터마크 이미지 출력
        if ($mw_basic[cf_watermark_use] && is_mw_file($mw_basic[cf_watermark_path])) {
            preg_match("/src='([^']+)'/iUs", $view[file][$i][view], $match);
            $watermark_file = mw_watermark_file($match[1]);
            $view[file][$i][view] = str_replace($match[1], $watermark_file, $view[file][$i][view]);
        }

	if ($mw_basic[cf_exif]) {
            $view[file][$i][view] = str_replace("onclick='image_window(this);'", "", $view[file][$i][view]);
            $view[file][$i][view] = str_replace("src", "name='exif' bf_no='{$i}' src", $view[file][$i][view]);
            $view[file][$i][view] = str_replace("alt=''", "alt='클릭하면 메타데이터를 보실 수 있습니다.'", $view[file][$i][view]);
            $view[file][$i][view] = str_replace("title=''", "title='클릭하면 메타데이터를 보실 수 있습니다.'", $view[file][$i][view]);
        }
        else if($mw_basic[cf_no_img_ext]) { // 이미지 확대 사용 안함
	    $view[file][$i][view] = str_replace("onclick='image_window(this);'", "", $view[file][$i][view]);
	    $view[file][$i][view] = str_replace("style='cursor:pointer;'", "", $view[file][$i][view]);
	}
        else {
            if (is_g5()) {
                $view[file][$i][view] = str_replace($img_class, 
                    $img_class." onclick='mw_image_window(this, {$view[file][$i][image_width]}, {$view[file][$i][image_height]});'", $view[file][$i][view]);
            }
	    $view[file][$i][view] = str_replace("onclick='image_window(this);'", 
		"onclick='mw_image_window(this, {$view[file][$i][image_width]}, {$view[file][$i][image_height]});'", $view[file][$i][view]);
	}
        echo $view[file][$i][view] . "<br/><br/>";
        if (trim($view[file][$i][content]))
            echo $view[file][$i][content] . "<br/><br/>";
    }
    else if ($mw_basic[cf_iframe_level] and $mw_basic[cf_iframe_level] <= $mb[mb_level]) {
        if (strstr($mw_basic['cf_multimedia'], '/image/') && preg_match("/\.($config[cf_image_extension])$/i", $view['file'][$i]['file'])) {
            echo mw_file_view($view['file'][$i]['path'].'/'.$view['file'][$i]['file'], $view)."<br><br>";
        }
        else if (strstr($mw_basic['cf_multimedia'], '/flash/') && preg_match("/\.($config[cf_flash_extension])$/i", $view['file'][$i]['file'])) {
            echo mw_file_view($view['file'][$i]['path'].'/'.$view['file'][$i]['file'], $view)."<br><br>";
        }
    }
}
$file_viewer = ob_get_contents();
ob_end_clean();

// 링크 첨부
$link_file_viewer = '';
for ($i=1; $i<=$g4['link_count']; $i++) {
    if (strstr($mw_basic['cf_multimedia'], '/youtube/') && preg_match("/youtu/i", $view['link'][$i])) {
        $link_file_viewer .= mw_youtube($view['link'][$i])."<br><br>";
        //$view['link'][$i] = '';
    }
    elseif (strstr($mw_basic['cf_multimedia'], '/youtube/') && preg_match("/vimeo/i", $view['link'][$i])) {
        $link_file_viewer .= mw_vimeo($view['link'][$i])."<br><br>";
        //$view['link'][$i] = '';
    }
    elseif (strstr($mw_basic['cf_multimedia'], '/link_movie/') && preg_match("/\.($config[cf_movie_extension])$/i", $view['link'][$i])) {
        $link_file_viewer .= mw_jwplayer($view['link'][$i])."<br><br>";
        $view['link'][$i] = '';
    }
    else if (strstr($mw_basic['cf_multimedia'], '/link_image/') && preg_match("/\.($config[cf_image_extension])[$\?]/i", $view['link'][$i])) {
        $link_file_viewer .= mw_file_view($view['link'][$i], $view)."<br><br>";
        $view['link'][$i] = '';
    }
    else if (strstr($mw_basic['cf_multimedia'], '/link_flash/') && preg_match("/\.($config[cf_flash_extension])[$\?]/i", $view['link'][$i])) {
        $link_file_viewer .= mw_file_view($view['link'][$i], $view)."<br><br>";
        $view['link'][$i] = '';
    }
    else if ($mw_basic['cf_youtube_only']) {
        $view['link'][$i] = '';
    }
}
$view[content] = $link_file_viewer . $view[content]; 

// 웹에디터 첨부 이미지 워터마크 처리
if ($mw_basic[cf_watermark_use] && is_mw_file($mw_basic[cf_watermark_path]))
    $view[content] = mw_create_editor_image_watermark($view[content]);

if (!$mw_basic[cf_zzal] && !strstr($view[content], "{이미지:") && !$write['wr_lightbox']) // 파일 출력  
    $view[content] = $file_viewer . $view[content];
else if (!strstr($view[content], "{동영상:"))
    $view[content] = $movie_viewer . $view[content]; 
else {
    $jwplayer = false;
    $jwplayer_count = 0;
}

if (!$mw_basic['cf_zzal'])
    $view[rich_content] = @preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/ie", "mw_view_image(\$view, '\\1', '\\2')", $view[content]);
else
    $view[rich_content] = @preg_replace("/{이미지\:([0-9]+)[:]?([^}]*)}/ie", "", $view[content]);

$view[rich_content] = preg_replace("/{동영상\:([0-9]+)[:]?([^}]*)}/ie", "mw_view_movie(\$view, '\\1', '\\2')", $view[rich_content]);

if ($mw_basic[cf_no_img_ext]) { // 이미지 확대 사용 안함
    $view[rich_content] = preg_replace("/name='target_resize_image\[\]' onclick='image_window\(this\)'/iUs", "", $view[rich_content]);

    if ($mw_basic[cf_image_save_close])
        $view[rich_content] = str_replace("<img", "<img oncontextmenu=\"return false\" style=\"-webkit-touch-callout:none\" ", $view[rich_content]);
}
else {
    // 웹에디터 이미지 클릭시 원본 사이즈 조정
    $data = $view[rich_content];
    $path = $size = null;
    preg_match_all("/<img\s+name='target_resize_image\[\]' onclick='image_window\(this\)'.*src=\"(.*)\"/iUs", $data, $matchs);
    for ($i=0; $i<count($matchs[1]); $i++) {
        $match = $matchs[1][$i];
        $match = preg_replace("/\?.*$/iUs", "", $match);
        $no_www = str_replace("www.", "", $g4[url]);
        $path = "";
        if (strstr($match, $g4[url])) {
            $path = str_replace($g4[url], $g4[path], $match);
        } elseif (strstr($match, $no_www)) {
            $path = str_replace($no_www, $g4[path], $match);
        } elseif (substr($match, 0, 1) == "/") {
            $path = $_SERVER[DOCUMENT_ROOT].$match;
        //} else { $path = $match;
        }
        $size = null;
        if ($path) {
            $size = @getimagesize($path);
        }
        else if ($path && ini_get('allow_url_fopen')) {
            $size = @getimagesize($path);
            /*$tmp = $g4['path']."/data/tmp-remote";
            mw_save_remote_image($match, $tmp);
            $size = getimagesize($tmp);
            unlink($tmp);*/
        }
        if ($size[0] && $size[1]) {
            $match = $matchs[1][$i];
            $match = str_replace("/", "\/", $match);
            $match = str_replace(".", "\.", $match);
            $match = str_replace("+", "\+", $match);
            $match = str_replace("?", "\?", $match);
            $pattern = "/(onclick=[\'\"]{0,1}image_window\(this\)[\'\"]{0,1}) (.*)(src=\"$match\")/iU";
            $replacement = "onclick='mw_image_window(this, $size[0], $size[1])' $2$3";

            // 이미지 저장 방지
            if ($mw_basic[cf_image_save_close])
                $replacement .= "oncontextmenu=\"return false\" style=\"-webkit-touch-callout:none\"";

            if ($size[0] > $board[bo_image_width])
                $replacement .= " class=\"content-image\" width=\"$board[bo_image_width]\"";
            $data = @preg_replace($pattern, $replacement, $data);
        }
    }
    $view[rich_content] = $data;
}

// 추천링크 방지
$view[rich_content] = preg_replace("/bbs\/good\.php\?/i", "#", $view[rich_content]);

$view[rich_content] = preg_replace_callback("/\[code\](.*)\[\/code\]/iUs", "_preg_callback", $view[rich_content]);

$view[rich_content] = mw_reg_str($view[rich_content]);

// 이미지 링크
$view[rich_content] = preg_replace("/\[\<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\"\s*[^\>]*\>.*\<\/a\>\]/iUs", "<img src='$1://$2.$3' id='target_resize_image[]' onclick='image_window(this);'>", $view[rich_content]);
$view[rich_content] = preg_replace("/\[(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\]/iUs", "<img src='$1://$2.$3' id='target_resize_image[]' onclick='image_window(this);'>", $view[rich_content]);
$view[rich_content] = preg_replace("/\[\<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp)\]\"\s*[^\>]*\>.*\]\<\/a\>/iUs", "<img src='$1://$2.$3' id='target_resize_image[]' onclick='image_window(this);'>", $view[rich_content]);

// 배추코드
$view[rich_content] = bc_code($view[rich_content], 1, 0);
if (strstr($write[wr_option], "html")) {
    $view[rich_content] = mw_tag_debug($view[rich_content]);
}
$view[rich_content] = mw_set_sync_tag($view[rich_content]);

if ($mw_basic[cf_iframe_level] && $mw_basic[cf_iframe_level] <= $mb[mb_level]) {
    $view[rich_content] = mw_special_tag($view[rich_content]);
}

$google_map_code = null;
$google_map_is_view = false;
if ($mw_basic[cf_google_map] && trim($write[wr_google_map])) {
    ob_start();
    ?>
    <script src="http://maps.google.com/maps/api/js?sensor=true&language=ko"></script>
    <script src="<?=$pc_skin_path?>/mw.js/mw.google.js"></script>
    <script>
    $(document).ready(function () {
        mw_google_map("google_map", "<?=addslashes($write[wr_google_map])?>");
    });
    </script>
    <div id="google_map" style="width:100%; height:300px; border:1px solid #ccc; margin:10px 0 10px 0;"></div>
    <?
    $google_map_code = ob_get_contents();
    ob_end_clean();

    if (strstr($view[rich_content], "{구글지도}")) {
        $view[rich_content] = preg_replace("/\{구글지도\}/", $google_map_code, $view[rich_content]);
        $google_map_is_view = true;
    }
}

if ($mw_basic[cf_contents_shop] == '2' and $write[wr_contents_price]) // 배추 컨텐츠샵 내용보기 결제
{
    $is_per = true;
    $is_per_msg = '예외오류';

    if (!$is_member) $is_per = false;

    $con = mw_is_buy_contents($member[mb_id], $bo_table, $wr_id);
    if (!$con and $is_per) $is_per = false;

    if (!$is_per) {

        $view[wr_contents_preview] = conv_content($view[wr_contents_preview], $html);
        ob_start();
        ?>
        <div class="contents_shop_view">
            <?=conv_content($view[wr_contents_preview], $html)?>
            <div style="margin:20px 0 0 0;"><input type="button" class="btn1" value="내용보기" onclick="buy_contents('<?=$bo_table?>','<?=$wr_id?>', 0)"/></div>
        </div>
        <script>
        function contents_shop_view() {
        }
        </script>
        <?
        $contents_shop_view = ob_get_contents();
        ob_end_clean();

        $view[wr_content] = $contents_shop_view;
        $view[content] = $view[wr_content];
        $view[rich_content] = $view[wr_content];
        $write[wr_content] = $view[wr_content];
        $write[content] = $view[wr_content];
        $view[file] = null;
    }
}

$view[rich_content] = mw_youtube_content($view[rich_content]);

$ob_exam = '';
$ob_exam_flag = false;
if ($mw_basic['cf_exam']) {
    if (is_mw_file("{$exam_path}/view.skin.php")) {
        ob_start();
        include("{$exam_path}/view.skin.php");
        $ob_exam = ob_get_clean();

        if (preg_match("/\[시험문제\]/i", $view[rich_content])) {
            $ob_exam_flag = true;
            $view[rich_content] = preg_replace("/\[시험문제\]/i", $ob_exam, $view[rich_content]);
        }
    }
}

$ob_marketdb = '';
$ob_marketdb_flag = false;
if ($mw_basic['cf_marketdb'] and $write['wr_marketdb']) { 
    if (is_mw_file("{$marketdb_path}/view.skin.php")) {
        ob_start();
        include("{$marketdb_path}/view.skin.php");
        $ob_marketdb = ob_get_clean();

        if (preg_match("/\[마케팅DB\]/i", $view[rich_content])) {
            $ob_marketdb_flag = true;
            $view[rich_content] = preg_replace("/\[마케팅DB\]/i", $ob_marketdb, $view[rich_content]);
        }
    }
}

//$view['rich_content'] = mw_path_to_url($view['rich_content']);

