
(function ($) {
    $.fn.mw_slider = function (opts) {
    return this.each(function () {
        var options = $.extend({}, $.fn.mw_slider.defaults, opts);

        var index = 0;
        var px = 0;
        var max = $(this).find("ul li").size();
        var html = $(this).find("ul").html();
        var par = '';
        var t = null;

        var $element = $(this);
        var $ul = $(this).find("ul");

        var setting = function () {
            if (options.way == 'top') {
                px = parseInt($element.css("height"));
                par = { top : '-=' + px + 'px' };
            }

            if (options.way == 'left') {
                px = parseInt($element.css("width"));
                par = { left : '-=' + px + 'px' };
                $element.css("white-space", "nowrap");
                $element.find("li").css("display", "inline-block");
            }
        }

        var run_scroll = function ($ul) {
            if (index == 0 && $ul.find("li").size() > max) {
                for (i=0; i<max; ++i) {
                    $ul.find("li").get(0).remove();
                }

                if (options.way == 'left') {
                    $ul.css("left", 0);
                }

                if (options.way == 'top') {
                    $ul.css("top", 0);
                }
            }

            if (++index % max == 0) {
                index = 0;
                $ul.append(html);
                setting();
            }
            /*$ul.delay(options.delay).animate(par, options.speed, function () {
                run_scroll($ul);
            });*/
            $ul.animate(par, options.speed, function () {
                t = setTimeout(function () { run_scroll($ul) } , options.delay);
            });
        };
        setting();
        t = setTimeout(function () { run_scroll($ul)  } , options.delay);
        //run_scroll($ul);

        $element.mouseover(function () {
            clearTimeout(t);
        });

        $element.mouseleave(function () {
            t = setTimeout(function () { run_scroll($ul); }, options.delay);
        });

    });}

    $.fn.mw_slider.defaults = {
        speed:500,
        delay:2000,
        way:'top',
    };
})(jQuery);

