/**
 * 스마트알람 (Smart-Alarm for Gnuboard4)
 *
 * Copyright (c) 2011 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

$("#moa_alarm").click(function () {
    if ($("#moa_box").css('display') == 'block') {
        $("#moa_box").css('display', 'none');
    }
    else {
        $("#moa").html("<img src='"+mw_moa_path+"/img/loading.gif'>");

        var left = $(this).position().left;
        var top = $(this).position().top;

        $.get(mw_moa_path + "/ajax.php", { now_path:g4_path }, function (val) {

            //$("#moa_count").text(0);
            $("#moa_alarm").append($("#moa_box"));
            $("#moa_box").css('display', 'block');
            $("#moa_box").css('left', left-350);
            $("#moa_box").css('top', top+20);
            $("#moa").html(val);
            mw_moa_ui();
        });
        $(document).one("click", function () {
            $("#moa_box").css('display', 'none');
        });
    }
    return false;
});

function mw_moa_ui()
{
    moa_font_color = '#444';
    moa_name_color = '#3B5998';
    moa_over_color = '#6d84b4';
    moa_bg_color = '#fff';

    $('#moa .moa_item').mouseover(function () {
        $(this).css('background-color', moa_over_color);
        $(this).css('color', moa_bg_color);
        $(this).find('.name').css('color', moa_bg_color);
    });

    $('#moa .moa_item').mouseout(function () {
        $(this).css('background-color', moa_bg_color);
        $(this).css('color', moa_font_color);
        $(this).find('.name').css('color', moa_name_color);
    });
}

function mw_moa_count()
{
    $.get(mw_moa_path + "/count.php", function (val) {
        if (val > 0) {
            $("#moa_count").html("<span style='color:red; font-weight:bold;'>"+val+"</span>");
        }
        else {
            $("#moa_count").text(val);
        }
    });
    //mw_moa_time = setTimeout(mw_moa_count, 30000); // 30초
}

mw_moa_count();

