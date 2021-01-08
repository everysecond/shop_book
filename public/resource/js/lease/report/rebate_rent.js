layui.use('laydate', function(){
    var laydate = layui.laydate;

    laydate.render({
        elem: '#test10'

        ,range: true
    });

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
load_Table();


