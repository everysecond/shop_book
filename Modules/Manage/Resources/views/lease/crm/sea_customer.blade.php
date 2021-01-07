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
    {{Html::style(asset('resource/css/common.css'))}}
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

    .layui-layer-setwin .layui-layer-close2 {
        right: 0 !important;
        top: 0 !important;
    }
</style>


<div class="layui-col-md12" align="center" style="height:100%;">
    <div class="layui-card" style="height:100%;">
        <div class="layui-form" align="right">
            <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                <ul class="layui-tab-title">

                    @foreach($water as $id=>$province)
                        <li onclick="load_Table({{$province['id']}})" @if($id == 0) class="layui-this"
                            @endif data-a="{{$province['id']}}">{{$province['name']}}</li>
                    @endforeach

                </ul>
                <div class="layui-tab-content">
                    @if(!empty($water))
                        <div class="layui-tab-item layui-show">

                            <div class="layui-form-item">
                                <form class="layui-form" onsubmit="return false">
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            {{--                                    <input type="text" class="layui-input" id="test10" placeholder="开始时间 - 结束时间" autocomplete="off">--}}
                                            <input type="text" class="layui-input" id="name" placeholder="名称、姓名、号码">
                                        </div>
                                    </div>
                                    <div class="layui-inline" align="left">
                                        <select name="history_deal" lay-verify="required" lay-search="" id='history_deal'>
                                            <option value="0">成交状态</option>
                                            <option value="2">成交</option>
                                            <option value="1">未成交</option>
                                        </select>
                                    </div>
                                    <div class="layui-inline" align="left">
                                        <select name="cus_level" lay-verify="required" lay-search="" id='cus_level'>
                                            <option value="0">客户等级</option>
                                            <option value="1">重点客户</option>
                                            <option value="2">普通客户</option>
                                            <option value="3">非优先客户</option>
                                        </select>
                                    </div>
                                    <div class="layui-inline" align="left">
                                        <select name="cus_source" lay-verify="required" lay-search="" id='cus_source'>
                                            <option value="0">客户来源</option>
                                            <option value="1">APP录入</option>
                                            <option value="2">租点系统</option>
                                            <option value="3">中台录入</option>
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
                                            <input type="text" class="layui-input" placeholder="创建时间" id="test12"
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
                            <div class="layui-inline" style="margin-bottom: 5px;width: 100%" id="cdt">
                                <button class="cps-btn mybtn" id="claim"
                                       >认领
                                </button>

                                <button class="cps-btn mybtn" id="distribute"
                                       >分配
                                </button>
                            @if($is_transfer >= 2)
                        <button class="cps-btn mybtn" id="transfer"
                                >转移到
                        </button>
                            @endif
                        <button class="cps-btn mybtn" title="导出excel,请控制筛选范围在1W以下，否则数据过大下载较慢" onclick="doExport()"
                                style="width: 80px;float: right;">
                            导出excel
                        </button>

                        </div>
                            <table id="demo" lay-filter="test"></table>


                        </div>
                    @endif
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


    var appid = $(".layui-this").attr("data-a");


    var sea_claim = sea_distribute ={};
    var sea_distribute ={};

    @foreach($is_claim as $id=>$item)
        sea_claim['{{$id}}'] = '{{$item}}';
    @endforeach

    @foreach($is_distribute as $id=>$item)
        sea_distribute['{{$id}}'] = '{{$item}}';
    @endforeach
    if (sea_claim[appid] != 2) {
        $("#claim").hide();
    }else{
        $("#claim").show();
    }
    if (sea_distribute[appid] != 2) {
        $("#distribute").hide();
    }else{
        $("#distribute").show();
    }

</script>

