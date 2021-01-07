//自定义颜色
var colorNormal = {
    color: function (params) {
        var colorList = [
            '#fbd437', '#3aa1ff', '#36cbcb', '#4ecb73',
            '#87f7cf', '#27727B', '#72ccff', '#f7c5a0',
            '#0098d9', '#2b821d', '#e87c25', '#e01f54'
        ];
        return colorList[params.dataIndex]
    }
};

//绑定下拉框联动事件
layui.use(['form', 'table', 'laydate'], function () {
    var element = layui.element;
    var form = layui.form;
    $ = layui.jquery;
    var table = layui.table;
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
        elem: '#date1'
    });
    laydate.render({
        elem: '#date2'
    });
    laydate.render({
        elem: '#date3'
        , type: 'date'
        , range: true
        , max: 0
    });

    laydate.render({
        elem: '#todatetime' //指定元素
    });
    form.on('select(timeType1)', function (data) {
        if (data.value == -1) {
            $('#date3').css('display', 'block');
        } else {
            $('#date3').css('display', 'none');
        }
    });

});

//添加每小时启动对比
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

//添加累计每小时启动对比
function addDay2() {
    var day = $("#hour2").val();
    if (day != "" && dayArr2.indexOf(day) == -1) {
        dayArr2.push(day);
        load_register_total();
        $(".clearBtn2").removeClass("hidden");
    }
}

//清空额外累计每小时对比
function clearDays2() {
    $("#hour2").val("");
    if (dayArr2 != []) {
        dayArr2 = [];
        $(".clearBtn2").addClass("hidden");
        load_register_total();
    }
}

//每小时转化数对比
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
        url: adminurl + "/lease/registertimehour",
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
                color: [
                    '#fbd437', '#3aa1ff', '#36cbcb', '#4ecb73', '#27727B'
                ],
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


//累计每小时转化数
var dayArr2 = [];
var box_register_total_chart = echarts.init(document.getElementById('box_register_total')); //获取装载数据表的容器
function load_register_total() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $("select[name='register_hour_agents2']").val(),
            dayArr: dayArr2,
            type: 2,
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/registertimehours",
        success: function (res) {
            box_register_total_option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    top: 'bottom',
                    left: 'center',
                    data: []
                },
                grid: {
                    top: '5%',
                    left: '0%',
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
                    type: 'category',
                    boundaryGap: false,
                    data: []
                },
                yAxis: {
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
                box_register_total_option.series = res.data.series;
                box_register_total_option.legend.data = res.data.days;
                box_register_total_option.xAxis.data = res.data.hourArr;
            }
            box_register_total_chart.clear();
            box_register_total_chart.setOption(box_register_total_option);//把echarts配置项启动
            setTimeout('box_register_total_chart.resize()', 10);
        }
    });
}


// 转化统计图 柱状图
var load_register_total3_cum_chart = echarts.init(document.getElementById('load_register_total3_cum')); //获取装载数据表的容器
function load_register_total3(datetime, area) {
    var datetime = $("#hour3").val();
    var agentId = $("select[name='register_hour_agents3']").val()
    $.ajax({
        type: 'POST',
        data: {'_token': _token, dayArr: datetime, agentId: agentId},
        dataType: 'json',
        url: adminurl + "/lease/registertimecum",
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
                    xAxis: [{
                        type: 'category',
                        data: res.data.headname,
                        axisTick: {
                            alignWithLabel: true
                        }

                    }],
                    yAxis: [
                        {
                            type: 'value'
                        }
                    ],
                    series: [
                        {
                            name: '人数',
                            type: 'bar',
                            barWidth: '50%',
                            data: res.data.datanum
                        }
                    ]
                };
                load_register_total3_cum_chart.setOption(times_option);//把echarts配置项启动
                setTimeout('load_register_total3_cum_chart.resize()', 10);
            } else {

            }

        }
    });
}


