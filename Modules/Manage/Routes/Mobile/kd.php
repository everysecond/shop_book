<?php
//快点首页
//快点站点权限
Route::get('siteMenu', 'KdHomeController@siteMenu')->name("api.siteMenu");
//业务简报数据
Route::get('simple', 'KdHomeController@simple')->name("api.data.simple");
//电池回收排行
Route::get('batteryRecycle', 'KdHomeController@batteryRecycle')->name("api.data.batteryRecycle");
//业务员回收排行
Route::get('managersRank', 'KdHomeController@managersRank')->name("api.data.managersRank");
//电池回收趋势
Route::get('recycleTrend', 'KdHomeController@recycleTrend')->name("api.data.recycleTrend");
//电池回收占比
Route::get('recyclePie', 'KdHomeController@recyclePie')->name("api.data.recyclePie");

//商家商户分析
//商户增长趋势
Route::get('userTrend', 'KdUserController@userTrend')->name("api.data.userTrend");
//待审核商家
Route::get('toAuth', 'KdUserController@toAuth')->name("api.data.toAuth");
//商家分布
Route::get('userArea', 'KdUserController@userArea')->name("api.data.userArea");
//实名认证
Route::get('authenticated', 'KdUserController@authenticated')->name("api.data.authenticated");

//商家钱包--余额区间分布
Route::get('balanceRange', 'KdUserController@balanceRange')->name("api.data.balanceRange");
//各区域分布
Route::get('balanceArea', 'KdUserController@balanceArea')->name("api.data.balanceArea");

//预付款--区间分布
Route::get('advanceRange', 'KdUserController@advanceRange')->name("api.data.advanceRange");
//各区域分布
Route::get('advanceArea', 'KdUserController@advanceArea')->name("api.data.advanceArea");

//专用金--区间分布
Route::get('specialRange', 'KdUserController@specialRange')->name("api.data.specialRange");
//各区域分布
Route::get('specialArea', 'KdUserController@specialArea')->name("api.data.specialArea");

//回收分析-未完成订单
Route::get('unfinishedRank', 'KdRecycleController@unfinishedRank')->name("api.data.unfinishedRank");
//回收订单类型占比
Route::get('recycleTypePie', 'KdRecycleController@recycleTypePie')->name("api.data.recycleTypePie");
//区域回收排行
Route::get('recycleAreaRank', 'KdRecycleController@recycleAreaRank')->name("api.data.recycleAreaRank");
//业务员回收排行
Route::get('managerAreaRank', 'KdRecycleController@managerAreaRank')->name("api.data.managerAreaRank");

//仓库库存—回收电池—库存区域分布
Route::get('stockAreaRank', 'KdRecycleController@stockAreaRank')->name("api.data.stockAreaRank");

Route::get('stocksRank', 'KdRecycleController@stocksRank')->name("api.data.stocksRank");
Route::get('sendTrend', 'KdRecycleController@sendTrend')->name("api.data.sendTrend");
Route::get('sendArea', 'KdRecycleController@sendArea')->name("api.data.sendArea");


