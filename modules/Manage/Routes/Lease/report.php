<?php
//今日指标view
Route::get('dashboard_today', 'DashBoardController@todayIndex')->name("lease.reports.view.dashboard.today");
//今日指标-基本指标api
Route::post('dash/base_data', 'DashBoardController@baseData')->name("lease.dash.base.data");
//今日指标-租赁指标api
Route::post('dash/lease_data', 'DashBoardController@leaseData')->name("lease.dash.lease.data");
//今日指标-续租指标api
Route::post('dash/renewal_data', 'DashBoardController@renewalData')->name("lease.dash.renewal.data");
//今日指标-退租指标api
Route::post('dash/rent_data', 'DashBoardController@rentData')->name("lease.dash.rent.data");
//今日指标-换租指标api
Route::post('dash/rent_change_data', 'DashBoardController@rentChangeData')->name("lease.dash.rent.change.data");
//今日指标-保险指标api
Route::post('dash/insurance_data', 'DashBoardController@insuranceData')->name("lease.dash.insurance.data");
//今日指标-基本指标折线图api
Route::post('dash/base_chart_data', 'DashBoardController@baseChartData')->name("lease.dash.base.chart.data");

//整体趋势view
Route::get('dashboard_total', 'DashBoardTotalController@totalIndex')->name("lease.reports.view.dashboard.total");
//整体趋势-基本指标api
Route::post('dash/base_total_data', 'DashBoardTotalController@baseTotalData')->name("lease.dash.base.total.data");
//整体趋势-基本指标折线图api
Route::post('dash/total_chart_data', 'DashBoardTotalController@totalChartData')->name("lease.dash.total.chart.data");
//整体趋势-租赁指标api
Route::post('dash/total_lease_data', 'DashBoardTotalController@leaseData')->name("lease.dash.lease.total.data");
//整体趋势-续租指标api
Route::post('dash/total_renewal_data', 'DashBoardTotalController@renewalData')->name("lease.dash.renewal.total.data");
//整体趋势-退租指标api
Route::post('dash/total_rebate_data', 'DashBoardTotalController@rebatelData')->name("lease.dash.rebate.data");
//整体趋势-换租指标api
Route::post('dash/total_change_data', 'DashBoardTotalController@changeData')->name("lease.dash.change.data");
//整体趋势-投保指标api
Route::post('dash/total_insurance_data', 'DashBoardTotalController@insuranceData')->name("lease.dash.insurance.total.data");


//用户分析view
Route::get('users_portrayal', 'ReportController@portrayal')->name("lease.reports.view.portrayal");
Route::get('register_chart', 'ReportController@register')->name("lease.reports.view.register");
Route::get('start_chart', 'StartLogController@startChartView')->name("lease.reports.view.start");
Route::get('terminal_chart', 'TerminalController@terminalView')->name("lease.reports.view.terminal");

//终端下载
Route::post('data/terminal/trend', 'TerminalController@trend')->name("lease.reports.terminal.trend");
Route::post('data/terminal/sort', 'TerminalController@terminalSort')->name("lease.reports.terminal.sort");
Route::post('terminal_table', 'TerminalController@terminalTable')->name("lease.reports.terminal.table");


//用户画像分析data api
Route::post('data/age', 'ReportController@age')->name("lease.reports.data.age");
Route::post('data/sex', 'ReportController@sex')->name("lease.reports.data.sex");
Route::post('data/model', 'ReportController@mobileModel')->name("lease.reports.data.model");
Route::post('data/area', 'ReportController@areaData')->name("lease.reports.data.area");
Route::post('data/lease_time', 'ReportController@leaseTime')->name("lease.reports.data.lease.time");
Route::post('data/lease_term', 'ReportController@leaseTerm')->name("lease.reports.data.lease.term");

//用户注册数据data api
Route::post('data/register_hour', 'ReportController@registerHour')->name("lease.reports.data.register.hour");
Route::post('data/register_hour_sum', 'ReportController@registerHourSum')->name("lease.reports.data.register.hour.sum");
Route::post('data/table_register_hour',
    'ReportController@tableRegisterHour')->name("lease.reports.data.table.register.hour");