//每小时转化统计表
function load_hour_table() {
    layui.use('table', function () {
        var datetime = $('#date1').val();
        var area = $('#agentId1 option:selected').val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_day'
            , method: 'post'
            , align: "right"
            , url: adminurl + "/lease/registertimehourlist"//数据接口
            , where: {_token: _token, datetime: datetime, area: area}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'insert_hour', title: '时间(1代表0~1点)', width: 150, fixed: 'left'}
                , {field: 'register_nums', title: '注册成功用户数'}
                , {field: 'login_nums', title: '登陆成功用户数'}
                , {field: 'index_num', title: '到达首页用户数'}
                , {field: 'scan_num', title: '发起扫码用户数'}
                , {field: 'detail_num', title: '到达租赁详情页用户数'}
                , {field: 'period_num', title: '选择租赁周期用户数'}
                , {field: 'deduction_num', title: '旧电池抵扣用户数'}
                , {field: 'submit_lease_num', title: '提交租赁单用户数'}
                , {field: 'mylease_num', title: '我的租赁页面用户数'}
                , {field: 'business_num', title: '商家确认扫码用户数'}
                , {field: 'topay_num', title: '待支付页面用户数'}
                , {field: 'dopay_num', title: '发起支付用户数'}
                , {field: 'pay_nums', title: '支付成功页面用户数'}
                , {field: 'divpay_num', title: '租赁-注册比'}
            ]]
        });
    });
}

//累计每小时转化统计表
function load_hours_table() {
    layui.use('table', function () {
        var datetime = $("#date2").val();
        var agentId = $("select[name='agentId2']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_total'
            , method: 'post'
            , align: "right"
            , url: adminurl + "/lease/registertimehourslist"//数据接口
            , where: {_token: _token, datetime: datetime, area: agentId}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'insert_hour', title: '时间(1代表0~1点)', width: 150, fixed: 'left'}
                , {field: 'register_nums', title: '注册成功用户数'}
                , {field: 'login_nums', title: '登陆成功用户数'}
                , {field: 'index_num', title: '到达首页用户数'}
                , {field: 'scan_num', title: '发起扫码用户数'}
                , {field: 'detail_num', title: '到达租赁详情页用户数'}
                , {field: 'period_num', title: '选择租赁周期用户数'}
                , {field: 'deduction_num', title: '旧电池抵扣用户数'}
                , {field: 'submit_lease_num', title: '提交租赁单用户数'}
                , {field: 'mylease_num', title: '我的租赁页面用户数'}
                , {field: 'business_num', title: '商家确认扫码用户数'}
                , {field: 'topay_num', title: '待支付页面用户数'}
                , {field: 'dopay_num', title: '发起支付用户数'}
                , {field: 'pay_nums', title: '支付成功页面用户数'}
                , {field: 'divpay_num', title: '租赁-注册比'}
            ]]
        });
    });
}


// 每日转化统计表
function load_day_total_table() {
    layui.use('table', function () {
        var datetime = $("#date3").val();
        var agentId = $("select[name='total_agentId3']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_total_day'
            , method: 'post'
            , align: "right"
            , url: adminurl + "/lease/registertimedayslist"//数据接口
            , where: {_token: _token, datetime: datetime, area: agentId}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'process_date', title: '时间', width: 150, fixed: 'left'}
                , {field: 'register_num', title: '注册成功用户数'}
                , {field: 'login_nums', title: '登陆成功用户数'}
                , {field: 'index_num', title: '到达首页用户数'}
                , {field: 'scan_num', title: '发起扫码用户数'}
                , {field: 'detail_num', title: '到达租赁详情页用户数'}
                , {field: 'period_num', title: '选择租赁周期用户数'}
                , {field: 'deduction_num', title: '旧电池抵扣用户数'}
                , {field: 'submit_lease_num', title: '提交租赁单用户数'}
                , {field: 'mylease_num', title: '我的租赁页面用户数'}
                , {field: 'business_num', title: '商家确认扫码用户数'}
                , {field: 'topay_num', title: '待支付页面用户数'}
                , {field: 'dopay_num', title: '发起支付用户数'}
                , {field: 'pay_nums', title: '支付成功页面用户数'}
                , {field: 'divpay_num', title: '租赁-注册比'}
            ]]
        });

    });
}

//标签页一数据加载
function load_tab_1() {
    load_time_hour();
    load_hour_table();
}

//标签页二数据加载
function load_tab_2() {
    load_register_total();
    load_hours_table();
}

//标签页三数据加载
function load_tab_3() {
    load_register_total3();
    load_day_total_table();
}

//默认加载标签页一
load_tab_1();

$(frames).resize(function() {
    lease_time_hour_chart.resize();
});




