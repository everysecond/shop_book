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
        url: adminurl + "/lease/dash/total_chart_data",
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
                    name: '日期',
                    type: 'category',
                    boundaryGap: false,
                    data: []
                },
                yAxis: {
                    name: '数量',
                    type: 'value',
                    minInterval: 1,
                    boundaryGap: [0, 0.1]
                },
                color: [
                    '#3aa1ff', '#36cbcb', '#4ecb73', '#27727B', '#fbd437'
                ],
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
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/base_total_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#register_num").html(res.data.register.num);
                $("#rate_register").html(res.data.register.rate + "%");
                changeColor("#rate_register");
                $("#down_num").html(res.data.down.num);
                $("#rate_down").html(res.data.down.rate + "%");
                changeColor("#rate_down");
                $("#login_num").html(res.data.login.num);
                $("#rate_login").html(res.data.login.rate + "%");
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
        url: adminurl + "/lease/dash/total_lease_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#lease_num").html(res.data.lease_num.num);
                $("#lease_amount").html(res.data.lease_amount.num);
                $("#lease_deposit").html(res.data.lease_deposit.num);
                $("#lease_month_num").html(res.data.lease_month.num);
            }
        }
    });
}

//续租指标
function load_lease_renewal() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/total_renewal_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#renewal_num").html(res.data.lease_num.num);
                $("#renewal_amount").html(res.data.lease_amount.num);
                $("#renewal_month_num").html(res.data.lease_month.num);
            }
        }
    });
}

//退租指标
function load_lease_rebate() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/total_rebate_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#rebate_num").html(res.data.lease_num.num);
                $("#rebate_amount").html(res.data.lease_amount.num);

            }
        }
    });
}

//换租指标
function load_lease_change() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/total_change_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#change_num").html(res.data.lease_num.num);

            }
        }
    });
}

function load_lease_insurance() {
    $.ajax({
        type: 'POST',
        data: {
            'agentId': $("select[name='agents']").val(),
            'date': $("#date1").val(),
            '_token': _token
        },
        dataType: 'json',
        url: adminurl + "/lease/dash/total_insurance_data",
        success: function (res) {
            if (res && res.code == 1) {
                $("#insurance_num").html(res.data.lease_num.num);
                $("#insurance_loss").html(res.data.lease_amount.num);

            }
        }
    });
}

function changeColor(select) {
    var text = $(select).html();
    text = text.replace("%", "");
    if (text != "-" && text * 1 > 0) {
        $(select).removeClass("color-green").addClass("color-red");
        $(select).html("+" + $(select).html());
    } else {
        $(select).addClass("color-green").removeClass("color-red");
    }
}

function load_data() {
    load_base_data();
    load_register_hour();
    load_lease_data();
    load_lease_renewal();
    load_lease_rebate();
    load_lease_change();
    load_lease_insurance();
}

load_data();

$(frames).resize(function() {
    register_hour_chart.resize();
});


