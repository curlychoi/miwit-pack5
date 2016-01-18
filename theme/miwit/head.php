<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/head.php');
    return;
}

include_once(G5_THEME_PATH.'/head.sub.php');

mw_script($theme_path.'/js/mw.navbar.js', 'async defer');
mw_css('/asset/font-awesome-4.4.0/css/font-awesome.min.css');
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

// 스크랩
$sql = " select count(*) as cnt from {$g5['scrap_table']} where mb_id = '{$member['mb_id']}' ";
$row = sql_fetch($sql);
$scrap_count = $row['cnt'];

// 현재 메뉴 찾기
$my_url = null;
$menu = null;
$is_sidebar = true;

if (strlen($_SERVER["REQUEST_URI"]) > 1)
{
    $my_url = set_http($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);

    $tmp = parse_url($my_url);
    parse_str($tmp['query'], $my_param);

    $sql = " select * from {$g5['menu_table']} where me_use = '1' order by me_order ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry))
    {
        $me_link = mw_url_style($row['me_link'], 'parameter');
        $tmp = parse_url($me_link);
        parse_str($tmp['query'], $me_param);

        if (strstr($my_url, $row['me_link']) or ($my_param['bo_table'] and $my_param['bo_table'] == $me_param['bo_table'])) {
            $menu = $row;
        }
    }
    unset($tmp);
}

$mw5_menu_extend = null;
if ($menu) {
    $mw5_menu_extend = sql_fetch("select * from {$mw5['menu_table']} where me_code = '{$menu['me_code']}' ", false);
    if ($mw5_menu_extend['me_no_side']) {
        $is_sidebar = false;
        echo "<style>";
        echo "#mw5 .main { width:{$mw['config']['cf_width']}px; }";
        echo "#mw5 .menu_title { width:{$mw['config']['cf_width']}px; }";
        echo "</style>";
    }

    if ($mw5_menu_extend['me_perm'] > $member['mb_level'])
        alert("메뉴에 접근할 권한이 없습니다.");
}

$mw5_menu = mw_get_menu();
$mw5_menu_count = count($mw5_menu);

if(defined('_INDEX_')) { // index에서만 실행
    include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
}

include_once(G5_PATH."/lib/mw.string.lib.php");

$cf_new_class = 'new-count';
switch ($mw['config']['cf_new']) {
    case 'count' : $cf_new_class = 'new-count'; break;
    case 'check' : $cf_new_class = 'new-check'; break;
    case 'no' : $cf_new_class = 'new-no'; break;
    default : $cf_new_class = 'new-count'; break;
}

if (!$mw['config']['cf_nav_no_scroll']) :
?>
<script>
$(window).on('load scroll resize mousewheel', function () {
    head_fixed.run();
});
</script>
<?php endif; ?>
<?php if (!$mw['config']['cf_no_css']) : ?>
<style>
<?php echo $mw['config']['cf_css']?>
</style>
<?php endif; ?>

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
        <li><a href="<?php echo G5_BBS_URL ?>/new.php">새글</a></li>
        <?php } else { ?>
        <li><a href="<?php echo G5_BBS_URL?>/memo.php" class="win_memo">쪽지 (<?php echo $memo_not_read?>)</a></li>
        <li><a href="<?php echo G5_BBS_URL?>/scrap.php" class="win_scrap">스크랩 (<?php echo $scrap_count?>)</a></li>
        <?php if ($config['cf_use_point']) { ?>
        <li><a href="<?php echo G5_BBS_URL?>/point.php" class="win_point">포인트 (<?php echo number_format($member['mb_point'])?>)</a></li>
        <?php } ?>
        <li><a href="<?php echo G5_BBS_URL?>/logout.php">로그아웃</a></li>
        <li><a href="<?php echo G5_BBS_URL?>/member_confirm.php?url=register_form.php">정보수정</a></li>
        <li><a href="<?php echo G5_BBS_URL ?>/new.php?mb_id=<?php echo $member['mb_id']?>">내글</a></li>
        <?php } ?>

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
<div class="effect">
<div class="background">
<div class="wrapper">
    <!-- 사이트 로고 -->
    <div class="logo"><?php echo mw_logo_planner()?></div>

    <?php if (!$mw['config']['cf_no_search']) : ?>
    <!-- 상단검색창 시작 -->
    <div class="search-box">
        <?php echo mw_eval($mw['config']['cf_search_html']); ?>
        <?php if (!$mw['config']['cf_no_quick_link']) echo mw_eval($mw['config']['cf_quick_link_html']); ?>
    </div>
    <?php endif; ?>
    <div class="blank"></div>
