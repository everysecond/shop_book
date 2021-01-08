//加载时间控件
layui.use('laydate', function () {
    var laydate = layui.laydate;
    laydate.render({
        elem: '#datetime1'
        , range: true
        , max: 0
    });
    laydate.render({
        elem: '#datetime2'
        , range: true
        , max: 0
    });
    laydate.render({
        elem: '#dateX'
        , range: true
        , max: 0
    });

});
//绑定下拉框联动事件
layui.use('form', function () {
    var form = layui.form;
    form.on('select(demo13)', function (data) {
        if (data.value == -1) {
            $('#datetime2').css('display', 'block');
        } else {
            $('#datetime2').css('display', 'none');
        }
    });
    form.on('select(demo12)', function (data) {
        if (data.value == -1) {
            $('#datetime1').css('display', 'block');
        } else {
            $('#datetime1').css('display', 'none');
        }
    });

});

//回收趋势
var lease_time_chart = echarts.init(document.getElementById('lease_time')); //获取装载数据表的容器
function lease_time_hour() {
    $.ajax({
        type: 'POST',
        data: {
            // agentId: $('#test13 option:selected').val(),
            dateRange: $("#datetime1").val(),
            days: $('#tes2 option:selected').val(),
            type: $('select[name="battery_type_1"]').val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/recycle/trend",
        success: function (res) {

            lease_hour_option = {
                title: {
                    // text: '租赁数量对比'
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    top: 'bottom',
                    left: 'center',
                    data: []
                },
                grid: {
                    top: '8%',
                    left: '3%',
                    right: '6%',
                    bottom: '8%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    name: '日期',
                    type: 'category',
                    boundaryGap: false,
                    data: []
                },
                yAxis: {
                    name: '申请数/电池只数',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: [
                    '#fbd437', '#3aa1ff', '#36cbcb', '#4ecb73', '#27727B'
                ],
                series: []
            };
            if (res && res.code == 1) {

                lease_hour_option.series = res.data.series;
                lease_hour_option.legend.data = res.data.legend;
                lease_hour_option.xAxis.data = res.data.xAxis;
            }
            lease_time_chart.clear();
            lease_time_chart.setOption(lease_hour_option);//把echarts配置项启动
            setTimeout('lease_time_chart.resize()', 10);
        }
    });
}

//回收分析
var box2_chart = echarts.init(document.getElementById('box2')); //获取装载数据表的容器
function load_broken() {
    $.ajax({
        type: 'POST',
        data: {
            dateRange: $("#datetime2").val(),
            days: $('#time2 option:selected').val(),
            type: $('select[name="battery_type_2"]').val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/recycle/analysis",
        success: function (res) {
            lease_hour_option = {
                color: ['#5793f3', '#d14a61', '#675bba'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                grid: {
                    top: '8%',
                    left: '4%',
                    right: '6%',
                    bottom: '8%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data: ['回收申请数', '回收电池只数']
                },
                xAxis: {
                    name: '省份',
                    type: 'category',
                    data: [],
                    axisPointer: {
                        type: 'shadow'
                    },
                    splitLine: {
                        show: false
                    },

                },
                yAxis: [
                    {
                        type: 'value',
                        name: '申请数/电池只数',
                        axisLabel: {
                            formatter: '{value}'
                        }
                    }/*,
                    {
                        type: 'value',
                        name: '金额',
                        axisLabel: {
                            formatter: '{value}元'
                        }
                    }*/
                ],
                series: [
                    {
                        name: '回收申请数',
                        type: 'bar',
                        data: [],
                    },
                    {
                        name: '回收电池只数',
                        type: 'bar',
                        data: [],
                    }/*,
                    {
                        name:'金额',
                        type:'line',
                        yAxisIndex: 1,
                        data:res.data.renewal_amount,
                    }*/
                ]
            };
            if (res && res.code == 1) {
                lease_hour_option.series = res.data.series;
                lease_hour_option.legend.data = res.data.legend;
                lease_hour_option.xAxis.data = res.data.xAxis;
            }
            box2_chart.clear();
            box2_chart.setOption(lease_hour_option);//把echarts配置项启动
            setTimeout('box2_chart.resize()', 10);
        }
    });
}

//回收统计
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
            , url: adminurl + "/service/recycle/table"//数据接口
            , where: {
                _token: _token,
                type: $("select[name='battery_type_3']").val(),
                num_type: $("select[name='num_type']").val(),
                dateRange: $("#dateX").val()
            }
            , page: true //开启分页
            , cols: [fieldArr]
        });
    });
}

lease_time_hour();
load_broken();
load_trend_table();
$(frames).resize(function () {

});