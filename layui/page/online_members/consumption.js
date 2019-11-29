layui.use(['form', 'util', 'layer', 'laydate', 'table', 'laytpl', 'util'], function() {
    var form = layui.form;
    var util = layui.util;
    layer = parent.layer === undefined ? layui.layer : top.layer,
        laypage = layui.laypage,
        upload = layui.upload,
        layedit = layui.layedit,
        laydate = layui.laydate,
        table = layui.table;
    $ = layui.jquery;
    // 获取userid
    function getQueryString(name) {
        var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
        var r = window.location.search.substr(1).match(reg);
        if (r != null) {
            return unescape(r[2]);
        }
        return null;
    }
    var uid = getQueryString("uid");
    $.ajax({
        url: "https://pay.imbatv.cn/api/user/get_log_expense",
        type: "post",
        data: {
            uid: uid,
        },
        dataType: 'json',
        success: function(data) {
            console.log(data);
            var list;
            list = data.data.list;
             if (list) {
                list = data.data.list;
            }else{
            list =[
                {good: "无", number: "无", price: "无", money: "无", starttime: "无", }
                ]
            }
            var tableIns = table.render({
                elem: '#newsList',
                url: "https://pay.imbatv.cn/api/user/get_log_expense",
                cellMinWidth: 95,
                page: true,
                height: "full-125",
                limit: 20,
                limits: [15, 30, 45, 60],
                id: "newsListTable",
                cols: [
                    [
                        { field: 'good', title: '商品名称', align: 'center' },
                        { field: 'number', title: '数量', align: 'center', },
                        { field: 'price', title: '单价', align: 'center', },
                        { field: 'money', title: '金额', align: "center" },
                        {
                            field: 'starttime',
                            title: '下单时间',
                            align: 'center',
                            templet: function(d) {
                                return createTime(d.starttime)
                            }
                        },

                    ]
                ],
                done: function(res, curr, count) {
                    // 隐藏列
                    // $(".layui-table-box").find("[data-field='state']").css("display", "none");
                    // $(".layui-table-box").find("[data-field='uid']").css("display", "none");
                }

            });
        },
        error: function(err) {}
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
    var str = y + "-" + m + "-" + d + " " + h + ":" + M;
    return str;
}