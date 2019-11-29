layui.use(['form', 'util', 'layer', 'laydate', 'table', 'laytpl', 'util'], function() {
    var form = layui.form;
    var util = layui.util;
    layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        laytpl = layui.laytpl,
        table = layui.table;


    var tableIns = table.render({
        elem: '#newsList',
        url: 'https://pay.imbatv.cn/api/news',
        limit: 15,
        limits: [15, 30, 45, 60],
        page: true,
        //,…… //其他参数
        cols: [
            [
                { field: 'id', title: 'ID', align: 'center', sort: true},
                { field: 'pic', title: '活动图片', align:"center",
                    templet:function(d){
                        return '<a href="'+d.pic+'" target="_blank"><img src="'+d.pic+'" height="26" /></a>';
                    }
                },
                { field: 'pic2', title: '备用图片', align:"center",
                    templet:function(d){
                        return '<a href="'+d.pic2+'" target="_blank"><img src="'+d.pic2+'" height="26" /></a>';
                    }
                },
                { field: 'title', title: '活动名称', align: 'center'},
                { field: 'type', title: '显示位置', align: 'center',
                    templet: function(d) {
                        return act_Type(d.type);
                    }
                },
                { field: 'createdate', title: '活动时间', align: 'center',
                    templet: function(d) {
                        return createTime(d.createdate);
                    }
                },
                { field: 'content', title: '活动内容', align: "center" },
                { title: '操作', templet: '#newsListBar', fixed: "right", align: "center" }
            ]
        ],
        done: function(res, curr, count) {
            // 隐藏列
            // console.log(res);
            var layEvent = res.event,
                data = res.data;
        }
    });
    // 验证手机号
    function isPhoneNo(phone) {
        var pattern = /^1[34578]\d{9}$/;
        return pattern.test(phone);
    }


    //修改商品
    function modify(edit){
        var index = layui.layer.open({
            title : "修改活动",
            type : 2,
            content : "commondityAdd.html?m=" + edit.id,
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                console.log(edit);
                console.log(edit.type_id);
                if(edit){
                    body.find(".item1 input").val(edit.title);
                    body.find(".item2 input").val(act_Type(edit.type));
                    body.find(".item3 input").val(edit.content);
                    body.find(".item4 input.item_spc").val(edit.pic);
                    body.find(".item5 input.item_spc").val(edit.pic2);
                    body.find(".item7 input").val(createTime(edit.createdate));
                    body.find(".item6 button.btn1").attr("lay-filter","demo2");
                    body.find(".item6 button.btn2").attr("data-id",edit.id);
                    form.render();
                }
                setTimeout(function(){
                    layui.layer.tips('点击此处返回活动列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },2000)
            }
        })
        layui.layer.full(index);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        // $(window).on("resize",function(){
        //     layui.layer.full(index);
        // })
    }
    $(window).one("resize",function(){
        $(".commondityAdd_btn").click(function(){
            var index = layui.layer.open({
                title : "新增活动",
                type : 2,
                content : "commondityAdd.html",
                success : function(layero, index){
                    setTimeout(function(){
                        layui.layer.tips('返回', '.layui-layer-setwin .layui-layer-close', {
                            tips: 3
                        });
                    },2000)
                }
            })          
            layui.layer.full(index);
        })

        $(".classifyAdd_btn").click(function(){
            var index = layui.layer.open({
                title : "新增活动",
                type : 2,
                content : "commondityAdd.html",
                success : function(layero, index){
                    setTimeout(function(){
                        layui.layer.tips('返回', '.layui-layer-setwin .layui-layer-close', {
                            tips: 3
                        });
                    },2000)
                }
            })          
            layui.layer.full(index);
        })
    }).resize();

    //列表操作
    table.on('tool(newsList)', function(obj) {
        var layEvent = obj.event,
            data = obj.data;
            // console.log(data);
        var list = data.detail;
        var order_id = obj.data.id;
        if (layEvent === 'modify') { //修改活动
            modify(data);
        } 
    });
})


function createTime(v) {
    var date = new Date(v * 1000);
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    m = m < 10 ? '0' + m : m;
    var d = date.getDate();
    d = d < 10 ? ("0" + d) : d;
    var h = date.getHours();
    h = h < 10 ? ("0" + h) : h;
    var M = date.getMinutes();
    M = M < 10 ? ("0" + M) : M;
    var S = date.getSeconds();
    S = S < 10 ? ("0" + S) : S;
    var str = y + "-" + m + "-" + d + " " + h + ":" + M + ":" + S;
    return str;
}
function act_Type(t) {
    switch (t){
        case '1':
            return '预约页';
            break;
        case '2':
            return '活动顶部';
            break;
        case '3':
            return '活动列表';
            break;
    }
    return
}