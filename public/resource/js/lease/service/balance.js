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
});

// 余额金额分布
var lease_times_chart = echarts.init(document.getElementById('box_lease_time')); //获取装载数据表的容器
function load_lease_time() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents1']").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/balance",
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
                        name: '金额区间',
                        type: 'category',
                        data: [],
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        name: '网点数',
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

//各区域余额统计
var box_stock_chart = echarts.init(document.getElementById('box_stock')); //获取装载数据表的容器
function load_box_stock() {
    $.ajax({
        type: 'POST',
        data: {'agentId': $("select[name='user_age_agents']").val(), '_token': _token},
        dataType: 'json',
        url: adminurl + "/service/balance_area",
        success: function (res) {
            box_stock_option = {
                color: ['#37a2da'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    },
                    formatter: function (params) {
                        var tar = params[1];
                        return tar.name + '<br/>' + tar.seriesName + ' : ' + tar.value;
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    name: '省份',
                    type: 'category',
                    splitLine: {show: false},
                    data: []
                },
                yAxis: {
                    name: '余额(￥)',
                    type: 'value'
                },
                series: [
                    {
                        name: '辅助',
                        type: 'bar',
                        stack: '总量',
                        itemStyle: {
                            normal: {
                                barBorderColor: 'rgba(0,0,0,0)',
                                color: 'rgba(0,0,0,0)'
                            },
                            emphasis: {
                                barBorderColor: 'rgba(0,0,0,0)',
                                color: 'rgba(0,0,0,0)'
                            }
                        },
                        data: []
                    },
                    {
                        name: '余额',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            normal: {
                                show: true,
                                position: 'inside',
                                formatter: function(params) {
                                    if (params.value > 0) {
                                        return params.value*1;
                                    } else {
                                        return '';
                                    }
                                }
                            }
                        },
                        data: []
                    }
                ]
            };
            if (res && res.code == 1) {
                box_stock_option.series[0].data = res.data.seriesDataOne;
                box_stock_option.series[1].data = res.data.seriesDataTwo;
                box_stock_option.xAxis.data = res.data.xAxis;
            }
            box_stock_chart.clear();
            box_stock_chart.setOption(box_stock_option);//把echarts配置项启动
            setTimeout('box_stock_chart.resize()', 10);
        }
    });
}

load_box_stock();

$(frames).resize(function() {
    lease_times_chart.resize();
    box_stock_chart.resize();
});