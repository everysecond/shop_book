//绑定下拉框联动事件
layui.use(['form', 'table', 'laydate'], function () {
    var element = layui.element;
    var form = layui.form;
    $ = layui.jquery;
    var table = layui.table;
    var laydate = layui.laydate;

    laydate.render({
        elem: '#datetime'
        , type: 'date'
        , range: true
    });

    laydate.render({
        elem: '#todatetime' //指定元素
    });
    form.on('select(timeType1)', function (data) {
        if (data.value == -1) {
            $('#date3').css('display', 'block');
        } else {
            $('#date3').css('display', 'none');
        }
    });

});
load_day_table()

//统计表
function load_day_table() {
    layui.use('table', function () {
        var datetime = $("#datetime").val();
        var agentId = $("select[name='agentId1']").val();
        var table = layui.table;
        //第一个实例
        table.render({
            elem: '#table_day'
            , method: 'post'
            , align: "right"
            , url: adminurl + "/lease/registerperiodlist"//数据接口
            , where: {_token: _token, datetime: datetime, area: agentId}
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




