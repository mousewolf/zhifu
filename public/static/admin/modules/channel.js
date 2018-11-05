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
            a = layui.admin,
            u = layui.util,
            n = layui.form;
        i.render({
            elem: "#app-channel-list",
            url: '/channel/getList',
            //自定义响应字段
            response: {
                statusName: 'code' //数据状态的字段名称
                ,statusCode: 1 //数据状态一切正常的状态码
                ,msgName: 'msg' //状态信息的字段名称
                ,dataName: 'data' //数据详情的字段名称
            },
            cols: [[{
                type: "checkbox",
                fixed: "left"
            },
                {
                    field: "id",
                    width: 100,
                    title: "ID",
                    sort: !0
                },
                {
                    field: "name",
                    width: 100,
                    title: "渠道名称"
                },
                {
                    field: "rate",
                    title: "费率",
                    width: 100,
                    align: "center"
                },
                {
                    field: "daily",
                    title: "日限额",
                    width: 110,
                    align: "center"
                },
                {
                    field: "param",
                    width: 400,
                    title: "渠道参数"
                },
                {
                    field: "remark",
                    width: 200,
                    title: "备注",
                },
                {
                    field: "create_time",
                    width: 200,
                    title: "创建时间",
                    templet: function(d) {return u.toDateString(d.create_time*1000); }
                },
                {
                    field: "status",
                    title: "状态",
                    templet: "#buttonTpl",
                    minWidth: 100,
                    align: "center"
                },
                {
                    title: "操作",
                    align: "center",
                    fixed: "right",
                    toolbar: "#table-system-order"
                }]],
            page: !0,
            limit: 10,
            limits: [10, 15, 20, 25, 30],
            text: "对不起，加载出现异常！"
        }),
            i.on("tool(app-channel-list)",
                function(e) {
                var s = e;
                    if ("edit" === e.event) {
                        t(e.tr);
                        layer.open({
                            type: 2,
                            title: "编辑渠道",
                            content: "/channel/edit?id=" + e.data.id,
                            area: ["940px", "610px"],
                            btn: ["确定", "取消"],
                            yes: function(e, f) {
                                var r = window["layui-layer-iframe" + e],
                                    l = "app-channel-submit",
                                    o = f.find("iframe").contents().find("#" + l);
                                r.layui.form.on("submit(" + l + ")",
                                    function(r) {
                                        var l = r.field;
                                        //提交修改
                                        t.post("/channel/edit",l,function (res) {
                                            if (res.code == 1){
                                                //更新数据表
                                                s.update({
                                                    name: l.name,
                                                    daily: l.daily,
                                                    param: l.param,
                                                    rate: l.rate,
                                                    status: l.status
                                                }),
                                                //渲染
                                                n.render(),
                                                layer.close(e);
                                            }
                                            layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                        });
                                    }),
                                    o.trigger("click")
                            },
                            success: function(e, t) {}
                        })
                    }
                });
            e("channel", {})
    });