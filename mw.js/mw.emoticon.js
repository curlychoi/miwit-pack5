
$("button[name=btn_emoticon]").click(function () {
    win_emoticon();
});

function win_emoticon(dir) {
    var url = '';
    url = board_skin_path + '/mw.proc/mw.emoticon.skin.php?bo_table=' + bo_table;
    if (typeof dir != 'undefined') {
        url += '&dir=' + dir;
    }

    emoticon_close();
    if (typeof special_close == 'function') { 
        special_close();
    }
    $("body").append('<div id="win_emoticon"></div>');
    $("#win_emoticon").load(url, function ()
    {
        emoticon_close_event();

        $("#win_emoticon").mouseenter(emoticon_close_unbind);
        $("#win_emoticon").mouseleave(emoticon_close_event);
    });
}

function emoticon_close_unbind()
{
    $('html').off('click');
}

function emoticon_close_event()
{
    $('html').one('click', function() {
        emoticon_close();
    });
    $(document).one('keyup', function(e) {
        if (e.keyCode == 27) emoticon_close();
    });
}

function emoticon_close() {
    $('#win_emoticon').remove();
}

function emoticon_add(img) {
    $("#wr_content").val($("#wr_content").val() + '\n[e:' + img + ']\n');
    $("#win_emoticon").remove();
}