Route::post('data/register_day', 'ReportController@registerDay')->name("lease.reports.data.register.day");
Route::post('data/register_from', 'ReportController@registerFrom')->name("lease.reports.data.register.from");
Route::post('data/register_total', 'ReportController@registerTotal')->name("lease.reports.data.register.total");

//用户启动统计数据data api
Route::post('data/start_hour', 'StartLogController@startHour')->name("lease.reports.data.start.hour");
Route::post('data/start_hour_sum', 'StartLogController@startHourSum')->name("lease.reports.data.start.hour.sum");
Route::post('data/table_start_hour', 'StartLogController@tableStartHour')->name("lease.reports.data.table.start.hour");
Route::post('data/start_day', 'StartLogController@startDay')->name("lease.reports.data.start.day");
Route::post('data/start_from', 'StartLogController@startFrom')->name("lease.reports.data.start.from");
Route::post('data/start_total', 'StartLogController@startTotal')->name("lease.reports.data.start.total");

//用户数据-活跃事件view
Route::get('active', 'ActiveEventController@index')->name("lease.reports.view.active");
//活跃事件趋势折线图
Route::post('active_trend', 'ActiveEventController@activeTrend')->name("lease.reports.active.trend");
//活跃事件统计柱状图
Route::post('active_data', 'ActiveEventController@activeData')->name("lease.reports.active.data");
//活跃事件统表
Route::post('active_table', 'ActiveEventController@activeTable')->name("lease.reports.active.table");

//新用户租赁view
Route::get('lease_new', 'NewUserLeaseController@leaseNewView')->name("lease.reports.view.lease.new");
//新用户租赁统计数据data api
Route::post('data/time_hour', 'NewUserLeaseController@leaseTimeHour')->name("lease.reports.data.lease.time.new");
Route::post('data/money_hour', 'NewUserLeaseController@leaseMoneyHour')->name("lease.reports.data.lease.money.new");
Route::post('data/new_lease_trend', 'NewUserLeaseController@newLeaseTrend')->name("lease.reports.data.lease.trend.new");
Route::post('data/new_lease_statistics',
    'NewUserLeaseController@newLeaseStatistics')->name("lease.reports.data.lease.statistics.new");

//老用户租赁view
Route::get('lease_old', 'OldUserLeaseController@leaseOldView')->name("lease.reports.view.lease.old");
//新用户租赁统计数据data api
Route::post('data/old_time_hour', 'OldUserLeaseController@leaseTimeHour')->name("lease.reports.data.lease.time.old");
Route::post('data/old_money_hour', 'OldUserLeaseController@leaseMoneyHour')->name("lease.reports.data.lease.money.old");
Route::post('data/old_lease_trend', 'OldUserLeaseController@oldLeaseTrend')->name("lease.reports.data.lease.trend.old");
Route::post('data/old_lease_statistics',
    'OldUserLeaseController@oldLeaseStatistics')->name("lease.reports.data.lease.statistics.old");

//新老用户租赁
Route::get('compare', 'NewUserLeaseController@index')->name("lease.reports.compare");
Route::post('data/new_old_statistics', 'NewUserLeaseController@newOldStatistics')->name("lease.reports.compare.statistics");
Route::post('data/day_statistics',
    'NewUserLeaseController@dayStatistics')->name("lease.reports.data.lease.statistics.new");

//电池型号view
Route::get('battery', 'BatteryController@index')->name("lease.reports.view.battery");
//电池型号租赁次数api
Route::post('data/battery_histogram', 'BatteryController@batteryHistogram')->name("lease.reports.data.battery.histogram");
//电池型号比例api
Route::post('data/battery_rate', 'BatteryController@batteryRate')->name("lease.reports.data.battery.rate");
//电池型号租赁统计表api
Route::post('data/battery_table', 'BatteryController@batteryTable')->name("lease.reports.data.battery.table");
//区域型号统计表api
Route::post('data/model_table', 'BatteryController@modelTable')->name("lease.reports.data.model.table");

//租赁分析
Route::get('lease_analysis', 'LeaseAnalysisController@index')->name("lease.reports.view.analysis");
Route::post('trend', 'LeaseAnalysisController@trend')->name("lease.reports.trend");
Route::post('area', 'LeaseAnalysisController@area')->name("lease.reports.area");
Route::post('cycle', 'LeaseAnalysisController@cycle')->name("lease.reports.cycle");

