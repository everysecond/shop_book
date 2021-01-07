//加载时间控件
layui.use('laydate', function () {
    var laydate = layui.laydate;
    laydate.render({
        elem: '#datetime2'
        , max: -1
    });
    laydate.render({
        elem: '#dateX'
        , range: true
        , max: -1
    });
    laydate.render({
        elem: '#datetime5'
        , max: -1
    });
    laydate.render({
        elem: '#date6'
        , range: true
        , max: -1
    });
});

//区域占比
var box2_chart = echarts.init(document.getElementById('box2')); //获取装载数据表的容器
function load_broken() {
    $.ajax({
        type: 'POST',
        data: {
            date: $("#datetime2").val(),
            type: $('select[name="battery_type_2"]').val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/stock/area",
        success: function (res) {
            lease_hour_option = {
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
                    top: '8%',
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
                    name: '电池数',
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
                        name: '电池数',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            normal: {
                                show: true,
                                position: 'inside',
                                formatter: function (params) {
                                    if (params.value > 0) {
                                        return params.value * 1;
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
                lease_hour_option.series[0].data = res.data.seriesDataOne;
                lease_hour_option.series[1].data = res.data.seriesDataTwo;
                lease_hour_option.xAxis.data = res.data.xAxis;
            }
            box2_chart.clear();
            box2_chart.setOption(lease_hour_option);//把echarts配置项启动
            setTimeout('box2_chart.resize()', 10);
        }
    });
}

//各区域库存统计
function load_trend_table() {
    layui.use('table', function () {
        var table = layui.table;
        var fieldArr = [{field: 'date', title: '日期', fixed: 'left'}];
        provinceArr.forEach(function (item) {
            fieldArr.push({field: item, title: item})
        });

        //第一个实例
        table.render({
            elem: '#trend_table'
            , method: 'post'
            , padding: 15
            , align: "right"
            , url: adminurl + "/service/stock/area/list"//数据接口
            , where: {
                _token: _token,
                type: $("select[name='battery_type_3']").val(),
                dateRange: $("#dateX").val()
            }
            , page: true //开启分页
            , cols: [fieldArr]
        });
    });
}

//电池型号占比
var box5_chart = echarts.init(document.getElementById('box5')); //获取装载数据表的容器
function load_broken5() {
    $.ajax({
        type: 'POST',
        data: {
            date: $("#datetime5").val(),
            type: $('select[name="battery_type_5"]').val(),
            agentId: $('select[name="agentId"]').val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/stock/battery",
        success: function (res) {
            lease_hour_option = {
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
                    top: '8%',
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    name: '电池型号',
                    type: 'category',
                    splitLine: {show: false},
                    data: []
                },
                yAxis: {
                    name: '电池数',
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
                        name: '电池数',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            normal: {
                                show: true,
                                position: 'inside',
                                formatter: function (params) {
                                    if (params.value > 0) {
                                        return params.value * 1;
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
                lease_hour_option.series[0].data = res.data.seriesDataOne;
                lease_hour_option.series[1].data = res.data.seriesDataTwo;
                lease_hour_option.xAxis.data = res.data.xAxis;
            }
            box5_chart.clear();
            box5_chart.setOption(lease_hour_option);//把echarts配置项启动
            setTimeout('box5_chart.resize()', 10);
        }
    });
}

//电池型号库存统计
function load_trend_table6() {
    layui.use('table', function () {
        var table = layui.table, batteryType = $("select[name='battery_type_6']").val();
        var fieldArr = [{field: 'date', title: '日期', fixed: 'left'}, {field: 'total', title: '总数'}];

        if (batteryType == 1 || batteryType == 2) {
            typeOne.forEach(function (item) {
                fieldArr.push({field: item, title: item});
            });
        } else if (batteryType == 4) {
            typeThree.forEach(function (item) {
                fieldArr.push({field: item, title: item});
            });
        } else if (batteryType == 0) {
            typeTwo.forEach(function (item) {
                fieldArr.push({field: item, title: item});
            });
        }

        //第一个实例
        table.render({
            elem: '#trend_table6'
            , method: 'post'
            , padding: 15
            , align: "right"
            , url: adminurl + "/service/stock/battery/list"//数据接口
            , where: {
                _token: _token,
                type: batteryType,
                agentId: $("select[name='agentId2']").val(),
                dateRange: $("#date6").val()
            }
            , page: true //开启分页
            , cols: [fieldArr]
        });
    });
}

//标签页一数据加载
function load_tab_1() {
    load_broken();
    load_trend_table();
}

//标签页二数据加载
function load_tab_2() {
    load_broken5();
    load_trend_table6();
}

//默认加载标签页一
load_tab_1();

$(frames).resize(function () {

});




