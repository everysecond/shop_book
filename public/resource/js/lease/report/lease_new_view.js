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
            type: 1,
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
            type: 2,
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

//新用户租赁趋势
var lease_trend_chart = echarts.init(document.getElementById('box_lease_trend')); //获取装载数据表的容器
function load_lease_trend() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='register_day_agents']").val(),
            dateRange: $("#date3").val(),
            days: $("select[name='days1']").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/data/new_lease_trend",
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
                    name: '租赁数',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: colorArr,
                series: {
                    "name": "租赁数",
                    "type": 'line',
                    "stack": "租赁数",
                    "symbolSize": 6,
                    "symbol": 'circle',
                    "data": []
                }
            };
            if (res && res.code == 1) {
                register_day_option.series.data = res.data.numData;
                register_day_option.xAxis.data = res.data.hourArr;
            }
            lease_trend_chart.clear();
            lease_trend_chart.setOption(register_day_option);//把echarts配置项启动
            setTimeout('lease_trend_chart.resize()', 10);
        }
    });
}

//新用户租赁统计表格
function load_lease_statistics() {
    layui.use('table', function () {
        var date1 = $("#date2").val();
        var agentId = $("select[name='agentId2']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_total'
            , method: 'post'
            , url: adminurl + "/lease/data/new_lease_statistics"//数据接口
            , where: {_token: _token, agentId: agentId, dateRange: date1, type: 1}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'date', title: '日期', fixed: 'left'}
                , {field: 'today_num', title: '当日租赁数'}
                , {field: 'total_num', title: '累计租赁数'}
                , {field: 'today_rental', title: '当日租赁总额'}
                , {field: 'total_rental', title: '累计租赁总额'}
            ]]
        });
    });
}

//标签页一数据加载
function load_tab_1() {
    load_time_hour();
    load_money_hour();
}

//标签页二数据加载
function load_tab_2() {
    load_lease_trend();
    load_lease_statistics();
}

//默认加载标签页一
load_tab_1();


$(frames).resize(function() {
    lease_time_hour_chart.resize();
    lease_money_hour_chart.resize();
    lease_trend_chart.resize();
});




