/**
 * Scroll-Top
 *
 * Copyright (c) 2011 Choi Jae-Young <www.miwit.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

var mw_scroll_top =
{
    // 시작 위치
    start_line: 100,

    // 스크롤 속도
    scroll_duration: 500,

    // 하단 여백
    bottom_margin: -150,

    container: '#mw5 .container .wrapper',
    top_button: 'top-button',
    top_button_move: function() {
        $('body, html').animate({scrollTop:0}, mw_scroll_top.scroll_duration);
    },
    top_button_control: function() {
        if ($(window).scrollTop() > mw_scroll_top.start_line) {
            $('#'+mw_scroll_top.top_button).fadeIn('slow');
        } else {
            $('#'+mw_scroll_top.top_button).fadeOut('slow');
        }
        var t = $(window).scrollTop() + $(window).height() - $('#'+mw_scroll_top.top_button).outerHeight() - 10;
        var m = $(mw_scroll_top.container).width() ? $(mw_scroll_top.container).outerHeight() - mw_scroll_top.bottom_margin : t;
        if (t >= m) t = m;

        var l = $(mw_scroll_top.container).width() ? $(mw_scroll_top.container).offset().left + $(mw_scroll_top.container).outerWidth() + 10
              : $(window).width() - $('#'+mw_scroll_top.top_button).outerWidth() - 10;

        $('#'+mw_scroll_top.top_button).css('top', t).css('left', l);
    },
    run: function() {
        $(window).ready(function () {
            top_button = $('<div id="'+mw_scroll_top.top_button+'"><i class="fa fa-chevron-up"></i></div>')
                            .css('position','absolute')
                            .css('cursor', 'pointer')
                            .css('display', 'none')
                            .click(function () { mw_scroll_top.top_button_move() } )
                            .appendTo('body');
            $(window).bind('scroll resize', function (e) { mw_scroll_top.top_button_control() } );
        });
    }
}

mw_scroll_top.run();

