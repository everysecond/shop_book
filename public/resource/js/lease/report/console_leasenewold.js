layui.use('laydate', function () {
    var laydate = layui.laydate;
    laydate.render({
        elem: '#datetime1'
        , range: true
    });

    laydate.render({
        elem: '#datetime2'
        , range: true
    });
});

layui.use('form', function () {
    var form = layui.form;
    form.on('select(demo13)', function (data) {
        if (data.value == 7) {
            $('#datetime2').css('display', 'block');
        } else {
            $('#datetime2').css('display', 'none');
        }
    })

    form.on('select(demo10)', function (data) {
        if (data.value == 7) {
            $('#datetime1').css('display', 'block');
        } else {
            $('#datetime1').css('display', 'none');
        }
    })
});




$("#search1").click(function () {
    load_time_hour();
});

load_time_hour();
//新老用户租赁数量对比
var lease_time_hour_chart = echarts.init(document.getElementById('box1')); //获取装载数据表的容器
function load_time_hour() {
    $.ajax({
        type: 'POST',
        data: {
            agentId: $('#test1 option:selected').val(),
            renewal_date: $("#datetime1").attr("value"),
            time_type: $('#tes1 option:selected').val(),
            type: 2,
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/newoldtotallist",
        success: function (res) {

            lease_hour_option = {
                title: {
                    // text: '租赁数量对比'
                },
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
                    left: '3%',
                    right: '8%',
                    bottom: '12%',
                    containLabel: true
                },
                // toolbox: {
                //     feature: {
                //         saveAsImage: {}
                //     },
                //     right: 15
                // },
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

                lease_hour_option.series = res.data.series;
                lease_hour_option.legend.data = res.data.linedays;
                lease_hour_option.xAxis.data = res.data.dayArr;
            }
            lease_time_hour_chart.clear();
            lease_time_hour_chart.setOption(lease_hour_option);//把echarts配置项启动
            setTimeout('lease_time_hour_chart.resize()', 10);
        }
    });
}
