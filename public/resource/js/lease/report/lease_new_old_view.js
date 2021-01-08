var colorArr = [
    '#2fc25b', '#1890ff', '#fbd437', '#27727B',
    '#87f7cf', '#36cbcb', '#72ccff', '#f7c5a0',
    '#0098d9', '#2b821d', '#e87c25', '#e01f54'
];
var colorNormal = {
    color: function (params) {
        return colorArr[params.dataIndex]
    },
    label: {
        show: true,
        formatter: '{b}:{c}({d}%)'
    },
    labelLine: {show: true}
};
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
        elem: '#hour3',
        max: -2
    });
    laydate.render({
        elem: '#hour4',
        max: -2
    });
    laydate.render({
        elem: '#date1'
        , type: 'date'
        , max: 0
    });
    laydate.render({
        elem: '#date2'
        , type: 'date'
        , max: 0
    });
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
        if (data.value == 7) {
            $('#datetime2').css('display', 'block');
        } else {
            $('#datetime2').css('display', 'none');
        }
    });

    form.on('select(demo12)', function (data) {
        if (data.value == 7) {
            $('#datetime1').css('display', 'block');
        } else {
            $('#datetime1').css('display', 'none');
        }
    })
});
//每小时租赁对比
var dayArr = [];
var lease_time_hour_chart = echarts.init(document.getElementById('lease_time_hour')); //获取装载数据表的容器
function load_time_hour() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $("select[name='lease_time_agents']").val(),
            dayArr: dayArr,
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/data/time_hour",
        success: function (res) {
            lease_hour_option = {
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
                    name: '租赁数',
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
            setTimeout('lease_time_hour_chart.resize()', 10);
        }
    });
}

//添加每小时租赁对比
function addDay() {
    var day = $("#hour1").val();
    if (day != "" && dayArr.indexOf(day) == -1) {
        dayArr.push(day);
        load_time_hour();
        $(".clearBtn1").removeClass("hidden");
    }
}

//清空额外每小时对比
function clearDays() {
    $("#hour1").val("");
    if (dayArr != []) {
        dayArr = [];
        $(".clearBtn1").addClass("hidden");
        load_time_hour();
    }
}

//每小时租赁金额对比
var dayArr2 = [];
var lease_money_hour_chart = echarts.init(document.getElementById('lease_money_hour')); //获取装载数据表的容器
function load_money_hour() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $("select[name='lease_money_agents']").val(),
            dayArr: dayArr2,
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/data/money_hour",
        success: function (res) {
            lease_money_hour_option = {
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
                    name: '租赁金额',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: colorArr,
                series: []
            };
            if (res && res.code == 1) {
                lease_money_hour_option.series = res.data.series;
                lease_money_hour_option.legend.data = res.data.days;
                lease_money_hour_option.xAxis.data = res.data.hourArr;
            }
            lease_money_hour_chart.clear();
            lease_money_hour_chart.setOption(lease_money_hour_option);//把echarts配置项启动
            setTimeout('lease_money_hour_chart.resize()', 10);
        }
    });
}

//添加累计每小时租赁金额对比
function addDay2() {
    var day = $("#hour2").val();
    if (day != "" && dayArr2.indexOf(day) == -1) {
        dayArr2.push(day);
        load_money_hour();
        $(".clearBtn2").removeClass("hidden");
    }
}

//清空额外累计每小时对比
function clearDays2() {
    $("#hour2").val("");
    if (dayArr2 != []) {
        dayArr2 = [];
        $(".clearBtn2").addClass("hidden");
        load_money_hour();
    }
}

//每小时租赁对比
var dayArr3 = [];
var lease_time_hour_chart3 = echarts.init(document.getElementById('lease_time_hour3')); //获取装载数据表的容器
function load_time_hour3() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $("select[name='lease_time_agents3']").val(),
            dayArr: dayArr3,
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/data/old_time_hour",
        success: function (res) {
            lease_hour_option = {
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
                    name: '租赁数',
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
            lease_time_hour_chart3.clear();
            lease_time_hour_chart3.setOption(lease_hour_option);//把echarts配置项启动
            setTimeout('lease_time_hour_chart3.resize()', 10);
        }
    });
}

//添加每小时租赁对比
function addDay3() {
    var day = $("#hour3").val();
    if (day != "" && dayArr3.indexOf(day) == -1) {
        dayArr3.push(day);
        load_time_hour3();
        $(".clearBtn3").removeClass("hidden");
    }
}

//清空额外每小时对比
function clearDays3() {
    $("#hour3").val("");
    if (dayArr3 != []) {
        dayArr3 = [];
        $(".clearBtn3").addClass("hidden");
        load_time_hour3();
    }
}

