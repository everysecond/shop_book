@extends('manage::layouts.master')
<link rel="stylesheet" href="{{asseturl("lib/layuiadmin/layui/css/layui.css")}}" media="all">
<link rel="stylesheet" href="{{asseturl("lib/layuiadmin/style/admin.css")}}" media="all">
<script src="{{asseturl("lib/echarts/jquery-3.2.1.min.js")}}"></script>
<script src="{{asseturl("lib/echarts/echarts.min.js")}}"></script>
@section('style')
    <style>
        .layui-col-md6, .layui-col-md12 {
            padding: 5px;
        }

        html, body {
            height: 100%;
        }

        html, body {
            height: 97%;
        }

        .layui-card-header {
            border-bottom: 1px solid #cccccc;
        }

        .layui-inline {
            margin-right: 5px !important;
        }

        .layui-table-cell {
            padding: 0 8px
        }

        .layadmin-shortcut li .layui-icon {
            display: inline-block;
            width: 100%;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border-radius: 2px;
            font-size: 14px;
            font-weight: bold;
            color: #009688;
            transition: all .3s;
            -webkit-transition: all .3s;
        }

        .search-div {
            width: 100%;
            height: 50px;
        }

        .search-div select {
            padding: 3px 5px 5px 10px;
            font-size: 13px;
        }

        #plan_list li {
            line-height: 53px;
            border-bottom: 1px solid #e4e4e4;
        }

        .div-mid {
            width: 100%;
            height: 350px;
        }

        .layui-col-xs1 {
            width: 14%;
        }
    </style>
@endsection

