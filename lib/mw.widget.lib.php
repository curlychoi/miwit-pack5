<?php
if (!defined("_GNUBOARD_")) exit;

function mw_connect()
{
    global $g4;
    global $config;

    $tmp = array();
    $list = array();

    $sql_admin = '';
    $sql_admin = " and mb_id <> '{$config['cf_admin']}' ";

    $sql = "select * from {$g4['login_table']} where mb_id <> '' {$sql_admin} ";
    $qry = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($qry); $i++)
    {
        if (in_array($row['mb_id'], $tmp)) continue;
        $tmp[] = $row['mb_id'];

        $mb = get_member($row['mb_id'], "mb_id, mb_nick, mb_homepage, mb_email");
        $mb['mb_nick'] = cut_str($mb['mb_nick'], 10, '');
        $name = get_sideview($mb['mb_id'], $mb['mb_nick'], $mb['mb_email'], $mb['mb_homepage']);

        $img = $g4['path']."/data/mw.basic.comment.image/".$row['mb_id'];
        if (is_file($img)) {
            $list[$i] = sprintf('<div class="profile"><img src="%s"></div>', $img);
        }
        else {
            $list[$i] = "<div class='profile noimage'></div>";
        }
    
        $list[$i].= sprintf('<div class="name">%s</div>', $name);
    }
    $current_connect = $i;

    shuffle($list);

    ob_start();
    ?>
    <div class="connect_side">
        <h2><a href="<?php echo G5_BBS_URL?>/current_connect.php">현재접속회원</a></h2>
        <div class="clear"></div>
        <?php
        foreach ((array)$list as $item) {
            printf('<div class="item">%s</div>', $item);
        }
        ?>
        <div class="clear"></div>
    </div>
    <?php
    return ob_get_clean();
}

function mw_latest_write($limit=5)
{
    global $g5;
    global $member;
    global $mw;

    ob_start();

    echo "<div class=\"latest_write\">\n";
    echo "<h2><a href=\"".G5_BBS_URL."/new.php\">최신글</a></h2>\n";
    echo "<ul>\n";

    $board_list = array();
    $sql = " select bo_table from {$g5['board_table']} where bo_use_search = '1' and bo_list_level <= {$member['mb_level']} ";
    $sql.= " and bo_table <> '{$mw['config']['cf_sidebar_notice_table']}' ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $board_list[] = $row['bo_table'];
    }
    $board_list = implode("','", $board_list);

    $sql = " select * from {$g5['board_new_table']} ";
    $sql.= "  where bo_table in ('{$board_list}') ";
    $sql.= "    and wr_id = wr_parent ";
    $sql.= "  order by bn_datetime desc limit {$limit}";
    $qry = sql_query($sql);
    $c = 0;
    while ($row = sql_fetch_array($qry)) {
        if (function_exists("mw_seo_url"))
            $href = mw_seo_url($row['bo_table'], $row['wr_id']);
        else
            $href = G5_BBS_URL."/board.php?bo_table={$row['bo_table']}&wr_id={$row['wr_id']}";

        $sql = " select * ";
        $sql.= "   from {$g5['write_prefix']}{$row['bo_table']} ";
        $sql.= "  where wr_id = '{$row['wr_id']}' ";
        $ro2 = sql_fetch($sql);

        $ro3 = sql_fetch("select bo_subject, bo_new from {$g5['board_table']} where bo_table = '{$row['bo_table']}' ");

        $ro2 = mw_get_list($ro2, $ro3, '', 100);

        $subject = $ro2['wr_subject'];
        $subject = strip_tags($subject);
        //$subject = addslashes($subject);

        $subject = "[{$ro3['bo_subject']}] ".$subject;

        $class = "";

        if ($ro3['bo_new'] && $row['bn_datetime'] >= date("Y-m-d H:i:s", G5_SERVER_TIME - ($ro3['bo_new'] * 3600)))
            $class.= " new ";

        if (strstr($ro2['wr_option'], 'secret'))
            $class.= " secret ";

        $comment_cnt = $ro2['wr_comment'];
        if (!$comment_cnt)
            $comment_cnt = '';

        echo "<li class=\"{$class}\">";
        echo "<a href=\"{$href}\">";
        echo "{$subject}";
        echo "<span class=\"comment\">{$comment_cnt}</span>";
        echo "</a>";
        echo "</li>\n";
        ++$c;
    }
    for ($i=$c; $i<$limit; ++$i) {
        echo "<li>&nbsp;</li>";
    }

    echo "</ul>";
    echo "</div>";

    return ob_get_clean();
}

function mw_latest_comment($limit=5)
{
    global $g5;
    global $member;

    ob_start();

    echo "<div class=\"latest_comment\">\n";
    echo "<h2><a href=\"".G5_BBS_URL."/new.php\">최신댓글</a></h2>\n";
    echo "<ul>\n";

    $board_list = array();
    $sql = "select bo_table from {$g5['board_table']} where bo_use_search = '1' and bo_list_level <= {$member['mb_level']} ";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $board_list[] = $row['bo_table'];
    }
    $board_list = implode("','", $board_list);

    $sql = " select * from {$g5['board_new_table']} ";
    $sql.= "  where bo_table in ('{$board_list}') ";
    $sql.= "    and wr_id != wr_parent ";
    $sql.= "    and mb_id != '@lucky-writing' ";
    $sql.= "  order by bn_datetime desc limit {$limit}";
    $qry = sql_query($sql);
    $c = 0;
    while ($row = sql_fetch_array($qry)) {
        if (function_exists("mw_seo_url"))
            $href = mw_seo_url($row['bo_table'], $row['wr_parent'], "#c_".$row['wr_id']);
        else
            $href = G5_BBS_URL."/board.php?bo_table={$row['bo_table']}&wr_id={$row['wr_parent']}#c_{$row['wr_id']}";

        $sql = " select wr_option, wr_content, wr_comment, mb_id ";
        $sql.= "   from {$g5['write_prefix']}{$row['bo_table']} ";
        $sql.= "  where wr_id = '{$row['wr_id']}' ";
        $ro2 = sql_fetch($sql);

        $ro3 = sql_fetch("select bo_subject, bo_new from {$g5['board_table']} where bo_table = '{$row['bo_table']}' ");

        $ro2 = mw_get_list($ro2, $ro3, '', 100);

        $content = $ro2['wr_content'];
        $content = htmlspecialchars($content);
        $content = cut_str($content,100);
        //$content = addslashes($content);

        $class = "";

        if ($ro3['bo_new'] && $row['bn_datetime'] >= date("Y-m-d H:i:s", G5_SERVER_TIME - ($ro3['bo_new'] * 3600)))
            $class.= " new ";

        if (strstr($ro2['wr_option'], 'secret')) {
            $class.= "secret";
            $content = "비밀글입니다.";
        }

        echo "<li class=\"{$class}\">";
        echo "<a href=\"{$href}\">";
        echo "{$content}";
        echo "</a>";
        echo "</li>\n";
        ++$c;
    }
    for ($i=$c; $i<$limit; ++$i) {
        echo "<li>&nbsp;</li>";
    }

    echo "</ul>";
    echo "</div>";

    return ob_get_clean();
}


