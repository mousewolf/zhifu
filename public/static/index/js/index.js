$(function () {
    var On = false;//调解导航背景
    roll($('.container>div'));
    $(window).scroll(function () {roll($('.container>div'));});
    function roll(obj) {
        var win_height = $(window).height();
        var scroll_top = $(window).scrollTop();
        obj.each(function (i) {
            var obj_top = obj.eq(i).offset().top;
            if ((scroll_top + win_height) > obj_top) {
                obj.eq(i).addClass("anim-go anim-end");
            }
        })
    };
    $('.head_menu').click(function () {
        $('.nav').addClass('active')
        $('.nav_bg').show()
    })
    $('.nav_bg').click(function () {
        On = false;
        $('.nav').removeClass('active')
        $('.nav_bg').fadeOut();
        $('.head_reg').slideUp();
    })
    /*help页面*/
    $('.help_nav>ul>li>a').click(function () {
        $(this).parent('li').addClass('avtive').siblings().removeClass('avtive').find('.help_nav2').slideUp();
        $(this).siblings('.help_nav2').slideToggle().find('li').eq(0).addClass('active').siblings().removeClass('active');
    })
    $('.help_nav2 li').click(function () {
        $(this).addClass('active').siblings().removeClass('active');
    })
    $('.vip_left li').click(function () {
        var Index = $(this).index();
        $(this).addClass('active').siblings().removeClass('active');
        $('.vip_list>div').eq(Index).addClass('active').siblings().removeClass('active')
    })

    /*bs*/
    $('.head_user').click(function () {
        if(On==false){
            On = true;
            $('.head_reg').slideDown();
            $('.nav_bg').show().css({top:'70px'});
        }else{
            On = false;
            $('.head_reg').slideUp();
            $('.nav_bg').hide().css({top:0})
        }

    })
})