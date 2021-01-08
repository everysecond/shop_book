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
        elem: '#test9'

        ,range: true
    });

    laydate.render({
        elem: '#test10'

        ,range: true
    });

    laydate.render({
        elem: '#test12'

        ,range: true
    });
});

//绑定下拉框联动事件
layui.use('form', function () {
    var form = layui.form;
    form.on('select(timeType1)', function (data) {
        if (data.value == 7) {
            $('#date1').css('display', 'block');
        } else {
            $('#date1').css('display', 'none');
        }
    });

    form.on('select(demo)',function(data){
        if (data.value == 7) {
            $('#test9').css('display','block');
        }else{
            $('#test9').css('display','none');
        }
    })
});



// 租赁次数
var lease_times_chart = echarts.init(document.getElementById('box_lease_time')); //获取装载数据表的容器
function load_lease_time() {

    var renewal_date =  $("#date1").val();
    var time_type =  $('#tes1 option:selected').val();
    var province_id =  $('#test11 option:selected').val();

    $.ajax({
        type: 'POST',
        data:{'renewal_date':renewal_date,'province_id':province_id,'time_type':time_type,'_token':_token},
        dataType: 'json',
            url: adminurl + "/service/exchange_distribution",
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
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        name:'换租数',
                        type: 'category',
                        data: ["0-50", "51-100", "101-150", "151-200", "201-250", "251-300", "301-350", "351-400", "401-450", "451-500"],
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        name:'网点数',
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '网点数',
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



var china_map_chart = echarts.init(document.getElementById('box_china_map')); //获取装载数据表的容器
function load_area_data() {
    var renewal_date =  $("#test9").val();
    var time_type =  $('#tes2 option:selected').val();
    $.ajax({
        type: 'POST',
        data: {'renewal_date':renewal_date,'time_type':time_type,'_token':_token},
        dataType: 'json',
        url: adminurl + "/service/area_exchange",
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
                    name: "换租平均数",
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
                            shadowColor: '#cbcbcb',
                            shadowBlur: 3,
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

load_area_data();
$(window).resize(function() {
    china_map_chart.resize();
    lease_times_chart.resize();

});