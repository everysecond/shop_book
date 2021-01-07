layui.use('laydate', function () {
    var laydate = layui.laydate;
    laydate.render({
        elem: '#date0'
        , type: 'date'
        , range: true
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
});

//绑定下拉框联动事件
layui.use('form', function () {
    var form = layui.form;
    form.on('select(timeType0)', function (data) {
        if (data.value == -1) {
            $('#date0').css('display', 'block');
        } else {
            $('#date0').css('display', 'none');
        }
    });

    form.on('select(timeType1)', function (data) {
        if (data.value == -1) {
            $('#date1').css('display', 'block');
        } else {
            $('#date1').css('display', 'none');
        }
    });
});

//下载趋势
var register_day_chart = echarts.init(document.getElementById('box_trend')); //获取装载数据表的容器
function load_register_day() {
    $.ajax({
        type: 'POST',
        data: {
            dateRange: $("#date0").val(),
            days: $("select[name='days0']").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/data/terminal/trend",
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
                    left: '3%',
                    right: '8%',
                    bottom: '3%',
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
                    name: '下载量',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: colorArr,
                series: {
                    "name": "下载量",
                    "type": 'line',
                    "stack": "下载量",
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

load_register_day()

// 下载量排行
var lease_times_chart = echarts.init(document.getElementById('box_income')); //获取装载数据表的容器
function load_lease_time() {
    $.ajax({
        type: 'POST',
        data: {
            dateRange: $('#date1').val(),
            days: $('select[name="days1"]').val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/data/terminal/sort",
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
                    left: '3%',
                    right: '8%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        name: '渠道',
                        type: 'category',
                        data: [],
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        name: '下载量',
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
var fieldArr = [{field: 'date', title: '日期', fixed: 'left'}];
for (var index in provinceArr) {
    fieldArr.push({
        field: index, title: provinceArr[index]
    });
}

//下载统计表
function load_table() {
    layui.use('table', function () {
        var date1 = $("#date2").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_income'
            , method: 'post'
            , padding: 15
            , align: "right"
            , url: adminurl + "/lease/terminal_table"//数据接口
            , where: {_token: _token, dateRange: date1}
            , page: true //开启分页
            , cols: [fieldArr]
        });
    });
}

load_table();
$(frames).resize(function () {
    box_stock_chart.resize();
    lease_times_chart.resize();
    register_day_chart.resize();
});