//续租管理
Route::get('renewal', 'RenewalController@show')->name("lease.renewal.show");
Route::post('renewal/search', 'RenewalController@search')->name("lease.renewal.search");
Route::post('renewal/histogram', 'RenewalController@histogram')->name("lease.renewal.histogram");
Route::post('renewal/broken', 'RenewalController@broken')->name("lease.renewal.broken");
Route::post('renewal/renewalArea', 'RenewalController@renewalArea')->name("lease.renewal.renewalArea");
Route::post('renewal/advanceRenewal', 'RenewalController@advanceRenewal')->name("lease.renewal.advanceRenewal");
//投保管理
Route::get('insurance', 'InsuranceController@show')->name("lease.insurance.show");
Route::post('insurance/search', 'InsuranceController@search')->name("lease.insurance.search");
Route::post('insurance/histogram', 'InsuranceController@histogram')->name("lease.insurance.histogram");
Route::post('insurance/broken', 'InsuranceController@broken')->name("lease.insurance.broken");

//退租管理
Route::get('rebate', 'RebateController@show')->name("lease.rebate.show");
Route::post('rebate/search', 'RebateController@search')->name("lease.rebate.search");
Route::post('rebate/histogram', 'RebateController@histogram')->name("lease.rebate.histogram");
Route::post('rebate/broken', 'RebateController@broken')->name("lease.rebate.broken");
Route::post('rebate/rentRebateArea', 'RebateController@rentRebateArea')->name("lease.rebate.rentRebateArea");
//换租管理
Route::get('rent_change', 'RentChangeController@show')->name("lease.rent_change.show");
Route::post('rent_change/search', 'RentChangeController@search')->name("lease.rent_change.search");
Route::post('rent_change/histogram', 'RentChangeController@histogram')->name("lease.rent_change.histogram");
Route::post('rent_change/broken', 'RentChangeController@broken')->name("lease.rent_change.broken");
Route::post('rent_change/rentChangeArea', 'RentChangeController@rentChangeArea')->name("lease.rent_change.rentChangeArea");
//电池报失管理
Route::get('loss_battery', 'LossBatteryController@show')->name("lease.loss_battery.show");
Route::post('loss_battery/search', 'LossBatteryController@search')->name("lease.loss_battery.search");
Route::post('loss_battery/histogram', 'LossBatteryController@histogram')->name("lease.loss_battery.histogram");
Route::post('loss_battery/broken', 'LossBatteryController@broken')->name("lease.loss_battery.broken");
Route::post('loss_battery/searchLoss', 'LossBatteryController@searchLoss')->name("lease.loss_battery.searchLoss");
//数据报表管理
Route::get('data_report/renewal_customer',
    'DataReportController@renewalCustomerView')->name("lease.data_report.renewal_customer");
Route::post('data_report/renewal_customer_search',
    'DataReportController@renewalCustomerSearch')->name("lease.data_report.renewal_customer_search");
Route::get('data_report/expire_renewal',
    'DataReportController@expireRenewalView')->name("lease.data_report.expire_renewal");
Route::post('data_report/expire_renewal_search',
    'DataReportController@expireRenewalSearch')->name("lease.data_report.expire_renewal_search");
Route::get('data_report/rebate_rent', 'DataReportController@rebateRentView')->name("lease.data_report.rebate_rent");
Route::post('data_report/rebate_rent_search',
    'DataReportController@rebateRentSearch')->name("lease.data_report.rebate_rent_search");



//租赁业务  新老用户租赁对比
Route::get('leasenewold', 'LeaseproController@Leasenewold')->name("lease.reports.leasenewold");
Route::post('newoldlist', 'LeaseproController@LeaseNewOldlist')->name("lease.reports.newoldlist");
Route::post('newoldmoneylist', 'LeaseproController@LeaseNewOldMoneylist')->name("lease.reports.newoldmoneylist");
Route::post('newoldtotallist', 'LeaseproController@getNewOldTotalLists')->name("lease.reports.newoldtotallist");

