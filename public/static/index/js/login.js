$(function() {
    var emailCheck = false;
    var passwordCheck = true;
    var verifyCheck = true;
    var checkboxCheck = true;
    var geetestCheck = false;
    $("#login-email").focus();
    var handler = function(captchaObj) {
        $("#captcha-box").empty();
        captchaObj.appendTo("#captcha-box");
        captchaObj.onSuccess(function() {
            var result = captchaObj.getValidate();
            $.ajax({
                url: "/validate/gt-verify",
                type: "POST",
                dataType: "json",
                data: {
                    geetest_challenge: result.geetest_challenge,
                    geetest_validate: result.geetest_validate,
                    geetest_seccode: result.geetest_seccode
                }
            }).done(function(data1) {
                if (data1.status == "success") {
                    geetestCheck = true;
                    $("#tel-cont").show();
                    loginBtnCheck()
                } else {
                    geetestInit()
                }
            }).fail(function() {
                console.log("error")
            })
        });
        window.gt = captchaObj
    };
    var geetestInit = function() {
        $.ajax({
            url: "/validate/gt-start",
            type: "POST",
            dataType: "json",
        }).done(function(data) {
            var product = "float";
            initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                offline: !data.success,
                new_captcha: true,
                product: product,
                width: "100%"
            }, handler)
        }).fail(function() {
            console.log("error")
        })
    };
    geetestInit();
    $(".c-checkbox").click(function(event) {
        if ($(this).attr("id") == "check") {
            $(this).attr("id", "checked");
            $(this).removeClass("c-checkbox");
            $(this).addClass("c-checkbox-checked");
            $(".account-protocol-tip-cont").hide();
            checkboxCheck = true
        } else {
            $(this).attr("id", "check");
            $(this).removeClass("c-checkbox-checked");
            $(this).addClass("c-checkbox");
            $(".account-protocol-tip-cont").show();
            checkboxCheck = false
        }
        loginBtnCheck()
    });

    function emailFn(email) {
        var tel = $(".account-tel").val();
        email = $.trim(email);
        if (email != "") {
            if (!EmailCheck(email)) {
                console.log("dfdfdfdf", email);
                $("#email-tip .c-error-icon").show();
                $("#email-tip .c-tip-text").addClass("c-error-text");
                $("#email-tip .c-tip-text").html("请输入正确的邮箱");
                $(".login-email-checked").hide();
                $("#login-email").addClass("account-input-error");
                emailCheck = false;
                $("#email-tip").show()
            } else {
                $("#email-tip .c-error-icon").hide();
                $("#email-tip .c-tip-text").removeClass("c-error-text");
                $("#email-tip .c-tip-text").html("请输入您的邮箱地址");
                $(".login-email-checked").show();
                $("#login-email").removeClass("account-input-error");
                emailCheck = true;
                $("#email-tip").hide()
            }
        } else {
            $("#email-tip .c-error-icon").show();
            $("#email-tip .c-tip-text").addClass("c-error-text");
            $(".login-email-checked").hide();
            $("#login-email").addClass("account-input-error");
            emailCheck = false;
            $("#email-tip").show()
        }
        loginBtnCheck()
    }
    $("#login-email").bind("input propertychange", function(event) {
        var email = $(this).val();
        emailFn(email);
        loginBtnCheck()
    });
    $("#login-password").focus(function(event) {
        $("#account-psw-tip").show();
        passwordTip(passwordType($("#login-password").val()));
        var email = $("#login-email").val();
        emailFn(email)
    });

    function loginFn() {
        var email = $.trim($("#login-email").val());
        var password = $("#login-password").val();
        var registerParams = {
            username: email,
            password: password
        };
        var url = window.location.href;
        if (url.indexOf("mtid=") != -1) {
            var mtid = (((url.split("?"))[1]).split("="))[1];
            registerParams.geet_id = mtid
        }
        if (emailCheck && passwordCheck && geetestCheck) {
            console.log("xhr");
            $.post("/login", registerParams, function(data, textStatus, xhr) {
                console.log("result", data, textStatus, xhr);
                $("#verify-tip-normal").hide();
                if (data.code == 1) {
                    window.location.href = "/user"
                } else {
                    $("#email-tip .c-error-icon").show();
                    $("#email-tip .c-tip-text").addClass("c-error-text");
                    $("#email-tip .c-tip-text").html(data.msg);
                    $(".login-email-checked").hide();
                    $("#login-email").addClass("account-input-error");
                    emailCheck = false;
                    $("#email-tip").show()
                }
            })
        }
    }
    $(".account-regist-btn").click(function(event) {
        loginFn()
    });
    $(".account-verify-input").bind("input propertychange", function(event) {
        var verifyCode = $(this).val();
        if (verifyCode == "") {
            $(this).addClass("account-input-error");
            verifyCheck = false
        } else {
            $(this).removeClass("account-input-error");
            verifyCheck = true
        }
        loginBtnCheck()
    });
    $(".account-verify-input").keyup(function(event) {
        if (event.keyCode == 13) {
            loginFn()
        }
    });

    function init() {
        var emial = $("#login-email").val();
        var tel = $(".account-tel").val();
        emailFn(emial);
        telephoneFn(tel)
    }

    function EmailCheck(email) {
        var EmailReg = /^[a-zA-Z0-9\-\_\.]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/i;
        return EmailReg.test($.trim(email)) && (($.trim(email)).indexOf(" ") == -1)
    }

    function loginBtnCheck() {
        if (emailCheck && passwordCheck && geetestCheck) {
            $(".account-regist-btn").removeClass("account-regist-btn-disabled")
        } else {
            $(".account-regist-btn").addClass("account-regist-btn-disabled")
        }
    }

    function passwordType(psw) {
        var reg1 = /^((?![a-z]).)*$/;
        var reg2 = /^((?![A-Z]).)*$/;
        var reg3 = /^((?![0-9]).)*$/;
        if ((!reg1.test(psw) && !reg2.test(psw)) || !reg1.test(psw) && !reg3.test(psw) || !reg2.test(psw) && !reg3.test(psw)) {
            if (!reg1.test(psw) && !reg2.test(psw) && !reg3.test(psw)) {
                return 3
            } else {
                return 2
            }
        } else {
            return 1
        }
    }

    function passwordTip(type) {
        if (type != 0) {
            if (type == 1) {
                $(".password-type-con .password-type-btn:nth-child(1)").css("background", "#FF5B5B");
                $(".password-type-con .password-type-btn:nth-child(2)").css("background", "#EEE");
                $(".password-type-con .password-type-btn:nth-child(3)").css("background", "#EEE");
                $(".password-type-con .password-type-text").css("color", "#CCC");
                $(".password-type-con .password-type-text").html("弱")
            } else {
                if (type == 2) {
                    $(".password-type-con .password-type-btn:nth-child(1)").css("background", "#F5A623");
                    $(".password-type-con .password-type-btn:nth-child(2)").css("background", "#F5A623");
                    $(".password-type-con .password-type-btn:nth-child(3)").css("background", "#EEE");
                    $(".password-type-con .password-type-text").css("color", "#CCC");
                    $(".password-type-con .password-type-text").html("中")
                } else {
                    if (type == 3) {
                        $(".password-type-con .password-type-btn:nth-child(1)").css("background", "#00B374");
                        $(".password-type-con .password-type-btn:nth-child(2)").css("background", "#00B374");
                        $(".password-type-con .password-type-btn:nth-child(3)").css("background", "#00B374");
                        $(".password-type-con .password-type-text").css("color", "#00B374");
                        $(".password-type-con .password-type-text").html("强")
                    }
                }
            }
            $(".password-type-con").show()
        } else {
            $(".password-type-con").hide()
        }
    }
});