/**
 * MW-Navbar
 *
 * Copyright (c) 2015 Choi Jae-Young <www.miwit.com>
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

var first_head_fixed = true;

var head_fixed =
{
    w: 0,
    h: 0,

    init: function ()
    {
        var css = null;

        w = parseInt($(".navbar").css("width"));
        h = parseInt($(".navbar").position().top);
        navbar_height = parseInt($(".navbar").css("height"));

        css_w = '100%';

        css  = "<style>";
        css += ".navbar-fixed { ";
        css += "    width:"+css_w+"; ";
        css += "    position:fixed; ";
        css += "    z-index:99; ";
        css += "    margin:0; ";
        css += "}";

        css += ".navbar-fixed-back { ";
        //css += "    width:"+css_w+"; ";
        css += "    font-size:0; ";
        css += "    line-height:0; ";
        css += "    clear:both; ";
        css += "    display:none; ";
        css += "    height:" + navbar_height + "px; ";
        css += "} ";

        $(".navbar").after("<div class='navbar-fixed-back'></div>");
        $("body").append(css);
    },

    run: function ()
    {
        if (first_head_fixed)
            head_fixed.init();

        sct = $(window).scrollTop();

        // normal
        if (sct < h) {
            first_head_fixed = false;
            if ($(".navbar").hasClass("navbar-fixed")) {
                $(".navbar").css("top", h-sct);
                $(".navbar").removeClass("navbar-fixed");
                $(".navbar-fixed-back").css("display", "none");
            }
        }
        // fixed
        else {
            $(".navbar").css("top", 0);

            if (first_head_fixed) {
                $(window).scrollTop(sct-h);
                first_head_fixed = false;
            }

            if (!$(".navbar").hasClass("navbar-fixed")) {
                $(".navbar").css("top", 0);
                $(".navbar").addClass("navbar-fixed");
                $(".navbar-fixed-back").css("display", "block");
            }
        }
    }
}

//$(window).on('load scroll resize mousewheel', function () { head_fixed.run(); });

$(window).ready(function () {
    $(".navbar .item, .navbar .select").mouseenter(function () {
        $(".navbar .item, .navbar .select").removeClass("underline");
        $(this).addClass("underline");
        $(".navbar .dropdown").hide();

        var $menu = $(".dropdown[data-role='"+$(this).attr("data-target")+"']");

        $menu.css("left", $(this).position().left);
        $menu.show();
    })

    $(".dropdown").mouseleave(function () {
        $(this).removeClass("underline");
    });

    $(".navbar").mouseleave(function () {
        $(".dropdown").hide();
        $(".navbar .item, .navbar .select").removeClass("underline");
    });
});

