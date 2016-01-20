$(document).ready(function () {
    $("#sns-follow > div").mouseenter(function () {
        $(this).find("img").animate({
            left:"10px"
        }, 300);
    });
    $("#sns-follow > div").mouseleave(function () {
        $(this).find("img").animate({
            left:"0"
        }, 300);
    });
});
