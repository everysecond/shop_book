<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>网点—注册审核</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
    <script src="{{asseturl("lib/echarts/jquery-3.2.1.min.js")}}"></script>
    <script src="{{asseturl("lib/echarts/echarts.min.js")}}"></script>
    <style>
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
        }

        .layui-col-md12, .layui-col-md6, .layui-card {
            padding: 10px
        }

        .title-font {
            font-family: 'Arial Negreta', 'Arial Normal', 'Arial';
            font-weight: 700;
            font-style: normal;
        }

        .search-div{
            width: 100%;
            height:50px;
        }

        .div-mid {
            width: 100%;
            height: 450px;
        }

        .layui-card-header {
            border-bottom: 1px solid #CCCCCC;
        }
    </style>
</head>
<body>
<div class="layui-col-md12">
    <div class="layui-card" style="margin-bottom:10px;">
        <div class="layui-card-header title-font mar15">注册审核趋势</div>
        <div class="layui-card-body">
            <div class="layui-card-body">
                <div class="search-div">
                    <div class="layui-form" style="float: right;">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                           autocomplete="off" id="date1" style="display: none">
                                </div>
                            </div>
                            <div class="layui-inline" align="left">
                                <select name="days1" lay-verify="required" lay-search=""
                                        lay-filter="timeType1">
                                    @foreach($timeType as $item)
                                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-inline" align="left">
                                <select name="register_day_agents" lay-verify="required" lay-search="">
                                    @foreach($provinces as $id=>$province)
                                        <option value="{{$id}}">{{$province}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-inline">
                                <button class="layui-btn layui-btn-normal" onclick="load_lease_trend()"
                                        lay-filter="formSearch"><i class="layui-icon"></i>搜索
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="div-mid" id="box_lease_trend" style="margin-bottom: 15px"></div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header title-font mar15">注册审核趋势统计</div>
        <div class="layui-form" align="right" style="width:100%;margin-top:10px;">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input"
                               autocomplete="off" placeholder="日期范围" id="date2">
                    </div>
                </div>
                <div class="layui-inline" align="left">
                    <select name="agentId2" lay-verify="required" lay-search="">
                        @foreach($provinces as $id=>$province)
                            <option value="{{$id}}">{{$province}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-inline" style="margin-right: 25px;">
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                            onclick="load_lease_statistics()"><i class="layui-icon"></i>搜索
                    </button>
                </div>
            </div>
        </div>
        <div style="margin: 5px 27px 25px 23px;">
            <table id="table_total"></table>
        </div>
    </div>
</div>

<script src="{{asseturl("lib/layuiadmin/layui/layui.js")}}"></script>
<script>
    var _token = '{{ csrf_token() }}';
    var adminurl = "{{adminurl()}}";
    layui.config({
        base: "{{asseturl("lib")}}" + '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'console']);
</script>
<script src="{{asseturl("js/lease/common.js?").time()}}"></script>
<script src="{{asseturl("js/lease/service/register_view.js?").time()}}"></script>
</body>
</html>