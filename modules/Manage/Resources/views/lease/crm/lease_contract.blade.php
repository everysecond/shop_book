<!DOCTYPE html>
<html class="full-height">
<head>
    <meta charset="utf-8">
    <title>租点—续租统计</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
    <script src="{{asseturl("lib/echarts/echarts.min.js")}}"></script>
    <script src="{{asseturl("lib/echarts/jquery.js")}}"></script>
    <script src="{{asseturl("lib/layui/layui.js")}}"></script>
</head>
<body>
<style>
    html, body {
        width: 100%;
        height: 100%;
        margin: 0;
    }

    .layui-col-md12, .layui-card {
        padding: 10px
    }

    .layui-col-md2 {
        margin: 1px 10px 1px 10px;
        padding: 5px;
        width: 13%;
        min-width: 220px;
    }

    h3 {
        font-family: 'Arial Normal', 'Arial';
        font-weight: 400;
        font-style: normal;
        color: #333333;
        line-height: normal;
        padding: 6px;
    }

    .number-title {
        display: block;
        font-family: 'Arial Negreta', 'Arial Normal', 'Arial';
        font-weight: 700;
        font-style: normal;
        font-size: 30px;
        color: #1ABC9C;
        padding: 5px;
    }

    .layui-col-md2:hover {
        border: 2px solid rgba(26, 188, 156, 1);
    }

    .layui-col-md2 {
        border: 2px solid white;
    }

    .base-title-click {
        border: 2px solid rgba(26, 188, 156, 1);
    }

    .layui-card-header {
        font-family: 'Arial Negreta', 'Arial Normal', 'Arial';
        font-weight: 700;
        font-style: normal;
        border-bottom: 0px;
    }

    .height100 {
        height: 100%;
    }

    .u100 {
        font-family: 'Arial Normal', 'Arial';
        font-weight: 400;
        font-style: normal;
        font-size: 13px;
        text-align: center;
        line-height: normal;
    }

    .color-grew {
        color: #999999;
    }

    .color-green {
        color: #1ABC9C;
    }

    .color-red {
        color: #F04844;
    }

    .ml7 {
        margin-left: 7px;
    }

    .div-mid {
        width: 100%;
        height: 350px;
    }

    .layui-card {
        margin-bottom: 10px;
    }

    .layui-card > .layui-card-body > .layui-col-md12 {
        padding: 0 !important;
    }
</style>


