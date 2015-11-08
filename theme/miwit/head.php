<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/head.php');
    return;
}

include_once(G5_THEME_PATH.'/head.sub.php');

mw_script($theme_path.'/js/mw.navbar.js');
mw_css('/asset/font-awesome-4.3.0/css/font-awesome.min.css');
mw_css($theme_path.'/style.css');
mw_css($theme_path.'/color/'.$mw['config']['cf_theme_color'].'/style.css');
mw_css($theme_path.'/layout.php');
mw_css($theme_path.'/css/mw.widget.css');

// 쪽지
$memo_not_read = 0;
if ($is_member) {
    $sql = " select count(*) as cnt ";
    $sql.= "   from {$g5['memo_table']} ";
    $sql.= "  where me_recv_mb_id = '{$member['mb_id']}' ";
    $sql.= "    and substring(me_read_datetime, 1, 1) = '0' ";
    $row = sql_fetch($sql);
    $memo_not_read = $row['cnt'];
}

// 현재 메뉴 찾기
$my_url = null;
$menu = null;
$is_sidebar = true;

if (strlen($_SERVER["REQUEST_URI"]) > 1) {
    $my_url = set_http($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
    $sql = " select * from {$g5['menu_table']} where me_use = '1' order by me_order ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        if (strstr($my_url, $row['me_link'])
            or strstr($my_url, preg_replace("/\/b\/([a-zA-Z0-9-_]+)$/iUs", "/".G5_BBS_DIR."/board.php?bo_table=$1", $row['me_link']))
            or strstr($my_url, preg_replace("/\/b\/([a-zA-Z0-9-_]+)$/iUs", "/".G5_BBS_DIR."/write.php?bo_table=$1", $row['me_link']))
            or strstr($my_url, str_replace("board.php", "write.php", $row['me_link']))) {
            $menu = $row;
            //break;
        }
        //$menu = sql_fetch(" select * from {$g5['menu_table']} where me_link like '{$my_url}%' limit 1");
    }
}

$mw5_menu_extend = null;
if ($menu) {
    $mw5_menu_extend = sql_fetch("select * from {$mw5['menu_table']} where me_code = '{$menu['me_code']}' ", false);
    if ($mw5_menu_extend['me_no_side']) {
        $is_sidebar = false;
    }
}

$mw5_menu = array();

$sql = " select *
           from {$g5['menu_table']}
          where me_use = '1'
            and length(me_code) = '2'
          order by me_order, me_id ";
$qry = sql_query($sql);
//for ($i=0; $row=sql_fetch_array($qry); $i++) {
$i = 0;
while ($row=sql_fetch_array($qry)) {
    $extend = sql_fetch("select * from {$mw5['menu_table']} where me_code = '{$row['me_code']}' ", false);
    if ($extend) {
        if ($extend['me_level'] > $member['mb_level']) {
            continue;
        }
        if ($extend['me_icon']) {
            $row['me_name'] = "<i class='fa fa-{$extend['me_icon']}'></i> ".$row['me_name'];
        }
    }

    $mw5_menu[$i] = $row;
    $mw5_menu[$i]['sub'] = array();

    $g_new = 0;
    $g_count = 0;
    $g_table = '';

    preg_match("/bo_table=([0-9a-zA-Z-_]+)&/", $row['me_link'].'&', $match);
    if (!$match[1])
        preg_match("/\/b\/([0-9a-zA-Z-_]+)&/", $row['me_link'].'&', $match);

    if ($match[1]) {
        $mw_skin_config = mw_skin_config($match[1]);
        if ($mw_skin_config['cf_attribute'] != "1:1" or $is_admin) {
            $b = sql_fetch(" select bo_count_write, bo_new from {$g5['board_table']} where bo_table = '{$match[1]}' ");
            $t = sql_fetch(" select count(*) as cnt from {$g5['write_prefix']}{$match[1]} where wr_is_comment = '' and wr_datetime >= DATE_SUB(NOW(), INTERVAL {$b['bo_new']} HOUR) ");

            $g_new += $t['cnt'];
            $g_count += $b['bo_count_write'];
            $g_table = $match[1];
        }
    }

    $j = 0;
    $sql2 = " select *
               from {$g5['menu_table']}
              where me_use = '1'
                and length(me_code) = '4'
                and substring(me_code, 1, 2) = '{$row['me_code']}'
              order by me_order, me_id ";
    $qry2 = sql_query($sql2);
    //for ($j=0; $row2=sql_fetch_array($qry2); $j++) {
    while ($row2=sql_fetch_array($qry2)) {
        $extend = sql_fetch("select * from {$mw5['menu_table']} where me_code = '{$row2['me_code']}' ", false);
        if ($extend) {
            if ($extend['me_level'] > $member['mb_level']) {
                continue;
            }
            if ($extend['me_icon']) {
                $row2['me_name'] = "<i class='fa fa-{$extend['me_icon']}'></i> ".$row2['me_name'];
            }
        }

        preg_match("/bo_table=([0-9a-zA-Z-_]+)&/", $row2['me_link'].'&', $match);
        if (!$match[1])
            preg_match("/\/b\/([0-9a-zA-Z-_]+)&/", $row2['me_link'].'&', $match);

        if ($match[1] && $match[1] == $g_table) {
            $row2['bo_new'] = $g_new;
            $row2['bo_count'] = $g_count;
        }
        else if ($match[1]) {
            $mw_skin_config = mw_skin_config($match[1]);
            if ($mw_skin_config['cf_attribute'] != "1:1" or $is_admin) {
                $b = sql_fetch(" select bo_count_write, bo_new from {$g5['board_table']} where bo_table = '{$match[1]}' ");
                $t = sql_fetch(" select count(*) as cnt from {$g5['write_prefix']}{$match[1]} where wr_is_comment = '' and wr_datetime >= DATE_SUB(NOW(), INTERVAL {$b['bo_new']} HOUR) ");

                $row2['bo_new'] = $t['cnt'];
                $row2['bo_count'] = $b['bo_count_write'];

                $g_new += $t['cnt'];
                $g_count += $b['bo_count_write'];
            }
        }

        $mw5_menu[$i]['sub'][$j] = $row2;
        ++$j;
    }
    if (!$j)
        $mw5_menu[$i]['sub'][0] = $row;

    $mw5_menu[$i]['new'] = $g_new;
    $mw5_menu[$i]['count'] = $g_count;

    ++$i;
}

