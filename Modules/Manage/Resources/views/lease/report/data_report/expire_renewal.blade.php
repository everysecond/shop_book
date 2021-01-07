<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>租点—续租统计</title>
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

        .search-div .layui-form-item {
            float: right;
        }

        .search-div select {
            padding: 3px 5px 5px 10px;
            font-size: 13px;
        }

        select, input {
            padding: 0 3px !important;
        }

        .layui-inline {
            margin-right: 3px !important;
        }
    </style>
</head>
<body>
<div class="layui-col-md12">
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">注册-租赁周期</div>
            <div class="layui-card-body">
                <div class="layui-form search-div">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" id="test10" placeholder="开始时间 - 结束时间"
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="layui-inline" align="left">
                            <select name="modules" lay-verify="required" lay-search="" id='test'>
                                @foreach($provinces as $id=>$province)
                                    <option value="{{$id}}">{{$province}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                    id='search'><i
                                        class="layui-icon"></i>搜索
                            </button>

                        </div>
                    </div>
                </div>
                <div style="margin: 10px 5px 10px 0;">
                    <table id="demo" style="padding: 15px" lay-filter="test"></table>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">退租-租赁周期</div>
            <div class="layui-card-body">
                <div class="layui-form search-div">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" id="test12" placeholder="开始时间 - 结束时间"
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="layui-inline" align="left">
                            <select name="modules" lay-verify="required" lay-search="" id='test2'>
                                {{--                            <option value="0">全部区域</option>--}}
                                @foreach($provinces as $id=>$province)
                                    <option value="{{$id}}">{{$province}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                    id='search2'><i
                                        class="layui-icon"></i>搜索
                            </button>

                        </div>
                    </div>
                </div>
                <div style="margin: 10px 5px 10px 0;">
                    <table id="demo2" lay-filter="test"></table>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">到期-续租周期</div>
            <div class="layui-card-body">
                <div class="layui-form search-div">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" id="test11" placeholder="开始时间 - 结束时间"
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="layui-inline" align="left">
                            <select name="modules" lay-verify="required" lay-search="" id='test1'>
                                {{--                            <option value="0">全部区域</option>--}}
                                @foreach($provinces as $id=>$province)
                                    <option value="{{$id}}">{{$province}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                    id='search1'><i
                                        class="layui-icon"></i>搜索
                            </button>

                        </div>
                    </div>
                </div>
                <div style="margin: 10px 5px 10px 0;">
                    <table id="demo1" lay-filter="test1"></table>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">续租客户来源</div>
            <div class="layui-card-body">
                <div class="layui-form search-div">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" id="test13" placeholder="开始时间 - 结束时间"
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="layui-inline" align="left">
                            <select name="modules" lay-verify="required" lay-search="" id='test3'>
                                {{--                            <option value="0">全部区域</option>--}}
                                @foreach($provinces as $id=>$province)
                                    <option value="{{$id}}">{{$province}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                    id='search3'><i
                                        class="layui-icon"></i>搜索
                            </button>

                        </div>
                    </div>
                </div>
                <div style="margin: 10px 5px 10px 0;">
                    <table id="demo3" lay-filter="test"></table>
                </div>
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
<script src="{{asseturl("js/lease/report/expire_renewal.js?").time()}}"></script>
</body>
</html>


