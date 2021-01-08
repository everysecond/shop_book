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

//各区域网点收益分布
var box_stock_chart = echarts.init(document.getElementById('box_income')); //获取装载数据表的容器
function load_box_stock() {
    $.ajax({
        type: 'POST',
        data: {
            dateRange: $("#date1").val(),
            days: $("select[name='days1']").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/income_area",
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
                    name: '收益(￥)',
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
                        name: '收益',
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


//各区域网点收益统计
function load_table() {
    layui.use('table', function () {
        var date1 = $("#date2").val();
        var table = layui.table;
        var fieldArr = [{field: 'date', title: '日期', fixed: 'left'}];
        provinceArr.forEach(function (item) {
            fieldArr.push({field: item, title: item})
        });

        //第一个实例
        table.render({
            elem: '#table_income'
            , method: 'post'
            , padding: 15
            , align: "right"
            , url: adminurl + "/service/income_table"//数据接口
            , where: {_token: _token, dateRange: date1}
            , page: true //开启分页
            , cols: [fieldArr]
        });
    });
}

load_table();

$(frames).resize(function() {
    box_stock_chart.resize();
});