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
        elem: '#test101'

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

    form.on('select(demo13)',function(data){
        if (data.value == 7) {
            $('#test101').css('display','block');
        }else{
            $('#test101').css('display','none');
        }
    })
});



$("#search").click(function(){
    load_Table();
});



function load_Table(){
    layui.use('table', function(){
        var renewal_date =  $("#test10").attr("value");
        var province_id =  $('#test option:selected').val();

        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#demo'
            ,method:'post'

            ,align:"right"
            ,url: adminurl + "/lease/insurance/search"//数据接口
            ,where: {_token: _token, province_id: province_id, renewal_date:renewal_date}
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'rent_date', title: '日期', fixed: 'left'}
                ,{field: 'rent_num', title: '当日租赁数'}
                ,{field: 'rent_insure_num', title: '租赁-投保数'}
                ,{field: 'renewal_num', title: '当日续租数'}
                ,{field: 'renewal_insure_num', title: '续租-投保数'}
                ,{field: 'insure_num', title: '当日投保数'}
                ,{field: 'uninsured_num', title: '当日未投保数'}
                ,{field: 'insure_rent', title: '当日投保占比'}


            ]]
        });

    });

}
load_Table();





$("#search1").click(function(){
    load();

});

function load(){
    var renewal_date =  $("#test9").attr("value");
    var time_type =  $('#tes1 option:selected').val();
    var province_id =  $('#test11 option:selected').val();

    $.ajax({
        type: 'POST',
        data:{'renewal_date':renewal_date,'province_id':province_id,'time_type':time_type,'_token':_token},
        dataType: 'json',
        url: adminurl + "/lease/insurance/histogram",
        success:function(res){

            var myChart = echarts.init(document.getElementById('box1'));//获取装载数据表的容器
            if ((res.data.insure_num == null && res.data.uninsured_num == null)||(res.data.insure_num == 0 && res.data.uninsured_num == 0) ) {
                option = {

                    series : [
                        {
                            noDataLoadingOption :{
                                text: '暂无数据',
                                effect:'bubble',
                                effectOption : {
                                    effect: {
                                        n: 0 //气泡个数为0
                                    }
                                },
                                textStyle: {
                                    fontSize: 200,
                                    fontWeight: 'bold'
                                }
                            }

                        }
                    ]
                };

            }else{
                option = {
                    title : {
                        text: '投保占比',
                        // subtext: '纯属虚构',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        // orient: 'vertical',
                        y: 'bottom',
                        data: ['已投保','未投保']
                    },
                    series : [
                        {
                            name: '投保占比',
                            type: 'pie',
                            radius : '55%',
                            center: ['50%', '50%'],
                            data:[
                                {value:res.data.insure_num, name:'已投保'},
                                {value:res.data.uninsured_num, name:'未投保'},

                            ],
                            itemStyle: {
                                emphasis: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                },

                            },
                            color: ['#fbd437', '#3aa1ff', '#36cbcb', '#4ecb73', '#CB4015'],
                        }
                    ]
                };
            }

            myChart.setOption(option)//把echarts配置项启动
            // myChart.hideLoading();
        },
        error:function(){
            layer.close(index);
            layer.msg("网络请求错误，稍后重试！");
            return false;
        }
    });
}
load();



$("#search2").click(function(){
    load_broken();
});


load_broken();
function load_broken(){
    var time_type =  $('#tes31 option:selected').val();
    var renewal_date =  $("#test101").attr("value");
    var province_id =  $('#test111 option:selected').val();
    $.ajax({
        type: 'POST',
        data:{'renewal_date':renewal_date,'province_id':province_id,'time_type':time_type,'_token':_token},
        dataType: 'json',
        url: adminurl + "/lease/insurance/broken",
        success:function(res){

            var myChart = echarts.init(document.getElementById('box_term')) //获取装载数据表的容器

            option = {
                color: ['#3398DB'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                title : {
                    text: '投保趋势',
                    x:'center'

                },
                xAxis: {
                    axisLine: {
                        lineStyle: {
                            color: '#000', // 颜色
                            width: 1 // 粗细
                        }
                    },
                    name:"日期",
                    splitLine:{

                        show:false
                    },
                    type: 'category',
                    data: res.data.renewal_date
                },
                yAxis: {
                    axisLine: {
                        lineStyle: {
                            color: '#000', // 颜色
                            width: 1 // 粗细
                        }
                    },
                    name:"投保数",
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