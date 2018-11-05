
$(function() {
    show_white();
});

$(window).scroll(function () {
    show_white();
});

function moveToGetApi() {
    $('#get_api').css('box-shadow', "0px 0px 10px white");
}

function outToGetApi() {
    $('#get_api').css('box-shadow', "");
}

function show_white() {
    if ($(window).scrollTop() < 50) {
        $('.header_logo').css({
            "background": "url('/static/white-logo-150-45.png') no-repeat center"
        });
        $('.header').css({
            "background": "transparent",
            "box-shadow": "0px 0px 0px #F5F5F5"
        });
        $('.header a').css({
            "color": "white"
        });
        $('.header li.active a').css({
            "color": "white",
            "border-bottom": " 2px solid white"
        });
        $('.reg_a').css({
            "color": "white",
            "background": "url('/static/index/images/head_rad.png') no-repeat center"
        });

        $('.reg_a').hover(function () {
            $('.reg_a').css({
                "color": "#333333",
                "background": "url('/static/index/images/head_rad_2.png') no-repeat center"
            })
        }, function () {
            $('.reg_a').css({
                "color": "white",
                "background": "url('/static/index/images/head_rad.png') no-repeat center"
            })
        });
    } else {
        // 页面变化
        $('.header_logo').css({
            "background": "url('/static/black-logo-200-60.png') no-repeat center"
        });
        $('.header').css({
            "background": "white",
            "box-shadow": "0px 1px 3px #F5F5F5"
        });
        $('.header a').css({
            "color": "black"
        });
        $('.header li.active a').css({
            "color": "#15bdf9",
            "border-bottom": " 2px solid #15bdf9"
        });
        $('.reg_a').css({
            "color": "#333333",
            "background": "url('/static/index/images/head_rad.png') no-repeat center"
        });

        $('.reg_a').hover(function () {
            $('.reg_a').css({
                "color": "#ffffff",
                "background": "url('/static/index/images/head_radh.png') no-repeat center"
            });
            $('.header .login_a').css({
                "color": "white"
            });
        }, function () {
            $('.reg_a').css({
                "color": "#333333",
                "background": "url('/static/index/images/head_rad.png') no-repeat center"
            })
        });
    }
}