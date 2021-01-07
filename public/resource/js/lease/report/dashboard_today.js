var colorArr = [
    '#2fc25b', '#1890ff', '#fbd437', '#27727B',
    '#87f7cf', '#36cbcb', '#72ccff', '#f7c5a0',
    '#0098d9', '#2b821d', '#e87c25', '#e01f54'
];
var colorNormal = {
    color: function (params) {
        return colorArr[params.dataIndex]
    },
    label:{
        show: true,
        formatter: '{b}:{c}({d}%)'
    },
    labelLine :{show:true}
};
//加载时间控件
layui.use('laydate', function () {
    var laydate = layui.laydate;
    laydate.render({
        elem: '#date1',
        max: 0
    });
});

var register_hour_chart = echarts.init(document.getElementById('box_register_hour')); //获取装载数据表的容器
var global_chart_type = "register";
$(".base-title").click(function () {
    $(".base-title").removeClass("base-title-click");
    $(this).addClass("base-title-click");
    global_chart_type = $(this).data("type");
    load_register_hour();
});

function load_register_hour() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            'type': global_chart_type,
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/base_chart_data",
        success: function (res) {
            register_hour_option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    top: 'bottom',
                    left: 'center',
                    data: []
                },
                grid: {
                    top: '10%',
                    left: '0%',
                    right: '3%',
                    bottom: '8%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    },
                    right: 15
                },
                xAxis: {
                    name:"小时",
                    type: 'category',
                    boundaryGap: false,
                    data: []
                },
                yAxis: {
                    name:"数量",
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: colorArr,
                series: []
            };
            if (res && res.code == 1) {
                register_hour_option.series = res.data.series;
                register_hour_option.legend.data = res.data.days;
                register_hour_option.xAxis.data = res.data.hourArr;
            }
            register_hour_chart.clear();
            register_hour_chart.setOption(register_hour_option);//把echarts配置项启动
            setTimeout('register_hour_chart.resize()', 10);
        }
    });
}

//基本指标
function load_base_data() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/base_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#register_num").html(res.data.register.num);
                $("#rate_register").html(res.data.register.rate+"%");
                changeColor("#rate_register");
                $("#down_num").html(res.data.down.num);
                $("#rate_down").html(res.data.down.rate+"%");
                changeColor("#rate_down");
                $("#start_num").html(res.data.start.num);
                $("#rate_start").html(res.data.start.rate+"%");
                changeColor("#rate_start");
                $("#login_num").html(res.data.login.num);
                $("#rate_login").html(res.data.login.rate+"%");
                changeColor("#rate_login");
            }
        }
    });
}

//租赁指标
function load_lease_data() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/lease_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#lease_num").html(res.data.lease_num.num);
                $("#lease_rate").html(res.data.lease_num.rate+"%");
                changeColor("#lease_rate");
                $("#lease_amount").html(res.data.lease_amount.num);
                $("#lease_amount_rate").html(res.data.lease_amount.rate+"%");
                changeColor("#lease_amount_rate");
                $("#lease_deposit").html(res.data.lease_deposit.num);
                $("#lease_deposit_rate").html(res.data.lease_deposit.rate+"%");
                changeColor("#lease_deposit_rate");
                $("#lease_month_num").html(res.data.lease_month.num);
                $("#lease_month_rate").html(res.data.lease_month.rate+"%");
                changeColor("#lease_month_rate");
                $("#expired_today").html(res.data.expired_today.num);
                $("#expired_today_rate").html(res.data.expired_today.rate+"%");
                changeColor("#expired_today_rate");
                $("#expired_10").html(res.data.expired_10.num);
                $("#expired_10_rate").html(res.data.expired_10.rate+"%");
                changeColor("#expired_10_rate");
                $("#expired_10_2_30").html(res.data.expired_10_2_30.num);
                $("#expired_10_2_30_rate").html(res.data.expired_10_2_30.rate+"%");
                changeColor("#expired_10_2_30_rate");
                $("#expired_30").html(res.data.expired_30.num);
                $("#expired_30_rate").html(res.data.expired_30.rate+"%");
                changeColor("#expired_30_rate");
            }
        }
    });
}

