load();
var mychart;
function load() {
    var datetime = $("#datetimeclick").val();
    var time_type = $('#clicktime option:selected').val();
    var province_id = $('#area option:selected').val();

    $.ajax({
        type: 'POST',
        data: {'datetime': datetime, 'province_id': province_id, 'time_type': time_type, '_token': _token},
        dataType: 'json',
        url: adminurl + "/lease/maturitydata",
        success: function (res) {
            myChart = echarts.init(document.getElementById('box'));//获取装载数据表的容器
            option = {
                title: {
                    text: '续租类型占比',
                    // subtext: '纯属虚构',
                    x: 'center'
                },
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    // orient: 'vertical',
                    y: 'bottom',
                    data: ['到期续租数', '到期退租数', '到期30天未处理']
                },
                series: [
                    {
                        name: '',
                        type: 'pie',
                        radius: '55%',
                        center: ['50%', '50%'],
                        data: [
                            {value: res.data.renewal, name: '到期续租数'},
                            {value: res.data.retirenum, name: '到期退租数'},
                            {value: res.data.other30num, name: '到期30天未处理'},
                        ],
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            },
                            normal:{
                                label:{
                                    show: true,
                                    formatter: '{b}:{c}({d}%)'
                                },
                                labelLine :{show:true}
                            }

                        },
                        color: ['#fbd437', '#3aa1ff', '#36cbcb', '#4ecb73', '#CB4015'],
                    }
                ]
            };
            myChart.setOption(option)//把echarts配置项启动
        },
        error: function () {
            layer.msg("网络请求错误，稍后重试！");
            return false;
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
});

load_table();

//每小时转化统计表
function load_table() {
    layui.use('table', function () {
        var datetime = $("#datetimeclicktwo").attr("value");
        var province_id = $('#areatwo option:selected').val();
        var table = layui.table;
        table.render({
            elem: '#tabledata'
            , method: 'post'
            , url: adminurl + "/lease/maturitydatalist"//数据接口
            , where: {_token: _token, province_id: province_id, datetime: datetime}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'contract_expired_at', title: '日期', width: 120, fixed: 'left'}
                , {field: 'id_counts', title: '到期数', width: 150,}
                , {field: 'renewal', title: '到期续租数'}
                , {field: 'retirenum', title: '到期退租数'}
                , {field: 'other30num', title: '到期30天未处理数'}

            ]]
        });
    });
}


$(frames).resize(function() {
    myChart.resize();
});




