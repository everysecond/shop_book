<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>租点—库存统计</title>
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
    </style>
</head>
<body>
<div class="layui-col-md12">
    <div class="layui-card" style="padding: 0;">
        <div class="layui-card-body" style="padding: 0;height: 100%;">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs-brief">
                <ul class="layui-tab-title" style="border-bottom-color: #cccccc;margin: 0 10px;">
                    <li class="title-font layui-this" onclick="load_tab_1()">区域库存</li>
                    <li class="title-font" onclick="load_tab_2()">电池型号</li>
                </ul>
                <div class="layui-tab-content" style="padding: 0; height: 100%;">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-col-md12" style="padding: 0">
                            <div class="bg-white">
                                <div class="layui-card-header title-font mar15">区域占比</div>
                                <div class="layui-card-body">
                                    <div class="layui-card-body">
                                        <div class="search-div">
                                            <div class="layui-form" align="right" style="margin-top:10px;">
                                                <div class="layui-form-item">
                                                    <div class="layui-inline">
                                                        <select lay-verify="required" name='battery_type_2'>
                                                            <option value="total">电池类型</option>
                                                            <option value="1">全新电池(单位:组)</option>
                                                            <option value="2">备用电池(单位:组)</option>
                                                            <option value="4">退回电池(单位:组)</option>
                                                            <option value="0">废旧电池(单位:只)</option>
                                                        </select>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <input type="text" class="layui-input" placeholder="选择时间"
                                                               autocomplete="off" id="datetime2">
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
                                <div class="layui-card-header title-font mar15">各区域库存统计</div>
                                <div class="layui-form" align="right" style="width:100%;margin-top:10px;">
                                    <div class="layui-form-item">
                                        <div class="layui-inline" align="left">
                                            <select name='battery_type_3'>
                                                <option value="total">电池类型</option>
                                                <option value="1">全新电池(单位:组)</option>
                                                <option value="2">备用电池(单位:组)</option>
                                                <option value="4">退回电池(单位:组)</option>
                                                <option value="0">废旧电池(单位:只)</option>
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
                        <div class="layui-col-md12" style="padding: 0">
                            <div class="bg-white">
                                <div class="layui-card-header title-font mar15">电池型号</div>
                                <div class="layui-card-body">
                                    <div class="layui-card-body">
                                        <div class="search-div">
                                            <div class="layui-form" align="right" style="margin-top:10px;">
                                                <div class="layui-form-item">
                                                    <div class="layui-inline">
                                                        <select lay-verify="required" name='battery_type_5'>
                                                            <option value="1">全新电池(单位:组)</option>
                                                            <option value="2">备用电池(单位:组)</option>
                                                            <option value="4">退回电池(单位:组)</option>
                                                            <option value="0">废旧电池(单位:只)</option>
                                                        </select>
                                                    </div>
                                                    <div class="layui-inline" align="left">
                                                        <select name="agentId">
                                                            @foreach($provinces2 as $id=>$province)
                                                                <option value="{{$id}}">{{$province}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="layui-inline">
                                                        <input type="text" class="layui-input" placeholder="选择日期"
                                                               autocomplete="off" id="datetime5">
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
                                <div class="layui-card-header title-font mar15">电池型号库存统计</div>
                                <div class="layui-form" align="right" style="width:100%;margin-top:10px;">
                                    <div class="layui-form-item">
                                        <div class="layui-inline" align="left">
                                            <select name='battery_type_6'>
                                                <option value="1">全新电池(单位:组)</option>
                                                <option value="2">备用电池(单位:组)</option>
                                                <option value="4">退回电池(单位:组)</option>
                                                <option value="0">废旧电池(单位:只)</option>
                                            </select>
                                        </div>
                                        <div class="layui-inline" align="left">
                                            <select name="agentId2">
                                                @foreach($provinces2 as $id=>$province)
                                                    <option value="{{$id}}">{{$province}}</option>
                                                @endforeach
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
    var provinceArr = [], typeOne = [], typeTwo = [], typeThree = [];
    @forEach(allUserProvinces() as $province)
    provinceArr.push('{{$province}}');
    @endforeach
    if (provinceArr.indexOf("全部区域") == -1) {
        provinceArr = ["全部区域"].concat(provinceArr);
    }

    @forEach(batteryStockTitle(1) as $modelName)
    typeOne.push('{{$modelName}}');
    @endforeach
    @forEach(batteryStockTitle(2) as $modelName)
    typeTwo.push('{{$modelName}}');
    @endforeach
    @forEach(batteryStockTitle(3) as $modelName)
    typeThree.push('{{$modelName}}');
    @endforeach
</script>
<script src="{{asseturl("js/lease/common.js?").time()}}"></script>
<script src="{{asseturl("js/lease/service/stock.js?").time()}}"></script>
</body>
</html>

