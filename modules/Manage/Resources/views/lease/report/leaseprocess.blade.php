<!DOCTYPE html>
<html class="full-height">
<head>
    <meta charset="utf-8">
    <title>租赁业务--登录-租赁</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
    <script src="{{asseturl("lib/echarts/echarts.min.js")}}"></script>
    <script src="{{asseturl("lib/echarts/jquery.js")}}"></script>

    <style>
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
        }

        div {
            /*padding: 1px;*/
            /*line-height: 1px;*/
            text-align: center;
        }

        .layui-col-md12, .layui-card {
            padding: 5px
        }

        .title-font {
            font-family: 'Arial Negreta', 'Arial Normal', 'Arial';
            font-weight: 700;
            font-style: normal;
        }

        .div-mid {
            width: 100%;
            height: 450px;
        }

        .layui-card-header {
            border-bottom: 1px solid #CCCCCC;
        }

        .pad10 {
            padding: 10px 10px 0 15px;
        }

        .pad10-15 {
            padding: 10px 15px;
        }

        .mar15 {
            margin: 0 25px;
        }

        .layui-card .layui-tab-brief .layui-tab-title li {
            padding: 0 15px;
            margin: 0;
        }

        .search-div {
            width: 100%;
            height: 30px;
        }

        .search-div select {
            padding: 3px 5px 5px 10px;
            font-size: 13px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<div class="layui-col-md12">
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                <ul class="layui-tab-title">
                    {{--<li class="layui-this" onclick="load_tab_1()">每小时转化</li>--}}
                    {{--<li onclick="load_tab_2()">累计每小时转化</li>--}}
                    <li onclick="load_tab_3()">转化统计</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">

                        {{--1 每小时转化漏斗  开始--}}
                        {{--<div class="layui-row">--}}
                            {{--<div id="box2" class="layui-col-md7" style="height:650px;">--}}
                            {{--</div>--}}
                            {{--<div id="box1" class="layui-col-md5">--}}
                                {{--<div class="layui-form" action="" align="right">--}}
                                    {{--<div class="layui-inline" align="left">--}}
                                        {{--<select id="datetime" name="datetime" class="layui-input" lay-filter="datetime"--}}
                                                {{--lay-verify="" placeholder=""--}}
                                                {{--lay-search>--}}
                                            {{--写一个循环--}}
                                            {{--<option value="">时间段选择</option>--}}
                                            {{--@foreach($timehourarr as $k=>$hour)--}}
                                                {{--<option value="{{$k}}">{{$hour}}</option>--}}
                                            {{--@endforeach--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                    {{--<div class="layui-inline" align="left">--}}
                                        {{--<select id="area" name="area" lay-search="">--}}
                                            {{--@foreach($provinces as $id=>$province)--}}
                                                {{--<option value="{{$id}}">{{$province}}</option>--}}
                                            {{--@endforeach--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                    {{--<div class="layui-inline">--}}
                                        {{--<button class="layui-btn layui-btn-normal" lay-submit lay-filter="formSearch"  onclick=" load()"><i--}}
                                                    {{--class="layui-icon">&#xe615;</i>搜索--}}
                                        {{--</button>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--1 每小时转化漏斗  结束--}}

                        {{--1 每小时流失统计表  开始--}}
                        {{--<div>--}}


                            {{--<div style="width: 100%;border-top: 10px solid #f2f2f2"></div>--}}
                            {{--<div class="layui-card-header title-font mar15">每小时转化统计表</div>--}}
                            {{--<div class="layui-form" align="right"--}}
                                 {{--style=" width:100%;margin-top:10px; margin-bottom:15px;">--}}
                                {{--<div class="layui-form-item">--}}
                                    {{--<div class="layui-inline">--}}
                                        {{--<div class="layui-input-inline">--}}
                                            {{--<input type="text" class="layui-input" placeholder="点击选择时间" autocomplete="off" id="todatetime"--}}
                                                   {{--name="todatetime">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="layui-inline" align="right">--}}
                                        {{--<select id="toarea" name="toarea" lay-verify="required" lay-search="">--}}
                                            {{--@foreach($provinces as $id=>$province)--}}
                                                {{--<option value="{{$id}}">{{$province}}</option>--}}
                                            {{--@endforeach--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                    {{--<div class="layui-inline">--}}
                                        {{--<button class="layui-btn layui-btn-normal" lay-submit=""--}}
                                                {{--lay-filter="formSearchTwo"--}}
                                                {{--onclick="load_hour_table()"><i class="layui-icon"></i>搜索--}}
                                        {{--</button>--}}

                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}


                            {{--<table id="tabledata" lay-filter="test"></table>--}}
                        {{--</div>--}}
                        {{--1 每小时流失统计表  结束--}}
                    {{--</div>--}}
                    {{--<div class="layui-tab-item">--}}
                        {{--2 累计每小时转化漏斗  开始--}}
                        {{--<div class="layui-row">--}}
                            {{--<div id="cumbox2" class="layui-col-md7" style="height:650px;">--}}
                            {{--</div>--}}
                            {{--<div class="layui-col-md5">--}}
                                {{--<div class="layui-form" action="">--}}
                                    {{--<div class="layui-inline" align="left">--}}
                                        {{--<select id="cumtime" name="cumtime" class="layui-input"--}}
                                                {{--lay-filter="datetime">--}}
                                            {{--写一个循环--}}
                                            {{--<option value="">时间段选择</option>--}}
                                            {{--@foreach($timehoursarr as $k=>$hour)--}}
                                                {{--<option value="{{$k}}">{{$hour}}</option>--}}
                                            {{--@endforeach--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                    {{--<div class="layui-inline" align="left">--}}
                                        {{--<select lay-search="" id="cumarea"--}}
                                                {{--name="cumarea">--}}
                                            {{--@foreach($provinces as $id=>$province)--}}
                                                {{--<option value="{{$id}}">{{$province}}</option>--}}
                                            {{--@endforeach--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                    {{--<div class="layui-inline">--}}
                                        {{--<button class="layui-btn layui-btn-normal" lay-submit--}}
                                                {{--lay-filter="cumformSearch" onclick="cumload()"><i--}}
                                                    {{--class="layui-icon" ></i>搜索--}}
                                        {{--</button>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--2 累计每小时转化漏斗  结束--}}

                        {{--2 累计每小时流失统计表  开始--}}
                        {{--<div>--}}

                            {{--<div style="width: 100%;border-top: 10px solid #f2f2f2"></div>--}}
                            {{--<div class="layui-card-header title-font mar15">累计每小时流失统计表</div>--}}
                            {{--<div class="layui-form" align="right"--}}
                                 {{--style=" width:100%;margin-top:10px; margin-bottom:15px;">--}}
                                {{--<div class="layui-form-item">--}}
                                    {{--<div class="layui-inline">--}}
                                        {{--<div class="layui-input-inline">--}}
                                            {{--<input type="text" class="layui-input" placeholder="点击选择时间" autocomplete="off" id="cumdatetime"--}}
                                                   {{--name="cumdatetime">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="layui-inline" align="right">--}}
                                        {{--<select id="cumtoarea" name="cumtoarea" lay-verify="required" lay-search="">--}}
                                            {{--@foreach($provinces as $id=>$province)--}}
                                                {{--<option value="{{$id}}">{{$province}}</option>--}}
                                            {{--@endforeach--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                    {{--<div class="layui-inline">--}}
                                        {{--<button class="layui-btn layui-btn-normal" lay-submit=""--}}
                                                {{--lay-filter="formSearchTwo"--}}
                                                {{--onclick="load_day_table()"><i class="layui-icon"></i>搜索--}}
                                        {{--</button>--}}

                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            {{--<table id="cumtabledata" lay-filter="test"></table>--}}
                        {{--</div>--}}

                        {{--2 累计每小时流失统计表  结束--}}
                    {{--</div>--}}
                    {{--<div class="layui-tab-item">--}}
                        {{--3 转化漏斗  开始--}}
                        <div class="layui-row">
                            <div id="totalbox2" class="layui-col-md7" style="height:650px;">
                            </div>
                            <div class="layui-col-md5">
                                <div class="layui-form" action="">

                                    <div class="layui-inline" wth180> <!-- 注意：这一层元素并不是必须的 -->
                                        <input type="text" class="layui-input" id="totaltime" name="totaltime"
                                               placeholder="点击选择时间" autocomplete="off">
                                    </div>


                                    <div class="layui-inline" align="left">
                                        <select lay-search="" id="totalarea"
                                                name="totalarea">
                                            @foreach($provinces as $id=>$province)
                                                <option value="{{$id}}">{{$province}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="layui-inline">
                                        <button class="layui-btn layui-btn-normal" lay-submit
                                                lay-filter="totalformSearch" onclick="totalload()"><i
                                                    class="layui-icon" >&#xe615;</i>搜索
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--3 转化漏斗  结束--}}

                        {{--3 转化统计表 开始--}}
                        <div>
                            <div style="width: 100%;border-top: 10px solid #f2f2f2"></div>
                            <div class="layui-card-header title-font mar15">转化统计表</div>
                            <div class="layui-form" align="right"
                                 style=" width:100%;margin-top:10px; margin-bottom:15px;">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <input type="text" class="layui-input" placeholder="点击选择时间" autocomplete="off"
                                                   id="totaldatetime"
                                                   name="totaldatetime">
                                        </div>
                                    </div>
                                    <div class="layui-inline" align="right">
                                        <select id="totaltoarea" name="totaltoarea" lay-search="">
                                            @foreach($provinces as $id=>$province)
                                                <option value="{{$id}}">{{$province}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="layui-inline">
                                        <button class="layui-btn layui-btn-normal" lay-submit=""
                                                lay-filter="formSearchTwo"
                                                onclick="load_day_total_table()"><i class="layui-icon"></i>搜索
                                        </button>

                                    </div>
                                </div>
                            </div>

                            <table id="totaltabledata" lay-filter="test"></table>
                        </div>
                        {{--3 转化统计表 结束--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</body>
<script src="{{asseturl("lib/layui/layui.js")}}"></script>

<script type="text/javascript">

    var _token = '{{ csrf_token() }}';
    var adminurl = "{{adminurl()}}";
    layui.config({
        base: "{{asseturl("lib")}}" + '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'console']);
</script>
<script src="{{asseturl("js/lease/report/leaseprocess.js?".time())}}"></script>

</html>