</div><!--wrapper-->
</div><!--background-->
</div><!--effect-->
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
        $href = $row2['me_link'];
        $bo_new = '';
        if ($row2['bo_new'])
            $bo_new = "<span class=\"{$cf_new_class}\">{$row2['bo_new']}</span>";

        echo "<li><a href=\"{$href}\" target=\"_{$row2['me_target']}\">{$row2['me_name']}{$bo_new}</a></li>\n";
    }
    echo "</ul>\n";
    $drop_menu = ob_get_clean();

    $nav_class = "item";
    if ($role == substr($menu['me_code'], 0, 2))
        $nav_class = "select";

    $me_name = $row['me_name'];
    if ($row['new'])
        $me_name .= "<span class='{$cf_new_class}'>{$row['new']}</span>";
    if ($j>1 or $row['me_link'] != $href)
        $me_name .= "<span class='caret'>∨</span>";

    echo "<li class='{$nav_class}' data-target='{$role}'>";
    echo "<a href=\"{$row['me_link']}\" target=\"_{$row['me_target']}\">{$me_name}</a></li>\n";

    if ($j>1 or $row['me_link'] != $href) echo $drop_menu;
}
if ($i == 0) {  
    echo "<li class=\"nothing\">메뉴 준비 중입니다.";
    if ($is_admin)
        echo "<a href=\"".G5_ADMIN_URL."/menu_list.php\">관리자모드 &gt; 환경설정 &gt; 메뉴설정</a>에서 설정하실 수 있습니다.";
    echo "</li>";
}
?>
</ul>
<?php if (!$mw['config']['cf_no_popular']) : ?>
<div class="popular"><?php echo mw_popular("theme/mw5")?></div>
<?php endif; ?>
</div><!--wrapper-->
</nav>
<!-- 그룹 메뉴 끝 -->

<?php if (!$mw['config']['cf_no_head']) echo mw_eval($mw['config']['cf_head_html']); ?>

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
                $buffer.= '<a href="'.$menu_parent['me_link'].'">';
                $buffer.= $menu_parent['me_name']."</a> <i class='fa fa-chevron-right'></i> ";
            }
        }
        else if (strlen($menu['me_code']) > 2) {
            $parent_code = substr($menu['me_code'], 0, 2);
            $menu_parent = sql_fetch("select * from {$g5['menu_table']} where me_code = '{$parent_code}'");
            $buffer.= '<a href="'.$menu_parent['me_link'].'">';
            $buffer.= $menu_parent['me_name']."</a> <i class='fa fa-chevron-right'></i> ";
        }
        if (!$menu['me_name'] && $board['bo_subject'])
            $menu_title = '<a href="'.G5_BBS_URL.'/board.php?bo_table='.$board['bo_table'].'">'.$board['bo_subject'].'</a>';
        else
            $menu_title = '<a href="'.$menu['me_link'].'">'.$menu['me_name'].'</a>';

        if (!strstr($buffer, "fa-") and !strstr($menu_title, "fa-"))
            $menu_title = "<i class='fa fa-arrow-circle-right'></i> ".$menu_title;

        $buffer.= $menu_title."</div>".PHP_EOL;
        echo $buffer;

        if (!defined("_INDEX_")) echo "<style>.main { margin-top:50px; } </style>";
    }
    ?>

    <div class="main">

    <?php if (!defined("_INDEX_") and !$mw['config']['cf_no_content_head']) echo mw_eval($mw['config']['cf_content_head_html']); ?>

