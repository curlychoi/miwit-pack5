////////////////////////////////////////////////
// G-Editor ver.1.0.5
//
// GPL License
// copyright sir.co.kr
// edit miwit.com
////////////////////////////////////////////////
geditor = function(name) {

/////////////// 사용자 설정 시작 ///////////////

// geditor.js 파일 경로
var ge_path             = g4_skin_path + '/mw.geditor';

// 상단 툴바 이미지 경로
var ge_icon_path        = ge_path + '/icons';

// 이모티콘 이미지 경로
var ge_emoticon_path    = g4_skin_path + '/mw.emoticon';

// 이모티콘 갯수
var ge_emoticon_count   = 93;

/////////////// 사용자 설정 끝 ///////////////

var ge_empty_path       = ge_icon_path + '/empty.gif';

var IS_IE               = true;

var _WYSIWYG            = 'WYSIWYG';
var _TEXT               = 'TEXT';
var _HTML               = 'HTML';

var ge_name             = 'geditor_'+name;
var ge_content          = name;
var ge_mode             = _WYSIWYG;
var ge_code             = '';
var ge_editor           = null;
var ge_width            = 0;
var ge_height           = 0;
var ge_image_width      = 0;
var ge_iframe           = ge_name+'_frame';
var ge_textarea         = ge_name+'_textarea';
var ge_source           = ge_name+'_source';

var ge_table_rows       = 3;
var ge_table_cols       = 3;
var ge_table_x          = 0;
var ge_table_y          = 0;

var ge_is_empty         = null;
var ge_range            = null;
var ge_image_preview    = null;
var ge_is_image         = false;

var ge_notag            = false;
var ge_nomode           = false;
var ge_noimg            = false;

var ge_color = [
"#FFFFFF","#FFCCCC","#FFCC99","#FFFF99","#FFFFCC",
"#99FF99","#99FFFF","#CCFFFF","#CCCCFF","#FFCCFF",
"#CCCCCC","#FF6666","#FF9966","#FFFF66","#FFFF33",
"#66FF99","#33FFFF","#66FFFF","#9999FF","#FF99FF",
"#C0C0C0","#FF0000","#FF9900","#FFCC66","#FFFF00",
"#33FF33","#66CCCC","#33CCFF","#6666CC","#CC66CC",
"#999999","#CC0000","#FF6600","#FFCC33","#FFCC00",
"#33CC00","#00CCCC","#3366FF","#6633FF","#CC33CC",
"#666666","#990000","#CC6600","#CC9933","#999900",
"#009900","#339999","#3333FF","#6600CC","#993399",
"#333333","#660000","#993300","#996633","#666600",
"#006600","#336666","#000099","#333399","#663366",
"#000000","#330000","#663300","#663333","#333300",
"#003300","#003333","#000066","#330099","#330033"];

this.notag = function() {
    ge_notag = true;
}

this.nomode = function() {
    ge_nomode = true;
}

this.noimg = function() {
    ge_noimg = true;
}

this.get_mode = function() {
    return ge_mode;
}

this.init = function() {
    ge_editor = document.getElementById(ge_iframe).contentWindow.document;
    ge_editor.designMode = "on";
    ge_editor.write("<html>");
    ge_editor.write("<head>");
    ge_editor.write("<style type=\"text/css\">")
    ge_editor.write("body { padding:0px; margin:5px; font-size:10pt; font-family:Dotum; }");
    ge_editor.write("td { font-size:10pt; font-family:Dotum; }");
    ge_editor.write("</style>");
    ge_editor.write("</head><body>");
    ge_editor.write(ge_code);
    ge_editor.write("</body></html>");
    ge_editor.close();

    if (navigator.appName.indexOf("Microsoft") != -1)
        IS_IE = true;
    else
        IS_IE = false;

    var self    = this;
    var editor  = ge_editor;
    var name    = ge_name;

    if (IS_IE) {
        ge_editor.attachEvent("onclick", function(event) { self.eventHandler(event, editor, name); });
        ge_editor.attachEvent("onkeypress", function(event) { self.eventHandler(event, editor, name); });
        ge_editor.attachEvent("onkeyup", function(event) { self.eventHandler(event, editor, name); });

        document.getElementById(ge_iframe).contentWindow.attachEvent("onblur", function(event) { self.eventHandler(event, editor, name); });

        document.getElementById(ge_textarea).attachEvent("onchange", this.update);
        document.getElementById(ge_source).attachEvent("onchange", this.update);
    } else {
        ge_editor.addEventListener("click", function(event) { self.eventHandler(event, editor, name); }, false);
        ge_editor.addEventListener("blur",    function(event) { self.eventHandler(event, editor, name); }, false);
        ge_editor.addEventListener("keyup", this.update, false);

        document.getElementById(ge_textarea).addEventListener("change", this.update, false);
        document.getElementById(ge_source).addEventListener("change", this.update, false);
    }

    if (ge_nomode == false)
        document.getElementById(ge_name+"_geditor_html_source_button").checked = false;

    //ge_editor.body.focus();
    this.get_range();
}

this.update = function() {
    switch(ge_mode) {
        case _WYSIWYG   : ge_code = ge_editor.body.innerHTML; break;
        case _TEXT      : ge_code = document.getElementById(ge_textarea).value; break;
        case _HTML      : ge_code = document.getElementById(ge_source).value; break;
    }
    document.getElementById(ge_content).style.backgroundImage = '';
    document.getElementById(ge_content).value = ge_code;

    if (ge_code) {
        document.getElementById(ge_content).style.backgroundImage = '';
        document.getElementById(ge_content).value = ge_code;
    } else {
        ge_code = document.getElementById(ge_content).value;
        switch(ge_mode) {
            case _WYSIWYG   : ge_editor.body.innerHTML = ge_code; break;
            case _TEXT      : document.getElementById(ge_textarea).value = ge_code; break;
            case _HTML      : document.getElementById(ge_source).value = ge_code; break;
        }
    }
}

this.eventHandler = function(event, editor, ge_name) {

    if (event.type == "click")  {
        eval(ge_name + ".clear_option()");
        eval(ge_name + ".get_tags()");
    }

    if (event.type == "keypress" && IS_IE) {
        // IE 의 경우 엔터를 입력하면 <p> 가 입력되기 때문에 이를 <br> 로 변경한다.
        var range = editor.selection.createRange();
        if (event.keyCode == 13 && range.parentElement().tagName != "LI") {
            event.returnValue = false;
            event.cancelBubble = true;
            range.pasteHTML("<br />");
            range.collapse(false);
            range.select();
        }
    }

    if (event.type == "blur")
        eval(ge_name + ".update()");

    if (event.type == "keyup") {
		switch (event.keyCode) {
			case 37:
			case 38:
			case 39:
			case 40:
			case 8:
                eval(ge_name + ".get_tags()");
		 }
    }
}

this.get_range = function() {
    //ge_editor.body.focus();
    if (IS_IE) {
        ge_range = ge_editor.selection.createRange();
    } else {
        ge_range = document.getElementById(ge_iframe).contentWindow.getSelection();
    }

    return ge_range;
}

this.get_tags = function() {
    var _parent = null;
    var ancestors = [];

    if (IS_IE)
    {
        var sel = ge_editor.selection;
        var rng = sel.createRange();

        if (sel.type == "Text" || sel.type == "None")
            _parent = rng.parentElement();
        else if (sel.type == "Control")
            _parent = rng.item(0);
        else
            _parent = ge_editor.document.body;
    }
    else
    {
        var sel = document.getElementById(ge_iframe).contentWindow.getSelection();
        var rng = sel.getRangeAt(0);

        _parent = rng.commonAncestorContainer;
        if (!rng.collapsed && rng.startContainer == rng.endContainer &&
            rng.startOffset - rng.endOffset < 2 && rng.startContainer.hasChildNodes())
        {
            _parent = rng.startContainer.childNodes[rng.startOffset];
        }

        while (_parent.nodeType == 3) {
            _parent = _parent.parentNode;
        }
    }
    while (_parent && (_parent.nodeType == 1) && (_parent.tagName.toLowerCase() != 'body')) {
        ancestors.push(_parent);
        _parent = _parent.parentNode;
    }

    ancestors.push(ge_editor.body);

    var path = '&nbsp;&lt;BODY&gt; ';

    for (var i = ancestors.length; --i >= 0;) {
        el = ancestors[i];
        if (!el || el.tagName.toUpperCase() == 'HTML' || el.tagName.toUpperCase() == 'BODY')
            continue;
        path += '&lt;<span style="text-decoration:underline">'+el.tagName.toUpperCase()+'</span>&gt; ';
    }

    if (ge_notag == false)
        document.getElementById("geditor_"+ge_name+"_path").innerHTML = path;
}

this.edit = function(key, value) {

    if (ge_mode!=_WYSIWYG)
        return;

    if (typeof value == 'undefined')
        value = null;

    ge_editor.body.focus();
    ge_editor.execCommand(key, false, value);

    this.update();
    this.get_tags();
}

this.text2html = function() {
    ge_code = document.getElementById(ge_textarea).value;
    ge_code = ge_code.replace(new RegExp("\n", "gi"), "<br />");
    ge_code = ge_code.replace(new RegExp("<br /><TBODY", "gi"), "<TBODY");
    ge_code = ge_code.replace(new RegExp("<br /></TBODY", "gi"), "</TBODY");
    ge_code = ge_code.replace(new RegExp("<br /><TR", "gi"), "<TR");
    ge_code = ge_code.replace(new RegExp("<br /></TR", "gi"), "</TR");
    ge_code = ge_code.replace(new RegExp("<br /><TD", "gi"), "<TD");
    ge_code = ge_code.replace(new RegExp("<br /></TD", "gi"), "</TD");
}

this.html2text = function(html) {
    ge_code = html;
    ge_code = ge_code.replace(new RegExp("<P>&nbsp;</P>", "gi"), "<br />");
    ge_code = ge_code.replace(new RegExp("<P>", "gi"), "");
    ge_code = ge_code.replace(new RegExp("</P>", "gi"), "<br />");
    ge_code = ge_code.replace(new RegExp("<br>", "gi"), "<br />");
    ge_code = ge_code.replace(new RegExp("<br />", "gi"), "\n");
    ge_code = ge_code.replace(new RegExp("<br>", "gi"), "\n");
    ge_code = ge_code.replace(new RegExp("\n\r", "gi"), "");
}

this.mode_change = function() {

    this.clear_option();

    switch(ge_mode) {

        case _WYSIWYG:
            ge_mode = _TEXT;
            this.html2text(ge_editor.body.innerHTML);
            document.getElementById(ge_iframe).style.display = 'none';
            document.getElementById(ge_iframe).style.display = 'none';
            document.getElementById(ge_source).style.display = 'none';
            document.getElementById(ge_textarea).style.display = 'block';
            document.getElementById(ge_textarea).value = ge_code;
            document.getElementById(ge_name+"_geditor_html_source_button").checked = false;
            document.getElementById(ge_name+"_geditor_html_source_div").style.display = 'none';
            document.getElementById(ge_name+"_geditor_status").value = _TEXT;
            //document.getElementById(ge_name+"_geditor_toolbar").style.visibility = 'hidden';
            document.getElementById(ge_name+"_geditor_toolbar").style.display = 'none';
            break;

        case _TEXT:
            ge_mode = _WYSIWYG;
            this.text2html();
            document.getElementById(ge_textarea).value = '';
            document.getElementById(ge_iframe).style.display = 'block';
            document.getElementById(ge_source).style.display = 'none';
            document.getElementById(ge_textarea).style.display = 'none';
            document.getElementById(ge_name+"_geditor_html_source_div").style.display = 'block';
            document.getElementById(ge_name+"_geditor_html_source_button").checked = false;
            document.getElementById(ge_name+"_geditor_status").value = _WYSIWYG;
            //document.getElementById(ge_name+"_geditor_toolbar").style.visibility = 'visible';
            document.getElementById(ge_name+"_geditor_toolbar").style.display = 'block';
            this.init();
            break;

        case _HTML:
            ge_mode = _TEXT;
            this.html2text(document.getElementById(ge_source).value);
            document.getElementById(ge_source).value = '';
            document.getElementById(ge_iframe).style.display = 'none';
            document.getElementById(ge_source).style.display = 'none';
            document.getElementById(ge_textarea).style.display = 'block';
            document.getElementById(ge_textarea).value = ge_code;
            document.getElementById(ge_name+"_geditor_html_source_button").checked = false;
            document.getElementById(ge_name+"_geditor_html_source_div").style.display = 'none';
            document.getElementById(ge_name+"_geditor_status").value = _TEXT;
            //document.getElementById(ge_name+"_geditor_toolbar").style.visibility = 'hidden';
            document.getElementById(ge_name+"_geditor_toolbar").style.display = 'none';
            break;
    }
}

this.html_source = function(code) {
    code = code.replace(new RegExp("<P>&nbsp;</P>", "gi"), "<br />");
    code = code.replace(new RegExp("<P>", "gi"), "");
    code = code.replace(new RegExp("</P>", "gi"), "<br />");
    code = code.replace(new RegExp("<br>", "gi"), "<br />");
    return code;
}

this.mode_source = function(flag) {
    if (flag==true) {
        ge_code = this.html_source(ge_editor.body.innerHTML);
        document.getElementById(ge_iframe).style.display = 'none';
        document.getElementById(ge_source).style.display = 'block';
        document.getElementById(ge_source).value = ge_code;
        document.getElementById(ge_name+"_geditor_status").value = _WYSIWYG;
        //document.getElementById(ge_name+"_geditor_toolbar").style.visibility = 'hidden';
        document.getElementById(ge_name+"_geditor_toolbar").style.display = 'none';
        ge_mode = _HTML;
    } else {
        ge_code = document.getElementById(ge_source).value;
        ge_mode = _WYSIWYG;
        document.getElementById(ge_source).value = '';
        document.getElementById(ge_iframe).style.display = 'block';
        document.getElementById(ge_source).style.display = 'none';
        //document.getElementById(ge_name+"_geditor_toolbar").style.visibility = 'visible';
        document.getElementById(ge_name+"_geditor_toolbar").style.display = 'block';
        this.init();
    }
}

this.run = function() {

    var content = document.getElementById(ge_content);

    if (content.style.width)
        ge_width = content.style.width;
    else if (content.offsetWidth)
        ge_width = content.offsetWidth;
    else if (content.cols)
        ge_width = content.cols*6.5;

    if (content.style.height)
        ge_height = content.style.height;
    else if (content.offsetHeight)
        ge_height = content.offsetHeight;
    else if (content.cols)
        ge_height = content.rows*20;

    ge_code = content.value;

    var div = document.createElement('div');
    draw = "<table id="+ge_name+"_outline border=0 cellpadding=0 cellspacing=0 width="+ge_width+"><tr><td valign=top>";
    draw += "<div>";
    draw += "<span style='cursor:pointer;' onclick=\""+ge_name+".height_decrease(100);\"><img src=\""+ge_icon_path+"/up.gif\" border=0></span>";
    draw += "<span style='cursor:pointer; margin-left:2px;' onclick=\""+ge_name+".height_original();\"><img src=\""+ge_icon_path+"/start.gif\" border=0></span>";
    draw += "<span style='cursor:pointer; margin-left:2px;' onclick=\""+ge_name+".height_increase(100);\"><img src=\""+ge_icon_path+"/down.gif\" border=0></span>";
    draw += "</div>";
    draw += "<div style=\"border:1px solid #ccc;\">";
    draw += "<div id="+ge_name+"_geditor_toolbar style=\"width:"+ge_width+"px; border-bottom:1px solid #ccc;\">";
    draw += "<div style=\"border:1px solid #fff; height:30px; background:url("+ge_icon_path+"/btn-bg.gif);\">";
    draw += "<div style=\"margin:4px 0 0 4px;\">";
    draw += "<span title=\"글꼴\" style=\"cursor:pointer;\" onclick=\""+ge_name+".font_family(this)\"><img src=\""+ge_icon_path+"/font.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"크기\" style=\"cursor:pointer;\" onclick=\""+ge_name+".font_size(this)\"><img src=\""+ge_icon_path+"/size.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"굵게\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('bold')\"><img src=\""+ge_icon_path+"/bold.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"글자색\" style=\"cursor:pointer;\" onclick=\""+ge_name+".insert_color(this,'fore')\"><img src=\""+ge_icon_path+"/forecolor.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"배경색\" style=\"cursor:pointer;\" onclick=\""+ge_name+".insert_color(this,'back')\"><img src=\""+ge_icon_path+"/backcolor.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"박스넣기\" style=\"cursor:pointer;\" onclick=\""+ge_name+".insert_box(this)\"><img src=\""+ge_icon_path+"/box.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"기울기\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('italic')\"><img src=\""+ge_icon_path+"/italic.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"밑줄\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('underline')\"><img src=\""+ge_icon_path+"/underline.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"가운데줄\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('strikethrough')\"><img src=\""+ge_icon_path+"/strike.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"왼쪽 정렬\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('JustifyLeft')\"><img src=\""+ge_icon_path+"/justifyleft.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"가운데 정렬\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('JustifyCenter')\"><img src=\""+ge_icon_path+"/justifycenter.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"오른쪽 정렬\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('JustifyRight')\"><img src=\""+ge_icon_path+"/justifyright.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"양쪽 정렬\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('JustifyFull')\"><img src=\""+ge_icon_path+"/justifyfull.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"줄간격\" style=\"cursor:pointer;\" onclick=\""+ge_name+".line_height(this)\"><img src=\""+ge_icon_path+"/line-height.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"숫자 목록\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('insertorderedlist')\"><img src=\""+ge_icon_path+"/orderedlist.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"점 목록\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('insertunorderedlist')\"><img src=\""+ge_icon_path+"/unorderedlist.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"내어쓰기\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('Outdent')\"><img src=\""+ge_icon_path+"/outdent.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"들여쓰기\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('Indent')\"><img src=\""+ge_icon_path+"/indent.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"링크넣기\" style=\"cursor:pointer;\" onclick=\""+ge_name+".insert_link(this)\"><img src=\""+ge_icon_path+"/link.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"링크삭제\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('UnLink')\"><img src=\""+ge_icon_path+"/unlink.gif\" align=\"absmiddle\"></span>";
    draw += "<span title=\"미디어\" style=\"cursor:pointer;\" onclick=\""+ge_name+".insert_movie(this)\"><img src=\""+ge_icon_path+"/media.gif\" align=\"absmiddle\"></span>";
    if (ge_noimg == false) {
    draw += "<span title=\"그림넣기\" style=\"cursor:pointer;\" onclick=\""+ge_name+".insert_image(this)\"><img src=\""+ge_icon_path+"/image.gif\" align=\"absmiddle\" width=\"19\" height=\"20\"></span>";
    }
    draw += "<span title=\"테이블 만들기\" style=\"cursor:pointer;\" onclick=\""+ge_name+".insert_table(this)\"><img src=\""+ge_icon_path+"/table.gif\" align=\"absmiddle\" width=\"19\" height=\"20\"></span>";
    draw += "<span title=\"표정 아이콘\" style=\"cursor:pointer;\" onclick=\""+ge_name+".insert_emoticon(this)\"><img src=\""+ge_icon_path+"/em.gif\" align=\"absmiddle\" width=\"19\" height=\"20\"></span>";
    draw += "<span title=\"속성 제거\" style=\"cursor:pointer;\" onclick=\""+ge_name+".edit('RemoveFormat')\"><img src=\""+ge_icon_path+"/removeformat.gif\" align=\"absmiddle\" width=\"20\" height=\"20\"></span>";
    draw += "</div>";
    draw += "</div>";
    draw += "</div>";
    draw += "<div id=geditor_"+ge_name+" style=\"width:"+ge_width+"px;height:"+(parseInt(ge_height)+5)+"px;\">";
    draw += "<iframe id=\""+ge_iframe+"\" style=\"width:100%;height:"+ge_height+"px;border:0;padding:0px;margin:0px;\" frameborder=0></iframe>";
    draw += "<textarea id=\""+ge_textarea+"\" style=\"width:100%;height:"+ge_height+"px;display:none;border:0;padding:5px;margin:0px;font-size:12px;font-family:Gulim;line-height:20px;word-break:break-all;\"></textarea>";
    draw += "<textarea id=\""+ge_source+"\" style=\"width:100%;height:"+ge_height+"px;display:none;border:0;padding:5px;margin:0px;font-size:12px;font-family:Gulim;line-height:20px;word-break:break-all;\"></textarea>";
    draw += "</div>";
    if (ge_notag == false) {
    draw += "<div id=geditor_"+ge_name+"_path style=\"overflow:hidden;line-height:25px;font-size:11px;margin-top:5px;width:"+ge_width+"px;height:25px;border-top:1px solid #ccc; background-color:#F9F9F9;\"></div>";
    }
    draw += "</div>";
    if (ge_nomode == false) {
    draw += "<div style=\"height:30px; margin:5px 0 0 0; background:url("+ge_icon_path+"/mode-bg.gif);\">";
    draw += "<div style=\"background:url("+ge_icon_path+"/mode-right.gif) top right no-repeat;\">";
    draw += "<input type=hidden id="+ge_name+"_geditor_status>";
    draw += "<span id="+ge_name+"_geditor_html_source_div style=\"font-size:11px; float:right; margin:5px 10px 0 0; color:#333;\"><input type=checkbox id="+ge_name+"_geditor_html_source_button onclick=\""+ge_name+".mode_source(this.checked)\">html</span>";
    draw += "<img src=\""+ge_icon_path+"/mode-change.gif\" style=\"cursor:pointer;\" onclick=\""+ge_name+".mode_change();\" align=\"absmiddle\">";
    draw += "<img src=\""+ge_icon_path+"/html-preview.gif\" style=\"cursor:pointer;\" onclick=\""+ge_name+".html_preview();\" align=\"absmiddle\">";
    draw += "</div></div>";
    }
    draw += "</td></tr></table>";
    draw += "<iframe name=\"geditor_"+ge_name+"_hidden_frame\" border=0 frameborder=0 width=0 height=0></iframe>";
    draw += "</div>";
    div.innerHTML = draw;
    document.getElementById(ge_content).parentNode.insertBefore(div, document.getElementById(ge_content));
    document.getElementById(ge_content).style.backgroundImage = '';
    document.getElementById(ge_content).style.display = 'none';
    this.init();
}

this.font_family = function(obj) {
    var font_kor  = ['Gulim', 'GulimChe', 'Dotum', 'DotumChe', 'Batang', 'BatangChe', 'Gungsuh', 'GungsuhChe'];
    var font_kori = ['굴림', '굴림체', '돋움', '돋움체', '바탕', '바탕체', '궁서', '궁서체'];
    var font_eng  = ['Verdana', 'Tahoma', 'Arial', 'Arial Black', 'Courier', 'Times New Roman'];

    var kor = '가나다라마바사';
    var eng = 'ABCDEFGHIJKLMN';

    this.get_range();
    this.clear_option();

    var div = this.get_option_div(obj);
    div.id = "geditor_option_div";

    for(var i=0; i<font_kor.length; i++) {
        var list = document.createElement('div');
        var btn = this.font_family_button()
        btn.style.fontFamily = font_kor[i];
        btn.onclick = new Function(ge_name + ".font_family_change('" + font_kor[i] + "')");
        btn.value = kor + " (" + font_kori[i] + ")";
        list.appendChild(btn);
        div.appendChild(list);
    }
    for(var i=0; i<font_eng.length; i++) {
        var list = document.createElement('div');
        var btn = this.font_family_button()
        btn.style.fontFamily = font_eng[i];
        btn.onclick = new Function(ge_name + ".font_family_change('" + font_eng[i] + "')");
        btn.value = eng + " (" + font_eng[i] + ")";
        list.appendChild(btn);
        div.appendChild(list);
    }
    document.body.appendChild(div);
}

this.font_family_button = function() {
    var btn = document.createElement('input');
    btn.type = 'button';
    btn.style.fontSize = '12px';
    btn.style.cursor = 'pointer';
    btn.style.border = '0';
    btn.style.backgroundColor = '#ffffff';
    btn.style.textAlign = 'center';
    btn.style.width = '250px';
    btn.style.height = '20px';
    btn.onmouseover = function() { this.style.backgroundColor = '#efefef'; }
    btn.onmouseout = function() { this.style.backgroundColor = '#ffffff'; }
    return btn;
}

this.font_family_change = function(font) {
    this.edit('fontName', font);
    this.clear_option();
}

this.font_size = function(obj) {
    this.get_range();
    this.clear_option();

    var div = this.get_option_div(obj);
    div.id = "geditor_option_div";

    //var size_pt = [8, 9, 10, 12, 14, 18, 24, 36];
    var size_pt = [8, 9, 12, 14, 18, 24, 36];

    //for(var i=1; i<8; i++) {
    for(var i=1; i<7; i++) {
        var list = document.createElement('div');
        var btn = this.font_size_button()
        //btn.onclick = new Function(ge_name + ".font_size_change('" + size_pt[i-1] + "pt')");
        btn.onclick = new Function(ge_name + ".font_size_change('" + i + "')");
        btn.value = size_pt[i-1] + " pt";
        list.appendChild(btn);
        div.appendChild(list);
    }
    document.body.appendChild(div);
}

this.font_size_button = function() {
    var btn = document.createElement('input');
    btn.type = 'button';
    btn.style.fontSize = '12px';
    btn.style.cursor = 'pointer';
    btn.style.border = '0';
    btn.style.backgroundColor = '#ffffff';
    btn.style.textAlign = 'center';
    btn.style.width = '50px';
    btn.style.height = '20px';
    btn.onmouseover = function() { this.style.backgroundColor = '#efefef'; }
    btn.onmouseout = function() { this.style.backgroundColor = '#ffffff'; }
    return btn;
}

this.font_size_change = function(size) {
    this.edit('fontSize', size);
    this.clear_option();
    //this.insert_editor("<span style=\"font-size:" + size + ";\">", "</span>");
}

this.line_height = function(obj) {
    this.get_range();
    this.clear_option();

    var div = this.get_option_div(obj);
    div.id = "geditor_option_div";

    var size_pt = [80, 100, 120, 150, 160, 180, 200];

    for(var i=1; i<7; i++) {
        var list = document.createElement('div');
        var btn = this.line_height_button()
        btn.onclick = new Function(ge_name + ".line_height_change('" + size_pt[i-1] + "%')");
        btn.value = size_pt[i-1] + "%";
        list.appendChild(btn);
        div.appendChild(list);
    }
    document.body.appendChild(div);
}

this.line_height_button = function() {
    var btn = document.createElement('input');
    btn.type = 'button';
    btn.style.fontSize = '12px';
    btn.style.cursor = 'pointer';
    btn.style.border = '0';
    btn.style.backgroundColor = '#ffffff';
    btn.style.textAlign = 'center';
    btn.style.width = '50px';
    btn.style.height = '20px';
    btn.onmouseover = function() { this.style.backgroundColor = '#efefef'; }
    btn.onmouseout = function() { this.style.backgroundColor = '#ffffff'; }
    return btn;
}

this.line_height_change = function(size) {
    this.insert_editor("<span style=\"line-height:" + size + ";\">", "</span>");
}

this.insert_image = function(obj) {

    this.clear_option();
    this.get_range();

    var self = this;

    var div = this.get_option_div(obj, 200);
    div.id = "geditor_option_image_div";
    div.innerHTML = '<div><b>이미지 파일 입력</b></div>';

    ge_is_empty = document.createElement('input');
    ge_is_empty.type = 'hidden';
    ge_is_empty.id = 'ge_is_empty';
    ge_is_empty.value = 'true';

    div.appendChild(ge_is_empty);

    var img_div = document.createElement("div");
    img_div.style.width = '300px';
    img_div.style.height = '100px';
    img_div.style.border = '1px solid #ccc';
    img_div.style.paddingTop = '10px';
    img_div.style.paddingBottom = '10px';
    img_div.style.marginBottom = '10px';
    img_div.style.textAlign = 'center';
    img_div.style.backgroundColor = '#ccc';

    ge_image_preview = document.createElement("img");
    ge_image_preview.id = ge_name + '_image';
    ge_image_preview.style.width = '100px';
    ge_image_preview.style.height = '100px';
    ge_image_preview.style.backgroundColor = '#fff';
    ge_image_preview.src = ge_empty_path;
    ge_image_preview.onerror = function() { this.src = ge_empty_path; ge_is_empty.value = 'true'; }

    img_div.appendChild(ge_image_preview);

    var file_div = document.createElement("div");

    var form = document.createElement('form');
    form.id = "geditor_image_form";
    form.method = 'post';
    form.encoding = 'multipart/form-data';
    form.target = "geditor_"+ge_name+"_hidden_frame";
    form.action = ge_path + '/upload.php';
    form.style.margin = '0';
    form.style.padding = '0';
    form.style.fontSize = 12;
    form.innerHTML = '파일 : ';

    var obj = document.createElement('input');
    obj.type = 'hidden';
    obj.name = 'obj';
    obj.value = ge_name;
    form.appendChild(obj);

    var token = document.createElement('input');
    token.type = 'hidden';
    token.name = 'token';
    token.value = Math.floor(Math.random()*10000);
    form.appendChild(token);

    var work = document.createElement('input');
    work.id = "geditor_image_form_work";
    work.type = 'hidden';
    work.name = 'work';
    work.value = 'upload';
    form.appendChild(work);

    var input_file = document.createElement("input");
    input_file.type = 'file';
    input_file.name = 'image';
    input_file.style.height = '22px';
    input_file.size = 15;
    input_file.onchange = function() {
        if (this.value) {
            ge_is_empty.value = 'false';
            input_addr.value = 'http://';
            work.value = 'upload';
            form.submit();
        }
    }
    form.appendChild(input_file)

    var input = document.createElement("input");
    input.type = 'button';
    input.value = '삭제';
    input.onclick = function() {
        work.value = 'delete';
        form.submit();
        ge_image_preview.src = ge_empty_path;
        ge_is_empty.value = 'true';
        input_addr.value = 'http://';
    }
    form.appendChild(input);

    file_div.appendChild(form);

    var addr_div = document.createElement("div");
    addr_div.style.fontSize = 12;
    addr_div.innerHTML = '주소 : ';

    var pre = null;

    var input_addr = document.createElement("input");
    input_addr.type = 'text';
    input_addr.style.height = '22px';
    input_addr.size = 30;
    input_addr.value = 'http://';
    input_addr.onkeyup = function() {
        clearTimeout(pre);
        pre = setTimeout(function() {
            if (input_file.value && ge_image_preview.src) {
                work.value = 'delete';
                form.submit();
            }
            ge_image_preview.src = input_addr.value;
            ge_is_empty.value = 'false';
        }, 1000);
    }

    addr_div.appendChild(input_addr);

    var align_div = document.createElement("div");
    align_div.style.fontSize = 12;
    align_div.innerHTML = '정렬 : ';

    var align_select = document.createElement("select");

    align_option_items = ['기본', '좌측정렬', '중앙정렬', '우측정렬'];
    align_option_value = ['', 'left', 'center', 'right'];

    for (i=0; i<align_option_items.length; i++) {
        var align_option = document.createElement("option");
        align_option.value = align_option_value[i];
        align_option.innerHTML = align_option_items[i];
        align_select.appendChild(align_option);
    }
    align_div.appendChild(align_select);

    var info_div = document.createElement('div');
    info_div.style.paddingTop = '10px';
    info_div.style.paddingBottom = '5px';
    info_div.style.color = '#717171';
    //info_div.innerHTML = '파일업로드가 주소입력보다 우선합니다.';

    var button_div = document.createElement('div');
    button_div.style.width = '300px';
    button_div.style.textAlign = 'center';
    button_div.style.paddingTop = '10px';
    button_div.style.paddingBottom = '10px';

    var submit = this.button('확인');
    submit.onclick  = function() {
        if (ge_is_empty.value != 'true') {
            file  = ge_image_preview.src;
            where = align_select.value;
            html  = "<img src=\"" + file + "\" align=\"" + where + "\"><br/>\n";
            if (where == 'center')
                html = "<div align=\"center\">" + html + "</div>";
            ge_is_image = true;
            self.insert_editor(html);
        } else {
            self.clear_option();
        }
    }

    var close = this.button('닫기');
    close.onclick = function() {
        self.clear_option();
    }

    button_div.appendChild(submit);
    button_div.appendChild(close);

    div.appendChild(img_div);
    div.appendChild(file_div);
    div.appendChild(addr_div);
    div.appendChild(align_div);
    div.appendChild(info_div);
    div.appendChild(button_div);
    document.body.appendChild(div);
}

this.button = function(text) {
    var btn = document.createElement("input");
    btn.type = 'button';
    btn.value = text;
    btn.style.backgroundColor = '#ffffff';
    btn.style.border = '1px solid #ccc';
    btn.style.width = '40px';
    btn.style.height = '22px';
    btn.style.marginLeft = '10px';
    return btn;
}

this.insert_image_preview = function(file) {
    ge_image_preview.src = file;
}

this.insert_emoticon = function(obj) {

    this.clear_option();
    this.get_range();

    var div = this.get_option_div(obj, 250);
    div.id = "geditor_option_div";
    div.style.width = '500px';

    var info = document.createElement("div");
    info.style.fontSize = 12;
    info.innerHTML = "<b>이모티콘</b> <a href=\"javascript:"+ge_name+".clear_option();\" style=\"color:#ccc;\">[닫기]</a>";

    var emoticons = document.createElement("div");

    for (var i=1; i<=ge_emoticon_count; i++) {
        var span = document.createElement("span");
        span.style.paddingRight = '5px';

        var img = document.createElement("img");
        img.src = ge_emoticon_path + "/em" + i + ".gif";
        img.style.cursor = 'pointer';
        img.onclick = new Function(ge_name+".insert_emoticon_to_editor(\""+img.src+"\")");

        span.appendChild(img)
        emoticons.appendChild(span);
    }
    div.appendChild(info);
    div.appendChild(emoticons);
    document.body.appendChild(div);
}

this.insert_emoticon_to_editor = function(file) {
    this.insert_editor("<img src=\""+file+"\" border=0>");
}

this.insert_box = function(obj) {

    this.clear_option();
    this.get_range();

    var self = this;

    var div = this.get_option_div(obj);
    div.id = "geditor_option_div";
    div.style.width = '200px';

    var info = document.createElement("div");
    info.style.fontSize = 12;
    info.innerHTML = "<b>박스</b> <a href=\"javascript:"+ge_name+".clear_option();\" style=\"color:#ccc;\">[닫기]</a>";

    div.appendChild(info);

    var bgcolor     = [ "#FFDAED","#C9EDFF","#D0FF9D","#FAFFA9","#E4E4E4" ];
    var bordercolor = [ "#FF80C2","#71D0FF","#6FD200","#CED900","#919191" ];

    for (var i=0; i<5; i++) {
        var color = bgcolor[i];
        var box_div = document.createElement("div");
        var box = document.createElement("input");
        box.type = 'button';
        box.style.border = '1px solid ' + bordercolor[i];
        box.style.backgroundColor = bgcolor[i];
        box.style.height = '10px';
        box.style.marginBottom = '5px';
        box.style.cursor = 'pointer';
        box.style.width = '200px';
        box.style.height = '20px';
        box.value = "";
        box.onclick = new Function(ge_name+".insert_box_to_editor(\""+bgcolor[i]+"\", \""+bordercolor[i]+"\")");
        box_div.appendChild(box)
        div.appendChild(box_div);
    }
    document.body.appendChild(div);
}

this.insert_box_to_editor = function(bgcolor, bordercolor) {
    this.insert_editor("<div style=\"background-color:" + bgcolor + "; padding:5px; border:1px solid " + bordercolor + "\">", "<br/></div><br/>");
}

this.insert_movie = function(obj) {

    this.clear_option();
    this.get_range();

    var self = this;
    var div = this.get_option_div(obj, 100);
    div.id = "geditor_option_div";

    var info = document.createElement("div");
    info.style.fontSize = '12px';
    info.style.border = '1px solid #ccc';
    info.style.padding = '5px';
    info.style.marginBottom = '10px';
    info.innerHTML = '<b>무비클립 주소 넣기</b>';

    var src_div = document.createElement("div");

    var info2 = document.createElement("span");
    info2.style.fontSize = '12px';
    info2.innerHTML = '주소 : ';

    var src = document.createElement("input");
    src.size = 25;
    src.style.fontSize = '12px';
    src.id = 'geditor_' + ge_name + '_insert_movie_src';

    src_div.appendChild(info2);
    src_div.appendChild(src);

    var size_div = document.createElement("div");
    size_div.style.fontSize = '12px';

    var info3 = document.createElement("span");
    info3.innerHTML = '가로 : ';

    var info4 = document.createElement("span");
    info4.innerHTML = '세로 : ';
    info4.style.marginLeft = '10px';

    var width = document.createElement("input");
    width.size = 5;
    width.id = 'geditor_' + ge_name + '_insert_movie_width';

    var height = document.createElement("input");
    height.size = 5;
    height.id = 'geditor_' + ge_name + '_insert_movie_height';

    size_div.appendChild(info3);
    size_div.appendChild(width);
    size_div.appendChild(info4);
    size_div.appendChild(height);

    var button = document.createElement("div");
    button.style.paddingTop = '10px';
    button.style.paddingBottom = '5px';
    button.style.textAlign = 'center';

    var submit = this.button('확인');
    submit.onclick  = function()
    {
        alert("위지윅 에디터에서는 정상적으로 보이지 않을 수 있습니다.\n\n미디어 삽입시 HTML source 모드 혹은 TEXT 모드로 꼭 확인해주세요.");
        var html = "<embed src=\""+src.value+"\" autostart=\"true\" loop=\"true\" width=\""+width.value+"\" height=\""+height.value+"\"></embed>";
        self.insert_editor(html);
    }
    var close = this.button('닫기');
    close.onclick  = function() { self.clear_option(); }

    button.appendChild(submit);
    button.appendChild(close);

    info.appendChild(src_div);
    info.appendChild(size_div);
    info.appendChild(button);

    div.appendChild(info);

    var info = document.createElement("div");
    info.style.fontSize = '12px';
    info.style.border = '1px solid #ccc';
    info.style.padding = '5px';

    info.innerHTML = '<b>무비클립 HTML 넣기</b>';

    var input_div = document.createElement("div");

    var input = document.createElement("textarea");
    input.cols = 30;
    input.rows = 3;
    input.style.fontSize = '12px';

    input_div.appendChild(input);

    var button = document.createElement("div");
    button.style.paddingTop = '10px';
    button.style.paddingBottom = '5px';
    button.style.textAlign = 'center';

    var submit = this.button('확인');
    submit.onclick  = function() {
        alert("위지윅 에디터에서는 정상적으로 보이지 않을 수 있습니다.\n\n미디어 삽입시 HTML source 모드 혹은 TEXT 모드로 꼭 확인해주세요.");
        self.insert_editor(input.value);
    }

    var close = this.button('닫기');
    close.onclick = function() { self.clear_option(); }

    button.appendChild(submit);
    button.appendChild(close);

    info.appendChild(input_div);
    info.appendChild(button);

    div.appendChild(info);

    document.body.appendChild(div);
}

this.insert_link = function(obj) {

    this.clear_option();
    this.get_range();

    var self = this;

    var div = this.get_option_div(obj, 100);
    div.id = "geditor_option_div";

    var info = document.createElement("div");
    info.style.fontSize = 12;
    info.innerHTML = '<b>링크 넣기</b>';

    var select = document.createElement("select");

    var option = document.createElement("option");
    option.value = '_blank';
    option.innerHTML = '새창';
    select.appendChild(option);

    var option = document.createElement("option");
    option.value = '_self';
    option.innerHTML = '현재창';
    select.appendChild(option);

    var protocol = document.createElement("select");

    var protocol_list = ['http://', 'ftp://', 'mailto:']
    for (i=0; i<protocol_list.length; i++) {
        var option = document.createElement("option");
        option.value = protocol_list[i];
        option.innerHTML = protocol_list[i];
        protocol.appendChild(option);
    }

    var input = document.createElement("input");
    input.size = 20;
    input.type = _TEXT;

    var submit = this.button('확인');
    submit.onclick = function() {
        input.value = input.value.replace(/http\:\/\//gi,'');
        input.value = input.value.replace(/ftp\:\/\//gi,'');
        input.value = input.value.replace(/mailto\:/gi,'');
        self.insert_editor("<a href=\"" + protocol.value + input.value + "\" target=\"" + select.value + "\">", "</a>");
    }

    var close = this.button('닫기');
    close.onclick  = function() { self.clear_option(); }

    div.appendChild(info);
    div.appendChild(select);
    div.appendChild(protocol);
    div.appendChild(input);
    div.appendChild(submit);
    div.appendChild(close);
    document.body.appendChild(div);
}

this.insert_table = function(obj) {

    this.clear_option();
    this.get_range();

    var self = this;
    var div = this.get_option_div(obj, 50);
    div.id = "geditor_option_div";

    var info = document.createElement("div");
    info.style.fontSize = 12;
    info.innerHTML = "<b>테이블</b> <a href=\"javascript:"+ge_name+".clear_option();\" style=\"color:#ccc;\">[닫기]</a>";

    var table = document.createElement("table");
    table.border = 0;
    table.cellSpacing = 2;
    table.id = 'geditor_insert_table_table';
    table.unselectable = "on";
    table.style.cursor = 'pointer';

    var tbody = document.createElement("tbody");
    tbody.id = 'geditor_insert_table_tbody';

    for (var i=0; i<ge_table_rows; i++){
        var tr = document.createElement("tr");
        tr.height = 10;
        tr.id = "tr"+i;
        for (var j=0; j<ge_table_cols; j++) {
            var td = document.createElement("td");
            td.style.width = '20px';
            td.style.height = '20px';
            td.style.border = '1px solid #ccc';
            td.style.fontSize = '10px';
            td.onmouseover = new Function(ge_name+".insert_table_mouse_over("+(i+1)+","+(j+1)+")");
            td.id = "td"+i+""+j;

            var img = document.createElement("img");
            img.src = ge_empty_path;
            img.style.width = '20px';
            img.style.height = '20px';
            img.onclick = new Function(ge_name+".insert_table_mouse_click("+(i+1)+","+(j+1)+")");
            td.appendChild(img);

            tr.appendChild(td);
        }
        tbody.appendChild(tr);
    }

    table.appendChild(tbody);

    div.appendChild(info);
    div.appendChild(table);
    document.body.appendChild(div);
}

this.insert_table_mouse_click = function(row,col)
{
    var table = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" bordercolor=\"#cccccc\" style=\"border-collapse:collapse\">";
    var width = Math.floor(100/col);
    for (var i=0; i<row; i++) {
        table += "<tr>";
        for (var j=0; j<col; j++) {
            table += "<td width=\""+width+"%\">&nbsp;</td>";
        }
        table += "</tr>";
    }
    table += "</table><br/>";

    this.insert_editor(table);
}

this.insert_table_mouse_over = function(row,col)
{
    var table = document.getElementById("geditor_insert_table_table");
    var rows = table.firstChild.childNodes;
    for (var a=0; a<rows.length; a++) {
        for (var b=0; b<rows[a].childNodes.length; b++) {
            rows[a].childNodes[b].bgColor = a < row && b < col ? '#EFEFEF' : '#FFFFFF';
        }
    }
    if (row == rows.length) {
        var tr = document.createElement("tr");
        tr.style.height = '10px';
        tr.id = "tr"+row;
        for (var j=0; j<rows[0].childNodes.length; j++) {
            var td = document.createElement("td");
            td.style.width = '20px';
            td.style.height = '20px';
            td.style.border = '1px solid #ccc';
            td.style.fontSize = '10px';
            td.onmouseover = new Function(ge_name+".insert_table_mouse_over("+(row+1)+","+(j+1)+")");
            td.id = "td"+row+j;

            var img = document.createElement("img");
            img.src = ge_empty_path;
            img.style.width = '20px';
            img.style.height = '20px';
            img.onclick = new Function(ge_name+".insert_table_mouse_click("+(row+1)+","+(j+1)+")");
            td.appendChild(img);

            tr.appendChild(td);
        }
        document.getElementById("geditor_insert_table_tbody").appendChild(tr);
    }
    else if (row >= ge_table_rows-1) {
        for (var i=rows.length-1; i>row; i--) {
            document.getElementById("geditor_insert_table_tbody").removeChild(document.getElementById("tr"+i));
        }
    }

    if (col == rows[0].childNodes.length)
    {
        for (var i=0; i<rows.length; i++) {
            var td = document.createElement("td");
            td.style.width = '20px';
            td.style.height = '20px';
            td.style.border = '1px solid #ccc';
            td.style.fontSize = '10px';
            td.onmouseover = new Function(ge_name+".insert_table_mouse_over("+(i+1)+","+(col+1)+")");
            td.id = "td"+i+col;

            var img = document.createElement("img");
            img.src = ge_empty_path;
            img.style.width = '20px';
            img.style.height = '20px';
            img.onclick = new Function(ge_name+".insert_table_mouse_click("+(i+1)+","+(col+1)+")");
            td.appendChild(img);

            rows[i].appendChild(td);
        }
    }
    else if (col >= ge_table_cols-1 && this.table_x > col)
    {
        for (var i=0; i<rows.length; i++) {
            id = "td"+i+""+(rows[i].childNodes.length-1);
            id = document.getElementById(id);
            document.getElementById("tr"+i).removeChild(id);
        }
    }
    this.table_x = col;
    this.table_y = row;
}

this.insert_color = function(obj, flag) {

    this.clear_option();
    this.get_range();

    var self = this;
    var div = this.get_option_div(obj);
    div.id = "geditor_option_div";

    var info = document.createElement("div");
    info.style.fontSize = 12;
    if (flag=='fore')
        info.innerHTML = "<b>글자색</b> <a href=\"javascript:"+ge_name+".clear_option();\" style=\"color:#ccc;\">[닫기]</a>";
    else
        info.innerHTML = "<b>배경색</b> <a href=\"javascript:"+ge_name+".clear_option();\" style=\"color:#ccc;\">[닫기]</a>";

    var table = document.createElement("table");
    table.border = 0;
    table.cellSpacing = 2;
    table.unselectable = "on";
    table.style.cursor = 'pointer';

    var tbody = document.createElement("tbody");

    fi = 0;
    for (var i=0; i<7; i++) {
        var tr = document.createElement("tr");
        tr.height = 10;
        tr.id = "tr"+i;
        for (var j=0; j<10; j++) {
            var td = document.createElement("td");
            td.width = 10;
            td.style.border = '1px solid #ccc';
            td.style.fontSize = '10px';
            td.style.backgroundColor = ge_color[fi];
            td.innerHTML = '&nbsp;';
            td.unselectable = "on";
            td.onclick = new Function(ge_name+"."+flag+"color(\""+ge_color[fi]+"\")");
            td.id = "td"+i+""+j;
            tr.appendChild(td);
            fi++;
        }
        tbody.appendChild(tr);
    }

    table.appendChild(tbody)

    div.appendChild(info);
    div.appendChild(table);
    document.body.appendChild(div);
}

this.forecolor = function(color) {
    this.edit('forecolor',color);
    this.clear_option();
}

this.backcolor = function(color) {
    if (IS_IE)
        this.edit('BackColor',color);
    else
        this.edit('Hilitecolor',color);

    this.clear_option();
}

this.insert_editor = function(begin, end) {

    switch(ge_mode) {

        case _WYSIWYG:
            if (typeof end == 'undefined') end = '';

            if (IS_IE) {
                ge_range.pasteHTML(begin + ge_range.htmlText + end);
                ge_range.select();
            } else  {
                var editor = document.getElementById(ge_iframe);
                var range  = editor.contentWindow.getSelection();

                editor.contentWindow.focus();

                if (range.focusNode.tagName == 'HTML') {

                    var range = editor.contentDocument.createRange();
                    range.setStart(editor.contentDocument.body,0);
                    range.setEnd(editor.contentDocument.body,0);

                    var tmp = document.createElement("div");
                    tmp.appendChild(range.extractContents());

                    range.insertNode(range.createContextualFragment(begin + tmp.innerHTML + end));
                    range.select();
                } else {
                    var range = range.getRangeAt(0);
                    var tmp = document.createElement("div");

                    tmp.appendChild(range.extractContents());

                    //range.insertNode(range.createContextualFragment(begin + tmp.innerHTML + end));
                    editor.contentWindow.document.execCommand("insertHTML", false, begin + tmp.innerHTML + end);
                }
            }
            this.update();
            break;

        case _TEXT:
            document.getElementById(ge_textarea).value += begin;
            break;

        case _HTML:
            document.getElementById(ge_source).value += begin;
            break;
    }
    this.clear_option();
}

this.get_option_div = function(obj, left) {

	ge_editor.body.focus();
	this.get_range();

    if (IS_IE) height = -1; else height = 5;

    if (typeof left == 'undefined') left = 0;

    var div = document.createElement("div");
    div.style.border = "#CCCCCC 1px solid";
    div.style.padding = "10px";
    div.style.display = "block";
    div.style.position = "absolute";
    div.style.zIndex = 1;
    div.style.backgroundColor = "#FFFFFF";
    div.style.top = this.get_top(obj) + obj.offsetHeight + height + 'px';
    div.style.left = this.get_left(obj) - left + 'px';
    div.style.textAlign = "left";
    div.style.fontSize = '12px';
    div.unselectable = "on";
    return div;
}

this.html_preview = function() {
    switch(ge_mode) {
        case _WYSIWYG:
            ge_code = ge_editor.body.innerHTML;
            break;
        case _TEXT:
            this.text2html();
            break;
        case _HTML:
            ge_code = document.getElementById(ge_name+"_source").value;
            break;
    }
    var pre = window.open('', 'pre', 'scrollbars=yes,width=600,height=500');
    pre.document.write(ge_code);
}

this.get_content = function() {

    switch(ge_mode) {

        case _WYSIWYG:
            return ge_editor.body.innerHTML;
            break;

        case _TEXT:
            this.text2html();
            this.init();
            return ge_editor.body.innerHTML;
            break;

        case _HTML:
            ge_mode = _TEXT;
            return document.getElementById(ge_name+"_source").value;
            break;
    }
}

this.clear_option = function () {
    if (document.getElementById("geditor_option_div") != null)  {
        document.body.removeChild(document.getElementById("geditor_option_div"));
        this.get_tags();
    }

    if (document.getElementById("geditor_option_image_div") != null) {
        if (ge_is_image == false) {
            if (document.getElementById('ge_is_empty') != null) {
                if (document.getElementById('ge_is_empty').value == 'false') {
                    document.getElementById("geditor_image_form_work").value = 'delete';
                    document.getElementById("geditor_image_form").submit();
                }
            }
        }
        document.body.removeChild(document.getElementById("geditor_option_image_div"));
        ge_is_image = false;
    }
}

this.get_ext = function(file) {
    var ext = '';
    ext = file.split(".");
    ext = ext[ext.length-1].toLowerCase();
    return ext;
}

this.get_image_size = function(path) {
    var size    = new Array();
    var image   = new Image();
    image.src   = path;
    size[0]     = image.width;
    size[1]     = image.height;
    return size;
}

this.get_top = function(obj) {
    var top = obj.offsetTop;
    var parent = obj.offsetParent;
    while(parent) {
        top += parent.offsetTop;
        parent = parent.offsetParent;
    }
    return top;
}

this.get_left = function(obj) {
    var left = obj.offsetLeft;
    var parent = obj.offsetParent;
    while(parent) {
        left += parent.offsetLeft;
        parent = parent.offsetParent;
    }
    return left;
}


this.height_decrease = function(row)
{
    switch(ge_mode) {
        case _WYSIWYG:
            o = parseInt(document.getElementById(ge_iframe).offsetHeight);
            break;
        case _TEXT:
            o = parseInt(document.getElementById(ge_textarea).offsetHeight);
            break;
        case _HTML:
            o = parseInt(document.getElementById(ge_source).offsetHeight);
            break;
    }
    h = o - row;

    if (h > 0) {
        document.getElementById("geditor_"+ge_name).style.height = h + 5 + 'px';
        document.getElementById(ge_iframe).style.height = h + 'px';
        document.getElementById(ge_textarea).style.height = h + 'px';
        document.getElementById(ge_source).style.height = h + 'px';
    }
}

this.height_original = function()
{
    document.getElementById("geditor_"+ge_name).style.height = parseInt(ge_height) + 5 + 'px';
    document.getElementById(ge_iframe).style.height = ge_height;
    document.getElementById(ge_textarea).style.height = ge_height;
    document.getElementById(ge_source).style.height = ge_height;
}

this.height_increase = function(row)
{
    switch(ge_mode) {
        case _WYSIWYG:
            h = parseInt(document.getElementById(ge_iframe).offsetHeight) + row;
            break;
        case _TEXT:
            h = parseInt(document.getElementById(ge_textarea).offsetHeight) + row;
            break;
        case _HTML:
            h = parseInt(document.getElementById(ge_source).offsetHeight) + row;
            break;
    }
    document.getElementById("geditor_"+ge_name).style.height = h + 5 + 'px';
    document.getElementById(ge_iframe).style.height = h + 'px';
    document.getElementById(ge_textarea).style.height = h + 'px';
    document.getElementById(ge_source).style.height = h + 'px';
}

this.set_ge_code = function (str) {
    ge_code = str;
}

this.get_ge_code = function (str) {
    return ge_code;
}
} // end class geditor


function geditor_load() {

	geditor_textareas = document.getElementsByTagName("textarea");

	for (i=0; i<geditor_textareas.length; i++)
	{
		geditor_run = geditor_textareas[i];

		if (geditor_run.getAttribute("geditor") != null && geditor_run.style.display.toLowerCase() != 'none' && geditor_run.style.visibility.toLowerCase() != 'hidden' && !geditor_run.readOnly && !geditor_run.disabled)
		{
			if (!geditor_run.id)
				geditor_run.id = geditor_run.name;

			gtag = geditor_run.getAttribute("gtag");
			gmode = geditor_run.getAttribute("gmode");
			gimg = geditor_run.getAttribute("gimg");

			eval("geditor_" + geditor_run.id + " = new geditor('" + geditor_run.id + "');");

			if (gtag == 'off')
				eval("geditor_" + geditor_run.id + ".notag();");

			if (gmode == 'off')
				eval("geditor_" + geditor_run.id + ".nomode();");

			if (gimg == 'off')
				eval("geditor_" + geditor_run.id + ".noimg();");

			eval("geditor_" + geditor_run.id + ".run();");
		}
	}
}

geditor_load();
