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
            elem: "#app-balance-list",
            url: "getList",
            //自定义响应字段
            response: {
                statusName: 'code' //数据状态的字段名称
                ,statusCode: 1 //数据状态一切正常的状态码
                ,msgName: 'msg' //状态信息的字段名称
                ,dataName: 'data' //数据详情的字段名称
            },
            cols: [[
                {
                    field: "id",
                    width: 100,
                    title: "ID",
                    sort: !0
                },
                {
                    field: "uid",
                    width: 100,
                    title: "商户UID",
                    sort: !0
                },
                {
                    field: "enable",
                    width: 200,
                    title: "可用余额"
                },
                {
                    field: "disable",
                    width: 180,
                    title: "冻结余额"
                },
                {
                    field: "status",
                    title: "状态",
                    templet: "#buttonTpl",
                    minWidth: 80,
                    align: "center"
                },
                {
                    field: "create_time",
                    title: "创建时间",
                    width: 180,
                    sort: !0,
                    templet: function(d) {return u.toDateString(d.create_time*1000); }
                },
                {
                    field: "update_time",
                    title: "更新时间",
                    width: 180,
                    sort: !0,
                    templet: function(d) {return u.toDateString(d.update_time*1000); }
                },
                {
                    title: "操作",
                    minWidth: 150,
                    align: "center",
                    fixed: "right",
                    toolbar: "#table-balance-list"
                }]],
            page: !0,
            limit: 10,
            limits: [10, 15, 20, 25, 30],
            text: "对不起，加载出现异常！"
        }),
            i.on("tool(app-balance-list)",
                function(e) {
                console.log(e);
                    if ("details" === e.event) {
                        t(e.tr);
                        layer.open({
                            type: 2,
                            title: "账户明细",
                            content: "details.html?id=" + e.data.uid,
                            maxmin: !0,
                            area:  ['80%', '60%'],
                            yes: function(f, t) {},
                            success: function(e, t) {}
                        })
                    }
                }),

        i.render({
            elem: "#app-balance-details-list",
            url: 'getDetails',
            //添加请求字段
            where: {
                uid:  t("input[ name='uid' ] ").val()
            },
            //自定义响应字段
            response: {
                statusName: 'code' //数据状态的字段名称
                ,statusCode: 1 //数据状态一切正常的状态码
                ,msgName: 'msg' //状态信息的字段名称
                ,dataName: 'data' //数据详情的字段名称
            },
            cols: [[{
                field: "type",
                templet: "#buttonType",
                width: 100,
                title: "资金类型"
            },
                {
                    field: "preinc",
                    width: 100,
                    title: "变动前金额",
                    style: "color:red"
                },
                {
                    field: "increase",
                    width: 100,
                    title: "增加金额",
                    style: "color:red"
                },
                {
                    field: "reduce",
                    width: 100,
                    title: "减少金额",
                    style: "color:red"
                },
                {
                    field: "suffixred",
                    width: 100,
                    title: "变动后金额",
                    style: "color:red"
                },
                {
                    field: "remarks",
                    title: "变动备注"
                },
                {
                    field: "update_time",
                    width: 200,
                    title: "更新时间",
                    templet: function(d) {return u.toDateString(d.update_time*1000); }
                }]],
            page: {
                limit: 10,
                limits: [10, 15, 20, 25, 30]
            },
            text: "对不起，加载出现异常！"
        }),
            e("balance", {})
    });