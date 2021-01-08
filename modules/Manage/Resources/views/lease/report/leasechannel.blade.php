<!DOCTYPE html>
<html class="full-height">
<head>
    <meta charset="utf-8">
    <title>租赁业务--租赁渠道转化</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
    <script src="{{asseturl("lib/echarts/jquery-3.2.1.min.js")}}"></script>
    <script src="{{asseturl("lib/echarts/echarts.min.js")}}"></script>
    <script src="{{asseturl("lib/echarts/china.js?".time())}}"></script>

    <style>
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
        }

        div {
            text-align: center;
        }

        .layui-col-md12 {
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
            margin: 5px;
            padding: 5px;
        }

        .search-div {
            width: 100%;
            height: 65px;
        }

        .search-div select {
            padding: 3px 5px 5px 10px;
            font-size: 13px;
        }

    </style>
</head>
<body>

<div class="layui-col-md12">
    <div class="layui-col-md12">

        {{--各渠道转化图  开始--}}
        <div class="layui-card">
            <div class="layui-card-header">各渠道转化图</div>
            <div class="layui-card-body">
                <div class="search-div">
                    <div class="layui-form" style="float: right;width:800px;margin-top:10px;">
                        <div class="layui-inline">
                            <input type="text" id="datetimeclick" name='datetime' class="layui-input"
                                   placeholder="开始时间 - 结束时间"
                                   style="display: none" autocomplete="off">
                        </div>
                        <div class="layui-inline" align="left">
                            <select name="modules" lay-verify="required" lay-search="" id='clicktime'
                                    lay-filter="clicktime">
                                @foreach($timeType as $item)
                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline" align="left">
                            <select lay-verify="required" lay-search="" id="area"
                                    name="area">
                                @foreach($provinces as $id=>$province)
                                    <option value="{{$id}}">{{$province}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                    id='search' onclick="load_channel_data()"><i class="layui-icon"></i>搜索
                            </button>
                        </div>
                    </div>
                </div>
                <div class="div-mid" id="box_lease_time"></div>
            </div>
        </div>
    </div>
    {{--区域租赁数  结束--}}

    {{--各渠道转化统计表  开始--}}
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">各渠道转化统计表</div>
            <div class="layui-card-body">
                <div class="search-div">
                    <div class="layui-form" action="">
                        <div class="layui-inline">
                            <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" autocomplete="off"
                                   id="datetimeclicktwo">
                        </div>
                        <div class="layui-inline" align="left">
                            <select name="channelname" lay-search="" id='clicktimetwo'
                                    lay-filter="clicktimetwo">
                                <option value="">注册渠道</option>
                                <option value="android">安卓</option>
                                <option value="ios">ios</option>
                            </select>
                        </div>
                        <div class="layui-inline" align="left">
                            <select lay-search="" id="areatwo" name="areatwo">
                                @foreach($provinces as $id=>$province)
                                    <option value="{{$id}}">{{$province}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearchTwo"
                                    id='search' onclick="load_table()"><i class="layui-icon"></i>搜索
                            </button>
                        </div>
                    </div>

                </div>
                <table id="tabledata" lay-filter="test"></table>
            </div>
        </div>
        {{--各渠道转化统计表  结束--}}
    </div>
</div>

</body>
<script src="{{asseturl("lib/layui/layui.js")}}"></script>
<script type="text/javascript">
    var adminurl = "{{adminurl()}}";
    var _token = '{{ csrf_token() }}';
</script>

<script src="{{asseturl("js/lease/report/leasechannel.js?".time())}}"></script>
</html>