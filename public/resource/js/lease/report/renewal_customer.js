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
load_Table();


