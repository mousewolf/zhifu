var YQ = new (function(){
    var that = this;
    this.create_loading = function(){
        if ($(".loading").length > 0)
        {
            $(".loading").show();
            return false;
        }
        var html = '<div class="loading"> <div><i class="fa fa-spin fa-spinner"></i></div> <p>正在加载中...请稍后...<a href="javascript:void(0)"><strong>点击关闭</strong></a></p> </div>';
        $("body").append(html);
        $(".loading strong").click();
    };
    this.loading = function(txt){
        that.create_loading();
        if (txt != undefined)
        {
            $(".loading p").text(txt);
        }
    };
    this.loaded = function(txt){
        $(".loading").hide();
    };
    this.create_fixed_alert = function(){
        if ($(".fixed-alert").length > 0)
        {
            that.show_fixed_alert();
            return false;
        }
        var html = '<div class="fixed-bg fixed-alert "><div class="fixed-dialog pay-result"> <a class="btn-close" href="javascript:;"><i class="fa fa-close"></i></a> <h3></h3> <p><a class="btn btn-success"  href="javascript:;" >确定</a></p> </div></div>';
        $("body").append(html);
        that.show_fixed_alert();
        $(".fixed-alert a.btn-close,.fixed-alert a.btn-success").click(function(){
            that.hide_alert();
        });
    };
    this.show_fixed_alert = function(){
        $(".fixed-alert").show(200);
        window.setTimeout(function(){
            var obj =  $(".fixed-alert .fixed-dialog");
            obj.css("left","-200px");
            obj.css("opacity","0");
            obj.fadeIn(200);
            obj.show();
            obj.stop().animate({left:'50%',opacity:1},500,"",function(){

            });
        },10);
    };
    var alert_auto_fade_out_timer;
    this.show_alert = function(txt,timeout,callback)
    {
        window.clearTimeout(alert_auto_fade_out_timer);
        that.create_fixed_alert();
        $(".fixed-alert h3").text(txt);
        if (timeout == undefined || timeout<=0)
        {
            timeout = 5000;
        }
        alert_auto_fade_out_timer = window.setTimeout(function(){
            if (callback!=undefined)
            {
                callback();
            }
            that.hide_alert();
        },timeout);
    };
    this.hide_alert = function(){
        window.setTimeout(function(){
            var obj =  $(".fixed-alert .fixed-dialog");
            obj.css("left","50%");
            obj.css("opacity","1");
            obj.fadeOut(200);
            obj.show();
            obj.stop().animate({left:'100%',opacity:0},300,"",function(){
                $(".fixed-alert").hide(200);
            });
        },10);
    };

    this.show_dialog = function(){

    };
    this.ajax = function(url,data,callback){
       // that.loading();
        $.ajax(url,{async:true,type:"POST",data:data,dataType:"JSON",complete:function(){
          //  that.loaded();
        },success:function(e){
            callback(true,e);
        },error:function(e){
            callback(false,e);
        }})
    };
    this.check_form = function(obj){
        var type = $(obj).attr("check-data");
        var value = $(obj).val();
        if (value=="")
        {
            return;
        }
        var result = false;
        switch (type)
        {
            case "username-login":

                var patrn= /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if (!patrn.exec(value))
                {
                    that.show_tip_ok(obj,"error","格式错误，用户名只能是邮箱！");
                }
                else
                {
                    that.ajax("/api/ajax/ajax_check_username",{username:value},function(success,e){
                        if (e.success == 1)
                        {
                            that.show_tip_ok(obj,"error","用户名不存在！");
                        }
                        else
                        {

                            that.show_tip_ok(obj,"ok","");
                        }
                    });
                }
                break;
            case "username-reg":
                var patrn=  /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if (!patrn.test(value))
                {
                    // that.show_tip_ok(obj,"error","格式错误，用户名只能是邮箱！！");
                }
                else
                {
                    // that.ajax("/api/ajax/ajax_check_username",{username:value},function(success,e){
                    //
                    //     if (e.success == 1)
                    //     {
                    //         that.show_tip_ok(obj,"ok","完成验证后，您可以使用该邮箱登录");
                    //     }
                    //     else
                    //     {
                    //         that.show_tip_ok(obj,"error","该邮箱以被使用，请更换其他邮箱。");
                    //     }
                    // });

                }
                break;
            case "password":
                var patrn=/^(\w){6,20}$/;
                if (!patrn.exec(value))
                {

                }
                else
                {
                    this.check_hack_password($(obj).val(),obj);
                }

                break;
            case "password2":
                var password_data = $(obj).attr("password-data");
                var password1 = $("#" + password_data).val();
                if (password1 == $(obj).val() && password1 != "")
                {


                }
                else
                {

                }
                break;
            default:
                return false;
        }
        return false;
    };
    this.check_hack_password = function(p,obj){
        var low = /^[0-9]{1,}$/;
        if (low.exec(p))
        {
            return;
        }
        var m = /^([0-9a-z]){1,}$/;
        if (m.exec(p))
        {
            return;
        }
        var h = /^([0-9a-zA-Z]){1,}$/;
        if (h.exec(p))
        {

            return;
        }



    };

    this.show_tip_ok = function(obj,status,txt){
        var tip = obj.parent().parent().find(".tip");
        console.log(tip.html());
        tip.removeClass("ok");
        tip.removeClass("error");
        tip.addClass(status);
        tip.html("<i class='fa'></i>" + txt);

        if (status=="ok")
        {
            tip.find(".fa").addClass("fa-check");
        }
        if (status=="error")
        {
            tip.find(".fa").addClass("fa-close");
        }
    };
    this.vaild_form = function(obj){
        var form = $(obj);
        form.find("input").blur();
        var tips = form.find(".tip");
        var success = 0;
        for (var i=0;i<tips.length;i++)
        {
            var tip = $(tips[i]);
            var icon = tip.find(".fa");
            if (!icon.hasClass("fa-check"))
            {
                that.show_alert("出错了：" + tip.text() + "");
                return false;
            }
        }
        return true;
    };
    this.refresh_check_code = function(){
        $(".check-code").attr('src',"/api/code/?t=" + Math.random());
    };
    var isOver = false;
    var hideTimer ;
    this.hide_user_menu = function(){
        window.clearTimeout(hideTimer);
        hideTimer = window.setTimeout(function(){
            if (isOver == false)
            {
                $(".top-user-info .sub-nav").slideUp();
            }
        },300);
    };
    $(function(){
        $("a.show-loading").click(function(){
            that.loading();
        });
        $("input.check-form").each(function(index,obj){
            $(this).blur(function(){
                that.check_form($(this));
            });
        });
        $(".top-user-info .username").mouseenter(function(){
            var obj = $(this);
            var sub = obj.parent().find(".sub-nav");
            sub.slideDown(500);
            sub.show();
            isOver = true;
        });
        $(".top-user-info .sub-nav").mouseenter(function() {
            isOver = true;
        });

        $(".top-user-info .sub-nav").mouseleave(function(){
            isOver = false;
            that.hide_user_menu();
        });
        $(".top-user-info .username").mouseleave(function(){
            isOver = false;
            that.hide_user_menu();
        });
        $(".check-code").click(function(){
            YQ.refresh_check_code();
        });
        $(".link_show_weixin").mouseenter(function(){
            $(this).find("img").show();
        });
        $(".link_show_weixin").mouseleave(function(){
            $(this).find("img").hide();
        });
        var video = $("video")[0];
        if (video != undefined)
        {
            video.volume = 0.2;
        }
    });
})();
//YQ.create_loading();