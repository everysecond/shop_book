<!DOCTYPE html>
<html class="full-height">
<head>
    <meta charset="utf-8">
    <title>租赁业务--租赁区域</title>
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
            /*padding: 5px;*/
            /*line-height: 5px;*/
            text-align: center;
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
            margin: 5px;
            padding: 5px;
        }

        .search-div {
            width: 100%;
            height: 30px;
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
        <div class="layui-card">
            {{--地区分布  结束--}}
            <div class="layui-card-header">租赁区域分布</div>
            <div class="layui-card-body">
                <div class="search-div">
                    <form class="layui-form" action="" style="float: right;width:800px;margin-top:10px;">
                        <div class="layui-inline">
                            <input value="" name='datetime' type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                   id="datetimeclick"
                                   style="display: none" autocomplete="off" >
                        </div>
                        <div class="layui-inline" align="left">
                            <select name="modules" lay-verify="required" lay-search="" id='clicktime'
                                    lay-filter="clicktime">
                                @foreach($timeType as $item)
                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formSearch"><i
                                        class="layui-icon">&#xe615;</i>搜索
                            </button>
                        </div>
                    </form>
                </div>
                <div class="div-mid" id="box_china_map"></div>
            </div>
        </div>
    </div>

    {{--地区分布  结束--}}

        {{--区域租赁数  开始--}}

        <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">区域租赁数</div>
            <div class="layui-card-body">
                <div class="search-div">
                    {{--<blockquote class="site-text layui-elem-quote">--}}
                    <form class="layui-form" action="" style="float: right;width:800px;margin-top:10px;">
                        <div class="layui-inline">
                            <input value="" name='datetimetwo' type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                   id="datetimeclicktwo"
                                   style="display: none" autocomplete="off">
                        </div>
                        <div class="layui-inline" align="left">
                            <select name="modtimetwo" lay-verify="required" lay-search="" id='clicktimetwo'
                                    lay-filter="clicktimetwo">
                                @foreach($timeType as $item)
                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formSearchTwo"><i
                                        class="layui-icon">&#xe615;</i>搜索
                            </button>
                        </div>
                    </form>
                </div>
                <div class="div-mid" id="box_lease_time"></div>
                {{--区域租赁数  结束--}}
            </div>
        </div>
    </div>


</div>


</body>
<script type="text/javascript">
    var adminurl = "{{adminurl()}}";
    var _token = '{{ csrf_token() }}';
</script>
<script src="{{asseturl("lib/layui/layui.js")}}"></script>
<script src="{{asseturl("js/lease/report/leasearea.js?".time())}}"></script>
</html>