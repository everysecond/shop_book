layui.use('laydate', function(){
    var laydate = layui.laydate;

    laydate.render({
        elem: '#test9'

        ,range: true
    });

    laydate.render({
        elem: '#test19'

        ,range: true
    });

    laydate.render({
        elem: '#test10'

        ,range: true
    });
    laydate.render({
        elem: '#test22'

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
    });

    form.on('select(demo19)',function(data){
        if (data.value == 7) {
            $('#test19').css('display','block');
        }else{
            $('#test19').css('display','none');
        }
    });

    form.on('select(demo12)',function(data){
        if (data.value == 7) {
            $('#test12').css('display','block');
        }else{
            $('#test12').css('display','none');
        }
    });

    form.on('select(demo22)',function(data){
        if (data.value == 7) {
            $('#test22').css('display','block');
        }else{
            $('#test22').css('display','none');
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
            ,url: adminurl + "/lease/renewal/search"//数据接口
            ,where: {_token: _token, province_id: province_id, renewal_date:renewal_date}
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'renewal_date', title: '日期', fixed: 'left'}
                ,{field: 'expire_unrent_num', title: '到期未处理数'}
                ,{field: 'renewal_num', title: '续租数'}
                ,{field: 'renewal_user_num', title: '续租用户数'}
                ,{field: 'renewal_amount', title: '续租总金额'}
                ,{field: 'renewal_average', title: '续租均额'}
                ,{field: 'advance_renewal', title: '提前续租数'}
                ,{field: 'expire_renewal_num', title: '到期当日续租数'}
                ,{field: 'overtime_ten_renewal_num', title: '到期0-10天续租数'}
                ,{field: 'overtime_ten_thirty_renewal_num', title: '到期10-30天续租数'}
            ]]
        });

    });

}
load_Table();



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
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:['续租数','续租金额']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: res.data.renewal_date,
                        axisPointer: {
                            type: 'shadow'
                        },
                        splitLine:{
                            show:false
                        },
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '数量',
                        // min: 0,
                        // max: 250,
                        // interval: 50,
                        axisLabel: {
                            formatter: '{value}'
                        }
                    },
                    {
                        type: 'value',
                        name: '金额',
                        // min: 0,
                        // max: 25,
                        // interval: 5,
                        axisLabel: {
                            formatter: '{value}元'
                        }
                    }
                ],
                series: [
                    {
                        name:'续租数',
                        type:'bar',
                        data:res.data.renewal_num,
                    },

                    {
                        name:'续租金额',
                        type:'line',
                        yAxisIndex: 1,
                        data:res.data.renewal_amount,
                    }
                ]
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
        url: adminurl + "/lease/renewal/histogram",
        success:function(res){

            var myChart = echarts.init(document.getElementById('box1'));//获取装载数据表的容器
            if ((res.data.advance_renewal == null && res.data.expire_renewal_num == null &&
                res.data.overtime_ten_renewal_num == null && res.data.overtime_ten_thirty_renewal_num == null)||
                (res.data.advance_renewal == 0 && res.data.expire_renewal_num == 0 &&
                    res.data.overtime_ten_renewal_num == 0 && res.data.overtime_ten_thirty_renewal_num == 0) ) {
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
                        text: '续租类型占比',
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
                        data: ['提前续租','到期当天续租','到期0-10天续租','到期10-30天续租']
                    },
                    series : [
                        {
                            name: '',
                            type: 'pie',
                            radius : '55%',
                            center: ['50%', '50%'],
                            data:[
                                {value:res.data.advance_renewal, name:'提前续租'},
                                {value:res.data.expire_renewal_num, name:'到期当天续租'},
                                {value:res.data.overtime_ten_renewal_num, name:'到期0-10天续租'},
                                {value:res.data.overtime_ten_thirty_renewal_num, name:'到期10-30天续租'},
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
    load_area();

});

function load_area(){
    var renewal_date =  $("#test22").attr("value");
    var time_type =  $('#tes22 option:selected').val();

    $.ajax({
        type: 'POST',
        data:{'renewal_date':renewal_date,'time_type':time_type,'_token':_token},
        dataType: 'json',
        url: adminurl + "/lease/renewal/renewalArea",
        success:function(res){
            var myChart = echarts.init(document.getElementById('box3')); //获取装载数据表的容器

            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:['续租数','到期数','续租金额']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: res.data.province_name,
                        axisPointer: {
                            type: 'shadow'
                        },
                        splitLine:{
                            show:false
                        },

                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '数量',
                        // min: 0,
                        // max: 250,
                        // interval: 50,
                        axisLabel: {
                            formatter: '{value}'
                        }
                    },
                    {
                        type: 'value',
                        name: '金额',
                        // min: 0,
                        // max: 25,
                        // interval: 5,
                        axisLabel: {
                            formatter: '{value}元'
                        }
                    }
                ],
                series: [
                    {
                        name:'续租数',
                        type:'bar',
                        data:res.data.renewal_num,
                    },
                    {
                        name:'到期数',
                        type:'bar',
                        data:res.data.expire_rent_num,
                    },
                    {
                        name:'续租金额',
                        type:'line',
                        yAxisIndex: 1,
                        data:res.data.renewal_amount,
                    }
                ]
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
load_area();




$("#search11").click(function(){
    advance_load();

});

function advance_load(){
    var renewal_date =  $("#test19").attr("value");
    var time_type =  $('#tes11 option:selected').val();
    var province_id =  $('#test111 option:selected').val();

    $.ajax({
        type: 'POST',
        data:{'renewal_date':renewal_date,'province_id':province_id,'time_type':time_type,'_token':_token},
        dataType: 'json',
        url: adminurl + "/lease/renewal/advanceRenewal",
        success:function(res){

            var myChart = echarts.init(document.getElementById('box11'));//获取装载数据表的容器
            if ((res.data.advance_one_five_renewal_num == null && res.data.advance_six_ten_renewal_num == null &&
                res.data.advance_ten_thirty_renewal_num == null && res.data.advance_over_thirty_renewal_num == null)||
                (res.data.advance_one_five_renewal_num == 0 && res.data.advance_six_ten_renewal_num == 0 &&
                    res.data.advance_ten_thirty_renewal_num == 0 && res.data.advance_over_thirty_renewal_num == 0) ) {
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
                        text: '提前续租分析',
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
                        data: ['提前0-5天','提前6-10天','提前10-30天','提前30天以上']
                    },
                    series : [
                        {
                            name: '',
                            type: 'pie',
                            radius : '55%',
                            center: ['50%', '50%'],
                            data:[
                                {value:res.data.advance_one_five_renewal_num, name:'提前0-5天'},
                                {value:res.data.advance_six_ten_renewal_num, name:'提前6-10天'},
                                {value:res.data.advance_ten_thirty_renewal_num, name:'提前10-30天'},
                                {value:res.data.advance_over_thirty_renewal_num, name:'提前30天以上'},
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
advance_load();
