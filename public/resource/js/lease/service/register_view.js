//加载时间控件
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
        , max: 0
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

//注册审核趋势
var lease_trend_chart = echarts.init(document.getElementById('box_lease_trend')); //获取装载数据表的容器
function load_lease_trend() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='register_day_agents']").val(),
            dateRange: $("#date1").val(),
            days: $("select[name='days1']").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/service/register_trend",
        success: function (res) {
            register_day_option = {
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
                    left: '2%',
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
                    name: '日期',
                    type: 'category',
                    boundaryGap: false,
                    data: []
                },
                yAxis: {
                    name: '数量',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: colorArr,
                series: []
            };
            if (res && res.code == 1) {
                register_day_option.series = res.data.series;
                register_day_option.xAxis.data = res.data.xAxis;
                register_day_option.legend.data = res.data.legend;
            }
            lease_trend_chart.clear();
            lease_trend_chart.setOption(register_day_option);//把echarts配置项启动
            setTimeout('lease_trend_chart.resize()', 10);
        }
    });
}

//注册审核趋势统计表格
function load_lease_statistics() {
    layui.use('table', function () {
        var date1 = $("#date2").val();
        var agentId = $("select[name='agentId2']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_total'
            , method: 'post'
            , url: adminurl + "/service/register_table"//数据接口
            , where: {_token: _token, agentId: agentId, dateRange: date1}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'date', title: '日期', fixed: 'left'}
                , {field: 'apply_num', title: '网点申请数'}
                , {field: 'crm_num', title: '网点录入数'}
                , {field: 'audited_num', title: '网点审核数'}
            ]]
        });
    });
}

load_lease_trend();
load_lease_statistics();

$(frames).resize(function() {
    lease_trend_chart.resize();
});