$mw5_menu_count = count($mw5_menu);

if(defined('_INDEX_')) { // index에서만 실행
    include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
}

include_once(G5_PATH."/lib/mw.string.lib.php");
?>
<div id="mw5">

<div class="top">
    <div class="wrapper">
    <ul class="left">
        <li><a href="#;" onclick="window.external.AddFavorite('http://<?php echo $_SERVER['HTTP_HOST']?>/' , '<?php echo $config['cf_title']?>');">즐겨찾기</a></li>
        <li><a href="<?php echo G5_BBS_URL ?>/current_connect.php">현재접속자 (<?php echo trim(connect())?>)</a></li>
        <li><a href="<?php echo G5_BBS_URL?>/new.php">최근게시물</a></li>
    </ul>
    <ul class="right">
        <?php if ($is_admin == "super") { ?>
        <li><a href="<?php echo G5_ADMIN_URL?>/">관리자</a></li>
        <?php } ?>
        <?php if (!$is_member) { ?>
        <li><a href="<?php echo G5_BBS_URL?>/login.php">로그인</a></li>
        <li><a href="<?php echo G5_BBS_URL?>/register.php">회원가입</a></li>
        <?php } else { ?>
        <li><a href="<?php echo G5_BBS_URL?>/memo.php" class="win_memo">쪽지 (<?php echo $memo_not_read?>)</a></li>
        <?php if ($config['cf_use_point']) { ?>
        <li><a href="<?php echo G5_BBS_URL?>/point.php" class="win_point">포인트 (<?php echo number_format($member['mb_point'])?>)</a></li>
        <?php } ?>
        <li><a href="<?php echo G5_BBS_URL?>/logout.php">로그아웃</a></li>
        <li><a href="<?php echo G5_BBS_URL?>/member_confirm.php?url=register_form.php">정보수정</a></li>
        <?php } ?>
        <li><a href="<?php echo G5_BBS_URL ?>/new.php">새글</a></li>

        <?php if ($is_member) { ?> 
        <?php include("$g4[path]/plugin/smart-alarm/_config.php"); ?> 
        <li id="moa_alarm">알림(<span id="moa_count">0</span>)</li> 
        <div id="moa_box"><div id="moa"></div></div> 
        <script> 
        g4_path = "<?php echo $g4['path']?>"; 
        mw_moa_path = "<?php echo $mw_moa_path?>"; 
        </script> 
        <link rel="stylesheet" href="<?php echo $mw_moa_path?>/style.css"/> 
        <script src="<?php echo $mw_moa_path?>/script.js"></script> 
        <?php } ?> 

    </ul>
    </div><!--wrapper-->
</div>

