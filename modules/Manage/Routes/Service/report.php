<?php

//用户数据-用户画像view
Route::get('users_portrayal', 'UserDataController@portrayal')->name("service.reports.view.portrayal");
//用户画像-年龄分布data
Route::post('age', 'UserDataController@age')->name("service.reports.data.age");
//用户画像-性别分布data
Route::post('sex', 'UserDataController@sex')->name("service.reports.data.sex");
//用户画像-区域分布data
Route::post('area', 'UserDataController@area')->name("service.reports.data.area");
//用户画像-网点数排行
Route::post('data/sort', 'UserDataController@sort')->name("service.reports.data.sort");
//用户数据-注册审核view
Route::get('register_chart', 'UserDataController@register')->name("service.reports.view.register");
//注册审核-注册审核趋势折线图
Route::post('register_trend', 'UserDataController@registerTrend')->name("service.reports.register.trend");
//注册审核-注册审核趋势表格
Route::post('register_table', 'UserDataController@registerTrendTable')->name("service.reports.register.table");
//网点启动次数view
Route::get('start_chart', 'StartLogController@startChartView')->name("service.reports.view.start");
//用户启动统计数据data api
Route::post('start_hour', 'StartLogController@startHour')->name("service.reports.data.start.hour");
Route::post('start_hour_sum', 'StartLogController@startHourSum')->name("service.reports.data.start.hour.sum");
Route::post('table_start_hour', 'StartLogController@tableStartHour')->name("service.reports.data.table.start.hour");
Route::post('start_day', 'StartLogController@startDay')->name("service.reports.data.start.day");
Route::post('start_from', 'StartLogController@startFrom')->name("service.reports.data.start.from");
Route::post('start_total', 'StartLogController@startTotal')->name("service.reports.data.start.total");
//用户数据-活跃事件view
Route::get('active', 'ActiveEventController@index')->name("service.reports.view.active");
//活跃事件趋势折线图
Route::post('active_trend', 'ActiveEventController@activeTrend')->name("service.reports.active.trend");
//活跃事件统计柱状图
Route::post('active_data', 'ActiveEventController@activeData')->name("service.reports.active.data");
//活跃事件统表
Route::post('active_table', 'ActiveEventController@activeTable')->name("service.reports.active.table");
//网点收益-余额view
Route::get('balance', 'BalanceController@index')->name("service.reports.view.balance");
//余额金额分布
Route::post('balance', 'BalanceController@balance')->name("service.reports.balance");
//各区域余额统计
Route::post('balance_area', 'BalanceController@balanceArea')->name("service.reports.balance.area");
//网点收益-收益view
Route::get('income', 'IncomeController@index')->name("service.reports.view.income");
//各区域网点收益分布
Route::post('income_area', 'IncomeController@incomeArea')->name("service.reports.income");
//各区域网点收益统计
Route::post('income_table', 'IncomeController@incomeTable')->name("service.reports.income.table");

//终端下载
Route::get('terminal_chart', 'TerminalController@terminalView')->name("service.reports.view.terminal");
Route::post('data/terminal/trend', 'TerminalController@trend')->name("service.reports.terminal.trend");
Route::post('data/terminal/sort', 'TerminalController@terminalSort')->name("service.reports.terminal.sort");
Route::post('terminal_table', 'TerminalController@terminalTable')->name("service.reports.terminal.table");

//网点库存-补货/退库
Route::get('stock', 'StockController@stockIndex')->name("service.reports.stock.index");
//补货趋势
Route::post('supply/trend', 'StockController@supplyTrend')->name("service.reports.stock.supply.trend");
//补货分析
Route::post('supply/analysis', 'StockController@supplyAnalysis')->name("service.reports.stock.supply.analysis");
//补货统计表
Route::post('supply/table', 'StockController@supplyTable')->name("service.reports.stock.supply.table");

//网点库存-退回
Route::get('stock/return', 'StockController@returnIndex')->name("service.reports.stock.return.index");
//退回趋势
Route::post('return/trend', 'StockController@returnTrend')->name("service.reports.stock.return.trend");
//退回分析
Route::post('return/analysis', 'StockController@returnAnalysis')->name("service.reports.stock.return.analysis");
//退回统计表
Route::post('return/table', 'StockController@returnTable')->name("service.reports.stock.return.table");

//网点库存-回收
Route::get('stock/recycle', 'StockController@recycleIndex')->name("service.reports.stock.recycle.index");
//回收趋势
Route::post('recycle/trend', 'StockController@recycleTrend')->name("service.reports.stock.recycle.trend");
//回收分析
Route::post('recycle/analysis', 'StockController@recycleAnalysis')->name("service.reports.stock.recycle.analysis");
//回收统计表
Route::post('recycle/table', 'StockController@recycleTable')->name("service.reports.stock.recycle.table");