//续租指标
function load_renewal_data() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/renewal_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#renewal_num").html(res.data.renewal_num.num);
                $("#renewal_rate").html(res.data.renewal_num.rate+"%");
                changeColor("#renewal_rate");
                $("#renewal_amount").html(res.data.renewal_amount.num);
                $("#renewal_amount_rate").html(res.data.renewal_amount.rate+"%");
                changeColor("#renewal_amount_rate");
                $("#advance_renewal").html(res.data.advance_renewal.num);
                $("#advance_renewal_rate").html(res.data.advance_renewal.rate+"%");
                changeColor("#advance_renewal_rate");
                $("#expire_renewal_num").html(res.data.expire_renewal_num.num);
                $("#expire_renewal_rate").html(res.data.expire_renewal_num.rate+"%");
                changeColor("#expire_renewal_rate");
                $("#overtime_ten_renewal_num").html(res.data.overtime_ten_renewal_num.num);
                $("#overtime_ten_renewal_rate").html(res.data.overtime_ten_renewal_num.rate+"%");
                changeColor("#overtime_ten_renewal_rate");
                $("#overtime_ten_thirty_renewal_num").html(res.data.overtime_ten_thirty_renewal_num.num);
                $("#overtime_ten_thirty_renewal_rate").html(res.data.overtime_ten_thirty_renewal_num.rate+"%");
                changeColor("#overtime_ten_thirty_renewal_rate");
                $("#renewal_month_total").html(res.data.renewal_month_total.num);
                $("#renewal_month_rate").html(res.data.renewal_month_total.rate+"%");
                changeColor("#renewal_month_rate");
            }
        }
    });
}

//退租指标
function load_rent_data() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/rent_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#rent_release_num").html(res.data.rent_release_num.num);
                $("#rent_release_rate").html(res.data.rent_release_num.rate+"%");
                changeColor("#rent_release_rate");
                $("#advance_rent_release").html(res.data.advance_rent_release.num);
                $("#advance_rent_release_rate").html(res.data.advance_rent_release.rate+"%");
                changeColor("#advance_rent_release_rate");
                $("#expire_rent_release_num").html(res.data.expire_rent_release_num.num);
                $("#expire_rent_release_rate").html(res.data.expire_rent_release_num.rate+"%");
                changeColor("#expire_rent_release_rate");
                $("#overtime_ten_rent_release_num").html(res.data.overtime_ten_rent_release_num.num);
                $("#overtime_ten_rent_release_rate").html(res.data.overtime_ten_rent_release_num.rate+"%");
                changeColor("#overtime_ten_rent_release_rate");
                $("#overtime_ten_thirty_rent_release_num").html(res.data.overtime_ten_thirty_rent_release_num.num);
                $("#overtime_ten_thirty_rent_release_rate").html(res.data.overtime_ten_thirty_rent_release_num.rate+"%");
                changeColor("#overtime_ten_thirty_rent_release_rate");
                $("#rent_release_amount").html(res.data.rent_release_amount.num);
                $("#rent_release_amount_rate").html(res.data.rent_release_amount.rate+"%");
                changeColor("#rent_release_amount_rate");
            }
        }
    });
}

//换租指标
function load_rent_change_data() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/rent_change_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#rent_change_num").html(res.data.rent_change_num.num);
                $("#rent_change_rate").html(res.data.rent_change_num.rate+"%");
                changeColor("#rent_change_rate");
            }
        }
    });
}

//保险指标
function load_insure_data() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/insurance_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#insure_num").html(res.data.insure_num.num);
                $("#insure_rate").html(res.data.insure_num.rate+"%");
                changeColor("#insure_rate");
                $("#report_loss_num").html(res.data.report_loss_num.num);
                $("#report_loss_rate").html(res.data.report_loss_num.rate+"%");
                changeColor("#report_loss_rate");
            }
        }
    });
}



function changeColor(select) {
    var text = $(select).html();
    text = text.replace("%","");
    if(text!="-" && text*1>0){
        $(select).removeClass("color-green").addClass("color-red");
        $(select).html("+"+$(select).html());
    }else{
        $(select).addClass("color-green").removeClass("color-red");
    }
}

function load_data() {
    load_base_data();
    load_register_hour();
    load_lease_data();
    load_renewal_data();
    load_rent_data();
    load_rent_change_data();
    load_insure_data();
}

load_data();


$(frames).resize(function() {
    register_hour_chart.clear();
    register_hour_chart.resize();
});


