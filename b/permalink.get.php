<?php
$qry = null;
foreach((array)$_GET as $key => $val) {
    $qry .= "&{$key}=".$val;
}
header('location: ../bbs/'.basename($_SERVER['SCRIPT_NAME']).'?'.$qry);
