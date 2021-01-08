<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>租点—概况—整体趋势</title>
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

        .layui-card {
            margin-bottom: 10px;
        }

        .layui-card > .layui-card-body > .layui-col-md12 {
            padding: 0 !important;
        }
        .layui-layer-TipsG{
            top: -6px !important;
        }
    </style>
</head>
<body>
<div class="layui-col-md12">
    <div class="layui-card">
        <div class="layui-card-header">
            基础指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="累计下载量及登录用户数均为2019-07-08开始记录"></i>
            <div class="layui-form" style="float: right;">
                <div class="layui-form-item" style="margin-bottom: 0">
                    <div class="layui-inline" align="left">
                        <select name="agents" lay-verify="required" lay-search="">
                            @foreach($provinces as $id=>$province)
                                <option value="{{$id}}">{{$province}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" onclick="load_data()"
                                lay-filter="formSearch" style="margin-top: -3px;">搜索
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-card-body" style="min-height: 100px;">
            <div class="layui-col-md2 height100 base-title base-title-click" data-type="register">
                <h3>累计注册量</h3>
                <span class="number-title" id="register_num">0</span>
                <span class="u100 color-grew ml7">昨日</span><span class="u100 color-green" id="rate_register">0%</span>
            </div>
            <div class="layui-col-md2 height100 base-title" data-type="down">
                <h3>累计下载量</h3>
                <span class="number-title" id="down_num">0</span>
                <span class="u100 color-grew ml7">昨日</span><span class="u100 color-green" id="rate_down">0%</span>
            </div>
            <div class="layui-col-md2 height100 base-title" data-type="login">
                <h3>登录用户数(近七日)</h3>
                <span class="number-title" id="login_num">0</span>
                <span class="u100 color-grew ml7">环比</span><span class="u100 color-green" id="rate_login">0%</span>
            </div>
        </div>
        <div class="layui-card-body" style="height: 350px;">
            <div class="div-mid" id="box_register_hour"></div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">
            租赁指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="此处租赁统计对象为所有新租订单"></i>
        </div>
        <div class="layui-card-body" style="min-height: 100px;">
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>累计租赁数</h3>
                    <span class="number-title" id="lease_num">0</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>累计租赁金额(元)</h3>
                    <span class="number-title" id="lease_amount">0</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>累计租赁押金(元)</h3>
                    <span class="number-title" id="lease_deposit">0</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>累计租赁周期(月)</h3>
                    <span class="number-title" id="lease_month_num">0</span>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">
            续租指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="此处续租统计对象为所有续租续约订单"></i>
        </div>
        <div class="layui-card-body" style="min-height: 100px;">
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>累计续租数</h3>
                    <span class="number-title" id="renewal_num">0</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>累计续租金额(元)</h3>
                    <span class="number-title" id="renewal_amount">0</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>累计续租周期(月)</h3>
                    <span class="number-title" id="renewal_month_num">0</span>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">
            退租指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="此处续租统计对象为所有退租订单"></i>
        </div>
        <div class="layui-card-body" style="min-height: 100px;">
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>累计退租数</h3>
                    <span class="number-title" id="rebate_num">0</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>累计退租返还金额(元)</h3>
                    <span class="number-title" id="rebate_amount">0</span>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">
            换租指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="此处换租统计对象为所有换租订单"></i>
        </div>
        <div class="layui-card-body" style="min-height: 100px;">
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>累计换租数</h3>
                    <span class="number-title" id="change_num">0</span>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">
            保险指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="此处保险统计对象为所有保险订单"></i>
        </div>
        <div class="layui-card-body" style="min-height: 100px;">
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>累计投保数</h3>
                    <span class="number-title" id="insurance_num">0</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>累计报失数</h3>
                    <span class="number-title" id="insurance_loss">0</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>实名认证数</h3>
                    <span class="number-title" id="">30085</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="{{asseturl("lib/layuiadmin/layui/layui.js")}}"></script>
<script src="{{asseturl("js/lease/common.js?").time()}}"></script>
<script>
    var _token = '{{ csrf_token() }}';
    var adminurl = "{{adminurl()}}";
    layui.config({
        base: "{{asseturl("lib")}}" + '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'console']);
</script>
<script src="{{asseturl("js/lease/report/dashboard_total.js?").time()}}"></script>
</body>
</html>