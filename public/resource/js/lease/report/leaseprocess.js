// var myChart = echarts.init(document.getElementById('box2')) //获取装载数据的容器
// var cummyChart = echarts.init(document.getElementById('cumbox2')) //获取装载数据的容器
var totalmyChart = echarts.init(document.getElementById('totalbox2')) //获取装载数据的容器
layui.use(['element', 'jquery', 'laypage', 'laydate', 'table'], function () {
    $ = layui.jquery;
    var laydate = layui.laydate;
    //执行一个laydate实例
    laydate.render({
        elem: '#todatetime' //指定元素
    });
    //执行第二个laydate实例
    laydate.render({
        elem: '#cumdatetime' //指定元素
    });

    //执行第三个laydate实例
    laydate.render({
        elem: '#totaltime' //指定元素
    });

    //执行第四个laydate实例
    laydate.render({
        elem: '#totaldatetime' //指定元素
        , range: true
    });
});

function load() {
    var datetime = $("#datetime").val();
    var area = $('#area option:selected').val();
    $.ajax({
        type: 'post',
        data: {'_token': _token, 'datetime': datetime, 'area': area},
        dataType: 'json',
        url: adminurl + "/lease/leasefunnellist",
        success: function (res) {
            if (res.code == 0) {
                option = {
                    title: {
                        text: '转化漏斗',
                        subtext: '租赁流程转化'
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c}%"
                    },
                    toolbox: {
                        feature: {
                            dataView: {readOnly: false},
                            restore: {},
                            saveAsImage: {}
                        }
                    },
                    legend: {
                        data: ['到达登录页', '到达首页', '到达扫码页', '到达租赁详情', '选择租赁周期', '选择旧电池折扣', '提交租赁单', '商家确认', '租赁待支付', '支付租赁']
                    },
                    calculable: true,
                    series: [
                        {
                            name: '漏斗图',
                            type: 'funnel',
                            left: '10%',
                            top: 60,
                            //x2: 80,
                            bottom: 60,
                            width: '80%',
                            // height: {totalHeight} - y - y2,
                            min: 0,
                            max: res.data[1],
                            minSize: '0%',
                            maxSize: '100%',
                            sort: 'descending',
                            gap: 2,
                            label: {
                                show: true,
                                position: 'inside'
                            },
                            labelLine: {
                                length: 5,
                                lineStyle: {
                                    width: 1,
                                    type: 'solid'
                                }
                            },
                            itemStyle: {
                                borderColor: '#fff',
                                borderWidth: 1
                            },
                            color: ['#49abfa', '#02c9ed', '#faca38', '#11c0ec', '#0edcda', '#ffb07b', '#72abf7', '#28beec']
                            ,
                            emphasis: {
                                label: {
                                    fontSize: 20
                                }
                            },

                            data: res.data[0]
                        }
                    ]
                };
                myChart.setOption(option)//把echarts配置项启动
                setTimeout('myChart.resize()', 10);
            } else if (res.code == 1002) {
                layer.msg(res.msg);
                setTimeout(function () {
                    location.href = adminurl + "/login";
                }, 200)
            } else {
                layer.msg(res.msg);
                return false;
            }
        },
        error: function () {
            layer.msg("网络请求错误，稍后重试！");
            return false;
        }
    });
}