//租赁业务  租赁注册
Route::get('leaseregister', 'LeaseproController@register')->name("lease.reports.leaseregister");
Route::post('registertimehour', 'LeaseproController@RegisterTimeHour')->name("lease.reports.registertimehour");
Route::post('registertimehours', 'LeaseproController@RegisterTimeHours')->name("lease.reports.registertimehours");
Route::post('registertimecum', 'LeaseproController@RegisterTimeCum')->name("lease.reports.registertimecum");
Route::post('registertimehourlist', 'LeaseproController@RegisterTimeHourlist')->name("lease.reports.registertimehourlist");
Route::post('registertimehourslist', 'LeaseproController@RegisterTimeHourslist')->name("lease.reports.registertimehourslist");
Route::post('registertimedayslist', 'LeaseproController@RegisterTimeDayslist')->name("lease.reports.registertimedayslist");

////租赁业务  租赁登录 leaseprocess  租赁流程转化--转化漏斗 Lease process
Route::get('leaseprocess', 'LeaseproController@show')->name("lease.reports.leaseprocessshow");
Route::post('leasefunnellist', 'LeaseproController@funnellists')->name("lease.reports.funnellist");
Route::post('leasecumfunnellist', 'LeaseproController@cumfunnellists')->name("lease.reports.cumfunnellist");
Route::post('leasetotalfunnellist', 'LeaseproController@totalfunnellists')->name("lease.reports.totalfunnellist");
Route::post('leaseprolist', 'LeaseproController@lists')->name("lease.reports.lists");
Route::post('leaseprocumlist', 'LeaseproController@cumlists')->name("lease.reports.cumlists");
Route::post('leaseprototallist', 'LeaseproController@totallists')->name("lease.reports.totallists");

//租赁业务-登陸注冊view
Route::get('login_rent', 'LoginRentController@index')->name("lease.reports.view.login_rent");
//租赁业务趋势折线图
Route::post('rent_trend', 'LoginRentController@rentTrend')->name("lease.reports.rent.trend");
//租赁业务统计柱状图
Route::post('rent_data', 'LoginRentController@rentData')->name("lease.reports.rent.data");
//租赁业务统表
Route::post('rent_table', 'LoginRentController@rentTable')->name("lease.reports.rent.table");

//租赁业务  租赁到期转化
Route::get('leasematurity', 'LeaseproController@maturity')->name("lease.reports.leasematurity");
Route::post('maturitydata', 'LeaseproController@maturitydata')->name("lease.reports.maturitydata");
Route::post('maturitydatalist', 'LeaseproController@maturitydatalist')->name("lease.reports.maturitydatalist");

//租赁业务  租赁区域 area
Route::get('leasearea', 'LeaseproController@area')->name("lease.reports.leasearea");
Route::post('areadata', 'LeaseproController@areadata')->name("lease.reports.leaseareadata");
Route::post('areadatalist', 'LeaseproController@areadatalist')->name("lease.reports.leaseareadatalist");

//租赁业务  渠道转化
Route::get('leasechannel', 'LeaseproController@channel')->name("lease.reports.leasechannel");
Route::post('channeldata', 'LeaseproController@channeldata')->name("lease.reports.channeldata");
Route::post('channeldatalist', 'LeaseproController@channeldatalist')->name("lease.reports.channeldatalist");

//租赁业务  注册-租赁发起周期
Route::get('leaseregisterperiod', 'LeaseproController@Leaseregisterperiod')->name("lease.reports.leaseregisterperiod");
Route::post('registerperiodlist', 'LeaseproController@RegisterPeriodlist')->name("lease.reports.registerperiodlist");



//数据大屏
Route::get('data/userArea', 'ReportController@userArea')->name("lease.reports.data.userArea");
Route::get('data/userAuth', 'ReportController@userAuth')->name("lease.reports.data.userAuth");


//同步任务
//同步网点库存日志
Route::get('sync/serviceStockLog', 'SyncController@serviceStockLog')->name("sync.service.stock.log");
//同步物流库存日志
Route::get('sync/logisticsStockLog', 'SyncController@syncLeaseLogisticsStockLog')->name("sync.logistics.stock.log");



