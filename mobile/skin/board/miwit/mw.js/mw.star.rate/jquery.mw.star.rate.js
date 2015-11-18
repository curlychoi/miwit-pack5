/**
 * Star Rate Plugin for JQuery
 *
 * Copyright (c) 2014 Choi Jae-Young <www.miwit.com>
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

(function ($) {

    $.fn.mw_star_rate = function (opts)
    {
        return this.each(function () {

        var options = $.extend({}, $.fn.mw_star_rate.defaults, opts);
        var check = 0;

        $element = $(this);

        var init = function ($my) {
            var html = '';
            html += "<div class=\"mw_star_rate\">";

            for (i=0.5;i<=options.max;i+=0.5) {
                html += "<div class=\"rate\" value=\"" + i + "\"></div>";
                i += 0.5;
                html += "<div class=\"rate\" value=\"" + i.toFixed(1) + "\" style='margin-right:5px;'></div>";
            }
            if (options.grade)
                html += "<div class=\"grade\"></div>";
            html += "</div>";

            $my.html(html);
            //$my.text(html);

            $my.find(".mw_star_rate").css("width", options.max * 29 + 100);

            clear($my, options.default_value);
        };

        var clear = function ($my, v) {
            value = check;
            if (v) {
                value = v;
            }
            if (!options.half)
                value = Math.ceil(value);

            $my.find(".rate").each(function () {
                if (value > 0 && parseFloat($(this).attr("value")) <= parseFloat(value)) {
                    file = imgon($(this).attr("value"));
                }
                else {
                    file = imgoff($(this).attr("value"));
                }
                view_img($(this), file);
            });

            grade_view($my, value);
        };

        var grade_view = function ($my, g)
        {
            if (!options.grade)
                return;

            if (options.grade_power > 1)
                g = parseFloat(g) * parseFloat(options.grade_power);

            $my.parent().find(".grade").text(parseFloat(g).toFixed(2));
        }

        var view_img = function ($my, file) {
            $my.css("background", "url(" + options.path + options.star + "/" + file + ") no-repeat");
        }

        var imgoff = function (val) {
            if (val.split(".")[1] == "5") {
                file = "lf.png";
            }
            else {
                file = "rf.png";
            }
            return file;
        }

        var imgon = function (val) {
            if (val.split(".")[1] == "5") {
                file = "lo.png";
            }
            else {
                file = "ro.png";
            }
            return file;
        }

        var select = function ($my, value) {
            if (!options.half)
                value = Math.ceil(value);

            if (parseFloat($my.attr("value")) <= parseFloat(value)) {
                file = imgon($my.attr("value"))
                view_img($my, file);
            }
            else {
                file = imgoff($my.attr("value"))
                view_img($my, file);
            }
        };

        init($element);

        $element.find(".rate").mouseenter(function () {
            if (options.readonly) {
                return;
            }

            value = $(this).attr("value");

            grade_view($(this), value);

            $(this).parent().find(".rate").each(function () {
                select($(this), value); 
            });
        });

        $element.find(".rate").click(function () {
            if (options.readonly) {
                if (options.readonly_msg) {
                    alert(options.readonly_msg);
                }
                return;
            }
            check = $(this).attr("value");

            if (!options.half)
                check = Math.ceil(check);

            $("#"+options.form_id).val(check);
        });

        $element.find(".mw_star_rate").mouseleave(function () {
            if (options.readonly) {
                return;
            }
            clear($(this));
        });

        });

    };

    $.fn.mw_star_rate.defaults = {
        img_path : "mw.star.rate/img/",
        readonly : false,
        readonly_msg : "평가할 수 없습니다.",
        default_value : 0,
        max : 5,
        half : true,
        star : "star1",
        form_id : "wr_rate",
        grade : true,
        grade_power : 2
    };
})(jQuery);


