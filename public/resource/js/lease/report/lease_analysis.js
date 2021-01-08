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
        elem: '#dateX'
        , type: 'date'
        , range: true
    });
    laydate.render({
        elem: '#dateCycle'
        , type: 'date'
        , range: true
    });
    laydate.render({
        elem: '#dateStatus'
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
    form.on('select(timeTypeX)', function (data) {
        if (data.value == -1) {
            $('#dateX').css('display', 'block');
        } else {
            $('#dateX').css('display', 'none');
        }
    });
    form.on('select(timeTypeCycle)', function (data) {
        if (data.value == -1) {
            $('#dateCycle').css('display', 'block');
        } else {
            $('#dateCycle').css('display', 'none');
        }
    });
    form.on('select(timeTypeStatus)', function (data) {
        if (data.value == -1) {
            $('#dateStatus').css('display', 'block');
        } else {
            $('#dateStatus').css('display', 'none');
        }
    });
});

// 租赁趋势图
var lease_times_chart = echarts.init(document.getElementById('box_lease_time')); //获取装载数据表的容器
function load_lease_time(){
    $.ajax({
        type: 'POST',
        data:{
            'agentId': $("select[name='agents1']").val(),
            dateRange: $("#date1").val(),
            days: $("select[name='days1']").val(),
            '_token':_token
        },
        dataType: 'json',
        url: adminurl + "/lease/trend",
        success:function(res){
            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:['续租数','到期数','续租金额']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: res.data.month,
                        axisPointer: {
                            type: 'shadow'
                        },
                        splitLine:{
                            show:false
                        },

                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '数量',
                        axisLabel: {
                            formatter: '{value}'
                        }
                    },
                    {
                        type: 'value',
                        name: '金额',
                        axisLabel: {
                            formatter: '{value}元'
                        }
                    }
                ],
                series: [
                    {
                        name:'租赁数',
                        type:'bar',
                        data:res.data.lease_num,
                    },
                    {
                        name:'租赁金额',
                        type:'line',
                        yAxisIndex: 1,
                        data:res.data.lease_amount,
                    }
                ]
            };
            lease_times_chart.setOption(option)//把echarts配置项启动
        },
        error:function(){
            layer.close(index);
            layer.msg("网络请求错误，稍后重试！");
            return false;
        }
    });
}
load_lease_time();

//租赁区域分布
var lease_area_chart = echarts.init(document.getElementById('lease_area_chart')); //获取装载数据表的容器
function load_lease_area(){
    $.ajax({
        type: 'POST',
        data:{
            dateRange: $("#dateX").val(),
            days: $("select[name='daysX']").val(),
            '_token':_token
        },
        dataType: 'json',
        url: adminurl + "/lease/area",
        success:function(res){
            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:['续租数','到期数','续租金额']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: res.data.province,
                        axisPointer: {
                            type: 'shadow'
                        },
                        splitLine:{
                            show:false
                        },
                        interval: 0
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '数量',
                        axisLabel: {
                            formatter: '{value}'
                        }
                    },
                    {
                        type: 'value',
                        name: '金额',
                        axisLabel: {
                            formatter: '{value}元'
                        }
                    }
                ],
                series: [
                    {
                        name:'租赁数',
                        type:'bar',
                        data:res.data.lease_num,
                    },
                    {
                        name:'租赁金额',
                        type:'line',
                        yAxisIndex: 1,
                        data:res.data.lease_amount,
                    }
                ]
            };
            lease_area_chart.setOption(option)//把echarts配置项启动
        },
        error:function(){
            layer.close(index);
            layer.msg("网络请求错误，稍后重试！");
            return false;
        }
    });
}
load_lease_area();

// 租赁周期分布
var lease_cycle_chart = echarts.init(document.getElementById('lease_cycle_chart')); //获取装载数据表的容器
function load_lease_cycle(){
    $.ajax({
        type: 'POST',
        data:{
            'agentId': $("select[name='agentsCycle']").val(),
            dateRange: $("#dateCycle").val(),
            days: $("select[name='daysCycle']").val(),
            '_token':_token
        },
        dataType: 'json',
        url: adminurl + "/lease/cycle",
        success:function(res){
            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:['续租数','到期数','续租金额']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: res.data.cycle,
                        axisPointer: {
                            type: 'shadow'
                        },
                        splitLine:{
                            show:false
                        },

                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '数量',
                        axisLabel: {
                            formatter: '{value}'
                        }
                    },
                    {
                        type: 'value',
                        name: '金额',
                        axisLabel: {
                            formatter: '{value}元'
                        }
                    }
                ],
                series: [
                    {
                        name:'租赁数',
                        type:'bar',
                        data:res.data.lease_num,
                    }
                ]
            };
            lease_cycle_chart.setOption(option)//把echarts配置项启动
        },
        error:function(){
            layer.close(index);
            layer.msg("网络请求错误，稍后重试！");
            return false;
        }
    });
}
load_lease_cycle();

//电池型号租赁统计表
function load_day_table() {
    layui.use('table', function () {
        var date1 = $("#date2").val();
        var agentId = $("select[name='agents2']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_battery'
            , method: 'post'
            , padding: 15
            , align: "right"
            , url: adminurl + "/lease/data/battery_table"//数据接口
            , where: {_token: _token, agentId: agentId, dateRange: date1}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'date', title: '日期', fixed: 'left',width:104}
                , {field: 'total', title: '租赁数'}
                , {field: 'model_one', title: '48V12A'}
                , {field: 'model_two', title: '48V20A'}
                , {field: 'model_three', title: '48V32A'}
                , {field: 'model_six', title: '48V45A'}
                , {field: 'model_four', title: '60V20A'}
                , {field: 'model_seven', title: '60V32A'}
                , {field: 'model_eight', title: '60V45A'}
                , {field: 'model_five', title: '72V20A'}
                , {field: 'model_nine', title: '72V32A'}
                , {field: 'other', title: '其它型号'}
            ]]
        });
    });
}

load_day_table();

$(frames).resize(function() {
    lease_times_chart.resize();
});