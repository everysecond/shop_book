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

        .layui-card{
            margin-bottom: 10px;
        }
        .layui-card>.layui-card-body>.layui-col-md12{
            padding: 0 !important;
        }
    </style>


        <div class="layui-col-md12" align="center" style="height:100%;">
        <div class="layui-card">
        <div class="layui-form" align="right" style="width:1380px;margin-top:10px;">
        <div class="layui-form-item">
        <div class="layui-inline">
        <div class="layui-input-inline">
        <input type="text" class="layui-input" id="test10" placeholder="开始时间 - 结束时间" autocomplete="off"
        >
        </div>
        </div>
        <div class="layui-inline" align="left"  >
        <select name="modules" lay-verify="required" lay-search="" id = 'test'  >
{{--        <option value="0">全部区域</option>--}}
    @foreach($provinces as $id=>$province)
    <option value="{{$id}}">{{$province}}</option>
    @endforeach
    </select>
    </div>
    <div class="layui-inline">
        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" id = 'search'><i class="layui-icon"></i>搜索
    </button>

    </div>
    </div>
    </div>


    <table id="demo" lay-filter="test" ></table>

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

    <script src="{{asseturl("js/lease/report/rebate_rent.js?").time()}}"></script>
    </body>
    </html>