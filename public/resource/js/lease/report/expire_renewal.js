layui.use('laydate', function(){
    var laydate = layui.laydate;

    laydate.render({
        elem: '#test10'

        ,range: true
    });

    laydate.render({
        elem: '#test11'

        ,range: true
    });

    laydate.render({
        elem: '#test12'

        ,range: true
    });

    laydate.render({
        elem: '#test13'

        ,range: true
    });
});




$("#search").click(function(){
    load_Table();
});



function load_Table(){

    layui.use('table', function(){
        var renewal_date =  $("#test10").val();
        var province_id =  $('#test option:selected').val();

        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#demo'
            , method: 'post'
            , align: "right"
            , url: adminurl + "/lease/registerperiodlist"//数据接口
            , where: {_token: _token, datetime: renewal_date, area: province_id}
            , page: true //开启分页
            , cols: [[ //表头
                {field: 'register_date', title: '日期', width: 150, fixed: 'left'}
                , {field: 'register_num', title: '当日注册数'}
                , {field: 'today_num', title: '当天发起租赁'}
                , {field: 'one_three_num', title: '1-3天发起租赁'}
                , {field: 'four_seven_num', title: '4-7天发起租赁'}
                , {field: 'eight_ten_num', title: '8-10天发起租赁'}
                , {field: 'eleven_thirty_num', title: '10-30天发起租赁'}
                , {field: 'thirty_no_num', title: '30天内未发起租赁'}
            ]]
        });

    });

}
load_Table();





$("#search1").click(function(){
    load_Table1();
});



function load_Table1(){
    layui.use('table', function(){
        var renewal_date =  $("#test11").val();
        var province_id =  $('#test1 option:selected').val();

        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#demo1'
            ,method:'post'
            ,align:"right"
            ,url: adminurl + "/lease/data_report/expire_renewal_search"//数据接口
            ,where: {_token: _token, province_id: province_id, renewal_date:renewal_date}
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'renewal_date', title: '日期', fixed: 'left'}
                ,{field: 'expire_rent_num', title: '当日到期数'}
                ,{field: 'expire_renewal_num', title: '当天发起续租'}
                ,{field: 'expire_rent_renewal_than', title: '当天发起续租占比'}
                ,{field: 'overtime_one_three_renewal_future_num', title: '1_3天发起续租'}
                ,{field: 'overtime_four_seven_renewal_future_num', title: '4_7天发起续租'}
                ,{field: 'overtime_eight_ten_renewal_future_num', title: '8_10天发起续租'}
                ,{field: 'overtime_ten_thirty_renewal_future_num', title: '11_30天发起续租'}
                ,{field: 'overtime_thirty_no_renewal_future_num', title: '30天内未发起续租'}

            ]]
        });

    });

}
load_Table1();







$("#search2").click(function(){
    load_Table2();
});



function load_Table2(){
    layui.use('table', function(){
        var renewal_date =  $("#test12").val();
        var province_id =  $('#test2 option:selected').val();

        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#demo2'
            ,method:'post'
            ,align:"right"
            ,url: adminurl + "/lease/data_report/rebate_rent_search"//数据接口
            ,where: {_token: _token, province_id: province_id, renewal_date:renewal_date}
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'rent_release_date', title: '日期', fixed: 'left'}
                ,{field: 'rent_release_num', title: '当日退租数'}
                ,{field: 'expire_rent_future_num', title: '当天发起租赁'}
                ,{field: 'expire_rent_than', title: '当天发起租赁占比'}
                ,{field: 'overtime_one_three_rent_future_num', title: '1_3天发起租赁'}
                ,{field: 'overtime_four_seven_rent_future_num', title: '4_7天发起租赁'}
                ,{field: 'overtime_eight_ten_rent_future_num', title: '8_10天发起租赁'}
                ,{field: 'overtime_ten_thirty_rent_future_num', title: '11_30天发起租赁'}
                ,{field: 'overtime_thirty_no_rent_future_num', title: '30天内未发起租赁'}

            ]]
        });

    });

}
load_Table2();



$("#search3").click(function(){
    load_Table3();
});



function load_Table3(){
    layui.use('table', function(){
        var renewal_date =  $("#test13").val();
        var province_id =  $('#test3 option:selected').val();

        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#demo3'
            ,method:'post'
            ,align:"right"
            ,url: adminurl + "/lease/data_report/renewal_customer_search"//数据接口
            ,where: {_token: _token, province_id: province_id, renewal_date:renewal_date}
            ,page: true //开启分页
            ,cols: [[ //表头
                {field: 'renewal_date', title: '日期',  fixed: 'left'}
                ,{field: 'renewal_num', title: '当日续租数'}
                ,{field: 'advance_renewal', title: '未到期续租'}
                ,{field: 'advance_renewal_than', title: '未到期续租占比'}
                ,{field: 'expire_renewal_num', title: '到期当日续租'}
                ,{field: 'expire_renewal_than', title: '到期当日续租占比'}
                ,{field: 'overtime_one_three_renewal_num', title: '已到期1_3天续租'}
                ,{field: 'overtime_four_seven_renewal_num', title: '已到期4_7天续租'}
                ,{field: 'overtime_eight_ten_renewal_num', title: '已到期8_10天续租'}
                ,{field: 'overtime_ten_thirty_renewal_num', title: '已到期11_30天续租'}

            ]]
        });

    });

}
load_Table3();