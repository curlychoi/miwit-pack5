

$("button[name=btn_special]").click(function () {
    event.stopPropagation();
    win_special();
});

$('html').click(function() {
    special_close();
});

$(document).keyup(function(e) {
    if (e.keyCode == 27) special_close();
});

function win_special(dir) {
    var url = '';
    url = board_skin_path + '/mw.proc/mw.special.characters.php';

    special_close();
    if (typeof emoticon_close == 'function') { 
        emoticon_close();
    }
    $("body").append('<div id="win_special"></div>');
    $("#win_special").load(url, function () {
        $(this).find("table td").click(function () {
            $("#wr_content").val($("#wr_content").val()+$(this).text());
            special_close();
        });
    });
    $("#win_special").click(function(event){
        event.stopPropagation();
    });
}

function special_close() {
    $('#win_special').remove();
}

function special_add(ch) {
    $("#wr_content").val($("#wr_content").val() + ch);
    $("#win_special").remove();
}

