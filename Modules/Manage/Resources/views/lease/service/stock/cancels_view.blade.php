<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>网点库存-退货</title>
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

        .layui-col-md12, .layui-card {
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
            <div class="layui-card-header">区域占比</div>
            <div class="layui-card-body">
                <div class="layui-form search-div">
                    <div class="layui-form-item" style="float: right;">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                       id="date1" style="display: none" autocomplete="off">
                            </div>
                        </div>
                        <div class="layui-inline" align="left">
                            <select name="days1" lay-verify="required" lay-search="" lay-filter="days1">
                                @foreach($timeType as $item)
                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline" align="right">
                            <select style="float: right;" name="type1">
                                <option value="1">申请数</option>
                                <option value="2">电池数</option>
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                    onclick="load_box_stock()"><i class="layui-icon"></i>搜索
                            </button>
                        </div>
                    </div>
                </div>
                <div class="div-mid" id="box_stock"></div>
            </div>
        </div>
    </div>

    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">区域分布</div>
            <div class="layui-card-body">
                <div class="layui-form search-div">
                    <div class="layui-form-item" style="float: right;">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                       id="date2" style="display: none" autocomplete="off">
                            </div>
                        </div>
                        <div class="layui-inline" align="left">
                            <select name="days2" lay-verify="required" lay-search=""
                                    lay-filter="days2">
                                @foreach($timeType as $item)
                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="layui-inline" align="right">
                            <select style="float: right;" name="type2">
                                <option value="1">申请数</option>
                                <option value="2">电池数</option>
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                    onclick="load_area_data()"><i class="layui-icon"></i>搜索
                            </button>
                        </div>
                    </div>
                </div>
                <div class="div-mid" id="box_china_map"></div>
            </div>
        </div>
    </div>


    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-header">各区域退货统计</div>
            <div class="layui-card-body">
                <div class="layui-form search-div">
                    <div class="layui-form-item" style="float: right;">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input"
                                       autocomplete="off" placeholder="日期范围" id="date3">
                            </div>
                        </div>
                        <div class="layui-inline" align="right">
                            <select style="float: right;" id="type3" name="type3">
                                <option value="1">申请数</option>
                                <option value="2">电池数</option>
                            </select>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                    onclick="load_table()"><i class="layui-icon"></i>搜索
                            </button>
                        </div>
                    </div>
                </div>
                <div style="margin: 10px 5px 10px 0;">
                    <table style="padding: 15px" id="tabledata"></table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asseturl("lib/layuiadmin/layui/layui.js")}}"></script>
<script>
    var _token = '{{ csrf_token() }}';
    var adminurl = "{{adminurl()}}";
</script>
<script src="{{asseturl("js/lease/common.js?").time()}}"></script>
<script src="{{asseturl("js/lease/service/cancelsview.js?").time()}}"></script>
</body>
</html>

