<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>租点—概况—今日指标</title>
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

        .layui-card{
            margin-bottom: 10px;
        }
        .layui-card>.layui-card-body>.layui-col-md12{
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
            基本指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="下载数量,启动次数,登录用户数均为2019-07-08开始记录"></i>
            <div class="layui-form" style="float: right;">
                <div class="layui-form-item" style="margin-bottom: 0">
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" class="layui-input" placeholder="今日"
                                   id="date1" autocomplete="off">
                        </div>
                    </div>
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
        <div class="layui-card-body" style="min-height: 120px">
            <div class="layui-col-md2 height100 base-title base-title-click" data-type="register">
                <h3>注册数量</h3>
                <span class="number-title" id="register_num">0</span>
                <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="rate_register">0%</span>
            </div>
            <div class="layui-col-md2 height100 base-title" data-type="down">
                <h3>下载数量</h3>
                <span class="number-title" id="down_num">0</span>
                <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="rate_down">0%</span>
            </div>
            <div class="layui-col-md2 height100 base-title" data-type="start">
                <h3>启动次数</h3>
                <span class="number-title" id="start_num">0</span>
                <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="rate_start">0%</span>
            </div>
            <div class="layui-col-md2 height100 base-title" data-type="login">
                <h3>登录用户数</h3>
                <span class="number-title" id="login_num">0</span>
                <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="rate_login">0%</span>
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
        <div class="layui-card-body" style="min-height:220px;">
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>租赁数</h3>
                    <span class="number-title" id="lease_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="lease_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>租赁金额(元)</h3>
                    <span class="number-title" id="lease_amount">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="lease_amount_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>租赁押金(元)</h3>
                    <span class="number-title" id="lease_deposit">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="lease_deposit_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>租赁周期(月)</h3>
                    <span class="number-title" id="lease_month_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="lease_month_rate">0%</span>
                </div>
            </div>
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>今日到期数</h3>
                    <span class="number-title" id="expired_today">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="expired_today_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>已到期0-10天</h3>
                    <span class="number-title" id="expired_10">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="expired_10_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>已到期10-30天</h3>
                    <span class="number-title" id="expired_10_2_30">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="expired_10_2_30_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>已到期30天</h3>
                    <span class="number-title" id="expired_30">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="expired_30_rate">0%</span>
                </div>

            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">
            续租指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="续租可查今日之前数据，今日数据未生成"></i>
        </div>
        <div class="layui-card-body" style="min-height:220px;">
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>续租数</h3>
                    <span class="number-title" id="renewal_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="renewal_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>续租金额(元)</h3>
                    <span class="number-title" id="renewal_amount">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="renewal_amount_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>续租周期(月)</h3>
                    <span class="number-title" id="renewal_month_total">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="renewal_month_rate">0%</span>
                </div>
            </div>
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>提前续租数</h3>
                    <span class="number-title" id="advance_renewal">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="advance_renewal_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>到期续租</h3>
                    <span class="number-title" id="expire_renewal_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="expire_renewal_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>已到期0-10天续租</h3>
                    <span class="number-title" id="overtime_ten_renewal_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="overtime_ten_renewal_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>已到期10-30天续租</h3>
                    <span class="number-title" id="overtime_ten_thirty_renewal_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="overtime_ten_thirty_renewal_rate">0%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">
            退租指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="退租可查今日之前数据，今日数据未生成"></i>
        </div>
        <div class="layui-card-body" style="min-height:220px;">
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>退租数</h3>
                    <span class="number-title" id="rent_release_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="rent_release_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>退租返还金额(元)</h3>
                    <span class="number-title" id="rent_release_amount">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="rent_release_amount_rate">0%</span>
                </div>
            </div>
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>提前退租数</h3>
                    <span class="number-title" id="advance_rent_release">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="advance_rent_release_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>到期退租</h3>
                    <span class="number-title" id="expire_rent_release_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="expire_rent_release_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>已到期0-10天退租</h3>
                    <span class="number-title" id="overtime_ten_rent_release_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="overtime_ten_rent_release_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>已到期10-30天退租</h3>
                    <span class="number-title" id="overtime_ten_thirty_rent_release_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="overtime_ten_thirty_rent_release_rate">0%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">
            换租指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="换租可查今日之前数据，今日数据未生成"></i>
        </div>
        <div class="layui-card-body" style="min-height: 120px">
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>换租数</h3>
                    <span class="number-title" id="rent_change_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="rent_change_rate">0%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">
            保险指标<i class="layui-icon layui-icon-tips" style="position: inherit;top: 0;right: 0;left: 7px;color:darkorange;"
                   data-tips="保险可查今日之前数据，今日数据未生成"></i>
        </div>
        <div class="layui-card-body" style="min-height: 120px">
            <div class="layui-col-md12">
                <div class="layui-col-md2 height100">
                    <h3>当日投保</h3>
                    <span class="number-title" id="insure_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="insure_rate">0%</span>
                </div>
                <div class="layui-col-md2 height100">
                    <h3>当日报失</h3>
                    <span class="number-title" id="report_loss_num">0</span>
                    <span class="u100 color-grew ml7">前一日</span><span class="u100 color-green" id="report_loss_rate">0%</span>
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
<script src="{{asseturl("js/lease/report/dashboard_today.js?").time()}}"></script>
</body>
</html>