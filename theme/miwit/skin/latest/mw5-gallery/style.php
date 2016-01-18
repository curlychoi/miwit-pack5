<?php
header("content-type:text/css; charset:utf-8");

$css = "gallery-{$_GET['bo_table']}-{$_GET['rows']}-{$_GET['subject_len']}";

$w = 170; // 가로
$h = 170; // 세로
?>
.<?php echo $css?> {
    border:0;
    text-align:left;
    overflow:hidden;
}

.<?php echo $css?> h2 {
    font-size:1.2em;
    line-height:30px;
    height:30px;
    border-bottom:2px solid #aaa;
    margin:0 0 10px 0;
    padding:0;

    -webkit-box-sizing:border-box;
    -moz-box-sizing:border-box;
    box-sizing:border-box;
}

.<?php echo $css?> h2 a {
    position:absolute;
    font-size:1.2em;
    line-height:30px;
    height:30px;
    border-bottom:2px solid #e74c3c;
    min-width:120px;
    padding-left:5px;
    text-decoration:none;

    -webkit-box-sizing:border-box;
    -moz-box-sizing:border-box;
    box-sizing:border-box;
}

.<?php echo $css?> ul {
    margin:0;
    padding:0;
    list-style:none;
    display:table;
    width:100%;
    table-layout:fixed;
    box-sizing:border-box;
    -moz-box-sizing:border-box;
    -webkit-box-sizing:border-box;
}

.<?php echo $css?> ul li {
    display:table-cell;
    vertical-align:top;
    text-align:center;
    background-color:#fff;
    color:#fff;
    box-sizing:border-box;
    -moz-box-sizing:border-box;
    -webkit-box-sizing:border-box;
}

.<?php echo $css?> ul li:last-child {
    margin-right:0;
}
.<?php echo $css?> ul li div.thumb {
    width:<?php echo $w?>px;
    height:<?php echo $h?>px;
    margin:0 auto 0 auto;
    box-sizing:border-box;
    -moz-box-sizing:border-box;
    -webkit-box-sizing:border-box;
}

.<?php echo $css?> ul li img {
    width:<?php echo $w?>px;
    height:<?php echo $h?>px;

    /*border-top:1px solid #ccc;
    border-right:1px solid #ccc;
    border-left:1px solid #ccc;*/

    box-sizing:border-box;
    -moz-box-sizing:border-box;
    -webkit-box-sizing:border-box;
}

.<?php echo $css?> ul li div.noimage {
    width:<?php echo $w?>px;
    height:<?php echo $h?>px;
    text-align:center;
    background-color:#efefef;
    margin:0 auto 0 auto;

    /*border-top:1px solid #ccc;
    border-right:1px solid #ccc;
    border-left:1px solid #ccc;*/

    box-sizing:border-box;
    -moz-box-sizing:border-box;
    -webkit-box-sizing:border-box;
}

.<?php echo $css?> ul li div.noimage i {
    font-size:50px;
    line-height:<?php echo $h?>px;
    color:#999;
}

.<?php echo $css?> ul li a:hover {
    color:#e74c3c;
}

.<?php echo $css?> ul li a {
    color:#fff;
}

.<?php echo $css?> ul li a:hover {
    text-decoration:none;
}

.<?php echo $css?> ul li div.title {
    color:#fff;
    width:<?php echo $w?>px;
    height:30px;
    margin-top:-30px;
    display:block;
    line-height:30px;
    font-size:13px;
    background-color:#777;
    box-sizing:border-box;
    -moz-box-sizing:border-box;
    -webkit-box-sizing:border-box;
    position:absolute;
    opacity:0.8;
}

.<?php echo $css?> .comment {
    position:absolute;
    right:0;
    font-size:.8em;
    color:#FF6600;
    color:#e74c3c;
    padding:0 5px 0 5px;
} 


