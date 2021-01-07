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
        url: adminurl + "/service/active_trend",
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
                    name: '页面访问次数',
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
        url: adminurl + "/service/active_data",
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
                        name: '访问次数',
                        type: 'value',
                        minInterval: 1,
                        boundaryGap: [0, 0.1]
                    }
                ],
                series: [
                    {
                        name: '访问次数',
                        type: 'bar',
                        barWidth: '60%',
                        data: [],
                        itemStyle: {        //上方显示数值
                            normal: {
                                label: {
                                    show: true, //开启显示
                                    position: 'top', //在上方显示
                                    textStyle: { //数值样式
                                        color: '#333',
                                        fontSize: 12
                                    }
                                }
                            }
                        }
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
            , url: adminurl + "/service/active_table"//数据接口
            , where: {_token: _token, agentId: agentId, dateRange: date1, type: type}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'date', title: '日期', fixed: 'left'}
                , {field: "slide/middle", title: "首页"}
                , {field: "stock-lease", title: "查看库存"}
                , {field: "rents", title: "查看租赁"}
                , {field: "replace", title: "查看换电"}
                , {field: "retires", title: "查看退租"}
                , {field: "bonus-calculate", title: "收益计算器"}
                , {field: "protocol/site_rent_flow", title: "租赁流程"}
                , {field: "recycle-battery", title: "电池行情"}
                , {field: "wallet", title: "查看余额"}
                , {field: "notifies", title: "消息中心"}
                , {field: "hot-problems", title: "服务中心"}
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