@section('content')
    <div class="layui-col-md12">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header" style="padding:4px 0 0 22px ">
                    <span class="header-str">快捷操作</span>
                </div>
                <div class="layui-card-body">
                    <div class="layui-carousel layadmin-carousel layadmin-shortcut"
                         style="height: 50px !important;padding: 15px 0">
                        <ul class="layui-row layui-col-space10">
                            <li class="layui-col-xs1">
                                <a class="open-frame" title="新增客户" data-width="900px"
                                   href="{{route('cus.create')}}" data-height="520px">
                                    <i class="layui-icon ">新增客户 >></i>
                                </a>
                            </li>
                            <li class="layui-col-xs1">
                                <a class="open-frame" title="新增联系人" data-width="850px"
                                   href="{{route('contact.create.by.console')}}" data-height="520px">
                                    <i class="layui-icon ">新增联系人 >></i>
                                </a>
                            </li>
                            <li class="layui-col-xs1">
                                <a href="{{route('plan.create',['type'=>1])}}" class="open-frame" title="新增跟进记录"
                                   data-width="900px" data-height="550px">
                                    <i class="layui-icon ">写跟进记录 >></i>
                                </a>
                            </li>
                            <li class="layui-col-xs1">
                                <a href="{{route('plan.create',['type'=>2])}}" class="open-frame" title="新增跟进计划"
                                   data-width="600px" data-height="600px">
                                    <i class="layui-icon ">写跟进计划 >></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md6">
            <div class="layui-card" style="height:400px;background-color: white">
                <div class="layui-card-header" style="padding:6px 0 6px 22px;">
                    <div class="layui-form search-div">
                        <span class="header-str">数据简报</span>
                        <div class="layui-form-item" style="float: right;">
                            <div class="layui-inline" style="width: 167px;">
                                <input type="text" class="layui-input" placeholder="开始时间 - 结束时间"
                                       autocomplete="off" id="date1" style="display: none;padding: 5px">
                            </div>
                            <div class="layui-inline" align="left" style="width: 85px;">
                                <select name="timeType1" lay-filter="timeType1">
                                    <option value="1">昨日</option>
                                    <option value="6">本周</option>
                                    <option value="29">本月</option>
                                    <option value="-1">自定义</option>
                                </select>
                            </div>
                            <div class="layui-inline" align="left" style="width: 85px;">
                                <select name="who1">
                                    <option value="myself">我的</option>
                                    <option value="under">我下属的</option>
                                    <option value="myall">我的及下属</option>
                                    @foreach($staffs as $id=>$staff)
                                        <option value="{{$id}}">{{$staff}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-inline">
                                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                        onclick="loadShortData()">搜索
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-card-body" style="height: 250px;padding: 50px;">
                    <div class="layui-col-md3 height50 pad-t30">
                        <h3 class="label-t">新增客户数</h3>
                        <span class="number-title" id="new_cus_num">0</span>
                    </div>
                    <div class="layui-col-md3 height50 pad-t30">
                        <h3 class="label-t">新增联系人数</h3>
                        <span class="number-title" id="new_contact_num">0</span>
                    </div>
                    <div class="layui-col-md3 height50 pad-t30">
                        <h3 class="label-t">新增跟进记录</h3>
                        <span class="number-title" id="new_fr_num">0</span>
                    </div>
                    <div class="layui-col-md3 height50 pad-t30">
                        <h3 class="label-t">新增跟进计划</h3>
                        <span class="number-title" id="new_fp_num">0</span>
                    </div>
                    <div class="layui-col-md3 height50 pad-t30">
                        <h3 class="label-t">租赁合约数</h3>
                        <span class="number-title" id="lease_contract_num">0</span>
                    </div>
                    <div class="layui-col-md3 height50 pad-t30">
                        <h3 class="label-t">网点合同数</h3>
                        <span class="number-title" id="service_contract_num">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md6">
            <div class="layui-card" style="height:400px;background-color: white">
                <div class="layui-card-header" style="padding:6px 0 6px 22px;">
                    <div class="layui-form search-div">
                        <span class="header-str">跟进计划</span>
                        <div class="layui-form-item" style="float: right;">
                            <div class="layui-inline" align="left" style="width: 85px;">
                                <select name="who2">
                                    <option value="myself">我的</option>
                                    <option value="under">我下属的</option>
                                    <option value="myall">我的及下属</option>
                                    @foreach($staffs as $id=>$staff)
                                        <option value="{{$id}}">{{$staff}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-inline">
                                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                        onclick="loadPlans()">搜索
                                </button>
                            </div>
                            <div class="layui-inline">
                                <a lay-href="{{route('plan.plan_index')}}">更多计划 >></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-card-body">
                    <ul id="plan_list">
                    </ul>
                </div>
            </div>
        </div>
        <div class="layui-col-md6">
            <div class="layui-card" style="height:400px;background-color: white">
                <div class="layui-card-header" style="padding:6px 0 6px 22px;">
                    <div class="layui-form search-div">
                        <span class="header-str">业绩趋势</span>
                        <div class="layui-form-item" style="float: right;">
                            <div class="layui-inline" style="width: 167px" align="left">
                                <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" id="test9"
                                       style="display: none;padding: 5px" autocomplete="off">
                            </div>
                            <div class="layui-inline" align="left" style="width: 100px">
                                <select name="modules" lay-verify="required" lay-search="" id='tes1' lay-filter="demo">
                                    @foreach($timeType as $item)
                                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-inline" align="left" style="width: 85px;">
                                <select name="who1">
                                    <option value="myself">我的</option>
                                    <option value="under">我下属的</option>
                                    <option value="myall">我的及下属</option>
                                    @foreach($staffs as $id=>$staff)
                                        <option value="{{$id}}">{{$staff}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="layui-inline">
                                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                        onclick="load_time_hour()">搜索
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="div-mid" id="lease_time_hour" style="margin-bottom: 15px"></div>

            </div>
        </div>
        <div class="layui-col-md6">
            <div class="layui-card" style="height:400px;background-color: white">
                <div class="layui-card-header" style="padding:6px 0 6px 22px;">
                    <div class="layui-form search-div">
                        <span class="header-str">业绩排行</span>
                        <div class="layui-form-item" style="float: right;">
                            <div class="layui-inline">
                                <div class="layui-input-inline" style="width: 167px">
                                    <input type="text" class="layui-input" placeholder="开始时间 - 结束时间" id="test19"
                                           style="display: none;padding: 5px" autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="layui-inline" align="left" style="width: 130px">
                                <select name="modules" lay-verify="required" lay-search="" id='tes11'
                                        lay-filter="demo1">
                                    @foreach($timeType as $item)
                                        <option value="{{$item['id']}}">{{$item['name']}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="layui-inline">
                                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="formSearch"
                                        onclick="load_rank()">搜索
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="div-mid" id="lease_thour" style="margin-bottom: 15px"></div>

            </div>
        </div>
        <div style="clear: both"></div>
    </div>
@endsection

<script src="{{asseturl("lib/layuiadmin/layui/layui.js")}}"></script>
<script>
    var _token = '{{ csrf_token() }}';
    var adminurl = "{{adminurl()}}";
    layui.config({
        base: "{{asseturl("lib")}}" + '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    })
</script>

@section('script')
    <script>
        var layer, loadPlans, loadShortData, load_time_hour, load_rank;
        layui.use(['laydate', 'index', 'common', 'form', 'http', 'util'], function () {
            var colorArr = [
                '#2fc25b', '#1890ff', '#fbd437', '#27727B',
                '#87f7cf', '#36cbcb', '#72ccff', '#f7c5a0',
                '#0098d9', '#2b821d', '#e87c25', '#e01f54'
            ];

            layer = layui.layer;
            var http = layui.http, laydate = layui.laydate, $ = layui.$, common = layui.common;
            laydate.render({
                elem: '#date1'
                , type: 'date'
                , range: true
            });

            laydate.render({
                elem: '#test9'

                , range: true
            })

            laydate.render({
                elem: '#test19'

                , range: true
            })
            var form = layui.form;
            form.on('select(timeType1)', function (data) {
                if (data.value == -1) {
                    $('#date1').css('display', 'block');
                } else {
                    $('#date1').css('display', 'none');
                }
            });

            form.on('select(demo)', function (data) {
                if (data.value == 7) {
                    $('#test9').css('display', 'block');
                } else {
                    $('#test9').css('display', 'none');
                }
            });
            form.on('select(demo1)', function (data) {
                if (data.value == 7) {
                    $('#test19').css('display', 'block');
                } else {
                    $('#test19').css('display', 'none');
                }
            })

            loadPlans = function () {
                $.ajax({
                    type: 'POST',
                    data: {'_token': "{{ csrf_token() }}", "record_type": 2, "type": $("select[name='who2']").val()},
                    dataType: 'json',
                    url: '{{route('plan.console')}}',
                    success: function (data) {
                        if (data.code == 200) {
                            $("#plan_list").html('');
                            var $html = "";
                            if (data.data != null) {
                                $.each(data.data, function (index, item) {
                                    var deltail = '/manage/crm/cus/detail/' + item.cus_id;
                                    var content = (item.content.length > 20 ? item.content.substring(20, 3) + '...' : item.content);
                                    var timeStr = '<span style="position:absolute;right: 30px"><i class="layui-icon layui-icon-log color-green"></i>' + item.follow_at + '</span>'
                                    $html += "<li><a lay-href='" + deltail + "'><span  class='ml15 sm-g-title'>" +
                                        "【" + (item.cus.name ? item.cus.name : item.cus.mobile) + "】</span><span>" + content + "</span></a>" + timeStr + "</li>";
                                });
                                $("#plan_list").append($html);
                            }
                        } else {
                            layer.msg(data.msg)
                        }
                    }
                });
            };
            loadPlans();

            loadShortData = function () {
                $.ajax({
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        "dateRange": $("#date1").val(),
                        "timeType1": $("select[name='timeType1']").val(),
                        "type": $("select[name='who1']").val()
                    },
                    dataType: 'json',
                    url: '{{route('console.data.brief')}}',
                    success: function (data) {
                        if (data.code == 200) {
                            if (data.data != null) {
                                $('#new_cus_num').html(data.data.new_cus_num);
                                $('#new_contact_num').html(data.data.new_contact_num);
                                $('#new_fr_num').html(data.data.new_fr_num);
                                $('#new_fp_num').html(data.data.new_fp_num);
                                $('#lease_contract_num').html(data.data.lease_contract_num);
                                $('#service_contract_num').html(data.data.service_contract_num);
                            }
                        } else {
                            layer.msg(data.msg)
                        }
                    }
                });
            };
            loadShortData();


            var dayArr = [];
            var lease_time_hour_chart = echarts.init(document.getElementById('lease_time_hour')); //获取装载数据表的容器
            load_time_hour = function () {
                var performance_date = $("#test9").val();

                var time_type = $('#tes1 option:selected').val();
                $.ajax({
                    type: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        "time_type": time_type,
                        "performance_date": performance_date,
                        "type": $("select[name='who2']").val()
                    },
                    dataType: 'json',
                    url: adminurl + "/crm/performanceTrend",
                    success: function (res) {
                        lease_hour_option = {
                            tooltip: {
                                trigger: 'axis'
                            },
                            legend: {
                                top: 'top',
                                left: 'center',
                                data: []
                            },
                            grid: {
                                top: '8%',
                                left: '3%',
                                right: '5%',
                                bottom: '12%',
                                containLabel: true
                            },
                            toolbox: {
                                feature: {
                                    saveAsImage: {}
                                },
                                right: 15
                            },
                            xAxis: {
                                name: '日期',
                                type: 'category',
                                boundaryGap: false,
                                data: []
                            },
                            yAxis: {
                                name: '合同数',
                                type: 'value',
                                minInterval: 1,
                                boundaryGap: [0, 0.1]
                            },
                            color: colorArr,
                            series: []
                        };
                        if (res && res.code == 1) {
                            lease_hour_option.series = res.data.series;
                            lease_hour_option.legend.data = res.data.days;
                            lease_hour_option.xAxis.data = res.data.hourArr;
                        }

                        lease_time_hour_chart.clear();
                        lease_time_hour_chart.setOption(lease_hour_option);//把echarts配置项启动

                    }
                });
            };
            load_time_hour();


            var lease_chart = echarts.init(document.getElementById('lease_thour')); //获取装载数据表的容器
            load_rank = function () {
                var performance_date = $("#test19").val();
                var time_type = $('#tes11 option:selected').val();


                $.ajax({
                    type: 'POST',

                    data: {
                        '_token': "{{ csrf_token() }}",
                        "time_type": time_type,
                        "performance_date": performance_date
                    },
                    dataType: 'json',
                    url: adminurl + "/crm/performanceRank",
                    success: function (res) {

                        lease_option = {
                            legend: {},
                            tooltip: {},
                            dataset: {
                                // dimensions: ['name', '2015', '2016', '2017'],
                                // source: [
                                //     {name: 'Matcha Latte', '2015': 43.3, '2016': 85.8},
                                //     {name: 'Milk Tea', '2015': 83.1, '2016': 73.4},
                                //     {name: 'Cheese Cocoa', '2015': 86.4, '2016': 65.2},
                                //     {name: 'Walnut Brownie', '2015': 72.4, '2016': 53.9}
                                // ]
                            },
                            xAxis: {type: 'category'},
                            yAxis: {},
                            // Declare several bar series, each will be mapped
                            // to a column of dataset.source by default.
                            series: [
                                {type: 'bar'},
                                {type: 'bar'}

                            ]
                        };

                        if (res && res.code == 1) {
                            lease_option.dataset.dimensions = ['name', '网点', '用户'];
                            lease_option.dataset.source = res.data;

                        }

                        lease_chart.clear();
                        lease_chart.setOption(lease_option);//把echarts配置项启动

                    }
                });
            }
            load_rank()

        });
    </script>
@endsection