//每小时租赁金额对比
var dayArr4 = [];
var lease_money_hour_chart4 = echarts.init(document.getElementById('lease_money_hour4')); //获取装载数据表的容器
function load_money_hour4() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $("select[name='lease_money_agents4']").val(),
            dayArr: dayArr4,
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/data/old_money_hour",
        success: function (res) {
            lease_money_hour_option = {
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
                    name: '租赁金额',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: colorArr,
                series: []
            };
            if (res && res.code == 1) {
                lease_money_hour_option.series = res.data.series;
                lease_money_hour_option.legend.data = res.data.days;
                lease_money_hour_option.xAxis.data = res.data.hourArr;
            }
            lease_money_hour_chart4.clear();
            lease_money_hour_chart4.setOption(lease_money_hour_option);//把echarts配置项启动
            setTimeout('lease_money_hour_chart4.resize()', 10);
        }
    });
}

//添加累计每小时租赁金额对比
function addDay4() {
    var day = $("#hour4").val();
    if (day != "" && dayArr4.indexOf(day) == -1) {
        dayArr4.push(day);
        load_money_hour4();
        $(".clearBtn4").removeClass("hidden");
    }
}

//清空额外累计每小时对比
function clearDays4() {
    $("#hour4").val("");
    if (dayArr4 != []) {
        dayArr4 = [];
        $(".clearBtn4").addClass("hidden");
        load_money_hour4();
    }
}

//新用户租赁统计表格
function load_lease_statistics() {
    layui.use('table', function () {
        var date1 = $("#date1").val();
        var agentId = $("select[name='agentId1']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_total1'
            , method: 'post'
            , url: adminurl + "/lease/data/day_statistics"//数据接口
            , where: {_token: _token, agentId: agentId, day: date1, type: 1}
            , page: false //开启分页
            , cols: [[ //表头
                {field: 'hour', title: '时间', fixed: 'left'}
                , {field: 'num', title: '租赁数'}
                , {field: 'rental', title: '租赁金额'}
            ]]
        });
    });
}

//老用户租赁统计表格
function load_lease_statistics2() {
    layui.use('table', function () {
        var date1 = $("#date2").val();
        var agentId = $("select[name='agentId2']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_total2'
            , method: 'post'
            , url: adminurl + "/lease/data/day_statistics"//数据接口
            , where: {_token: _token, agentId: agentId, day: date1, type: 2}
            , page: false //开启分页
            , cols: [[ //表头
                {field: 'hour', title: '时间', fixed: 'left'}
                , {field: 'num', title: '租赁数'}
                , {field: 'rental', title: '租赁金额'}
            ]]
        });
    });
}

//新老用户租赁数量对比
var lease_time_chart = echarts.init(document.getElementById('lease_time')); //获取装载数据表的容器
function lease_time_hour() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $('#test13 option:selected').val(),
            renewal_date: $("#datetime1").val(),
            time_type: $('#tes2 option:selected').val(),
            type: 2,
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/newoldlist",
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
                    right: '3%',
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
                    name: '租赁数',
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
                lease_hour_option.legend.data = res.data.linedays;
                lease_hour_option.xAxis.data = res.data.dayArr;
            }
            lease_time_chart.clear();
            lease_time_chart.setOption(lease_hour_option);//把echarts配置项启动
            setTimeout('lease_time_chart.resize()', 10);
        }
    });
}

//新老用户租赁金额对比
var box2_chart = echarts.init(document.getElementById('box2')); //获取装载数据表的容器
function load_broken() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $('#test12 option:selected').val(),
            renewal_date: $("#datetime2").val(),
            time_type: $('#time2 option:selected').val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/newoldmoneylist",
        success: function (res) {
            lease_hour_option = {
                title: {
                    // text: '租赁金额对比'
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
                    right: '3%',
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
                    name: '金额数(元)',
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
                lease_hour_option.legend.data = res.data.linedays;
                lease_hour_option.xAxis.data = res.data.dayArr;
            }
            box2_chart.clear();
            box2_chart.setOption(lease_hour_option);//把echarts配置项启动
            setTimeout('box2_chart.resize()', 10);
        }
    });
}

//新老用户租赁统计表格
function load_trend_table() {
    layui.use('table', function () {
        var date1 = $("#dateX").val();
        var agentId = $("select[name='agentIdX']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#trend_table'
            , method: 'post'
            , url: adminurl + "/lease/data/new_old_statistics"//数据接口
            , where: {_token: _token, agentId: agentId, dateRange: date1}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'date', title: '日期', fixed: 'left'}
                , {field: 'new_num', title: '新用户租赁数'}
                , {field: 'old_num', title: '老用户租赁数'}
                , {field: 'new_rental', title: '新用户租赁金额'}
                , {field: 'old_rental', title: '老用户租赁金额'}
            ]]
        });
    });
}

//标签页一数据加载
function load_tab_1() {
    lease_time_hour();
    load_broken();
    load_trend_table();
}

//标签页二数据加载
function load_tab_2() {
    load_time_hour();
    load_money_hour();
    load_lease_statistics();
}

//标签页二数据加载
function load_tab_3() {
    load_time_hour3();
    load_money_hour4();
    load_lease_statistics2();
}

//默认加载标签页一
load_tab_1();


$(frames).resize(function () {

});




