<?php
include_once("_common.php");
//include_once("$g4[path]/lib/mw.builder.lib.php");

$g4[title] = "포인트 정책";
include_once("_head.php");

$list = array();

$sql = " select *
           from {$g5['menu_table']}
          where me_use = '1'
            and length(me_code) = '2'
          order by me_order, me_id ";
$qry = sql_query($sql);
for ($i=0; $row=sql_fetch_array($qry); $i++) {
    $list[$i] = $row;
    $list[$i]['sub'] = array();

    preg_match("/bo_table=([0-9a-zA-Z-_]+)&/", $row['me_link'].'&', $match);
    if (!$match[1])
        preg_match("/\/b\/([0-9a-zA-Z-_]+)&/", $row['me_link'].'&', $match);

    $c = 0;
    $sql2 = " select *
               from {$g5['menu_table']}
              where me_use = '1'
                and length(me_code) = '4'
                and substring(me_code, 1, 2) = '{$row['me_code']}'
              order by me_order, me_id ";
    $qry2 = sql_query($sql2);
    for ($j=0; $row2=sql_fetch_array($qry2); $j++) {
        preg_match("/bo_table=([0-9a-zA-Z-_]+)&/", $row2['me_link'].'&', $match);
        if (!$match[1])
            preg_match("/\/b\/([0-9a-zA-Z-_]+)&/", $row2['me_link'].'&', $match);

        if ($match[1])
            $row2['bo_table'] = $match[1];
       else
            continue;

        $list[$i]['sub'][$j] = $row2;
    }
    if (!$j)
        $list[$i]['sub'][0] = $row;
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
<colgroup width="100"/>
<colgroup width=""/>
<colgroup width="80"/>
<colgroup width="80"/>
<colgroup width="80"/>
<colgroup width="80"/>
<tr>
    <td class="thead"> 서비스 </td>
    <td class="thead"> 메뉴 </td>
    <td class="thead"> 글읽기 </td>
    <td class="thead"> 글쓰기 </td>
    <td class="thead"> 코멘트 쓰기 </td>
    <td class="thead"> 다운로드 </td>
</tr>
<?php
for ($i=0; $row=$list[$i]; $i++) { 
    $rowspan = count($list[$i]['sub']);
    echo "<tr>".PHP_EOL;
    echo "<td class='tbody' rowspan='{$rowspan}'>{$row['me_name']}</td>".PHP_EOL;
    //for ($j=0; $row2=$list[$i]['sub'][$j]; $j++) {
    $j = 0;
    foreach ($list[$i]['sub'] as $key => $row2) {
        if ($j++>0) echo "<tr>".PHP_EOL;

        $table = sql_fetch("select * from {$g5['board_table']} where bo_table = '{$row2['bo_table']}' ");

        echo "<td class='tbody left'><a href='{$row2['me_link']}'>{$row2['me_name']}</a></td>".PHP_EOL;
        echo "<td class='tbody right'>{$table['bo_read_point']}점</td>".PHP_EOL;
        echo "<td class='tbody right'>{$table['bo_write_point']}점</td>".PHP_EOL;
        echo "<td class='tbody right'>{$table['bo_comment_point']}점</td>".PHP_EOL;
        echo "<td class='tbody right'>".number_format($table['bo_download_point'])."점</td>".PHP_EOL;
        echo "</tr>".PHP_EOL;
    }
}
?>
</table>

<?
include_once("_tail.php");