<!-- 헤더 시작 -->
<div class="head">
<div class="wrapper">
    <!-- 사이트 로고 -->
    <div class="logo"><?php echo mw_logo_planner()?></div>

    <!-- 상단검색창 시작 -->
    <div class="search-box">
        <form name="fmainsearch" action="<?php echo G5_URL?>/plugin/united-search/">
            <input type="hidden" name="sfl" value="wr_subject||wr_content">
            <input type="hidden" name="sop" value="and">
            <span class="search-text"><input type=text name=stx></span>
            <input type="submit" value="검색" class="search-button">
        </form>

        <!-- 퀵링크 시작 -->
        <div class="quick-link">
        <ul>
            <li><a href="http://www.miwit.com/b/mw_builder" target="_blank">빌더다운</a></li>
            <li><a href="http://www.miwit.com/b/g4_skin" target="_blank">스킨다운</a></li>
            <li><a href="http://www.miwit.com/plugin/project/" target="_blank">프로젝트</a></li>
            <li><a href="http://www.miwit.com/plugin/family/" target="_blank">통합팩</a></li>
            <li><a href="http://www.miwit.com/b/mw_tip" target="_blank">매뉴얼,팁</a></li>
            <li><a href="http://www.miwit.com/b/g4_site" target="_blank">사용후기</a></li>
            <li><a href="http://www.miwit.com/b/g4_qna" target="_blank">질문게시판</a></li>
            <li><a href="http://www.miwit.com/b/mw_board" target="_blank">커뮤니티</a></li>
        </ul>
        </div>
        <!-- 퀵링크 끝 -->
    </div>
    <div class="blank"></div>
</div><!--wrapper-->
</div><!-- head -->

<!-- 그룹 메뉴 시작 -->
<nav class="navbar navbar-fixed">
<div class="wrapper">
<ul>
<?php
for ($i=0; $row=$mw5_menu[$i]; ++$i)
{
    $role = substr($row['me_code'], 0, 2);

    ob_start();
    echo "<ul class=\"dropdown\" data-role=\"{$role}\">\n";
    for ($j=0; $row2=$mw5_menu[$i]['sub'][$j]; ++$j) {
        $bo_new = '';
        if ($row2['bo_new'])
            $bo_new = "<span class=\"new\">{$row2['bo_new']}</span>";

        echo "<li><a href=\"{$row2['me_link']}\" target=\"_{$row2['me_target']}\">{$row2['me_name']}{$bo_new}</a></li>\n";
    }
    echo "</ul>\n";
    $drop_menu = ob_get_clean();

    $nav_class = "item";
    if ($role == substr($menu['me_code'], 0, 2))
        $nav_class = "select";

    $me_name = $row['me_name'];
    if ($row['new'])
        $me_name .= "<span class='new'>{$row['new']}</span>";
    if ($j>1)
        $me_name .= "<span class='caret'>∨</span>";

    echo "<li class='{$nav_class}' data-target='{$role}'>";
    echo "<a href=\"{$row['me_link']}\" target=\"_{$row['me_target']}\">{$me_name}</a></li>\n";

    if ($j>0) echo $drop_menu;
}
if ($i == 0) {  
    echo "<li class=\"nothing\">메뉴 준비 중입니다.";
    if ($is_admin)
        echo "<a href=\"".G5_ADMIN_URL."/menu_list.php\">관리자모드 &gt; 환경설정 &gt; 메뉴설정</a>에서 설정하실 수 있습니다.";
    echo "</li>";
}
?>
</ul>
<div class="popular"><?php echo mw_popular("theme/mw5")?></div>
</div><!--wrapper-->
</nav>
<!-- 그룹 메뉴 끝 -->

<div class="container">
<div class="wrapper">

    <?php
    if (!defined("_INDEX_") && ($menu or $bo_table)) {
        $buffer = "<div class='menu_title'>";
        if (strlen($menu['me_code']) < 4 && $bo_table) {
            $sql = "select * ";
            $sql.= "  from {$g5['menu_table']} ";
            $sql.= " where left(me_code, 2) = '{$menu['me_code']}' ";
            $sql.= "   and CHAR_LENGTH(me_code) = 4 ";
            $sql.= "   and (me_link like '%bo_table={$bo_table}' or me_link like '%/b/{$bo_table}')";
            $menu_child = sql_fetch($sql);
            if ($menu_child) {
                $menu_parent = $menu;
                $menu = $menu_child;
                $buffer.= $menu_parent['me_name']." <i class='fa fa-chevron-right'></i> ";
            }
        }
        else if (strlen($menu['me_code']) > 2) {
            $parent_code = substr($menu['me_code'], 0, 2);
            $menu_parent = sql_fetch("select * from {$g5['menu_table']} where me_code = '{$parent_code}'");
            $buffer.= $menu_parent['me_name']." <i class='fa fa-chevron-right'></i> ";
        }
        if (!$menu['me_name'] && $board['bo_subject'])
            $menu_title = $board['bo_subject'];
        else
            $menu_title = $menu['me_name'];

        if (!strstr($buffer, "fa-") and !strstr($menu_title, "fa-"))
            $menu_title = "<i class='fa fa-arrow-circle-right'></i> ".$menu_title;

        $buffer.= $menu_title."</div>".PHP_EOL;
        echo $buffer;

        if (!defined("_INDEX_")) echo "<style>.main { margin-top:50px; } </style>";
    }
    ?>

    <div class="main">

