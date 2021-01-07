<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//业务趋势
Route::get('renewal/rentTrend', 'Lease\\RenewalController@rentTrend')->name("lease.reports.rentTrend");
//业务统计
Route::get('renewal/rankRentRenewal', 'Lease\\RenewalController@rankRentRenewal')->name("lease.reports.rankRentRenewal");

//业务分析电池型号
Route::get('renewal/rentAnalysisType', 'Lease\\RenewalController@rentAnalysisType')->name("lease.reports.rentAnalysisType");

//业务分析电池型号
Route::get('renewal/rentAnalysisTypeNew', 'Lease\\RenewalController@rentAnalysisTypeNew')->name("lease.reports.rentAnalysisTypeNew");

//业务分析周期
Route::get('renewal/rentCycle', 'Lease\\RenewalController@rentCycle')->name("lease.reports.rentCycle");

//业务汇总
Route::get('renewal/rentSummary', 'Lease\\RenewalController@rentSummary')->name("lease.reports.rentSummary");
//Route::get('renewal/rentSummaryNew', 'Lease\\RenewalController@rentSummaryNew')->name("lease.reports.rentSummaryNew");
Route::get('renewal/rentSummaryNew/{id}', 'Lease\\RenewalController@rentSummaryNew')->name('lease.reports.rentSummaryNew');

//快点销售趋势
Route::get('renewal/saleOrder', 'Lease\\RenewalController@saleOrder')->name('lease.reports.saleOrder');

//快点回收趋势
Route::get('renewal/retrieOrder', 'Lease\\RenewalController@retrieOrder')->name('lease.reports.retrieOrder');

//快点业务统计
Route::get('renewal/koodTable', 'Lease\\RenewalController@koodTable')->name("lease.reports.koodTable");

//快点业务汇总
Route::get('renewal/koodSummaryNew', 'Lease\\RenewalController@koodSummaryNew')->name("lease.reports.koodSummaryNew");

//业务地图
Route::get('renewal/rentMap', 'Lease\\RenewalController@rentMap')->name("lease.reports.rentMap");

//移动端登录
Route::get('loginIn', 'LoginController@loginIn')->name("mobile.loginIn");
//验证码
Route::get('codeVerification', 'LoginController@codeVerification')->name("mobile.codeVerification");
Route::group(['middleware' => ["auth.api","permission.api"], 'prefix' => 'mobile', 'namespace' => 'Mobile'],function () {
    include_once base_path('modules/Manage/Routes/Mobile/mobile.php');
    include_once base_path('modules/Manage/Routes/Mobile/kd.php');
});

