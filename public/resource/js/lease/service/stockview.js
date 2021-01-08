load_area_data();
load_box_stock();
load_table();

//区域分布
var box_stock_chart = echarts.init(document.getElementById('box_stock')); //获取装载数据表的容器
function load_box_stock() {
    $.ajax({
        type: 'POST',
        data:
     {'_token': _token,'type': $("select[name='type1']").val(),'time_type': $("select[name='days1']").val(),'datetime': $("#date1").val()},
        dataType: 'json',
        url: adminurl + "/service/replenishmentdata",
        success: function (res) {
            if (res.code == 1) {
                option = {
                    color: ['#3398DB'],
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
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

                        type : 'category',
                        splitLine: {show:false},
                        data : res.data.areaname
                    },
                    yAxis: {
                        name: '补货数',
                        type : 'value'
                    },
                    series: [
                        {

                            type: 'bar',
                            stack:  '总量',
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
                            data: res.data.areadatacum
                        },
                        {
                            name: '补货数',
                            type: 'bar',
                            stack: '总量',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'inside'
                                }
                            },
                            data:res.data.areadata
                        }
                    ]
                };
                box_stock_chart.clear();
                box_stock_chart.setOption(option);//把echarts配置项启动
                setTimeout('box_stock_chart.resize()', 10);
            } else {
                layer.msg('暂无数据')
            }
        }
    });
}


//地区分布
var china_map_chart = echarts.init(document.getElementById('box_china_map')); //获取装载数据表的容器
function load_area_data() {
    $.ajax({
        type: 'POST',
        data: {'_token': _token,'type': $("select[name='type2']").val(),'time_type': $("select[name='days2']").val(),'datetime': $("#date2").val()},

        dataType: 'json',
        url: adminurl + "/service/replenishmentarea",
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
                    name: "分布图",
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



layui.use(['element', 'jquery', 'form', 'table', 'laydate'], function () {
    var element = layui.element;
    var form = layui.form;
    $ = layui.jquery;
    var laydate = layui.laydate;

    laydate.render({
        elem: '#date1'
        , range: true
    });
    laydate.render({
        elem: '#date2'
        , range: true
    });
    laydate.render({
        elem: '#date3'
        , range: true
    });
    form.on('select(days1)', function (data) {
        if (data.value == 7) {
            $('#date1').css('display', 'block');
        } else {
            $('#date1').css('display', 'none');
        }
    })

    form.on('select(days2)', function (data) {
        if (data.value == 7) {
            $('#date2').css('display', 'block');
        } else {
            $('#date2').css('display', 'none');
        }
    })


});

//表格
function load_table() {
    layui.use('table', function () {
        var datetime = $('#date3').val();
        var type = $('#type3 option:selected').val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#tabledata'
            , method: 'post'
            , text: {
                none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
            }
            , skin: 'line' //行边框风格
            , even: true //开启隔行背景
            , size: 'sm' //小尺寸的表格
            , url: adminurl + "/service/replenishmentlist"//数据接口
            , where: {_token: _token, todatetime: datetime, type: type}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'date', title: '日期', width: 180, fixed: 'left'}
                , {field: 'total_num', title: '总数'}
                , {field: 'num_65', title: '湖南'}
                , {field: 'num_78', title: '安徽'}
                , {field: 'num_85', title: '江西'}
                , {field: 'num_91', title: '河南'}
                , {field: 'num_110', title: '广西'}
                , {field: 'num_118', title: '江苏'}
                , {field: 'num_129', title: '湖北'}
                , {field: 'num_132', title: '福建'}
                , {field: 'num_145', title: '浙江'}
                , {field: 'num_209', title: '台湾'}
                , {field: 'num_214', title: '山东'}
            ]]
        });
    });
}