function cumload() {
    var datetime = $("#cumtime").val();
    var area = $('#cumarea option:selected').val();
    $.ajax({
        type: 'post',
        data: {'_token': _token, datetime: datetime, area: area},
        dataType: 'json',
        url: adminurl + "/lease/leasecumfunnellist",
        success: function (res) {
            if (res.code == 0) {
                option = {
                    title: {
                        text: '转化漏斗',
                        subtext: '租赁流程转化'
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c}%"
                    },
                    toolbox: {
                        feature: {
                            dataView: {readOnly: false},
                            restore: {},
                            saveAsImage: {}
                        }
                    },
                    legend: {
                        data: ['到达登录页', '到达首页', '到达扫码页', '到达租赁详情', '选择租赁周期', '选择旧电池折扣', '提交租赁单', '商家确认', '租赁待支付', '支付租赁']
                    },
                    calculable: true,
                    series: [
                        {
                            name: '漏斗图',
                            type: 'funnel',
                            left: '10%',
                            top: 60,
                            //x2: 80,
                            bottom: 60,
                            width: '80%',
                            // height: {totalHeight} - y - y2,
                            min: 0,
                            max: res.data[1],
                            minSize: '0%',
                            maxSize: '100%',
                            sort: 'descending',
                            gap: 2,
                            label: {
                                show: true,
                                position: 'inside'
                            },
                            labelLine: {
                                length: 5,
                                lineStyle: {
                                    width: 1,
                                    type: 'solid'
                                }
                            },
                            itemStyle: {
                                borderColor: '#fff',
                                borderWidth: 1
                            },
                            color: ['#49abfa', '#02c9ed', '#faca38', '#11c0ec', '#0edcda', '#ffb07b', '#72abf7', '#28beec']
                            ,
                            emphasis: {
                                label: {
                                    fontSize: 20
                                }
                            },

                            data: res.data[0]
                        }
                    ]
                };
                cummyChart.setOption(option)//把echarts配置项启动
                setTimeout('cummyChart.resize()', 10);
            } else if (res.code == 1002) {
                layer.msg(res.msg);
                setTimeout(function () {
                    location.href = adminurl + "/login";
                }, 200)
            } else {
                layer.msg(res.msg);
                return false;
            }
        },
        error: function () {
            // layer.close(index);
            layer.msg("网络请求错误，稍后重试！");
            return false;
        }
    });
}

function totalload() {

    var datetime = $("#totaltime").val();
    var area = $('#totalarea option:selected').val();

    $.ajax({
        type: 'post',
        data: {'_token': _token, 'datetime': datetime, 'area': area},
        dataType: 'json',
        url: adminurl + "/lease/leasetotalfunnellist",
        success: function (res) {
            if (res.code == 0) {
                option = {
                    title: {
                        text: '转化漏斗',
                        subtext: '租赁流程转化'
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c}%"
                    },
                    toolbox: {
                        feature: {
                            dataView: {readOnly: false},
                            restore: {},
                            saveAsImage: {}
                        }
                    },
                    legend: {
                        data: ['到达登录页', '到达首页', '到达扫码页', '到达租赁详情', '选择租赁周期', '选择旧电池折扣', '提交租赁单', '商家确认', '租赁待支付', '支付租赁']
                    },
                    calculable: true,
                    series: [
                        {
                            name: '漏斗图',
                            type: 'funnel',
                            left: '20%',
                            top: 60,
                            //x2: 80,
                            bottom: 60,
                            width: '80%',
                            // height: {totalHeight} - y - y2,
                            min: 0,
                            max: res.data[1],
                            minSize: '0%',
                            maxSize: '100%',
                            sort: 'descending',
                            gap: 2,
                            label: {
                                show: true,
                                position: 'inside'
                            },
                            labelLine: {
                                length: 5,
                                lineStyle: {
                                    width: 1,
                                    type: 'solid'
                                }
                            },
                            itemStyle: {
                                borderColor: '#fff',
                                borderWidth: 1
                            },
                            color: ['#49abfa', '#02c9ed', '#faca38', '#11c0ec', '#0edcda', '#ffb07b', '#72abf7', '#28beec']
                            ,
                            emphasis: {
                                label: {
                                    fontSize: 20
                                }
                            },

                            data: res.data[0]
                        }
                    ]
                };
                totalmyChart.setOption(option)//把echarts配置项启动
                setTimeout('totalmyChart.resize()', 10);
            } else if (res.code == 1002) {
                layer.msg(res.msg);
                setTimeout(function () {
                    location.href = adminurl + "/login";
                }, 200)
            } else {
                layer.msg(res.msg);
                return false;
            }
        },
        error: function () {
            // layer.close(index);
            layer.msg("网络请求错误，稍后重试！");
            return false;
        }
    });
}

