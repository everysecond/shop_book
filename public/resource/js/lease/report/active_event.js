//加载时间控件
layui.use('laydate', function () {
    var laydate = layui.laydate;
    laydate.render({
        elem: '#date1'
        , type: 'date'
        , range: true
    });
    laydate.render({
        elem: '#date2'
        , type: 'date'
        , range: true
        , max: 0
    });
    laydate.render({
        elem: '#date3'
        , type: 'date'
        , range: true
        , max: 0
    });
});
//绑定下拉框联动事件
layui.use('form', function () {
    var form = layui.form;
    form.on('select(timeType1)', function (data) {
        if (data.value == -1) {
            $('#date1').css('display', 'block');
        } else {
            $('#date1').css('display', 'none');
        }
    });
    form.on('select(timeType2)', function (data) {
        if (data.value == -1) {
            $('#date3').css('display', 'block');
        } else {
            $('#date3').css('display', 'none');
        }
    });
});

//活跃事件趋势
var lease_trend_chart = echarts.init(document.getElementById('box_lease_trend')); //获取装载数据表的容器
function load_lease_trend() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='register_day_agents']").val(),
            dateRange: $("#date1").val(),
            days: $("select[name='days1']").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/rent_trend",
        success: function (res) {
            register_day_option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    top: 'bottom',
                    left: 'center',
                    selected: {},
                    data: []
                },
                grid: {
                    top: '8%',
                    left: '2%',
                    right: '4%',
                    bottom: '8%',
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
                    name: '用户数',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: colorArr,
                series: []
            };
            if (res && res.code == 1) {
                register_day_option.series = res.data.series;
                register_day_option.xAxis.data = res.data.xAxis;
                register_day_option.legend.data = res.data.legend;
                register_day_option.legend.selected = res.data.hiddenLegend;
            }
            lease_trend_chart.clear();
            lease_trend_chart.setOption(register_day_option);//把echarts配置项启动
            setTimeout('lease_trend_chart.resize()', 10);
        }
    });
}

var lease_times_chart = echarts.init(document.getElementById('box_active_event')); //获取装载数据表的容器
function load_lease_time() {
    $.ajax({
        type: 'POST',
        data: {
            dateRange: $("#date3").val(),
            days: $("select[name='days3']").val(),
            'agentId': $("select[name='agentsId']").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/rent_data",
        success: function (res) {
            times_option = {
                color: ['#3398DB'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        name: '事件类型',
                        type: 'category',
                        data: [],
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        name: '用户数',
                        type: 'value',
                        minInterval: 1,
                        boundaryGap: [0, 0.1]
                    }
                ],
                series: [
                    {
                        name: '网点数',
                        type: 'bar',
                        barWidth: '60%',
                        data: []
                    }
                ]
            };
            if (res && res.code == 1) {
                times_option.series[0].data = res.data.seriesData;
                times_option.xAxis[0].data = res.data.xAxis;
            }
            lease_times_chart.setOption(times_option);//把echarts配置项启动
        }
    });
}

load_lease_time();

//注册审核趋势统计表格
function load_lease_statistics() {
    layui.use('table', function () {
        var date1 = $("#date2").val();
        var agentId = $("select[name='agentId2']").val();
        var type = $("select[name='type']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_total'
            , method: 'post'
            , url: adminurl + "/lease/rent_table"//数据接口

            , where: {_token: _token, area: agentId, datetime: date1}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'process_date', title: '日期', width: 180, fixed: 'left'}
                , {field: 'login_nums', title: '登陆用户数'}
                , {field: 'index_num', title: '到达首页用户数'}
                , {field: 'scan_num', title: '发起扫码用户数'}
                , {field: 'detail_num', title: '到达租赁详情页用户数'}
                , {field: 'period_num', title: '选择租赁周期用户数'}
                , {field: 'deduction_num', title: '旧电池抵扣用户数'}
                , {field: 'submit_lease_num', title: '提交租赁单用户数'}
                , {field: 'business_num', title: '商家确认扫码用户数'}
                , {field: 'topay_num', title: '待支付页面用户数'}
                , {field: 'dopay_num', title: '发起支付用户数'}
                , {field: 'pay_nums', title: '支付成功页面用户数'}
                , {field: 'divpay_num', title: '租赁-登录比'}

            ]]
        });
    });
}

load_lease_trend();
load_lease_statistics();

$(frames).resize(function() {
    lease_trend_chart.resize();
    lease_times_chart.resize();
});




