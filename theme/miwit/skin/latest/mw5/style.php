<?php
header("content-type:text/css; charset:utf-8");

$css = "mw5-{$_GET['bo_table']}-{$_GET['rows']}-{$_GET['subject_len']}";
?>
.<?php echo $css?> {
    border:0;
    text-align:left;
}

.<?php echo $css?> h2 {
    font-size:1.2em;
    line-height:30px;
    height:30px;
    border-bottom:2px solid #aaa;
    margin:0 0 10px 0;
    padding:0;

    -webkit-box-sizing: border-box; /* Safari/Chrome, other WebKit */
    -moz-box-sizing: border-box;    /* Firefox, other Gecko */
    box-sizing: border-box;         /* Opera/IE 8+ */

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

    -webkit-box-sizing: border-box; /* Safari/Chrome, other WebKit */
    -moz-box-sizing: border-box;    /* Firefox, other Gecko */
    box-sizing: border-box;         /* Opera/IE 8+ */
}

.<?php echo $css?> ul {
    margin:0;
    padding:0;
    list-style:none;
}

.<?php echo $css?> ul li {
    font-size:1em;
    line-height:25px;
    border-bottom:1px solid #efefef;
    position:relative;
    white-space:nowrap;
    overflow:hidden;
}

.<?php echo $css?> ul li:before {
    font-family:FontAwesome;
    font-weight:normal;
    font-style:normal;
    display:inline-block;
    text-decoration:inherit;
    content:"\f0da";
    width:10px;
    text-align:center;
    margin-right:5px;
    color:#888;
    color:#e67e22;
    color:#bdc3c7;
}

.<?php echo $css?> ul li.new:before {
    color:#e74c3c;
}

.<?php echo $css?> ul li.secret:before {
    content:"\f023";
}


.<?php echo $css?> ul li a:hover {
    color:#e74c3c;
}

.<?php echo $css?> .comment {
    position:absolute;
    right:0;
    font-size:.8em;
    color:#FF6600;
    background-color:#fff;
    padding:0 5px 0 5px;
} 

