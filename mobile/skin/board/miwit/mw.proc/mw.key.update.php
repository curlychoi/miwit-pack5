<?php
include_once("_common.php");

if (!$bo_table or !$wr_id)
    die("데이터가 없습니다.");

include_once("{$board_skin_path}/mw.lib/mw.skin.basic.lib.php");

if ($write['wr_key_password'] == sql_password($wr_key_password)) {
    set_session($ss_key_name."_".$write['wr_id'], TRUE);
    die("ok");
}
else if ($write['mb_id'] and $write['mb_id'] == $member['mb_id']) {
    set_session($ss_key_name."_".$write['wr_id'], TRUE);
    die("self");
}
else if ($is_admin) {
    set_session($ss_key_name."_".$write['wr_id'], TRUE);
    die("admin");
}
else {
    die("비밀번호가 올바르지 않습니다.");
}

