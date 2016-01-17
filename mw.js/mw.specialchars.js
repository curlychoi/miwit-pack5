
$("button[name=btn_special]").click(function () {
    win_special();
});

function win_special(dir) {
    var url = '';
    url = board_skin_path + '/mw.proc/mw.special.characters.php';

    special_close();
    if (typeof special_close == 'function') { 
        special_close();
    }
    $("body").append('<div id="win_special"></div>');
    $("#win_special").load(url, function () {
        $(this).find("table td").click(function () {
            $("#wr_content").val($("#wr_content").val()+$(this).text());
            special_close();
        });
        special_close_event();

        $("#win_special").mouseenter(special_close_unbind);
        $("#win_special").mouseleave(special_close_event);
    });
}

function special_close_unbind()
{
    $('html').off('click');
}

function special_close_event()
{
    $('html').one('click', function() {
        special_close();
    });
    $(document).one('keyup', function(e) {
        if (e.keyCode == 27) special_close();
    });
}

function special_close() {
    $('#win_special').remove();
}

function special_add(ch) {
    $("#wr_content").val($("#wr_content").val() + ch);
    $("#win_special").remove();
}

