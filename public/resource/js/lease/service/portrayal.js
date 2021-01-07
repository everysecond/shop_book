//用户年龄分布
var age_chart = echarts.init(document.getElementById('box_age')); //获取装载数据表的容器
function load_age_data() {
    $.ajax({
        type: 'POST',
        data: {'agentId': $("select[name='user_age_agents']").val(), '_token': _token},
        dataType: 'json',
        url: adminurl + "/service/age",
        success: function (res) {
            age_option = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{b} : {c} ({d}%)"
                },
                legend: {
                    bottom: 30,
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
            if (res && res.code == 1) {
                age_option.legend.data = res.data.ageArr;
                age_option.series[0].data = res.data.ageData;
            }
            age_chart.setOption(age_option);//把echarts配置项启动
        }
    });
}

load_age_data();
//用户性别分布
var sex_chart = echarts.init(document.getElementById('box_sex')); //获取装载数据表的容器
function load_sex_data() {
    $.ajax({
        type: 'POST',
        data: {'agentId': $("select[name='user_sex_agents']").val(), '_token': _token},
        dataType: 'json',
        url: adminurl + "/service/sex",
        success: function (res) {
            sex_option = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{b} : {c} ({d}%)"
                },
                legend: {
                    bottom: 30,
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
            if (res && res.code == 1) {
                sex_option.legend.data = res.data.sexArr;
                sex_option.series[0].data = res.data.sexData;
            }
            sex_chart.setOption(sex_option);//把echarts配置项启动
        }
    });
}

load_sex_data();

//用户地区分布
// var china_map_chart = echarts.init(document.getElementById('box_china_map')); //获取装载数据表的容器
// function load_area_data() {
//     $.ajax({
//         type: 'POST',
//         data: {'_token': _token},
//         dataType: 'json',
//         url: adminurl + "/service/area",
//         success: function (res) {
//             china_map_option = {
//                 tooltip: {
//                     trigger: "item"
//                 },
//                 dataRange: {
//                     orient: "horizontal",
//                     min: 0,
//                     max: 1000,
//                     text: ["高", "低"],
//                     realtime: false,
//                     splitNumber: 0,
//                     inRange: {
//                         // color: ['#fdfdf0', '#f7c345', '#e2311b']
//                         color: ['#b4e0f8', '#50a4e8']
//                     },
//                     borderColor: "white"
//                 },
//                 series: [{
//                     name: "用户地区分布图",
//                     type: "map",
//                     map: "china",
//                     mapLocation: {
//                         x: "center"
//                     },
//                     label: {
//                         normal: {
//                             textStyle: {
//                                 fontSize: 12,
//                                 color: '#a85e17'
//                             }
//                         }
//                     },
//                     selectedMode: "multiple",
//                     itemStyle: {
//                         normal: {
//                             borderWidth: 0.1,
//                             borderColor: "white",
//                             label: {
//                                 show: !0
//                             },
//                             shadowColor: '#dcfcfc',
//                             shadowBlur: 10,
//                             color: "#0068dc"
//                         },
//                         emphasis: {
//                             shadowBlur: 10,
//                             shadowOffsetX: 0,
//                             shadowColor: 'rgba(166,166,166,0.5)',
//                             areaColor: "#dcfcfc",
//                         },
//                     },
//                     data: []
//                 }]
//             };
//             if (res && res.code == 1) {
//                 china_map_option.series[0].data = res.data.areaData;
//                 china_map_option.dataRange.min = res.data.min;
//                 china_map_option.dataRange.max = res.data.max;
//             }
//             china_map_chart.setOption(china_map_option);//把echarts配置项启动
//         }
//     });
// }
//
// load_area_data();

// 网点数排行
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
        url: adminurl + "/service/data/sort",
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
                    left: '3%',
                    right: '6%',
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

$(frames).resize(function() {
    age_chart.resize();
    sex_chart.resize();
    // china_map_chart.resize();
    lease_times_chart.resize();
});