<div class="layui-col-md12" align="center" style="height:100%;">
    <div class="layui-card" style="height:100%;">
        <div class="layui-form" align="right">
            <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                <ul class="layui-tab-title">
                    <li onclick="load_Table(1)"  class="layui-this"
                        data-a="1">我的</li>
                    <li onclick="load_Table(2)"
                        data-a="2">我协作的</li>
                    <li onclick="load_Table(3)"
                        data-a="3">下属的</li>
                    <li onclick="load_Table(4)"
                        data-a="4">下属协作的</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">

                        <div class="layui-form-item">
                            <form class="layui-form" onsubmit="return false">
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        {{--                                    <input type="text" class="layui-input" id="test10" placeholder="开始时间 - 结束时间" autocomplete="off">--}}
                                        <input type="text" class="layui-input" id="name" placeholder="合同编号、用户、负责人、网点">
                                    </div>
                                </div>
                                <div class="layui-inline" align="left">
                                    <select name="status" lay-verify="required" lay-search="" id='status'>
                                        <option value="0">合同状态</option>
                                        <option value="1">生效中</option>
                                        <option value="2">未生效</option>
                                    </select>
                                </div>
                                <div class="layui-inline" align="left">
                                    <select name="test13" lay-verify="required" lay-search="" id='test13'>
                                        {{--                            <option value="0">全部区域</option>--}}
                                        @foreach($provinces as $id=>$province)
                                            <option value="{{$id}}">{{$province}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input" placeholder="生效时间" id="test12"
                                                autocomplete="off">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input" placeholder="到期时间" id="test9"
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <button class="layui-btn " lay-submit="" onclick="load_Table()"
                                            style="border-color: rgb(59, 177, 156);color:white"><i
                                                class="layui-icon"></i>搜索
                                    </button>
                                    <button type="reset" class="layui-btn layui-btn-primary"
                                            style="border-color: rgb(59, 177, 156);color:rgb(59, 177, 156)">清空
                                    </button>
                                </div>
                            </form>


                        </div>

                        <table id="demo" lay-filter="test"></table>


                    </div>

                </div>
            </div>

        </div>


    </div>
</div>

<script>
    var adminurl = "{{adminurl()}}";
    var _token = '{{ csrf_token() }}';
    layui.config({
        base: "{{asseturl("lib")}}" + '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'console']);

</script>

{{--<script src="{{asseturl("js/lease/report/expire_renewal.js?").time()}}"></script>--}}
<script>


    var tree = "", table, treeLoad;
    layui.use('laydate', function () {
        var laydate = layui.laydate;

        laydate.render({
            elem: '#test10'

            , range: true
        });


        laydate.render({
            elem: '#test9'

            ,range: true
        });

        laydate.render({
            elem: '#test12'

            ,range: true
        });

    });



    var load_Table;
    layui.use(['index', 'table', 'form', 'common', 'http', 'tree', 'util'], function () {
        var name = $("#name").val();
        var status = $('#status').val();
        var province_id = $('#test13').val();
        var constract_begin_at = $('#test12').val();
        var constract_end_at = $('#test9').val();

        var form = layui.form, http = layui.http, common = layui.common,
            layer = layui.layer, util = layui.util;
        var tree = layui.tree;
        var table = layui.table;
        var appid = $(".layui-this").attr("data-a");

        //第一个实例
        table.render({
            elem: '#demo'
            , method: 'post'
            , align: "right"
            , url: adminurl + "/crm/lease_contract_search"//数据接口
            , where: {
                _token: _token,
                status: status,
                province_id: province_id,
                constract_begin_at: constract_begin_at,
                constract_end_at: constract_end_at,
                name: name,
                appid: appid
            }
            , page: true //开启分页

            , cols: [[
                // {type: 'checkbox', value: "id"}
                {field: 'contract_no', title: '合同编号', sort: true}
                , {
                    field: 'status', title: '合约状态',
                    templet: function (item) {
                        if (item.status == 3) {
                            return "生效中";
                        } else {
                            return "未生效";
                        }
                    }

                }
                , {field: 'model_name', title: '电池型号'}
                , {field: 'audited_at', title: '租期',
                     templet: function (item) {
                            if (item.lease_unit == "year") {
                                var year = '年';
                            }else if(item.lease_unit == "month") {
                                var year = '个月';
                            }

                         return item.lease_term + year;

                     }
                }
                , {field: 'rentals', title: '租金',
                    templet: function (item) {

                        return JSON.parse(item.rentals);

                    }


                }
                , {field: 'prepayment', title: '预付款'}
                , {field: 'payment_payed_at', title: '支付时间'}
                , {field: 'effected_at', title: '生效时间'}
                , {field: 'lease_expired_at', title: '到期时间'}
                , {field: 'user_nickname', title: '用户',event: 'setSign',
                    templet: function (item) {
                        if (!item.user_nickname) {
                            //return '<a class="layui-table-cell laytable-cell-3-service_name" style="color: #68ccf7">'+item.user_mobile+'</a>'
                            return '<a class="open-frame-r color-green" data-method="get" ' +
                                'title="客户详情" data-width="1050px" data-type="detail"' +
                                ' data-height="100%">' + item.user_mobile + '</a>';
                        }else{
                            //return '<a class="layui-table-cell laytable-cell-3-service_name" style="color: #68ccf7">'+item.user_nickname+'</a>'
                            return '<a class="open-frame-r color-green" data-method="get" ' +
                                'title="客户详情" data-width="1050px" data-type="detail"' +
                                ' data-height="100%">' + item.user_nickname + '</a>';
                        }
                    }
        
                }
                , {field: 'service_name', title: '网点',event: 'setSigns',

                    templet: function (item) {

                        // return '<a class="layui-table-cell laytable-cell-3-service_name" style="color: #68ccf7">'+item.service_name+'</a>'
                        return '<a class="open-frame-r color-green" data-method="get" ' +
                            'title="客户详情" data-width="1050px" data-type="detail"' +
                            ' data-height="100%">' + item.service_name + '</a>';
                    }

                }
                , {field: 'area', title: '区域',
                    templet: function (item) {

                    return item.service_province_name + item.service_city_name;

                    }

                }
                , {field: 'charger_name', title: '负责人'}
            ]]
        });

        load_Table = function (id) {
            if (!id) id = $(".layui-this").attr("data-a");
            var constract_begin_at = $('#test12').val();
            var constract_end_at = $('#test9').val();
            table.reload('demo', {
                page:{curr:1},
                where: {
                    _token: _token,
                    status: $("#status").val(),
                    province_id: $('#test13').val(),
                    name: $("#name").val(),
                    constract_begin_at: constract_begin_at,
                    constract_end_at: constract_end_at,
                    appid: id
                }
            });
        }
    });


    layui.use('table', function () {
        var table = layui.table;
        //监听单元格事件
        table.on('tool(test)', function (obj) {
            var data = obj.data;


            if (obj.event === 'setSign') {

                var id = data.ids;
                layer.open({
                    type: 2,
                    title: "kehu",
                    // closeBtn: 0, //不显示关闭按钮
                    shade: [0],
                    area: ['1050px', '100%'],
                    offset: 'r', //右下角弹出
                    anim: 2,
                    content: '/manage/crm/cus/detail/'+id,
                    {{--content: layui.common.route("{{route('crm.reports.sea_customer_detail')}}?id=" + id),--}}
                    end: function () { //此处用于演示

                    }
                });


            }

            if (obj.event === 'setSigns') {

                var id = data.b_user.id;
                layer.open({
                    type: 2,
                    title: "kehu",
                    // closeBtn: 0, //不显示关闭按钮
                    shade: [0],
                    area: ['1050px', '100%'],
                    offset: 'r', //右下角弹出
                    anim: 2,
                    content: '/manage/crm/cus/detail/'+id,
                    {{--content: layui.common.route("{{route('crm.reports.sea_customer_detail')}}?id=" + id),--}}
                    end: function () { //此处用于演示

                    }
                });


            }

        });

    });


    $("#claim").click(function () {
        layer.confirm(' 你确定认领这个客户吗？', {}, function () {
            var checkStatus = layui.table.checkStatus('demo').data;
            var arr = new Array();
            var table = layui.table;
            $.each(checkStatus, function (i, n) {
                arr[i] = n.id;
            });
            var customer_id = arr.join(",");
            $.ajax({
                type: 'POST',
                data: {'customer_id': customer_id, '_token': _token},
                dataType: 'json',
                url: adminurl + "/crm/sea_customer_claim",
                success: function (res) {
                    if (res.code == 1) {
                        table.reload('demo');
                        layer.msg('认领成功', {icon: 1});
                    } else {
                        layer.msg('网络请求错误，稍后重试！');
                    }
                },
            });

        });

    });


    var tree = "";

    var isLoad = false;
    var table;
    var treeLoad;
    layui.use(['index', 'table', 'form', 'common', 'http', 'tree', 'util'], function () {
        var form = layui.form, http = layui.http, common = layui.common,
            layer = layui.layer, util = layui.util, $ = layui.$;
        tree = layui.tree;
        table = layui.table;


        $("#distribute").click(function () {

            var checkStatus = layui.table.checkStatus('demo').data;
            var arr = new Array();
            var table = layui.table;
            $.each(checkStatus, function (i, n) {
                arr[i] = n.id;
            });
            var customer_id = arr.join(",");

            layer.open({
                type: 2,
                title: "分配",
                content: layui.common.route("{{route('crm.reports.sea_customer_distribute_view')}}?customer_id=" + customer_id),
                area: ['500px', '400px'],
                shadeClose: true,
                btn: ['确定', '取消'],
                yes: function (index, layero) {
                    var iframeWindow = window["layui-layer-iframe" + index];
                    if (common.isFunction(iframeWindow.layerYesCallback)) {
                        iframeWindow.layerYesCallback(index, layero);
                    }
                }
            });
        });


        $("#transfer").click(function () {

            var checkStatus = layui.table.checkStatus('demo').data;
            var arr = new Array();
            var table = layui.table;
            $.each(checkStatus, function (i, n) {
                arr[i] = n.id;
            });
            var customer_id = arr.join(",");

            layer.open({
                type: 2,
                title: "转移",
                content: layui.common.route("{{route('crm.reports.sea_customer_transfer_view')}}?customer_id=" + customer_id),
                area: ['500px', '400px'],
                shadeClose: true,
                btn: ['确定', '取消'],
                yes: function (index, layero) {
                    var iframeWindow = window["layui-layer-iframe" + index];
                    if (common.isFunction(iframeWindow.layerYesCallback)) {
                        iframeWindow.layerYesCallback(index, layero);
                    }
                }
            });
        });

    });


</script>
</body>
</html>