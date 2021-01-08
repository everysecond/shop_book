<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layuiAdmin 控制台主页一</title>
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
    .layadmin-shortcut li .layui-icon {
        display: inline-block;
        width: 100%;
        height: 60px;
        line-height: 60px;
        text-align: center;
        border-radius: 2px;
        font-size: 14px;
        font-weight: bold;
        color: #009688;
        transition: all .3s;
        -webkit-transition: all .3s;
    }</style>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">



                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">快捷方式</div>
                        <div class="layui-card-body">

                            <div class="layui-carousel layadmin-carousel layadmin-shortcut">
                                <div carousel-item>
                                    <ul class="layui-row layui-col-space10">
                                        <li class="layui-col-xs3">
                                            <a lay-href="{{route('lease.reports.view.dashboard.today')}}">
                                                <i class="layui-icon ">今日指标 >></i>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="{{route('lease.reports.view.register')}}">
                                                <i class="layui-icon ">用户注册数据 >></i>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="{{route('lease.reports.leasenewold')}}">
                                                <i class="layui-icon ">新老用户租赁对比 >></i>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="{{route('lease.reports.leaseregister')}}">
                                                <i class="layui-icon ">注册-租赁 >></i>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="{{route('lease.renewal.show')}}">
                                                <i class="layui-icon ">续租数据 >></i>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="{{route('lease.rebate.show')}}">
                                                <i class="layui-icon ">退租数据 >></i>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="{{route('lease.rent_change.show')}}">
                                                <i class="layui-icon ">换租数据 >></i>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="{{route('lease.insurance.show')}}">
                                                <i class="layui-icon ">投保分析 >></i>
                                            </a>
                                        </li>
                                    </ul>
{{--                                    <ul class="layui-row layui-col-space10">--}}
{{--                                        <li class="layui-col-xs3">--}}
{{--                                            <a lay-href="set/user/info.html">--}}
{{--                                                <i class="layui-icon ">今日指标 >></i>--}}
{{--                                            </a>--}}
{{--                                        </li>--}}
{{--                                    </ul>--}}

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">数据统计</div>
                        <div class="layui-card-body">

                            <div class="layui-carousel layadmin-carousel layadmin-backlog">
                                <div carousel-item>
                                    <ul class="layui-row layui-col-space10">
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3>今日用户注册量</h3>
                                                <p><cite>{{$regis_num}}</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3>今日网点审核量</h3>
                                                <p><cite>暂无数据</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3>今日租赁量</h3>
                                                <p><cite>{{$rent_num}}</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3>昨日续租数</h3>
                                                <p><cite>{{$renewal_num}}</cite></p>
                                            </a>
                                        </li>
                                    </ul>
                                    <ul class="layui-row layui-col-space10">
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3>昨日退租数</h3>
                                                <p><cite>{{$rebate_num}}</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3>昨日换租数</h3>
                                                <p><cite>{{$exchange_num}}</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" class="layadmin-backlog-body">
                                                <h3>昨日投保数</h3>
                                                <p><cite>{{$insurance_num}}</cite></p>
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">租赁趋势</div>
                        <div class="layui-card-body">
                            <div style="height: 500px">
                                <div class="layui-form" align="right" >
                                    <div class="layui-form-item">
                                        <div class="layui-inline">
                                            <div class="layui-input-inline">
                                                <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" autocomplete="off" id="datetime1"
                                                       style="display: none">
                                            </div>
                                        </div>
                                        <div class="layui-inline" align="left">
                                            <select name="modules" lay-verify="required" lay-search="" id='tes1' lay-filter="demo10">
                                                @foreach($timeType as $item)
                                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="layui-inline" align="left">
                                            <select name="modules" lay-verify="required" lay-search="" id='test1'>
                                                @foreach($provinces as $id=>$province)
                                                    <option value="{{$id}}">{{$province}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="layui-inline">
                                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" id='search1'><i
                                                        class="layui-icon"></i>搜索
                                            </button>

                                        </div>
                                    </div>
                                </div>

                                <div style="width:800px;height:450px;" id="box1"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">续租趋势</div>
                        <div class="layui-card-body">
                            <div style="height: 500px">
                                <div class="layui-form" align="right" style="width:800px;">
                                    <div class="layui-form-item">
                                        <div class="layui-inline">
                                            <div class="layui-input-inline">
                                                <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" id="test12"
                                                       style="display: none" autocomplete="off"
                                                >
                                            </div>
                                        </div>
                                        <div class="layui-inline" align="left">
                                            <select name="modules" lay-verify="required" lay-search="" id='tes2' lay-filter="demo12">
                                                @foreach($timeType as $item)
                                                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="layui-inline" align="left">
                                            <select name="modules" lay-verify="required" lay-search="" id='test13'>
                                                {{--                            <option value="0">全部区域</option>--}}
                                                @foreach($provinces as $id=>$province)
                                                    <option value="{{$id}}">{{$province}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="layui-inline">
                                            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch" id='search2'><i
                                                        class="layui-icon"></i>搜索
                                            </button>

                                        </div>
                                    </div>
                                </div>
                                <div id="box2" style="width:800px;height:450px;"></div>




                            </div>
                        </div>
                    </div>
                </div>
{{--                <div class="layui-col-md6">--}}
{{--                    <div class="layui-card">--}}
{{--                        <div class="layui-card-header">网点库存统计</div>--}}
{{--                        <div class="layui-card-body">--}}
{{--                            <div style="height: 500px"> </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="layui-col-md6">--}}
{{--                    <div class="layui-card">--}}
{{--                        <div class="layui-card-header">网点余额统计</div>--}}
{{--                        <div class="layui-card-body">--}}
{{--                            <div style="height: 500px"> </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}





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

<script src="{{asseturl("js/lease/report/console_renewal.js?").time()}}"></script>
<script src="{{asseturl("js/lease/report/console_leasenewold.js?").time()}}"></script>
</body>
</html>

