//加载时间控件
layui.use('laydate', function () {
    var laydate = layui.laydate;
    laydate.render({
        elem: '#hour1',
        max: -2
    });
    laydate.render({
        elem: '#hour2',
        max: -2
    });
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
        elem: '#date3'
        , type: 'date'
        , range: true
        , max: 0
    });
    laydate.render({
        elem: '#date4'
        , type: 'date'
        , range: true
        , max: 0
    });
    laydate.render({
        elem: '#date5'
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
            $('#date3').css('display', 'block');
        } else {
            $('#date3').css('display', 'none');
        }
    });

    form.on('select(timeType2)', function (data) {
        if (data.value == -1) {
            $('#date4').css('display', 'block');
        } else {
            $('#date4').css('display', 'none');
        }
    })
});
//每小时启动对比
var dayArr = [];
var register_hour_chart = echarts.init(document.getElementById('box_register_hour')); //获取装载数据表的容器
function load_register_hour() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $("select[name='register_hour_agents']").val(),
            dayArr: dayArr,
            type: 3,
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/start_hour",
        success: function (res) {
            register_hour_option = {
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
                    left: '1%',
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
                    name: '小时',
                    type: 'category',
                    boundaryGap: false,
                    data: []
                },
                yAxis: {
                    name: '启动次数',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: colorArr,
                series: []
            };
            if (res && res.code == 1) {
                register_hour_option.series = res.data.series;
                register_hour_option.legend.data = res.data.days;
                register_hour_option.xAxis.data = res.data.hourArr;
            }
            register_hour_chart.clear();
            register_hour_chart.setOption(register_hour_option);//把echarts配置项启动
            setTimeout('register_hour_chart.resize()', 10);
        }
    });
}

//添加每小时启动对比
function addDay() {
    var day = $("#hour1").val();
    if (day != "" && dayArr.indexOf(day) == -1) {
        dayArr.push(day);
        load_register_hour();
        $(".clearBtn1").removeClass("hidden");
    }
}

//清空额外每小时对比
function clearDays() {
    $("#hour1").val("");
    if (dayArr != []) {
        dayArr = [];
        $(".clearBtn1").addClass("hidden");
        load_register_hour();
    }
}

//累计每小时启动对比
var dayArr2 = [];
var register_total_chart = echarts.init(document.getElementById('box_register_total')); //获取装载数据表的容器
function load_register_total() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $("select[name='register_hour_agents2']").val(),
            dayArr: dayArr2,
            type: 4,
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/start_hour",
        success: function (res) {
            register_total_option = {
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
                    left: '1%',
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
                    name: '小时',
                    type: 'category',
                    boundaryGap: false,
                    data: []
                },
                yAxis: {
                    name: '启动次数',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: colorArr,
                series: []
            };
            if (res && res.code == 1) {
                register_total_option.series = res.data.series;
                register_total_option.legend.data = res.data.days;
                register_total_option.xAxis.data = res.data.hourArr;
            }
            register_total_chart.clear();
            register_total_chart.setOption(register_total_option);//把echarts配置项启动
            setTimeout('register_total_chart.resize()', 10);
        }
    });
}

//添加累计每小时启动对比
function addDay2() {
    var day = $("#hour2").val();
    if (day != "" && dayArr2.indexOf(day) == -1) {
        dayArr2.push(day);
        load_register_total();
        $(".clearBtn2").removeClass("hidden");
    }
}

//清空额外累计每小时对比
function clearDays2() {
    $("#hour2").val("");
    if (dayArr2 != []) {
        dayArr2 = [];
        $(".clearBtn2").addClass("hidden");
        load_register_total();
    }
}

//每小时启动量表格
function load_day_table() {
    layui.use('table', function () {
        var date1 = $("#date1").val();
        var agentId = $("select[name='agentId1']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_day'
            , method: 'post'
            , padding: 15
            , align: "right"
            , url: adminurl + "/service/table_start_hour"//数据接口
            , where: {_token: _token, agentId: agentId, dateRange: date1, type: 3}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'date', title: '日期/启动次数', width: 120, fixed: 'left'}
                , {field: 'hour1', title: '0-1', width: 70}
                , {field: 'hour2', title: '1-2', width: 70}
                , {field: 'hour3', title: '2-3', width: 70}
                , {field: 'hour4', title: '3-4', width: 70}
                , {field: 'hour5', title: '4-5', width: 70}
                , {field: 'hour6', title: '5-6', width: 70}
                , {field: 'hour7', title: '6-7', width: 70}
                , {field: 'hour8', title: '7-8', width: 70}
                , {field: 'hour9', title: '8-9', width: 70}
                , {field: 'hour10', title: '9-10', width: 70}
                , {field: 'hour11', title: '10-11', width: 70}
                , {field: 'hour12', title: '11-12', width: 70}
                , {field: 'hour13', title: '12-13', width: 70}
                , {field: 'hour14', title: '13-14', width: 70}
                , {field: 'hour15', title: '14-15', width: 70}
                , {field: 'hour16', title: '15-16', width: 70}
                , {field: 'hour17', title: '16-17', width: 70}
                , {field: 'hour18', title: '17-18', width: 70}
                , {field: 'hour19', title: '18-19', width: 70}
                , {field: 'hour20', title: '19-20', width: 70}
                , {field: 'hour21', title: '20-21', width: 70}
                , {field: 'hour22', title: '21-22', width: 70}
                , {field: 'hour23', title: '22-23', width: 70}
                , {field: 'hour24', title: '23-24', width: 70}
                , {field: 'total', title: '总启动次数', width: 95}
            ]]
        });
    });
}

