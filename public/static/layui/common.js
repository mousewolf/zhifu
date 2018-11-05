layui.use(['form', 'jquery', 'laydate', 'layer', 'laypage',   'element'], function() {
    var form = layui.form(),
        layer = layui.layer,
        $ = layui.jquery,
        dialog = layui.dialog;
    //获取当前iframe的name值
    var iframeObj = $(window.frameElement).attr('name');
    //全选
    form.on('checkbox(allChoose)', function(data) {
        var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
        child.each(function(index, item) {
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });
    //渲染表单
    form.render();

    //顶部导出
    $('.exportBtn').click(function() {
        var url=$(this).attr('data-url');
        //将iframeObj传递给父级窗口,执行操作完成刷新
        parent.page("批量导出", url, iframeObj, w = "700px", h = "620px");
        return false;

    }).mouseenter(function() {

        dialog.tips('批量导出', '.exportBtn');

    });

    //列表跳转
    $('#table-list,.tool-btn').on('click', '.go-btn', function() {
        var url=$(this).attr('data-url');
        var id = $(this).attr('data-id');
        window.location.href=url+"?id="+id;
        return false;
    });
});

/**
 * 控制iframe窗口的刷新操作
 */
var iframeObjName;

//父级弹出页面
function page(title, url, obj, w, h) {
    if(title == null || title == '') {
        title = false;
    };
    if(url == null || url == '') {
        url = "404.html";
    };
    if(w == null || w == '') {
        w = '700px';
    };
    if(h == null || h == '') {
        h = '350px';
    };
    iframeObjName = obj;
    //如果手机端，全屏显示
    if(window.innerWidth <= 768) {
        var index = layer.open({
            type: 2,
            title: title,
            area: [320, h],
            fixed: false, //不固定
            content: url
        });
        layer.full(index);
    } else {
        var index = layer.open({
            type: 2,
            title: title,
            area: [w, h],
            fixed: false, //不固定
            content: url
        });
    }
}

/**
 * 刷新子页,关闭弹窗
 */
function refresh(index) {
    //根据传递的name值，获取子iframe窗口，执行刷新
    if(window.frames[iframeObjName]) {
        window.frames[iframeObjName].location.reload(index);

    } else {
        window.location.reload(index);
    }

    layer.closeAll(index);
}