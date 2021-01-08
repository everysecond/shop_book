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



$("#search2").click(function(){
    load_broken();

});

function load_broken(){
    var renewal_date =  $("#test12").attr("value");
    var time_type =  $('#tes2 option:selected').val();
    var province_id =  $('#test13 option:selected').val();

    $.ajax({
        type: 'POST',
        data:{'renewal_date':renewal_date,'province_id':province_id,'time_type':time_type,'_token':_token},
        dataType: 'json',
        url: adminurl + "/lease/renewal/broken",
        success:function(res){
            var myChart = echarts.init(document.getElementById('box2')) //获取装载数据表的容器

            option = {
                color: ['#3398DB'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                // title : {
                //     text: '续租趋势',
                //     x:'center'
                //
                // },
                grid: {
                    top: '5%',
                    left: '3%',
                    right: '8%',
                    bottom: '12%',
                    containLabel: true
                },
                xAxis: {
                    splitLine:{

                        show:false
                    },
                    type: 'category',
                    data: res.data.renewal_date
                },
                yAxis: {
                    splitLine:{
                        lineStyle:{
                            type:'dashed'    //设置网格线类型 dotted：虚线   solid:实线
                        },
                        show:true
                    },

                    type: 'value'
                },
                series: [{
                    symbol:'circle',
                    symbolSize:2,
                    data: res.data.renewal_num,
                    type: 'line'
                }]
            };
            myChart.setOption(option)//把echarts配置项启动

        },
        error:function(){
            layer.close(index);
            layer.msg("网络请求错误，稍后重试！");
            return false;
        }
    });
}
load_broken();




