<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>网点收益-余额</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
    <script src="{{asseturl("lib/echarts/jquery-3.2.1.min.js")}}"></script>
    <script src="{{asseturl("lib/echarts/echarts.min.js")}}"></script>
    <style>
        html, body{
            width: 100%;
            height: 100%;
            margin: 0;
        }
        .layui-col-md12,.layui-card{padding: 5px}
        .layui-card-header{
            font-family: 'Arial Negreta', 'Arial Normal', 'Arial';
            font-weight: 700;
            font-style: normal;
        }
        .div-mid{
            width:100%;
            height:400px;
            margin: 0 auto;
        }
        .search-div{
            width: 100%;
            height:50px;
        }
        .search-div select{
            padding: 3px 5px 5px 10px;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="layui-col-md12">
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">余额金额分布</div>
            <div class="layui-card-body">
                <div class="layui-form search-div">
                    <div class="layui-form-item"  style="float: right;">
                        <div class="layui-inline" align="right">
                            <select style="float: right;" name="agents1">
                                @foreach($provinces as $id=>$province)
                                    <option value="{{$id}}">{{$province}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" onclick="load_lease_time()"><i class="layui-icon"></i>搜索
                            </button>
                        </div>
                    </div>
                </div>
                <div class="div-mid" id="box_lease_time"></div>
            </div>
        </div>
    </div>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">各区域余额统计</div>
            <div class="layui-card-body">
                <div class="div-mid" id="box_stock"></div>
            </div>
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
<script src="{{asseturl("js/lease/service/balance.js?").time()}}"></script>
</body>
</html>

