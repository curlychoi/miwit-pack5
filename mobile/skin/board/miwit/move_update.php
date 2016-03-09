<?php
include_once("./_common.php");

// 게시판 관리자 이상 복사, 이동 가능
if ($is_admin != 'board' && $is_admin != 'group' && $is_admin != 'super') 
    alert_close("게시판 관리자 이상 접근이 가능합니다.");

if ($sw != "move" && $sw != "copy")
    alert("sw 값이 제대로 넘어오지 않았습니다.");

include_once("$board_skin_path/mw.lib/mw.skin.basic.lib.php");

if ($move_memo_use) {

    $sw_msg = $sw == 'move' ? '이동' : '복사';

    $bo_list = '';
    $sql = "select bo_subject from $g4[board_table] where bo_table in ('".implode("','", (array)$chk_bo_table)."')";
    $qry = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($qry); ++$i) {
        if ($i>0) $bo_list .= ", ";
        $bo_list .= $row[bo_subject];
    }
    $memo = " 회원님의 아래 게시물이 [{$board[bo_subject]}] 게시판에서 ";
    $memo.= " [{$bo_list}] 게시판으로 {$sw_msg} 조치 되었습니다.\n\n";

    $list = array();
    $sql = "select wr_subject, mb_id from $write_table where wr_id in ('".implode("','", explode(",",$wr_id_list))."')";
    $qry = sql_query($sql);
    while ($row = sql_fetch_array($qry)) {
        $list[$row[mb_id]] .= "- ".htmlspecialchars($row[wr_subject])."\n";
    }

    foreach ((array)$list as $mb_id => $bo_list) {
        $me_memo = $memo . $bo_list;

        $tmp_row = sql_fetch(" select max(me_id) as max_me_id from $g4[memo_table] ");
        $me_id = $tmp_row[max_me_id] + 1;

        // 쪽지 INSERT
        $sql = " insert into $g4[memo_table]
                        ( me_id, me_recv_mb_id, me_send_mb_id, me_send_datetime, me_memo )
                 values ( '$me_id', '{$mb_id}', '$member[mb_id]', '$g4[time_ymdhis]', '$me_memo' ) ";
        sql_query($sql);

        // 실시간 쪽지 알림 기능
        $sql = " update $g4[member_table]
                    set mb_memo_call = '$member[mb_id]'
                  where mb_id = '$mb_id' ";
        sql_query($sql);
    }
}

mw_move($board, $wr_id_list, $chk_bo_table, $sw);

$msg = "해당 게시물을 선택한 게시판으로 $act 하였습니다.";
$opener_href = "$g4[bbs_path]/board.php?bo_table=$bo_table&$qstr";

$script = "";
if ($sw == 'move')
    $script = "opener.document.location.href = \"{$opener_href}\"";

echo <<<HEREDOC
<meta http-equiv='content-type' content='text/html; charset={$g4['charset']}'> 
<script type="text/javascript">
alert("{$msg}");
{$script}
window.close();
</script>
HEREDOC;
