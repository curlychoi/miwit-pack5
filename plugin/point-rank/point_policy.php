<?php
include_once("_common.php");
//include_once("$g4[path]/lib/mw.builder.lib.php");

$g4[title] = "포인트 정책";
include_once("_head.php");

$list = array();
for ($i=0; $row=$mw5_menu[$i]; ++$i) {
    $latest_table = mw_get_board($row['me_link']);
    if ($latest_table and !in_array($latest_table, $list))
        $list[] = $latest_table;

    for ($j=0; $row2=$mw5_menu[$i]['sub'][$j]; $j++) {
        $latest_table = mw_get_board($row2['me_link']);
        if ($latest_table and !in_array($latest_table, $list))
            $list[] = $latest_table;
    }
}

$latest = array();
$loop_max = count($list);
$real_max = $loop_max;
for ($i=0; $i<$loop_max; ++$i)
{
    $mw_skin_config = mw_skin_config($list[$i]);

    // 1:1 게시판 출력 안함
    if ($mw_skin_config['cf_attribute'] == '1:1') {
        $real_max--;
        continue;
    }

    $latest[$loop_index]['bo_table'] = $list[$i];
    $latest[$loop_index]['skin'] = 'theme/mw5';
    $latest[$loop_index]['count'] = 6;
    $latest[$loop_index]['length'] = 50;

    if ($mw_skin_config['cf_type'] == 'gall') {
        $latest[$loop_index]['skin'] = 'theme/mw5-gallery';
        $latest[$loop_index]['count'] = 2;
        $latest[$loop_index]['length'] = 10;

        if ($loop_index==$real_max and $loop_index%2!=0) {
            $latest[$loop_index]['count'] = 4;
        }
    }

    $loop_index++;
}


?>
<style>
.info-box { padding:10px; background-color:#efefef; margin-bottom:10px; border:1px solid #ddd; text-align:left; }
.info { height:20px; margin:0 0 0 10px; font-size:12px; }
.point-policy { background-color:#ddd; }
.point-policy td { background-color:#fff; }
.point-policy .thead { height:30px; text-align:center; font-weight:bold; background-color:#fafafa; }
.point-policy .tbody { height:25px; text-align:center; }
.point-policy .tbody.right { text-align:right; padding-right:10px; }
.point-policy .tbody.left { text-align:left; padding-left:10px; }
.point-policy .tbody a:hover { text-decoration:underline; }
</style>

<div class="info-box">
<div class='info'>·사이트에서는 회원님들의 각종 혜택을 위해 포인트 제도를 운영하고 있습니다.</div> 
<div class='info'>·각 게시판 활동 포인트는 아래 표를 참고해주세요.</div> 
<div class='info'>·포인트 정책은 수시로 변경될 수 있으며 이를 별도로 통보하지 않습니다.</div> 
<div class='info'>·포인트 획득을 위해 도배, 의미없는 글을 작성하는 등의 행위는 통보없이 "포인트 몰수"  될 수 있습니다. </div> 
</div>
<?
if ($config[cf_register_point]) echo "<div class='info'>· 회원가입 포인트 : <strong>".number_format($config[cf_register_point])."</strong> 점</div>";
if ($config[cf_login_point]) echo "<div class='info'>· 로그인 포인트 : <strong>".number_format($config[cf_login_point])."</strong> 점</div>";
?>
<table border=0 cellpadding=0 cellspacing=1 width=100% class="point-policy">
<colgroup width=""/>
<colgroup width="100"/>
<colgroup width="100"/>
<colgroup width="100"/>
<colgroup width="100"/>
<tr>
    <td class="thead"> 게시판 </td>
    <td class="thead"> 글읽기 </td>
    <td class="thead"> 글쓰기 </td>
    <td class="thead"> 코멘트 </td>
    <td class="thead"> 다운로드 </td>
</tr>
<?php
foreach ((array)$list as $row) { 
    $table = sql_fetch("select * from {$g5['board_table']} where bo_table = '{$row}' ");
    $url = mw_seo_url($row);
    echo "<tr>".PHP_EOL;
    echo "<td class='tbody left'><a href='{$url}'>{$table['bo_subject']}</a></td>".PHP_EOL;
    echo "<td class='tbody right'>{$table['bo_read_point']}점</td>".PHP_EOL;
    echo "<td class='tbody right'>{$table['bo_write_point']}점</td>".PHP_EOL;
    echo "<td class='tbody right'>{$table['bo_comment_point']}점</td>".PHP_EOL;
    echo "<td class='tbody right'>".number_format($table['bo_download_point'])."점</td>".PHP_EOL;
    echo "</tr>".PHP_EOL;
}
?>
</table>
<?
include_once("_tail.php");
