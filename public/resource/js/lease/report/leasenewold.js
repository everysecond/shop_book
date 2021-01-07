layui.use('laydate', function () {
    var laydate = layui.laydate;
    laydate.render({
        elem: '#datetime1'
        , range: true
    });

    laydate.render({
        elem: '#datetime2'
        , range: true
    });
});

layui.use('form', function () {
    var form = layui.form;
    form.on('select(demo13)', function (data) {
        if (data.value == 7) {
            $('#datetime2').css('display', 'block');
        } else {
            $('#datetime2').css('display', 'none');
        }
    })

    form.on('select(demo12)', function (data) {
        if (data.value == 7) {
            $('#datetime1').css('display', 'block');
        } else {
            $('#datetime1').css('display', 'none');
        }
    })
});


$("#search2").click(function () {
    load_time_hour();
});

$("#search3").click(function () {
    load_broken();
});

//新老用户租赁金额对比
var box2_chart = echarts.init(document.getElementById('box2')); //获取装载数据表的容器
load_broken();

function load_broken() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $('#test12 option:selected').val(),
            renewal_date: $("#datetime2").attr("value"),
            time_type: $('#time2 option:selected').val(),
            type: 2,
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

load_time_hour();
//新老用户租赁数量对比
var lease_time_hour_chart = echarts.init(document.getElementById('lease_time_hour')); //获取装载数据表的容器
function load_time_hour() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $('#test13 option:selected').val(),
            renewal_date: $("#datetime1").attr("value"),
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
            lease_time_hour_chart.clear();
            lease_time_hour_chart.setOption(lease_hour_option);//把echarts配置项启动
            setTimeout('lease_time_hour_chart.resize()', 10);
        }
    });
}

$(frames).resize(function() {
    box2_chart.resize();
    lease_time_hour_chart.resize();
});

