<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>租点—用户分析—终端下载</title>
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
            padding: 5px
        }

        .layui-card-header {
            font-family: 'Arial Negreta', 'Arial Normal', 'Arial';
            font-weight: 700;
            font-style: normal;
        }

        .div-mid {
            width: 100%;
            height: 400px;
            margin: 0 auto;
        }

        .search-div {
            width: 100%;
            height: 50px;
        }

        .search-div select {
            padding: 3px 5px 5px 10px;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="layui-col-md12">
    <div class="layui-col-md6">
        <div class="layui-card">
            <div class="layui-card-header">下载趋势</div>
            <div class="layui-card-body">
                <div class="layui-form search-div">
                    <div class="layui-form-item" style="float: right;">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                       autocomplete="off" id="date0" style="display: none">
                            </div>
                        </div>
                        <div class="layui-inline" align="left" style="width: 140px">
                            <select name="days0" lay-verify="required" lay-search=""
                                    lay-filter="timeType0">
                                @foreach($timeType as $item)
                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                    onclick="load_register_day()"><i class="layui-icon"></i>搜索
                            </button>
                        </div>
                    </div>
                </div>
                <div class="div-mid" id="box_trend"></div>
            </div>
        </div>
    </div>
    <div class="layui-col-md6">
        <div class="layui-card">
            <div class="layui-card-header">下载量排行</div>
            <div class="layui-card-body">
                <div class="layui-form search-div">
                    <div class="layui-form-item" style="float: right;">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                       autocomplete="off" id="date1" style="display: none">
                            </div>
                        </div>
                        <div class="layui-inline" align="left"  style="width: 140px">
                            <select name="days1" lay-verify="required" lay-search=""
                                    lay-filter="timeType1">
                                @foreach($timeType as $item)
                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                    onclick="load_lease_time()"><i class="layui-icon"></i>搜索
                            </button>
                        </div>
                    </div>
                </div>
                <div class="div-mid" id="box_income"></div>
            </div>
        </div>
    </div>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">下载统计表</div>
            <div class="layui-form" align="right" style="width:100%;margin-top:10px;">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input"
                                   autocomplete="off" placeholder="日期范围" id="date2">
                        </div>
                    </div>
                    <div class="layui-inline" style="margin-right: 25px;">
                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                onclick="load_table()"><i class="layui-icon"></i>搜索
                        </button>
                    </div>
                </div>
            </div>
            <div style="margin: 10px 5px 10px 0;">
                <table style="padding: 15px" id="table_income"></table>
            </div>
        </div>
    </div>
</div>

<script src="{{asseturl("lib/layuiadmin/layui/layui.js")}}"></script>
<script>
    var _token = '{{ csrf_token() }}';
    var adminurl = "{{adminurl()}}";
    var provinceArr = [];
    @forEach($channelArr as $channel=>$channelName)
        provinceArr['{{$channel}}'] = '{{$channelName}}';
    @endforeach
    layui.config({
        base: "{{asseturl("lib")}}" + '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'console']);
</script>
<script src="{{asseturl("js/lease/common.js?").time()}}"></script>
<script src="{{asseturl("js/lease/report/terminal.js?").time()}}"></script>
</body>
</html>

