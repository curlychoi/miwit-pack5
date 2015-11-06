<?php
$qry = null;
foreach((array)$_GET as $key => $val) {
    $qry .= "&{$key}=".$val;
}
header('location: ../bbs/'.basename($_SERVER['PHP_SELF']).'?'.$qry);