//累计每小时启动量表格
function load_day_total_table() {
    layui.use('table', function () {
        var date = $("#date2").val();
        var agentId = $("select[name='agentId2']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_total'
            , method: 'post'
            , padding: 15
            , align: "right"
            , url: adminurl + "/service/table_start_hour"//数据接口
            , where: {_token: _token, agentId: agentId, dateRange: date, type: 4}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'date', title: '日期/启动次数', width: 120, fixed: 'left'}
                , {field: 'hour1', title: '0-1'}
                , {field: 'hour2', title: '0-2'}
                , {field: 'hour3', title: '0-3'}
                , {field: 'hour4', title: '0-4'}
                , {field: 'hour5', title: '0-5'}
                , {field: 'hour6', title: '0-6'}
                , {field: 'hour7', title: '0-7'}
                , {field: 'hour8', title: '0-8'}
                , {field: 'hour9', title: '0-9'}
                , {field: 'hour10', title: '0-10'}
                , {field: 'hour11', title: '0-11'}
                , {field: 'hour12', title: '0-12'}
                , {field: 'hour13', title: '0-13'}
                , {field: 'hour14', title: '0-14'}
                , {field: 'hour15', title: '0-15'}
                , {field: 'hour16', title: '0-16'}
                , {field: 'hour17', title: '0-17'}
                , {field: 'hour18', title: '0-18'}
                , {field: 'hour19', title: '0-19'}
                , {field: 'hour20', title: '0-20'}
                , {field: 'hour21', title: '0-21'}
                , {field: 'hour22', title: '0-22'}
                , {field: 'hour23', title: '0-23'}
                , {field: 'hour24', title: '0-24'}
            ]]
        });

    });
}

//启动趋势统计
var register_day_chart = echarts.init(document.getElementById('box_register_day')); //获取装载数据表的容器
function load_register_day() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='register_day_agents']").val(),
            dateRange: $("#date3").val(),
            days: $("select[name='days1']").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/start_day",
        success: function (res) {
            register_day_option = {
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
                    name: '启动次数',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: colorArr,
                series: {
                    "name": "启动趋势",
                    "type": 'line',
                    "stack": "启动趋势",
                    "symbolSize": 6,
                    "symbol": 'circle',
                    "data": []
                }
            };
            if (res && res.code == 1) {
                register_day_option.series.data = res.data.numData;
                register_day_option.xAxis.data = res.data.hourArr;
            }
            register_day_chart.clear();
            register_day_chart.setOption(register_day_option);//把echarts配置项启动
            setTimeout('register_day_chart.resize()', 10);
        }
    });
}

//用户启动渠道分布
var register_from_chart = echarts.init(document.getElementById('box_register_from')); //获取装载数据表的容器
function load_from_data() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='register_from_agents']").val(),
            dateRange: $("#date4").val(),
            days: $("select[name='days2']").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/start_from",
        success: function (res) {
            from_option = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{b} : {c} ({d}%)"
                },
                legend: {
                    bottom: 0,
                    left: 'center',
                    data: ["抱歉！暂无数据"]
                },
                series: [
                    {
                        type: 'pie',
                        radius: ['40%', '60%'],
                        center: ['50%', '42%'],
                        selectedMode: 'single',
                        data: [{"value": 0, "name": "抱歉！暂无数据"}],
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            },
                            normal: colorNormal
                        }
                    }
                ]
            };
            if (res && res.code == 1 && res.data.modelArr.length != 0) {
                from_option.legend.data = res.data.modelArr;
                from_option.series[0].data = res.data.modelData;
            }
            register_from_chart.clear();
            register_from_chart.setOption(from_option);//把echarts配置项启动
            setTimeout('register_from_chart.resize()', 10);
        }
    });
}

//用户启动统计
function load_register_total_table() {
    layui.use('table', function () {
        var date = $("#date5").val();
        var agentId = $("select[name='agentId3']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_register_total'
            , method: 'post'
            , padding: 15
            , url: adminurl + "/service/start_total"//数据接口
            , where: {_token: _token, agentId: agentId, dateRange: date, type: 4}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'date', title: '日期'},
                {field: 'total', title: '当日启动数'}
            ]]
        });
    });
}

//标签页一数据加载
function load_tab_1() {
    load_register_hour();
    load_day_table();
}

//标签页二数据加载
function load_tab_2() {
    load_register_total();
    load_day_total_table();
}

//标签页三数据加载
function load_tab_3() {
    load_register_day();
    load_from_data();
    load_register_total_table();
}

//默认加载标签页一
load_tab_1();

$(frames).resize(function () {
    register_hour_chart.resize();
});




