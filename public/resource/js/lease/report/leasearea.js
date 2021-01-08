load_area_data("", "");
load_area_data_cum("", "");
//用户地区分布
var china_map_chart = echarts.init(document.getElementById('box_china_map')); //获取装载数据表的容器
function load_area_data(datetime, modules) {
    $.ajax({
        type: 'POST',
        data: {'_token': _token, datetime: datetime, time_type: modules},
        dataType: 'json',
        url: adminurl + "/lease/areadata",
        success: function (res) {
            china_map_option = {
                tooltip: {
                    trigger: "item"
                },
                dataRange: {
                    orient: "horizontal",
                    min: 0,
                    max: 1000,
                    text: ["高", "低"],
                    realtime: false,
                    splitNumber: 0,
                    inRange: {
                        // color: ['#fdfdf0', '#f7c345', '#e2311b']
                        color: ['#eaf3f9', '#6dbff6', '#57b7f7', '#43aef6', '#2da6f6', '#19a0f9', '#079bfd']
                        // color: ['#b4e0f8', '#50a4e8']
                    },
                    borderColor: "white"
                },
                series: [{
                    name: "租赁地区分布图",
                    type: "map",
                    map: "china",
                    mapLocation: {
                        x: "center"
                    },
                    label: {
                        normal: {
                            textStyle: {
                                fontSize: 12,
                                color: '#a85e17'
                            }
                        }
                    },
                    selectedMode: "multiple",
                    itemStyle: {
                        normal: {
                            borderWidth: 0.1,
                            borderColor: "white",
                            label: {
                                show: !0
                            },
                            color: "#0068dc"
                        },
                        emphasis: {
                            shadowBlur: 3,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(166,166,166,0.5)',
                            areaColor: "white",
                        },
                    },
                    data: []
                }]
            };
            if (res && res.code == 1) {
                china_map_option.series[0].data = res.data.areaData;
                china_map_option.dataRange.min = res.data.min;
                china_map_option.dataRange.max = res.data.max;
            }
            china_map_chart.setOption(china_map_option);//把echarts配置项启动
        }
    });
}


// 区域租赁数 柱状图
var lease_times_chart = echarts.init(document.getElementById('box_lease_time')); //获取装载数据表的容器


function load_area_data_cum(datetime, modules) {
    $.ajax({
        type: 'POST',
        data: {'_token': _token, datetime: datetime, time_type: modules},
        dataType: 'json',
        url: adminurl + "/lease/areadatalist",
        success: function (res) {
            if (res && res.code == 1) {
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
                            name: '渠道',
                            type: 'category',
                            data: res.data.areaname,
                            axisTick: {
                                alignWithLabel: true
                            }
                        }
                    ],
                    yAxis: [
                        {
                            name: '租赁数',
                            type: 'value'
                        }
                    ],
                    series: [
                        {
                            name: '租赁人数',
                            type: 'bar',
                            barWidth: '20%',
                            data: res.data.areadata
                        }
                    ]
                };

            } else {

            }
            lease_times_chart.setOption(times_option);//把echarts配置项启动
        }
    });
}

layui.use(['element', 'jquery', 'form', 'table', 'laydate'], function () {
    var element = layui.element;
    var form = layui.form;
    $ = layui.jquery;
    var table = layui.table;
    var laydate = layui.laydate;

    laydate.render({
        elem: '#datetimeclick'
        , range: true
    });

    laydate.render({
        elem: '#datetimeclicktwo'
        , range: true
    });

    form.on('select(clicktime)', function (data) {
        if (data.value == 7) {
            $('#datetimeclick').css('display', 'block');
        } else {
            $('#datetimeclick').css('display', 'none');
        }
    })

    form.on('select(clicktimetwo)', function (data) {
        if (data.value == 7) {
            $('#datetimeclicktwo').css('display', 'block');
        } else {
            $('#datetimeclicktwo').css('display', 'none');
        }
    })


    var renewal_date = $("#weektime").attr("value");
    var province_id = $('#test option:selected').val();

    //数据搜索
    form.on('submit(formSearch)', function (data) {
        if (data.field.datetime == "" && data.field.modules == "") {
            layer.msg("搜索内容不能为空！");
        } else {
            load_area_data(data.field.datetime, data.field.modules);
        }
        return false;
    });
    //数据搜索
    form.on('submit(formSearchTwo)', function (data) {
        if (data.field.datetimetwo == "" && data.field.modtimetwo == "") {
            layer.msg("搜索内容不能为空！");
        } else {
            load_area_data_cum(data.field.datetimetwo, data.field.modtimetwo);
        }
        return false;
    });

});

$(frames).resize(function() {
    china_map_chart.resize();
    lease_times_chart.resize();
});