//网点库存-库存统计
Route::get('stock/stock', 'StockController@stock')->name("service.reports.stock.stock");
//区域库存占比
Route::post('stock/area', 'StockController@stockArea')->name("service.reports.stock.area");
//各区域库存统计
Route::post('stock/area/list', 'StockController@areaList')->name("service.reports.stock.area.list");
//电池型号占比
Route::post('stock/battery', 'StockController@battery')->name("service.reports.stock.battery");
//电池型号库存统计
Route::post('stock/battery/list', 'StockController@batteryList')->name("service.reports.stock.battery.list");


//网点收益-提现view
Route::get('withdraw', 'WithdrawController@index')->name("service.reports.view.withdraw");
//各区域网点提现分布
Route::post('withdraw_area', 'WithdrawController@withdrawArea')->name("service.reports.withdrawArea");
//各区域网点提现统计
Route::post('withdraw_table', 'WithdrawController@withdrawTable')->name("service.reports.income.withdrawTable");
//各区域网点提现频率
Route::post('withdraw_rate', 'WithdrawController@withdrawRate')->name("service.reports.income.withdrawRate");

//网点库存-补货view
Route::get('stockview', 'StockDataController@stockview')->name("service.reports.view.stock");
Route::post('replenishmentdata', 'StockDataController@replenishmentData')->name("service.reports.replenishmentdata");
Route::post('replenishmentarea', 'StockDataController@replenishmentArea')->name("service.reports.replenishmentarea");
Route::post('replenishmentlist', 'StockDataController@replenishmentList')->name("service.reports.replenishmentlist");

//网点库存-退货  cancelview
Route::get('cancelsview', 'StockDataController@cancelsview')->name("service.reports.view.cancelsview");
Route::post('cancelsdata', 'StockDataController@cancelsData')->name("service.reports.cancelsdata");
Route::post('cancelsarea', 'StockDataController@cancelsArea')->name("service.reports.cancelsarea");
Route::post('cancelslist', 'StockDataController@cancelsList')->name("service.reports.cancelslist");

//网点库存-回收  retrieveview
Route::get('retrieveview', 'StockDataController@retrieveview')->name("service.reports.view.retrieveview");
Route::post('retrievedata', 'StockDataController@retrieveData')->name("service.reports.retrievedata");
Route::post('retrievearea', 'StockDataController@retrieveArea')->name("service.reports.retrievearea");
Route::post('retrievelist', 'StockDataController@retrieveList')->name("service.reports.retrievelist");

//网点库存-库存统计  statisticsview
Route::get('statisticsview', 'StockDataController@statisticsview')->name("service.reports.view.statisticsview");
Route::post('statisticsdata', 'StockDataController@statisticsData')->name("service.reports.statisticsdata");
Route::post('statisticsarea', 'StockDataController@statisticsArea')->name("service.reports.statisticsarea");
Route::post('statisticslist', 'StockDataController@statisticsList')->name("service.reports.statisticslist");

//业务转化-租赁业务
Route::get('rent_service', 'RentServiceController@rentService')->name("service.reports.view.rent_service");
Route::post('rent_distribution', 'RentServiceController@rentDistribution')->name("service.reports.rent_distribution");
Route::post('area_rent', 'RentServiceController@areaRent')->name("service.reports.area_rent");
//换组业务
Route::get('exchange_service', 'ExchangeServiceController@exchangeService')->name("service.reports.view.exchange_service");
Route::post('exchange_distribution', 'ExchangeServiceController@exchangeDistribution')->name("service.reports.exchange_distribution");
Route::post('area_exchange', 'ExchangeServiceController@areaExchange')->name("service.reports.area_exchange");
//退租业务
Route::get('retire_service', 'RetireServiceController@retireService')->name("service.reports.view.retire_service");
Route::post('retire_distribution', 'RetireServiceController@retireDistribution')->name("service.reports.retire_distribution");
Route::post('area_retire', 'RetireServiceController@areaRetire')->name("service.reports.area_retire");

//数据大屏
//网点分布排行
Route::get('serviceArea', 'UserDataController@serviceArea')->name("service.reports.data.serviceArea");
//各网点店家收益分布排行
Route::get('incomeRank', 'IncomeController@incomeRank')->name("service.reports.incomeRank");
//各区域网点收益分布排行
Route::get('incomeAreaRank', 'IncomeController@incomeAreaRank')->name("service.reports.incomeAreaRank");

//租点-实时数据
Route::get('actualData/{index}', 'UserDataController@actualData')->name("service.reports.data.actualData");
//租点-增长趋势
Route::get('dataTrend/{type}', 'UserDataController@dataTrend')->name("service.reports.data.dataTrend");