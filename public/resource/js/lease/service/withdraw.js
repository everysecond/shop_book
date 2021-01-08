layui.use('laydate', function(){
    var laydate = layui.laydate;

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

layui.use('form', function(){
    var form = layui.form;

    form.on('select(demo)',function(data){
        if (data.value == 7) {
            $('#test9').css('display','block');
        }else{
            $('#test9').css('display','none');
        }
    })

    form.on('select(demo12)',function(data){
        if (data.value == 7) {
            $('#test12').css('display','block');
        }else{
            $('#test12').css('display','none');
        }
    })
});



$("#search").click(function(){
    load_table();
});


//各区域网点收益统计
function load_table() {

    layui.use('table', function () {
        var renewal_date =  $("#test10").attr("value");
        var province_id =  $('#test option:selected').val();

        var table = layui.table;
        var fieldArr = [{field: 'date', title: '日期', fixed: 'left'}];
        provinceArr.forEach(function (item) {
            fieldArr.push({field: item, title: item})
        });

        //第一个实例
        table.render({
            elem: '#demo'
            , method: 'post'
            , padding: 15
            , align: "right"
            , url: adminurl + "/service/withdraw_table"//数据接口
            ,where: {_token: _token, province_id: province_id, dateRange:renewal_date }
            , page: true //开启分页
            , cols: [fieldArr]
        });
    });
}

load_table();



$("#search2").click(function(){
    load_broken();

});
var lease_times_chart = echarts.init(document.getElementById('box2')) //获取装载数据表的容器
function load_broken(){
    var renewal_date =  $("#test12").attr("value");
    var time_type =  $('#tes2 option:selected').val();
    var province_id =  $('#test13 option:selected').val();

    $.ajax({
        type: 'POST',
        data:{'renewal_date':renewal_date,'province_id':province_id,'time_type':time_type,'_token':_token},
        dataType: 'json',
        url: adminurl + "/service/withdraw_area",
        success:function(res){


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
                    right: '8%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {

                        name:'提现金额',
                        type: 'category',
                        data: ["0-50", "51-100", "101-150", "151-200", "201-250", "251-300", "301-350", "351-400", "401-450", "451-500"],
                        axisTick: {
                            alignWithLabel: true
                        }

                    }
                ],
                yAxis: [
                    {
                        name:'提现数',
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '提现数',
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

        },
        error:function(){
            layer.close(index);
            layer.msg("网络请求错误，稍后重试！");
            return false;
        }
    });
}
load_broken();






$("#search1").click(function(){
    load();

});

function load(){


    var province_id =  $('#test11 option:selected').val();

    $.ajax({
        type: 'POST',
        data:{'province_id':province_id,'_token':_token},
        dataType: 'json',
        url: adminurl + "/service/withdraw_rate",
        success:function(res){
            var myChart = echarts.init(document.getElementById('box1'));//获取装载数据表的容器

                option = {
                    tooltip: {
                        trigger: 'item',
                        formatter: "{a} <br/>{b}: {c} ({d}%)"
                    },
                    legend: {
                        orient: 'vertical',
                        x: 'left',
                        data:res.data.ageArr
                    },

                    series: [
                        {

                            type:'pie',
                            radius: ['50%', '70%'],
                            avoidLabelOverlap: false,
                            label: {
                                normal: {
                                    show: false,
                                    position: 'center'
                                },
                                emphasis: {
                                    show: true,
                                    textStyle: {
                                        fontSize: '30',
                                        fontWeight: 'bold'
                                    }
                                }
                            },
                            labelLine: {
                                normal: {
                                    show: false
                                }
                            },
                            data:res.data.ageData
                        }
                    ]
                };



            myChart.setOption(option)//把echarts配置项启动

        },

    });
}
load();

$(window).resize(function() {
    lease_times_chart.resize();


});