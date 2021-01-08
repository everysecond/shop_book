<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>租点—网点补货/库存</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
    <link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
    <script src="{{asseturl("lib/echarts/jquery-3.2.1.min.js")}}"></script>
    <script src="{{asseturl("lib/echarts/echarts.min.js")}}"></script>
    {{Html::style(asset('resource/css/common.css'))}}
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
            margin: 0 15px;
        }

        .layui-card .layui-tab-brief .layui-tab-title li {
            padding: 0 15px;
            margin: 0;
        }

        .search-div {
            width: 100%;
            height: 50px;
        }

        .search-div select {
            padding: 3px 5px 5px 10px;
            font-size: 13px;
        }

        .hidden {
            display: none;
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
    <div class="layui-card" style="padding: 0;">
        <div class="layui-card-body" style="padding: 0;height: 100%;">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs-brief">
                <ul class="layui-tab-title" style="border-bottom-color: #cccccc;margin: 0 10px;">
                    <li class="title-font layui-this" onclick="load_tab_1()">补货</li>
                    <li class="title-font" onclick="load_tab_2()">退库</li>
                </ul>
                <div class="layui-tab-content" style="padding: 0; height: 100%;">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-col-md6" style="padding: 0 5px 0 0">
                            <div class="bg-white">
                                <div class="layui-card-header title-font mar15">补货趋势</div>
                                <div class="layui-card-body">
                                    <div class="layui-card-body">
                                        <div class="search-div">
                                            <div class="layui-form" align="right" style="margin-top:10px;">
                                                <div class="layui-form-item">
                                                    <div class="layui-inline" style="width: 100px">
                                                        <select lay-verify="required" name='battery_type_1'>
                                                            <option value="">电池类型</option>
                                                            <option value="1">全新电池</option>
                                                            <option value="2">备用电池</option>
                                                        </select>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                                               autocomplete="off" id="datetime1"
                                                               style="display: none;width: 160px">
                                                    </div>
                                                    <div class="layui-inline" align="left" style="width: 100px">
                                                        <select lay-search="" id='tes2' lay-filter="demo12">
                                                            @foreach($timeType as $item)
                                                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    {{--                                            <div class="layui-inline" align="left">--}}
                                                    {{--                                                <select name="modules" lay-search="" id='test13'>--}}
                                                    {{--                                                    @foreach($provinces2 as $id=>$province)--}}
                                                    {{--                                                        <option value="{{$id}}">{{$province}}</option>--}}
                                                    {{--                                                    @endforeach--}}
                                                    {{--                                                </select>--}}
                                                    {{--                                            </div>--}}
                                                    <div class="layui-inline">
                                                        <button class="layui-btn layui-btn-normal"
                                                                onclick="lease_time_hour()">
                                                            <i class="layui-icon"></i>搜索
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="div-mid" id="lease_time"
                                             style="margin-bottom: 15px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md6" style="padding: 0 0 0 5px">
                            <div class="bg-white">
                                <div class="layui-card-header title-font mar15">补货分析</div>
                                <div class="layui-card-body">
                                    <div class="layui-card-body">
                                        <div class="search-div">
                                            <div class="layui-form" align="right" style="margin-top:10px;">
                                                <div class="layui-form-item">
                                                    <div class="layui-inline" style="width: 100px">
                                                        <select lay-verify="required" name='battery_type_2'>
                                                            <option value="">电池类型</option>
                                                            <option value="1">全新电池</option>
                                                            <option value="2">备用电池</option>
                                                        </select>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                                               autocomplete="off" id="datetime2"
                                                               style="display: none;width: 160px">
                                                    </div>
                                                    <div class="layui-inline" align="left"  style="width: 100px">
                                                        <select name="modules" lay-verify="required" lay-search=""
                                                                id='time2' lay-filter="demo13">
                                                            @foreach($timeType as $item)
                                                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <button class="layui-btn layui-btn-normal"
                                                                onclick="load_broken()">
                                                            <i class="layui-icon"></i>搜索
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="div-mid" id="box2"
                                             style="margin-bottom: 15px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md12" style="padding: 10px 0 0 0">
                            <div class="bg-white">
                                <div class="layui-card-header title-font mar15">补货统计表</div>
                                <div class="layui-form" align="right" style="width:100%;margin-top:10px;">
                                    <div class="layui-form-item">
                                        <div class="layui-inline" align="left">
                                            <select name='battery_type_3'>
                                                <option value="">电池类型</option>
                                                <option value="1">全新电池</option>
                                                <option value="2">备用电池</option>
                                            </select>
                                        </div>
                                        <div class="layui-inline" align="left">
                                            <select name='num_type'>
                                                <option value="1">补货申请数</option>
                                                <option value="2">补货电池组数</option>
                                            </select>
                                        </div>
                                        <div class="layui-inline">
                                            <div class="layui-input-inline">
                                                <input type="text" class="layui-input"
                                                       autocomplete="off" placeholder="日期范围" id="dateX">
                                            </div>
                                        </div>
                                        <div class="layui-inline" style="margin-right: 25px;">
                                            <button class="layui-btn layui-btn-normal" lay-submit=""
                                                    lay-filter="formSearch"
                                                    onclick="load_trend_table()"><i class="layui-icon"></i>搜索
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div style="padding: 5px 27px 25px 23px;">
                                    <table id="trend_table"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-tab-item">
                        <div class="layui-col-md6" style="padding: 0 5px 0 0">
                            <div class="bg-white">
                                <div class="layui-card-header title-font mar15">退库趋势</div>
                                <div class="layui-card-body">
                                    <div class="layui-card-body">
                                        <div class="search-div">
                                            <div class="layui-form" align="right" style="margin-top:10px;">
                                                <div class="layui-form-item">
                                                    <div class="layui-inline" style="width: 100px">
                                                        <select lay-verify="required" name='battery_type_4'>
                                                            <option value="">电池类型</option>
                                                            <option value="1">全新电池</option>
                                                            <option value="2">备用电池</option>
                                                        </select>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                                               autocomplete="off" id="datetime4"
                                                               style="display: none;width: 160px">
                                                    </div>
                                                    <div class="layui-inline" align="left" style="width: 100px">
                                                        <select lay-search="" id='tes4' lay-filter="demo4">
                                                            @foreach($timeType as $item)
                                                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    {{--                                            <div class="layui-inline" align="left">--}}
                                                    {{--                                                <select name="modules" lay-search="" id='test13'>--}}
                                                    {{--                                                    @foreach($provinces2 as $id=>$province)--}}
                                                    {{--                                                        <option value="{{$id}}">{{$province}}</option>--}}
                                                    {{--                                                    @endforeach--}}
                                                    {{--                                                </select>--}}
                                                    {{--                                            </div>--}}
                                                    <div class="layui-inline">
                                                        <button class="layui-btn layui-btn-normal"
                                                                onclick="lease_time_hour4()">
                                                            <i class="layui-icon"></i>搜索
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="div-mid" id="lease_time4"
                                             style="margin-bottom: 15px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md6" style="padding: 0 0 0 5px">
                            <div class="bg-white">
                                <div class="layui-card-header title-font mar15">退库分析</div>
                                <div class="layui-card-body">
                                    <div class="layui-card-body">
                                        <div class="search-div">
                                            <div class="layui-form" align="right" style="margin-top:10px;">
                                                <div class="layui-form-item">
                                                    <div class="layui-inline" style="width: 100px">
                                                        <select lay-verify="required" name='battery_type_5'>
                                                            <option value="">电池类型</option>
                                                            <option value="1">全新电池</option>
                                                            <option value="2">备用电池</option>
                                                        </select>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                                               autocomplete="off" id="datetime5"
                                                               style="display: none;width: 160px">
                                                    </div>
                                                    <div class="layui-inline" align="left"  style="width: 100px">
                                                        <select name="modules" lay-verify="required" lay-search=""
                                                                id='time5' lay-filter="demo5">
                                                            @foreach($timeType as $item)
                                                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <button class="layui-btn layui-btn-normal"
                                                                onclick="load_broken5()">
                                                            <i class="layui-icon"></i>搜索
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="div-mid" id="box5"
                                             style="margin-bottom: 15px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md12" style="padding: 10px 0 0 0">
                            <div class="bg-white">
                                <div class="layui-card-header title-font mar15">退库统计表</div>
                                <div class="layui-form" align="right" style="width:100%;margin-top:10px;">
                                    <div class="layui-form-item">
                                        <div class="layui-inline" align="left">
                                            <select name='battery_type_6'>
                                                <option value="">电池类型</option>
                                                <option value="1">全新电池</option>
                                                <option value="2">备用电池</option>
                                            </select>
                                        </div>
                                        <div class="layui-inline" align="left">
                                            <select name='num_type6'>
                                                <option value="1">退库申请数</option>
                                                <option value="2">退库电池组数</option>
                                            </select>
                                        </div>
                                        <div class="layui-inline">
                                            <div class="layui-input-inline">
                                                <input type="text" class="layui-input"
                                                       autocomplete="off" placeholder="日期范围" id="date6">
                                            </div>
                                        </div>
                                        <div class="layui-inline" style="margin-right: 25px;">
                                            <button class="layui-btn layui-btn-normal" lay-submit=""
                                                    lay-filter="formSearch"
                                                    onclick="load_trend_table6()"><i class="layui-icon"></i>搜索
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div style="padding: 5px 27px 25px 23px;">
                                    <table id="trend_table6"></table>
                                </div>
                            </div>
                        </div>
                    </div>
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
    var provinceArr = [];
    @forEach(allUserProvinces() as $province)
    provinceArr.push('{{$province}}');
    @endforeach
    if(provinceArr.indexOf("全部区域") == -1){
        provinceArr = ["全部区域"].concat(provinceArr);
    }
</script>
<script src="{{asseturl("js/lease/common.js?").time()}}"></script>
<script src="{{asseturl("js/lease/service/supply.js?").time()}}"></script>
</body>
</html>