//每小时转化统计表
function load_hour_table() {
    layui.use('table', function () {
        var datetime = $('#todatetime').val();
        var toarea = $('#toarea option:selected').val();
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
            , url: adminurl + "/lease/leaseprolist"//数据接口
            , where: {_token: _token, toarea: toarea, todatetime: datetime}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'insert_hour', title: '时间(1代表0点-1点之间)', width: 180, fixed: 'left'}
                , {field: 'login_nums', title: '登陆用户数'}
                , {field: 'index_num', title: '到达首页用户数'}
                , {field: 'scan_num', title: '发起扫码用户数'}
                , {field: 'detail_num', title: '到达租赁详情页用户数'}
                , {field: 'period_num', title: '选择租赁周期用户数'}
                , {field: 'deduction_num', title: '旧电池抵扣用户数'}
                , {field: 'submit_lease_num', title: '提交租赁单用户数'}
                , {field: 'business_num', title: '商家确认扫码用户数'}
                , {field: 'topay_num', title: '待支付页面用户数'}
                , {field: 'dopay_num', title: '发起支付用户数'}
                , {field: 'pay_nums', title: '支付成功页面用户数'}
                , {field: 'divpay_num', title: '租赁-登录比'}

            ]]
        });
    });
}

//累计每小时转化统计表
function load_day_table() {
    layui.use('table', function () {
        var datetime = $('#cumdatetime').val();
        var toarea = $('#cumtoarea option:selected').val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#cumtabledata'
            , method: 'post'
            , text: {
                none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
            }
            , skin: 'line' //行边框风格
            , even: true //开启隔行背景
            , size: 'sm' //小尺寸的表格
            , url: adminurl + "/lease/leaseprocumlist"//数据接口
            , where: {_token: _token, toarea: toarea, todatetime: datetime}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'insert_hour', title: '时间(2代表0点-2点之间)', width: 180, fixed: 'left'}
                , {field: 'login_num', title: '登陆用户数'}
                , {field: 'index_num', title: '到达首页数'}
                , {field: 'scan_num', title: '到达扫码页'}
                , {field: 'detail_num', title: '到达租赁详情页'}
                , {field: 'period_num', title: '到达选择租赁周期'}
                , {field: 'deduction_num', title: '到达旧电池抵扣'}
                , {field: 'submit_lease_num', title: '到达提交租赁'}
                , {field: 'business_num', title: '到达商家确认'}
                , {field: 'topay_num', title: '到达租赁待支付'}
                , {field: 'pay_num', title: '到达支付租赁'}
            ]]
        });
    });
}

//转化统计表
function load_day_total_table() {
    layui.use('table', function () {
        var datetime = $('#totaldatetime').val();
        var toarea = $('#totaltoarea option:selected').val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#totaltabledata'
            , method: 'post'
            , text: {
                none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
            }
            , skin: 'line' //行边框风格
            , even: true //开启隔行背景
            , size: 'sm' //小尺寸的表格
            , url: adminurl + "/lease/leaseprototallist"//数据接口
            , where: {_token: _token, area: toarea, datetime: datetime}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'process_date', title: '日期', width: 180, fixed: 'left'}
                , {field: 'login_nums', title: '登陆用户数'}
                , {field: 'index_num', title: '到达首页用户数'}
                , {field: 'scan_num', title: '发起扫码用户数'}
                , {field: 'detail_num', title: '到达租赁详情页用户数'}
                , {field: 'period_num', title: '选择租赁周期用户数'}
                , {field: 'deduction_num', title: '旧电池抵扣用户数'}
                , {field: 'submit_lease_num', title: '提交租赁单用户数'}
                , {field: 'business_num', title: '商家确认扫码用户数'}
                , {field: 'topay_num', title: '待支付页面用户数'}
                , {field: 'dopay_num', title: '发起支付用户数'}
                , {field: 'pay_nums', title: '支付成功页面用户数'}
                , {field: 'divpay_num', title: '租赁-登录比'}

            ]]
        });
    });
}


//标签页一数据加载
// function load_tab_1() {
//     load();  //初始化漏斗图
//     load_hour_table();
// }
//
// //标签页二数据加载
// function load_tab_2() {
//     cumload();
//     load_day_table();
// }

//标签页三数据加载
function load_tab_3() {
    totalload();
    load_day_total_table();
}

//默认加载标签页一
// load_tab_1();
load_tab_3();

$(frames).resize(function() {
    totalmyChart.resize();
});
