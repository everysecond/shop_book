layui.use(['element', 'jquery', 'form', 'table', 'laydate'], function () {
    var element = layui.element;
    var form = layui.form;
    $ = layui.jquery;
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


});
// 租赁渠道
load_channel_data();
var lease_times_chart = echarts.init(document.getElementById('box_lease_time')); //获取装载数据表的容器
function load_channel_data() {

    var datetime = $("#datetimeclick").val();
    var province_id = $('#area option:selected').val();
    var time_type = $('#clicktime option:selected').val();

    $.ajax({
        type: 'POST',
        data: {'_token': _token, datetime: datetime, time_type: time_type, area: province_id},
        dataType: 'json',
        url: adminurl + "/lease/channeldata",
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
                            name: '渠道转化人数',
                            type: 'bar',
                            barWidth: '40%',
                            data: res.data.areadata
                        }
                    ]
                };
                lease_times_chart.setOption(times_option);//把echarts配置项启动
            } else {
                layer.msg('暂无数据')
            }
        }
    });
}


load_table();

//每小时转化统计表
function load_table() {
    layui.use('table', function () {
        var table = layui.table;
        var datetimetwo = $("#datetimeclicktwo").val();
        var province_id = $('#areatwo option:selected').val();
        var channelname = $('#clicktimetwo option:selected').val();
        //第一个实例
        table.render({
            elem: '#tabledata'
            , method: 'post'
            , align: "right"
            , url: adminurl + "/lease/channeldatalist"//数据接口
            , where: {_token: _token, datetime: datetimetwo, channelname: channelname, province_id: province_id}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'process_date', title: '日期', width: 150, fixed: 'left'}
                , {field: 'systemtype', title: '渠道', width: 150}
                , {field: 'register_num', title: '注册成功用户数'}
                , {field: 'login_num', title: '登陆成功用户数'}
                , {field: 'index_num', title: '到达首页用户数'}
                , {field: 'scan_num', title: '发起扫码用户数'}
                , {field: 'detail_num', title: '到达租赁详情页用户数'}
                , {field: 'period_num', title: '选择租赁周期用户数'}
                , {field: 'deduction_num', title: '旧电池抵扣用户数'}
                , {field: 'submit_lease_num', title: '提交租赁单用户数'}
                , {field: 'business_num', title: '我的租赁页面用户数'}
                , {field: 'mylease_num', title: '我的租赁页面用户数'}
                , {field: 'topay_num', title: '待支付页面用户数'}
                , {field: 'dopay_num', title: '发起支付用户数'}
                , {field: 'pay_num', title: '支付成功页面用户数'}
                , {field: 'divpay_num', title: '租赁-登录比'}
            ]]
        });
    })

}

$(frames).resize(function() {
    lease_times_chart.resize();
});

