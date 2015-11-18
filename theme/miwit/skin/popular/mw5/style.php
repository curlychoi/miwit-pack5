<?php
header("content-type:text/css; charset:utf-8");
?>
#mw5 .popular .scroll {
    color:#fff;
    font-size:.8em;

    position:relative;
    height:40px;
    width:200px;
    display:block;
    overflow:visible;
    overflow:hidden;
}

#mw5 .popular .scroll ul {
    position:absolute;
    margin:0;
    padding:0;
    list-style:none;
    background-color:#000;
    background-color:transparent;
}

#mw5 .popular .scroll ul li {
    height:40px;
    width:150px;
    padding-left:25px;
    border:1px solid #000;
    border:0;

    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

#mw5 .popular .scroll ul li a {
    color:#fff;
    border:1px solid #000;
    border:0;

    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;

    float:left;
    width:100px;
    word-break: break-all;
    white-space:nowrap;
    overflow:hidden;
}

<?php
for ($i=1; $i<=10; ++$i) {
    echo "
    #mw5 .popular .scroll li.rank{$i}:before {
        content:'{$i}';
        width:15px;
        float:left;
        text-align:center;
        background-color:#34495e;
        border:0;
        padding:2px 0 1px 0;
        margin:12px 0 0 -25px;
        /*-webkit-border-radius:1em;
        -moz-border-radius:1em;
        border-radius:1em;*/
    }\n";
}
?>

#mw5 .popular .scroll ul li .arrow {
    position:absolute;
    margin-top:15px;
    right:0;
}

#mw5 .popular .scroll ul li .arrow.anew:before {
    font-family:FontAwesome;
    content:"\f072";
    margin-right:5px;
}

#mw5 .popular .scroll ul li .arrow.up:before {
    font-family:FontAwesome;
    content:"\f062";
    margin-right:5px;
}

#mw5 .popular .scroll ul li .arrow.down:before {
    font-family:FontAwesome;
    content:"\f063";
    margin-right:5px;
}

#mw5 .popular .scroll ul li .arrow.nogap:before {
    font-family:FontAwesome;
    content:"\f068";
    margin-right:5px;
}

#mw5 .popular .popular-hover {
    color:#fff;
    font-size:.8em;

    position:absolute;
    height:220px;
    width:200px;
    display:block;
    background-color:#fff;

    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;

    border:2px solid #aaa;
    /*box-shadow:1px 1px 10px #444;*/

    left:0;
    top:0;

    z-index:999;

    display:none;
}

#mw5 .popular .popular-hover ul {
    position:absolute;
    margin:10px 0 0 0;
    padding:0 0 0 10px;
    list-style:none;
}

#mw5 .popular .popular-hover ul li {
    height:20px;
    width:170px;
    padding:0 0 0 25px;
    border:0;
    margin:0;
    clear:both;

    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

#mw5 .popular .popular-hover ul li:hover {
    background-color:#efefef;
}

#mw5 .popular .popular-hover ul li a:visited,
#mw5 .popular .popular-hover ul li a:hover,
#mw5 .popular .popular-hover ul li a:link,
#mw5 .popular .popular-hover ul li a:active {
    color:#000;
    margin:0;
    padding:0;
    line-height:20px;

    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;

    float:left;
    width:100px;
    height:20px;
    word-break: break-all;
    white-space:nowrap;
    overflow:hidden;
}

<?php
for ($i=1; $i<=10; ++$i) {
    echo "
    #mw5 .popular .popular-hover li.rank{$i}:before {
        content:'{$i}';
        width:15px;
        float:left;
        text-align:center;
        background-color:#34495e;
        border:0;
        padding:2px 0 1px 0;
        margin:2px 0 0 -25px;
        /*-webkit-border-radius:1em;
        -moz-border-radius:1em;
        border-radius:1em;*/
    }\n";
}
?>

#mw5 .popular .popular-hover ul li .arrow {
    color:#000;
    position:absolute;
    margin-top:5px;
    right:5px;
    width:18px;
}

#mw5 .popular .popular-hover ul li .arrow.anew:before {
    font-family:FontAwesome;
    content:"\f072";
    margin-right:5px;
    position:absolute;
    right:20px;
}

#mw5 .popular .popular-hover ul li .arrow.up:before {
    font-family:FontAwesome;
    content:"\f062";
    position:absolute;
    right:20px;
}

#mw5 .popular .popular-hover ul li .arrow.down:before {
    font-family:FontAwesome;
    content:"\f063";
    position:absolute;
    right:20px;
}

#mw5 .popular .popular-hover ul li .arrow.nogap:before {
    font-family:FontAwesome;
    content:"\f068";
    position:absolute;
    right:20px;
}

