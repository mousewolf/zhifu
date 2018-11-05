
$(function () {
    var time = 60;
    var emailCheck = false;
    var passwordCheck = false;
    var telCheck = false;
    var verifyCheck = false;
    var sendCheck  = false;
    var checkboxCheck = true;
    var geetestCheck = false;

    function referrerShow() {
        var url = window.location.href;
        if (url.indexOf("mtid=") != -1) {
            var mtid = (((url.split("?"))[1]).split("="))[1];
            $.post('/api/front/get_member_name',
                {
                    mtid: mtid
                },
                function (data, textStatus, xhr) {
                    data = JSON.parse(data);
                    if (data.status == 1) {
                        $('.account-referrer span').html("“" + data.msg + "”");
                        $('.account-referrer').show();
                    }
                });
        }
    }

    referrerShow();

    $('#regist-email').focus();
    var handler = function (captchaObj) {
        $('#captcha-box').empty();
        captchaObj.appendTo('#captcha-box');
        captchaObj.onSuccess(function () {
            var result = captchaObj.getValidate();
            $.ajax({
                url: '/validate/gt-verify',
                type: 'POST',
                dataType: 'json',
                data: {
                    geetest_challenge: result.geetest_challenge,
                    geetest_validate: result.geetest_validate,
                    geetest_seccode: result.geetest_seccode
                }
            })
                .done(function (res) {
                    if (res.status == "success") {
                        geetestCheck = true;
                        $('#tel-cont').show();
                        $('#machine-verify-tip').hide();
                    }
                    else {
                        layer.msg("验证失败，请重新验证！");
                        geetestInit();
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        });
        window.gt = captchaObj;
    };
    var geetestInit = function () {
        $.ajax({
            url: '/validate/gt-start',
            type: 'POST',
            dataType: 'json',
        })
            .done(function (res) {

                var product = 'popup';

                initGeetest({
                    gt: res.gt,
                    challenge: res.challenge,
                    offline: !res.success,
                    new_captcha: true,

                    product: product,
                    width: "100%"
                }, handler)
            })
            .fail(function () {
                console.log("error");
            });
    };
    geetestInit();

    $(".c-checkbox").click(function (event) {
        if ($(this).attr('id') == "check") {
            $(this).attr('id', 'checked');
            $(this).removeClass('c-checkbox');
            $(this).addClass('c-checkbox-checked');
            $(".account-protocol-tip-cont").hide();
            checkboxCheck = true;
        }
        else {
            $(this).attr('id', 'check');
            $(this).removeClass('c-checkbox-checked');
            $(this).addClass('c-checkbox');
            $(".account-protocol-tip-cont").show();
            checkboxCheck = false;
        }
        registerBtnCheck();
    });

    //邮箱存在判断
    function emailFn(email) {
        var tel = $('.account-tel').val();
        email = $.trim(email);
        if (email != "") {
            if (!EmailCheck(email)) {
                $("#email-tip .c-error-icon").show();
                $("#email-tip .c-tip-text").addClass('c-error-text');
                $("#email-tip .c-tip-text").html("请输入正确的邮箱");
                $(".regist-email-checked").hide();
                $("#regist-email").addClass('account-input-error');
                emailCheck = false;
                $('#email-tip').show();
                // telephoneFn(tel);
            }
            else {
                $.post('/validate/can-use-user',
                    {
                        username: email
                    },
                    function (data, textStatus, xhr) {
                        console.log(data, textStatus, xhr);
                        //data = JSON.parse(data);
                        if (data.code == 1) {
                            $("#email-tip .c-error-icon").hide();
                            $("#email-tip .c-tip-text").removeClass('c-error-text');
                            $("#email-tip .c-tip-text").html("请输入您的邮箱地址");
                            $(".regist-email-checked").show();
                            $("#regist-email").removeClass('account-input-error');
                            emailCheck = true;
                            $('#email-tip').hide();
                        }
                        else {
                            $("#email-tip .c-error-icon").show();
                            $("#email-tip .c-tip-text").addClass('c-error-text');
                            $("#email-tip .c-tip-text").html(data.msg);
                            $(".regist-email-checked").hide();
                            $("#regist-email").addClass('account-input-error');
                            emailCheck = false;
                            $('#email-tip').show();
                        }
                        // telephoneFn(tel);
                        verifyBtnCheck();
                        registerBtnCheck();
                    });

            }
        }
        else {
            $("#email-tip .c-error-icon").show();
            $("#email-tip .c-tip-text").addClass('c-error-text');
            $(".regist-email-checked").hide();
            $("#regist-email").addClass('account-input-error');
            emailCheck = false;
            $('#email-tip').show();
        }
        registerBtnCheck();
    }

    function telephoneFn(tel) {
        if (teleCheck(tel)) {
            $.ajax({
                url: '/validate/can-use-phone',
                type: 'POST',
                dataType: 'json',
                data: {
                    phone: tel
                }
            })
                .done(function (data) {

                    if(data.code ==1){
                        $('#tel-tip').hide();
                        $('#verify-cont').show();
                        $('.account-tel').removeClass('account-tel-error');
                        $('.account-tel-cont .c-input-checked-icon').show();
                        telCheck = true;

                        if (emailCheck && time == 60) {
                            $('.account-verify-btn').removeClass('account-verify-btn-disable');
                            verifyCanSend = true;
                        }

                    }else{
                        $('#tel-tip .c-error-text').html(data.msg);
                        $('.account-tel').addClass('account-tel-error');
                        $('.account-tel-cont .c-input-checked-icon').hide();
                        $('#tel-tip').show();
                        telCheck = false;

                        $('.account-verify-btn').addClass('account-verify-btn-disable');
                        verifyCanSend = false;

                    }
                })
                .fail(function () {
                    console.log("error");
                });
        }
        else {
            $('#tel-tip .c-error-text').html("请输入正确的手机号码");
            $('.account-tel').addClass('account-tel-error');
            $('.account-tel-cont .c-input-checked-icon').hide();
            $('#tel-tip').show();
            telCheck = false;

            $('.account-verify-btn').addClass('account-verify-btn-disable');
            verifyCanSend = false;
        }

        registerBtnCheck();
    }

    $("#regist-email").bind('input propertychange', function (event) {
        var email = $(this).val();
        emailFn(email);
    });

    $("#regist-psw").focus(function (event) {
        $("#account-psw-tip").show();

        passwordTip(passwordType($("#regist-psw").val()));
        //$('.password-type-con').show();
    });
    $("#regist-psw").blur(function (event) {
        if (passwordCheck) {
            $("#account-psw-tip").hide();
            $('#regist-psw').removeClass('account-input-error');

            passwordTip(passwordType($("#regist-psw").val()));
            //$('.password-type-con').hide();
        }
        else {
            $('#regist-psw').addClass('account-input-error');
        }
        registerBtnCheck();
    });
    $("#regist-psw").bind('input propertychange', function () {
        passwordTip(passwordType($("#regist-psw").val()));

        var psw = $("#regist-psw").val();
        var check1 = false;
        var check2 = false;
        var check3 = false;

        //空格校验
        var charReg = /[^a-zA-Z0-9,\.<>/?;:'\"\[\]\{\}\|\\`~!@#\$%\^&\*\(\)_\+-=]+/;
        if (psw.length == 0) {
            $(".psw-check1 .c-tip-icon").removeClass('c-tip-icon-correct');
            check1 = false;
        }
        else if ((psw.indexOf(" ") == -1) && !charReg.test(psw)) {
            $(".psw-check1 .c-tip-icon").addClass('c-tip-icon-correct');
            check1 = true;
        }
        else {
            $(".psw-check1 .c-tip-icon").removeClass('c-tip-icon-correct');
            check1 = false;
        }

        //长度校验
        if ((psw.length >= 6) && (psw.length <= 18)) {
            $(".psw-check2 .c-tip-icon").addClass('c-tip-icon-correct');
            check2 = true;
        }
        else {
            $(".psw-check2 .c-tip-icon").removeClass('c-tip-icon-correct');
            check2 = false;
        }

        //数字、大小写字母校验
        var numAndLetterReg = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])/;
        if (numAndLetterReg.test(psw)) {
            $(".psw-check3 .c-tip-icon").addClass('c-tip-icon-correct');
            check3 = true;
        }
        else {
            $(".psw-check3 .c-tip-icon").removeClass('c-tip-icon-correct');
            check3 = false;
        }

        if (check1 && check2 && check3) {
            $(".regist-psw-checked").show();
            $('#captcha-cont').show();
            passwordCheck = true;
        }
        else {
            $(".regist-psw-checked").hide();
            passwordCheck = false;
        }
    });

    var verifyCanSend = false;
    $('.account-tel').bind('input propertychange', function (event) {
        var tel = $(this).val();
        telephoneFn(tel);
    });
    $('.account-tel').blur(function (event) {
        var tel = $(this).val();
        telephoneFn(tel);
    });

    function verifyBtnCheck() {
        if (emailCheck && telCheck) {
            $('.account-verify-btn').removeClass('account-verify-btn-disable');
            verifyCanSend = true;
        }
        else {
            $('.account-verify-btn').addClass('account-verify-btn-disable');
            verifyCanSend = false;
        }
    }

    var verifyDisabled = false;

    //发送验证码event
    $('.account-verify-btn').click(function (event) {
        var mobile = $('.account-tel').val();
        var email = $('#regist-email').val();
        if (geetestCheck) {
            $('#machine-verify-tip').hide();
            if (!verifyDisabled && verifyCanSend) {
                var inter = setInterval(function () {
                    if (time <= 0) {
                        clearInterval(inter);
                        $('.account-verify-btn').removeClass('account-verify-btn-disable');
                        $('.account-verify-btn').html("发送短信验证码");
                        time = 60;
                        verifyDisabled = false;
                    } else {
                        $('.account-verify-btn').addClass('account-verify-btn-disable');
                        $('.account-verify-btn').html(time + "s后重新发送");
                        verifyDisabled = true;
                        time = time - 1;
                    }

                }, 1000);
                $.post(
                    '/validate/sms',
                    {
                        'phone': mobile
                    },
                    function (data, textStatus, xhr) {
                        if (data.code == 1) {
                            $('#verify-tip').hide();
                            $('#verify-tip-normal').show();
                            sendCheck = true;
                        }
                        else {
                            $('#verify-tip-normal').hide();
                            $('#verify-tip .c-error-text').html(data.msg);
                            $('#verify-tip').show();
                            sendCheck = false;
                        }
                    });
            }
        }
        else {
            $('#machine-verify-tip .c-error-text').html("请先完成验证");
            $('#machine-verify-tip').show();
        }
    });

    //注册按钮
    function registerFn() {
        var email = $.trim($('#regist-email').val());
        var password = $('#regist-psw').val();
        var mobile = $('.account-tel').val();
        var verifyCode = $('.account-verify-input').val();
        var registerParams = {
            account: email,
            password: password,
            phone: mobile,
            code: verifyCode
        };
        var url = window.location.href;
        if (url.indexOf("mtid=") != -1) {
            var mtid = (((url.split("?"))[1]).split("="))[1];
            registerParams.geet_id = mtid;
        }
        console.log('&&&',emailCheck && passwordCheck && telCheck && verifyCheck && sendCheck && checkboxCheck && geetestCheck);
        if (emailCheck && passwordCheck && telCheck && verifyCheck && sendCheck && checkboxCheck && geetestCheck) {
            console.log('xhr');
            $.post(
                '/register',
                registerParams,
                function (data, textStatus, xhr) {
                    console.log('result',data, textStatus, xhr);
                    // $('#verify-tip-normal').hide();
                    if (data.code == 1) {
                        $('#verify-tip').hide();
                        console.log('--------------register-success-------------------');
                        layer.alert(data.msg,{title: "注册成功", btn: ['确定']},function () {
                            window.location.href = "/";
                        });
                    }else{
                        $('#verify-tip .c-error-text').html(data.msg);
                        $('#verify-tip').show();
                    }
                });
        }
    }

    $('.account-regist-btn').click(function (event) {
        registerFn();
    });

    $('.account-verify-input').bind('input propertychange', function (event) {
        var verifyCode = $(this).val();
        if (verifyCode == "") {
            $(this).addClass('account-input-error');
            verifyCheck = false;
        } else {
            $(this).removeClass('account-input-error');
            verifyCheck = true;
        }
        registerBtnCheck();
    });
    $('.account-verify-input').keyup(function (event) {
        if (event.keyCode == 13) {
            registerFn();
        }
    });

    function init() {
        var emial = $('#regist-email').val();
        var tel = $('.account-tel').val();
        emailFn(emial);
        telephoneFn(tel);
    }

    //邮箱校验
    function EmailCheck(email) {
        var EmailReg = /^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/i;
        return EmailReg.test($.trim(email)) && (($.trim(email)).indexOf(' ') == -1);
    }

    //手机号码校验
    function teleCheck(tel) {
        // var telReg = /^1[3|4|5|8][0-9]\d{8}$/;
        var telReg = /^1[0-9]\d{9}$/;
        return telReg.test(tel);
    }

    //注册按钮灰化检查
    function registerBtnCheck() {
        if (emailCheck && passwordCheck && telCheck && verifyCheck && sendCheck && checkboxCheck && geetestCheck) {
            $('.account-regist-btn').removeClass('account-regist-btn-disabled');
        }
        else {
            $('.account-regist-btn').addClass('account-regist-btn-disabled');
        }
    }

    //密码复杂度判断
    function passwordType(psw) {
        //小写字母
        var reg1 = /^((?![a-z]).)*$/;
        //大写字母
        var reg2 = /^((?![A-Z]).)*$/;
        //数字
        var reg3 = /^((?![0-9]).)*$/;

        if ((!reg1.test(psw) && !reg2.test(psw)) ||
            !reg1.test(psw) && !reg3.test(psw) ||
            !reg2.test(psw) && !reg3.test(psw)) {
            if (!reg1.test(psw) && !reg2.test(psw) && !reg3.test(psw)) {
                return 3;
            } else {
                return 2;
            }
        } else {
            return 1;
        }
    }

    //密码复杂度提示 type: 0(不显示) 1(弱) 2(中) 3(强)
    function passwordTip(type) {
        if (type != 0) {
            if (type == 1) {
                $('.password-type-con .password-type-btn:nth-child(1)').css('background', '#FF5B5B');
                $('.password-type-con .password-type-btn:nth-child(2)').css('background', '#EEE');
                $('.password-type-con .password-type-btn:nth-child(3)').css('background', '#EEE');
                $('.password-type-con .password-type-text').css('color', '#CCC');
                $('.password-type-con .password-type-text').html('弱');
            } else if (type == 2) {
                $('.password-type-con .password-type-btn:nth-child(1)').css('background', '#F5A623');
                $('.password-type-con .password-type-btn:nth-child(2)').css('background', '#F5A623');
                $('.password-type-con .password-type-btn:nth-child(3)').css('background', '#EEE');
                $('.password-type-con .password-type-text').css('color', '#CCC');
                $('.password-type-con .password-type-text').html('中');
            } else if (type == 3) {
                $('.password-type-con .password-type-btn:nth-child(1)').css('background', '#00B374');
                $('.password-type-con .password-type-btn:nth-child(2)').css('background', '#00B374');
                $('.password-type-con .password-type-btn:nth-child(3)').css('background', '#00B374');
                $('.password-type-con .password-type-text').css('color', '#00B374');
                $('.password-type-con .password-type-text').html('强');
            }
            $('.password-type-con').show();
        } else {
            $('.password-type-con').hide();
        }
    }

    // init();

})