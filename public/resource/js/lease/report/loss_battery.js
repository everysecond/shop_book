layui.use('laydate', function(){
    var laydate = layui.laydate;

    laydate.render({
        elem: '#test9'

        ,range: true
    });

    laydate.render({
        elem: '#test101'

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

    form.on('select(demo13)',function(data){
        if (data.value == 7) {
            $('#test10').css('display','block');
        }else{
            $('#test10').css('display','none');
        }
    })
});



    $("#search").click(function(){
        load_Table();
    });


    load_Table();
    function load_Table(){
            var time_type =  $('#tes3 option:selected').val();
            var renewal_date =  $("#test10").attr("value");
            var province_id =  $('#test option:selected').val();
        $.ajax({
            type: 'POST',
            data:{'renewal_date':renewal_date,'province_id':province_id,'time_type':time_type,'_token':_token},
            dataType: 'json',
            url: adminurl + "/lease/loss_battery/search",
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
                        text: '报失数统计',
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
                        data: res.data.rent_date
                    },
                    yAxis: {
                        axisLine: {
                            lineStyle: {
                                color: '#000', // 颜色
                                width: 1 // 粗细
                            }
                        },
                        name:"报失数",
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
                        data: res.data.report_loss_num,
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
        url: adminurl + "/lease/loss_battery/histogram",
        success:function(res){

            var myChart = echarts.init(document.getElementById('box2'));//获取装载数据表的容器
            if ((res.data.rent_num == null && res.data.report_loss_num == null)||
                (res.data.rent_num == 0 && res.data.report_loss_num == 0)) {
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
                        text: '报失租赁比',
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
                        data: ['租赁数','报失数']
                    },
                    series : [
                        {
                            name: '',
                            type: 'pie',
                            radius : '55%',
                            center: ['50%', '50%'],
                            data:[
                                {value:res.data.rent_num, name:'租赁数'},
                                {value:res.data.report_loss_num, name:'报失数'},
                                // {value:1548, name:'搜索引擎'}
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
    var renewal_date =  $("#test9").attr("value");
    var time_type =  $('#tes1 option:selected').val();
    var province_id =  $('#test11 option:selected').val();

    $.ajax({
        type: 'POST',
        data:{'renewal_date':renewal_date,'province_id':province_id,'time_type':time_type,'_token':_token},
        dataType: 'json',
        url: adminurl + "/lease/loss_battery/broken",
        success:function(res){

            var myChart = echarts.init(document.getElementById('box1'));//获取装载数据表的容器
            if ((res.data.report_loss_num == null && res.data.insure_num == null)||
                (res.data.report_loss_num == 0 && res.data.insure_num == 0)) {
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
                        text: '报失投保比',
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
                        data: ['报失数','投保数']
                    },
                    series : [
                        {
                            name: '',
                            type: 'pie',
                            radius : '55%',
                            center: ['50%', '50%'],
                            data:[
                                {value:res.data.report_loss_num, name:'报失数'},
                                {value:res.data.insure_num, name:'投保数'},

                                // {value:1548, name:'搜索引擎'}
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

        },
        error:function(){
            layer.close(index);
            layer.msg("网络请求错误，稍后重试！");
            return false;
        }
    });
}
load();

$("#search3").click(function(){
    load_Table1();
});



function load_Table1(){
    layui.use('table', function(){
        var renewal_date =  $("#test101").attr("value");
        var province_id =  $('#test3 option:selected').val();

        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#demo1'
            ,method:'post'
            ,align:"right"
            ,url: adminurl + "/lease/loss_battery/searchLoss"//数据接口
            ,where: {_token: _token, province_id: province_id, renewal_date:renewal_date}
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'rent_date', title: '日期',  fixed: 'left'}
                ,{field: 'report_loss_num', title: '报失数'}
                ,{field: 'report_loss_user_num', title: '报失用户数'}

            ]]
        });

    });

}
load_Table1();




