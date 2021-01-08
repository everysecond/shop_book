<?php
//时间选项卡
Route::get('timeList', 'ManageController@timeList')->name("api.timeList");
//移动端菜单
Route::get('menus', 'ManageController@menus')->name("api.menus");
//首页-基础指标
Route::get('basic', 'BasicDataController@basic')->name("api.data.basic");
//电池租赁型号
Route::get('batteryPie', 'BasicDataController@batteryPie')->name("api.data.batteryPie");
//电池租赁趋势
Route::get('batteryTrend', 'BasicDataController@batteryTrend')->name("api.data.batteryTrend");
//网点租赁电池库存
Route::get('serviceStock', 'BasicDataController@serviceStock')->name("api.data.serviceStock");
















 //租点首页
//个人省份权限
Route::get('managerMenu', 'SummaryController@managerMenu')->name("api.managerMenu");
//业务数据
Route::get('businessData', 'SummaryController@businessData')->name("api.businessData");
//财务数据
Route::get('financialData', 'SummaryController@financialData')->name("api.financialData");
//租赁成交量
Route::get('LeaseVolume', 'SummaryController@LeaseVolume')->name("api.LeaseVolume");
//网点新增排行
Route::get('serviceRank', 'SummaryController@serviceRank')->name("api.serviceRank");

//快点首页
    Route::get('kdBasic', 'KdBasicDataController@basic')->name("api.data.kdBasic");
//快点d电池型号占比
Route::get('batteyType', 'KdBasicDataController@batteyType')->name("api.data.batteyType");
////快点d电池销售趋势
Route::get('saleTrend', 'KdBasicDataController@saleTrend')->name("api.data.saleTrend");
//快点d电池销售排行
Route::get('batterySale', 'KdBasicDataController@batterySale')->name("api.data.batterySale");
//快点d电池销售排行
Route::get('batterySaleman', 'KdBasicDataController@batterySaleman')->name("api.data.batterySaleman");