<script>
    var tree = "", table, treeLoad;

    layui.use('laydate', function () {
        var laydate = layui.laydate;

        laydate.render({
            elem: '#test12'

            , range: true
        });

    });




    var load_Table,doExport;
    layui.use(['index', 'table', 'form', 'common', 'http', 'tree', 'util'], function () {
        var name = $("#name").val();
        var created_at = $('#test12').val();
        var cus_level = $('#cus_level').val();
        var cus_source = $('#cus_source').val();
        var history_deal = $('#history_deal').val();
        var province_id = $('#test13').val();
        var form = layui.form, http = layui.http, common = layui.common,
            layer = layui.layer, util = layui.util;
        var tree = layui.tree;
        var table = layui.table;
        var appid = $(".layui-this").attr("data-a");



        //第一个实例
        var ins1=table.render({
            elem: '#demo'
            , method: 'post'
            , align: "right"
            , url: adminurl + "/crm/sea_customer_search"//数据接口
            , where: {
                _token: _token,
                // cus_type: cus_type,
                created_at: created_at,
                cus_level: cus_level,
                cus_source: cus_source,
                history_deal: history_deal,
                province_id: province_id,
                name: name,
                appid: appid
            }
            , page: true //开启分页

            , cols: [[
                {type: 'checkbox', value: "id"}
                , {
                    field: 'name', title: '客户名称', sort: true, event: 'setSign',
                    templet: function (item) {
                        var name = item.name ? item.name : item.mobile;
                        //return '<a class="layui-table-cell laytable-cell-3-name" style="color: #68ccf7">' + name + '</a>'
                        return '<a class="open-frame-r color-green" data-method="get" ' +
                            'title="客户详情" data-width="1050px" data-type="detail"' +
                            ' data-height="100%">' + name + '</a>';
                    }


                }
                , {
                    field: 'cus_type', title: '客户类型',
                    templet: function (item) {
                        if (item.cus_type == 1) {
                            return "租点用户";
                        } else if (item.cus_type == 2) {
                            return "租点网点";
                        }
                    }

                }
                , {
                    field: 'cus_level', title: '客户等级',
                    templet: function (item) {
                        if (item.cus_level == 0) {
                            return "暂无";
                        } else if (item.cus_level == 1) {
                            return "重点客户";
                        } else if (item.cus_level == 2) {
                            return "普通客户";
                        } else if (item.cus_level == 3) {
                            return "非优先客户";
                        }
                    }


                }
                , {
                    field: 'cus_source', title: '客户来源',
                    templet: function (item) {
                        if (item.cus_source == 1) {
                            return "APP录入";
                        } else if (item.cus_source == 2) {
                            return "租点系统";
                        } else if (item.cus_source == 3) {
                            return "中台录入";
                        }
                    }

                }
                , {field: 'mobile', title: '客户号码'}
                , {field: 'area', title: '所在地区'}
                , {field: 'pre_charger_name', title: '前负责人'}
                , {field: 'constract_end_at', title: '合同到期时间'}
                , {field: 'last_follow_at', title: '最后跟进'}
                , {field: 'created_at', title: '创建时间'}
            ]],
            // done: function (res, curr, count) {
            //     exportData=res.data; 		//获取表格中的数据集合。
            // }
        });

        doExport = function () {//检索筛选
            layer.confirm('速率约为500条/秒，确定导出?',function () {

                var params = {
                    cus_source: $('#cus_source').val(),
                    cus_level: $('#cus_level').val(),
                    history_deal: $('#history_deal').val(),
                    province_id: $('#test13').val(),
                    created_at : $('#test12').val(),
                    appid:$(".layui-this").attr("data-a"),
                    name:$("#name").val(),

                };
                queryString = $.param(params);
                window.location.href = "{{route('crm.reports.export')}}?" + queryString;
                layer.closeAll();
            })
        }


        load_Table = function (id) {
            if (!id) id = $(".layui-this").attr("data-a");


            var sea_claim = sea_distribute ={};
            var sea_distribute ={};

            @foreach($is_claim as $id=>$item)
                sea_claim['{{$id}}'] = '{{$item}}';
            @endforeach

                    @foreach($is_distribute as $id=>$item)
                sea_distribute['{{$id}}'] = '{{$item}}';
            @endforeach
            if (sea_claim[id] != 2) {
                $("#claim").hide();

            }else{
                $("#claim").show();
            }
            if (sea_distribute[id] != 2) {
                $("#distribute").hide();
            }else{
                $("#distribute").show();
            }


            table.reload('demo', {
                page: {curr: 1},
                where: {
                    province_id: $('#test13').val(),
                    _token: _token,
                    history_deal: $("#history_deal").val(),
                    created_at: $('#test12').val(),
                    cus_level: $("#cus_level").val(),
                    cus_source: $("#cus_source").val(),
                    name: $("#name").val(),
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
                var id = data.id;

                layer.open({
                    type: 2,
                    title: false,
                    closeBtn: 1, //不显示关闭按钮
                    shade: [0],
                    area: ['1050px', '100%'],
                    offset: 'r', //右下角弹出
                    anim: 2,
                    {{--content: '{{route('crm.reports.sea_customer_team_del',$model->id)}}',--}}
                    content: layui.common.route("{{route('crm.reports.sea_customer_detail')}}?id=" + id),
                    yes: function (index, layero) {
                        var frameWindow = window["layui-layer-iframe" + index];
                        if (common.isFunction(frameWindow.layerYesCallback)) {
                            frameWindow.layerYesCallback(index, layero);
                        }

                    }
                });


            }
        });

    });




    $("#claim").click(function () {
        var checkStatus = layui.table.checkStatus('demo').data;

        if (checkStatus == "") {
            return layer.msg('请选择数据');

        }

        layer.confirm(' 你确定认领这个客户吗？', {}, function () {
            var checkStatus = layui.table.checkStatus('demo').data;
            var arr = new Array();
            var table = layui.table;
            var appid = $(".layui-this").attr("data-a");
            $.each(checkStatus, function (i, n) {
                arr[i] = n.id;
            });
            var customer_id = arr.join(",");

            $.ajax({
                type: 'POST',
                data: {'customer_id': customer_id, 'appid': appid, '_token': _token},
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


            if (checkStatus == "") {
                return layer.msg('请选择数据');

            }
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


            if (checkStatus == "") {
                return layer.msg('请选择数据');

            }
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