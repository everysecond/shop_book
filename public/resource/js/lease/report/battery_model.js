var colorArr = [
    '#2fc25b', '#1890ff', '#fbd437', '#27727B',
    '#87f7cf', '#36cbcb', '#72ccff', '#f7c5a0',
    '#0098d9', '#2b821d', '#e87c25', '#e01f54'
];
var colorNormal = {
    color: function (params) {
        return colorArr[params.dataIndex]
    },
    label:{
        show: true,
        formatter: '{b}:{c}({d}%)'
    },
    labelLine :{show:true}
};
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
    laydate.render({
        elem: '#dateX'
        , type: 'date'
        , range: true
    });
    laydate.render({
        elem: '#dateY'
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
    form.on('select(timeTypeX)', function (data) {
        if (data.value == -1) {
            $('#dateX').css('display', 'block');
        } else {
            $('#dateX').css('display', 'none');
        }
    });
    form.on('select(timeTypeY)', function (data) {
        if (data.value == -1) {
            $('#dateY').css('display', 'block');
        } else {
            $('#dateY').css('display', 'none');
        }
    });
});

// 电池型号统计柱状图
var lease_times_chart = echarts.init(document.getElementById('box_lease_time')); //获取装载数据表的容器
function load_lease_time() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents1']").val(),
            dateRange: $("#date1").val(),
            days: $("select[name='days1']").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/data/battery_histogram",
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
                    top: '8%',
                    left: '2%',
                    right: '10%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        name: '电池型号',
                        type: 'category',
                        data: ["48V12A", "48V20A", "48V32A", "60V20A", "72V20A"],
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        name: '租赁次数',
                        type: 'value',
                        minInterval: 1,
                        boundaryGap: [0, 0.1]
                    }
                ],
                series: [
                    {
                        name: '租赁次数',
                        type: 'bar',
                        barWidth: '60%',
                        data: [0, 0, 0, 0, 0],
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

//电池型号租赁统计表
function load_day_table() {
    layui.use('table', function () {
        var date1 = $("#date2").val();
        var agentId = $("select[name='agents2']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_battery'
            , method: 'post'
            , padding: 15
            , align: "right"
            , url: adminurl + "/lease/data/battery_table"//数据接口
            , where: {_token: _token, agentId: agentId, dateRange: date1}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'date', title: '日期', fixed: 'left',width:104}
                , {field: 'total', title: '租赁数'}
                , {field: 'model_one', title: '48V12A'}
                , {field: 'model_two', title: '48V20A'}
                , {field: 'model_three', title: '48V32A'}
                , {field: 'model_six', title: '48V45A'}
                , {field: 'model_four', title: '60V20A'}
                , {field: 'model_seven', title: '60V32A'}
                , {field: 'model_eight', title: '60V45A'}
                , {field: 'model_five', title: '72V20A'}
                , {field: 'model_nine', title: '72V32A'}
                , {field: 'other', title: '其它型号'}
            ]]
        });
    });
}

load_day_table();

//区域型号统计表
function load_model_table() {
    layui.use('table', function () {
        var date1 = $("#dateY").val();
        var agentId = $("select[name='agentsY']").val();
        var days = $("select[name='daysY']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_model'
            , method: 'post'
            , padding: 15
            , align: "right"
            , url: adminurl + "/lease/data/model_table"//数据接口
            , where: {_token: _token, agentId: agentId, dateRange: date1,days: days}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'area', title: '地区', fixed: 'left',width:104}
                , {field: 'total', title: '租赁数'}
                , {field: 'model_one', title: '48V12A'}
                , {field: 'model_two', title: '48V20A'}
                , {field: 'model_three', title: '48V32A'}
                , {field: 'model_six', title: '48V45A'}
                , {field: 'model_four', title: '60V20A'}
                , {field: 'model_seven', title: '60V32A'}
                , {field: 'model_eight', title: '60V45A'}
                , {field: 'model_five', title: '72V20A'}
                , {field: 'model_nine', title: '72V32A'}
                , {field: 'other', title: '其它型号'}
            ]]
        });
    });
}

load_model_table();


$(frames).resize(function() {
    lease_times_chart.resize();
});