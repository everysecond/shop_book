<!DOCTYPE html>
<html class="full-height">
<head>
    <meta charset="utf-8">
    <title>租点—租赁数量对比</title>
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
        height: 450px;
    }
</style>
<div class="layui-col-md12">
<div class="layui-col-md12">
    <div class="layui-card">
        <div class="layui-form" align="right" style="margin-top:10px;">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" autocomplete="off" id="datetime1"
                               style="display: none">
                    </div>
                </div>
                <div class="layui-inline" align="left">
                    <select name="modules" lay-verify="required" lay-search="" id='tes2' lay-filter="demo12">
                        @foreach($timeType as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-inline" align="left">
                    <select name="modules" lay-verify="required" lay-search="" id='test13'>
                        @foreach($provinces as $id=>$province)
                            <option value="{{$id}}">{{$province}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" id='search2'><i
                                class="layui-icon"></i>搜索
                    </button>

                </div>
            </div>
        </div>
        <div class="layui-card-header title-font mar15">租赁数量对比</div>
        <div class="div-mid" id="lease_time_hour"></div>
    </div>
</div>


<div class="layui-col-md12">
    <div class="layui-card">
        <div class="layui-form" align="right" style="margin-top:10px;">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" autocomplete="off" id="datetime2"
                               style="display: none">
                    </div>
                </div>
                <div class="layui-inline" align="left">
                    <select name="modules" lay-verify="required" lay-search="" id='time2' lay-filter="demo13">
                        @foreach($timeType as $item)
                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-inline" align="left">
                    <select name="modules" lay-verify="required" lay-search="" id='test12'>
                        @foreach($provinces as $id=>$province)
                            <option value="{{$id}}">{{$province}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" id='search3' onclick="load_broken()"><i
                                class="layui-icon" ></i>搜索
                    </button>

                </div>
            </div>
        </div>
        <div class="layui-card-header title-font mar15">租赁金额对比</div>
        <div id="box2" style="height:450px;"></div>
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

<script src="{{asseturl("js/lease/report/leasenewold.js?").time()}}"></script>
</body>
</html>