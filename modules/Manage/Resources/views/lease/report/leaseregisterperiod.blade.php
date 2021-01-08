<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>租点—新用户租赁</title>
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

        .layui-col-md12, .layui-card {
            padding: 10px
        }

        .title-font {
            font-family: 'Arial Negreta', 'Arial Normal', 'Arial';
            font-weight: 700;
            font-style: normal;
        }

        .layui-card-header {
            border-bottom: 1px solid #CCCCCC;
        }

        .mar15 {
            margin: 0 25px;
        }

        .layui-card .layui-tab-brief .layui-tab-title li {
            padding: 0 15px;
            margin: 0;
        }


        .search-div select {
            padding: 3px 5px 5px 10px;
            font-size: 13px;
        }

        div {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="layui-col-md12">
    <div class="layui-card" style="padding: 0;">
        <div class="layui-card-body" style="padding: 0;">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs-brief">
                <div class="layui-tab-content" style="padding: 0;">
                    <div class="layui-card-header title-font mar15">注册-租赁发起周期</div>
                    <div class="layui-form" align="right" style="width:100%;margin-top:10px;">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" placeholder="日期范围" autocomplete="off" id="datetime">
                                </div>
                            </div>
                            <div class="layui-inline" align="left">
                                <select name="agentId1" lay-verify="required" lay-search="">
                                    @foreach($provinces as $id=>$province)
                                        <option value="{{$id}}">{{$province}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-inline">
                                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                        onclick="load_day_table()"><i class="layui-icon"></i>搜索
                                </button>

                            </div>
                        </div>
                    </div>
                    <table style="padding: 15px" id="table_day"></table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<!----添加角色----->
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
<script src="{{asseturl("js/lease/report/leaseregisterperiod.js?".time())}}"></script>
</html>