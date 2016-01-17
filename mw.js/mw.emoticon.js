
$("button[name=btn_emoticon]").click(function () {
    event.stopPropagation();
    win_emoticon();
});

$('html').click(function() {
    emoticon_close();
});

$(document).keyup(function(e) {
    if (e.keyCode == 27) emoticon_close();
});


function win_emoticon(dir) {
    var url = '';
    url = board_skin_path + '/mw.proc/mw.emoticon.skin.php?bo_table=' + bo_table;
    if (typeof dir != 'undefined') {
        url += '&dir=' + dir;
    }

    emoticon_close();
    $("body").append('<div id="win_emoticon"></div>');
    $("#win_emoticon").load(url, function () {
        $("#win_emoticon > select[name=dir]").change(function () {
            var sel = $(this).val();
            win_emoticon(sel);
        });
    });
    $("#win_emoticon").click(function(event){
        event.stopPropagation();
    });
}
function emoticon_close() {
    $('#win_emoticon').remove();
}

function emoticon_add(img) {
    $("#wr_content").val($("#wr_content").val() + '\n[e:' + img + ']\n');
    $("#win_emoticon").remove();
}
