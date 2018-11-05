/*
 *  +----------------------------------------------------------------------
 *  | 草帽支付系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */

layui.define(["table", "form"],
    function(e) {
        var t = layui.$,
            i = layui.table,
            u = layui.util,
            n = layui.form;

        i.render({
            elem: "#app-admin-user-manage",
            url: "/admin/userList",
            //自定义响应字段
            response: {
                statusCode: 1 //数据状态一切正常的状态码
            },
            cols: [
                [{
                    type: "checkbox",
                    fixed: "left"
                }, {
                    field: "id",
                    width: 80,
                    title: "ID",
                    sort: !0
                }, {
                    field: "loginname",
                    title: "登录名"
                }, {
                    field: "telphone",
                    title: "手机"
                }, {
                    field: "email",
                    title: "邮箱"
                }, {
                    field: "role",
                    title: "角色"
                }, {
                    field: "jointime",
                    title: "加入时间",
                    sort: !0,
                    templet: function(d) {return u.toDateString(d.jointime*1000); }
                }, {
                    field: "check",
                    title: "审核状态",
                    templet: "#buttonTpl",
                    minWidth: 80,
                    align: "center"
                }, {
                    title: "操作",
                    width: 150,
                    align: "center",
                    fixed: "right",
                    toolbar: "#table-useradmin-admin"
                }]
            ],
            text: "对不起，加载出现异常！"
        }),
        i.on("tool(app-admin-user-manage)",
            function(e) {
                e.data;
                if ("del" === e.event) layer.prompt({
                        formType: 1,
                        title: "敏感操作，请验证口令"
                    },
                    function(t, i) {
                        layer.close(i),
                            layer.confirm("确定删除此管理员？",
                                function(t) {
                                    console.log(e),
                                        e.del(),
                                        layer.close(t)
                                })
                    });
                else if ("edit" === e.event) {
                    t(e.tr);
                    layer.open({
                        type: 2,
                        title: "编辑管理员",
                        content: "../../../views/user/administrators/adminform.html",
                        area: ["420px", "420px"],
                        btn: ["确定", "取消"],
                        yes: function(e, t) {
                            var l = window["layui-layer-iframe" + e],
                                r = "LAY-user-back-submit",
                                n = t.find("iframe").contents().find("#" + r);
                            l.layui.form.on("submit(" + r + ")",
                                function(t) {
                                    t.field;
                                    i.reload("LAY-user-front-submit"),
                                        layer.close(e)
                                }),
                                n.trigger("click")
                        },
                        success: function(e, t) {}
                    })
                }
            }),
        i.render({
            elem: "#app-admin-user-role",
            url: "/admin/groupList",
            //自定义响应字段
            response: {
                statusCode: 1 //数据状态一切正常的状态码
            },
            cols: [
                [{
                    type: "checkbox",
                    fixed: "left"
                }, {
                    field: "id",
                    width: 80,
                    title: "ID",
                    sort: !0
                }, {
                    field: "name",
                    title: "角色名"
                }, {
                    field: "rules",
                    title: "拥有权限"
                }, {
                    field: "describe",
                    title: "具体描述"
                }, {
                    title: "操作",
                    width: 200,
                    align: "center",
                    fixed: "right",
                    toolbar: "#table-admin-user-role"
                }]
            ],
            text: "对不起，加载出现异常！"
        }),
        i.on("tool(app-admin-user-role)",
            function(e) {
                var d = e.data;
                if ("del" === e.event) layer.confirm("确定删除此角色？",
                    function(t) {
                        e.del(),
                            layer.close(t)
                    });
                else if ("auth" === e.event) {
                    t(e.tr);
                    layer.open({
                        type: 2,
                        title: "角色授权",
                        content: "/admin/menuAuth.html?id="+ d.id,
                        area: ["700px", "650px"],
                        btn: ["确定", "取消"],
                        yes: function(d, t) {
                            var l = window["layui-layer-iframe" + d],
                                i = "app-user-auth-submit",
                                r = t.find("iframe").contents().find("#" + i);
                            l.layui.form.on("submit(app-user-auth-submit)",
                                function(t) {
                                    var l = t.field;
                                    console.log(l);
                                    e.render(),
                                        layer.close(e)
                                }),
                                r.trigger("click")
                        },
                        success: function(e, t) {}
                    })
                }
                else if ("edit" === e.event) {
                    t(e.tr);
                    layer.open({
                        type: 2,
                        title: "编辑角色",
                        content: "/admin/groupEdit.html?id="+ d.id,
                        area: ["700px", "650px"],
                        btn: ["确定", "取消"],
                        yes: function(e, t) {
                            var l = window["layui-layer-iframe" + e],
                                r = t.find("iframe").contents().find("#LAY-user-role-submit");
                            l.layui.form.on("submit(app-user-role-submit)",
                                function(t) {
                                    t.field;
                                    i.reload("app-user-back-role"),
                                        layer.close(e)
                                }),
                                r.trigger("click")
                        },
                        success: function(e, t) {}
                    })
                }
            }),
        e("useradmin", {})
    });
