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
        elem: '#test22'

        ,range: true
    })

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
            ,url: adminurl + "/lease/rebate/search"//数据接口
            ,where: {_token: _token, province_id: province_id, renewal_date:renewal_date}
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'rent_release_date', title: '日期',  fixed: 'left'}
                ,{field: 'rent_release_num', title: '当日退租数'}
                ,{field: 'advance_rent_release', title: '提前退租数'}
                ,{field: 'expire_rent_release_num', title: '到期当日退租数'}
                ,{field: 'overtime_ten_rent_release_num', title: '到期0-10天退租数'}
                ,{field: 'overtime_ten_thirty_rent_release_num', title: '到期10-30天退租数'}

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
        url: adminurl + "/lease/rebate/broken",
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
                    data:['退租数','退租金额']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: res.data.rent_release_date,
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
                        name:'退租数',
                        type:'bar',
                        data:res.data.rent_release_num,
                    },

                    {
                        name:'退租金额',
                        type:'line',
                        yAxisIndex: 1,
                        data:res.data.rent_release_amount,
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



// $("#search2").click(function(){
//     load_broken();
//
// });
//
// function load_broken(){
//     var renewal_date =  $("#test12").attr("value");
//     var time_type =  $('#tes2 option:selected').val();
//     var province_id =  $('#test13 option:selected').val();
//
//     $.ajax({
//         type: 'POST',
//         data:{'renewal_date':renewal_date,'province_id':province_id,'time_type':time_type,'_token':_token},
//         dataType: 'json',
//         url: adminurl + "/lease/rebate/broken",
//         success:function(res){
//             var myChart = echarts.init(document.getElementById('box2')) //获取装载数据表的容器
//
//             option = {
//                 color: ['#3398DB'],
//                 tooltip: {
//                     trigger: 'axis',
//                     axisPointer: {            // 坐标轴指示器，坐标轴触发有效
//                         type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
//                     }
//                 },
//                 title : {
//                     text: '退租趋势',
//                     x:'center'
//
//                 },
//                 xAxis: {
//                     name:'日期',
//                     axisLine: {
//                         lineStyle: {
//                             color: '#000', // 颜色
//                             width: 1 // 粗细
//                         }
//                     },
//                     splitLine:{
//
//                         show:false
//                     },
//                     type: 'category',
//                     data: res.data.rent_release_date
//                 },
//                 yAxis: {
//                     name:'退租数',
//                     axisLine: {
//                         lineStyle: {
//                             color: '#000', // 颜色
//                             width: 1 // 粗细
//                         }
//                     },
//                     splitLine:{
//                         lineStyle:{
//                             type:'dashed'    //设置网格线类型 dotted：虚线   solid:实线
//                         },
//                         show:true
//                     },
//
//                     type: 'value'
//                 },
//                 series: [{
//                     symbol:'circle',
//                     symbolSize:2,
//                     data: res.data.rent_release_num,
//                     type: 'line'
//                 }]
//             };
//             myChart.setOption(option)//把echarts配置项启动
//
//         },
//         error:function(){
//             layer.close(index);
//             layer.msg("网络请求错误，稍后重试！");
//             return false;
//         }
//     });
// }
// load_broken();


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
        url: adminurl + "/lease/rebate/histogram",
        success:function(res){

            var myChart = echarts.init(document.getElementById('box1'));//获取装载数据表的容器
            if ((res.data.advance_rent_release == null && res.data.expire_rent_release_num == null &&
                res.data.overtime_ten_rent_release_num == null && res.data.overtime_ten_thirty_rent_release_num == null)||
                (res.data.advance_rent_release == 0 && res.data.expire_rent_release_num == 0 &&
                    res.data.overtime_ten_rent_release_num == 0 && res.data.overtime_ten_thirty_rent_release_num == 0) ) {
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
                        text: '退租类型占比',
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
                        data: ['提前退租','到期当天退租','到期0-10天退租','到期10-30天退租']
                    },
                    series : [
                        {
                            name: '续租类型占比',
                            type: 'pie',
                            radius : '55%',
                            center: ['50%', '50%'],
                            data:[
                                {value:res.data.advance_rent_release, name:'提前退租'},
                                {value:res.data.expire_rent_release_num, name:'到期当天退租'},
                                {value:res.data.overtime_ten_rent_release_num, name:'到期0-10天退租'},
                                {value:res.data.overtime_ten_thirty_rent_release_num, name:'到期10-30天退租'},
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
        url: adminurl + "/lease/rebate/rentRebateArea",
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
                    data:['退租数','退租金额']
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
                        name:'退租数',
                        type:'bar',
                        data:res.data.renewal_num,
                    },
                    {
                        name:'退租金额',
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