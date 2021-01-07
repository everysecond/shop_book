<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>租点—启动统计</title>
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
            margin: 0 25px;
        }

        .layui-card .layui-tab-brief .layui-tab-title li {
            padding: 0 15px;
            margin: 0;
        }

        .search-div{
            width: 100%;
            height:50px;
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
        <div class="layui-card-body" style="padding: 0;">
            <div class="layui-tab layui-tab-brief" lay-filter="component-tabs-brief">
                <ul class="layui-tab-title" style="border-bottom-color: #cccccc;margin: 0 15px;">
                    <li class="title-font layui-this" onclick="load_tab_1()">每小时启动</li>
                    <li class="title-font" onclick="load_tab_2()">累计每小时</li>
                    <li class="title-font" onclick="load_tab_3()">启动统计</li>
                </ul>
                <div class="layui-tab-content" style="padding: 0;">
                    <div class="layui-tab-item  layui-show" style="height: 100%;">
                        <div class="layui-card-header title-font mar15">每小时对比</div>
                        <div class="layui-card-body">
                            <div class="layui-card-body">
                                <div class="search-div">
                                    <div style="float: left" class="layui-form">
                                        <div class="layui-input-inline">
                                            <input type="text" class="layui-input" id="hour1"
                                                   autocomplete="off" placeholder="选择对比日期">
                                        </div>
                                        <button class="layui-btn" onclick="addDay()">添加对比</button>
                                        <a href="javascript:;" class="clearBtn1 hidden" onclick="clearDays()"
                                           style="color:#1abc9c">清空</a>
                                    </div>
                                </div>
                                <div class="div-mid" id="box_register_hour" style="margin-bottom: 15px"></div>
                            </div>
                        </div>
                        <div style="width: 100%;border-top: 10px solid #f2f2f2"></div>
                        <div class="layui-card-header title-font mar15">每小时统计</div>
                        <div class="layui-form" align="right" style="width:100%;margin-top: 20px;">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input"
                                               autocomplete="off" placeholder="日期范围" id="date1">
                                    </div>
                                </div>
                                <div class="layui-inline" style="margin-right: 25px;">
                                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                            onclick="load_day_table()"><i class="layui-icon"></i>搜索
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div style="margin: 5px 27px 25px 23px;">
                            <table style="padding: 15px" id="table_day"></table>
                        </div>
                    </div>
                    <div class="layui-tab-item" style="width:100%;height: 100%;">
                        <div class="layui-card-header title-font mar15">累计每小时对比</div>
                        <div class="layui-card-body">
                            <div class="layui-card-body">
                                <div class="search-div">
                                    <div style="float: left" class="layui-form">
                                        <div class="layui-input-inline">
                                            <input type="text" class="layui-input" id="hour2"
                                                   autocomplete="off" placeholder="选择对比日期">
                                        </div>
                                        <button class="layui-btn" onclick="addDay2()">添加对比</button>
                                        <a href="javascript:;" class="clearBtn2 hidden" onclick="clearDays2()"
                                           style="color:#1abc9c">清空</a>
                                    </div>
                                </div>
                                <div class="div-mid" id="box_register_total" style="margin-bottom: 15px"></div>
                            </div>
                        </div>
                        <div style="width: 100%;border-top: 10px solid #f2f2f2"></div>
                        <div class="layui-card-header title-font mar15">累计每小时统计</div>
                        <div class="layui-form" align="right" style="width:100%;margin-top: 20px;">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="text" class="layui-input"
                                               autocomplete="off" placeholder="日期范围" id="date2">
                                    </div>
                                </div>
                                <div class="layui-inline" style="margin-right: 25px;">
                                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                            onclick="load_day_total_table()"><i class="layui-icon"></i>搜索
                                    </button>

                                </div>
                            </div>
                        </div>
                        <div style="margin: 5px 27px 25px 23px;">
                            <table id="table_total"></table>
                        </div>
                    </div>
                    <div class="layui-tab-item" style="width:100%;height: 100%;">
                        <div class="layui-card-header title-font mar15">启动趋势</div>
                        <div class="layui-card-body">
                            <div class="layui-card-body">
                                <div class="search-div">
                                    <div class="layui-form" style="float: right;">
                                        <div class="layui-form-item">
                                            <div class="layui-inline">
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                                           id="date3" style="display: none" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="layui-inline" align="left">
                                                <select name="days1" lay-verify="required" lay-search=""
                                                        lay-filter="timeType1">
                                                    @foreach($timeType as $item)
                                                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="layui-inline">
                                                <button class="layui-btn layui-btn-normal" onclick="load_register_day()"
                                                        lay-filter="formSearch"><i class="layui-icon"></i>搜索
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="div-mid" id="box_register_day" style="margin-bottom: 15px"></div>
                            </div>
                        </div>
                        <div style="width: 100%;border-top: 10px solid #f2f2f2"></div>
                        <div class="layui-col-md6" style="padding: 0;">
                            <div class="layui-card" style="height: 600px;">
                                <div class="layui-card-header title-font">启动终端</div>
                                <div class="search-div">
                                    <div class="layui-form" align="right">
                                        <div class="layui-form-item" style="margin-top: 20px;">
                                            <div class="layui-inline">
                                                <div class="layui-input-inline">
                                                    <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                                           id="date4" style="display: none" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="layui-inline" align="left">
                                                <select name="days2" lay-verify="required" lay-search=""
                                                        lay-filter="timeType2">
                                                    @foreach($timeType as $item)
                                                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="layui-inline">
                                                <button class="layui-btn layui-btn-normal" onclick="load_from_data()"
                                                        lay-filter="formSearch"><i class="layui-icon"></i>搜索
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="height: 50px;"></div>
                                <div id="box_register_from" style="width:100%;height:400px;margin: 0 auto;"></div>
                            </div>
                        </div>
                        <div class="layui-col-md6" style="padding: 0 0 0 10px;">
                            <div class="layui-card" style="height: 600px;">
                                <div class="layui-card-header title-font">启动统计</div>
                                <div class="layui-form" align="right" style="width:100%;margin-top: 20px;">
                                    <div class="layui-form-item">
                                        <div class="layui-inline">
                                            <div class="layui-input-inline">
                                                <input type="text" class="layui-input"
                                                       autocomplete="off" placeholder="日期范围" id="date5">
                                            </div>
                                        </div>
                                        <div class="layui-inline">
                                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                                    onclick="load_register_total_table()"><i class="layui-icon"></i>搜索
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin: 5px 10px 10px 10px;">
                                    <table style="padding:5px 15px" id="table_register_total"></table>
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
</script>
<script src="{{asseturl("js/lease/common.js?").time()}}"></script>
<script src="{{asseturl("js/lease/service/start_log_view.js?").time()}}"></script>
</body>
</html>

