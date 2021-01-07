<?php
//公海规则流入设置
Route::get('rule_settings', 'RuleSettingsController@index')->name("crm.view.rule_settings");
Route::post('rule_create', 'RuleSettingsController@ruleCreate')->name("crm.reports.rule_create");

Route::get('export', 'SeaCustomerController@export')->name("crm.reports.export");
//公海客户
Route::get('sea_customer', 'SeaCustomerController@index')->name("crm.view.sea_customer");
Route::post('sea_customer_search', 'SeaCustomerController@seaCustomerSearch')->name("crm.reports.sea_customer_search");
Route::get('sea_customer_detail', 'SeaCustomerController@seaCustomerDetail')->name("crm.reports.sea_customer_detail");
Route::post('sea_customer_claim', 'SeaCustomerController@seaCustomerClaim')->name("crm.reports.sea_customer_claim");
Route::get('sea_customer_distribute_view', 'SeaCustomerController@seaCustomerDistributeView')->name("crm.reports.sea_customer_distribute_view");
Route::post('sea_customer_distribute', 'SeaCustomerController@seaCustomerDistribute')->name("crm.reports.sea_customer_distribute");
Route::get('sea_customer_transfer_view', 'SeaCustomerController@seaCustomerTransferView')->name("crm.reports.sea_customer_transfer_view");
Route::post('sea_customer_transfer', 'SeaCustomerController@seaCustomerTransfer')->name("crm.reports.sea_customer_transfer");
//Route::get('sea_customer_transfer_view', 'SeaCustomerController@seaCustomerTransferView')->name("crm.reports.sea_customer_transfer_view");
Route::get('sea_customer_team/{id}', 'SeaCustomerController@seaCustomerTeam')->name("crm.reports.sea_customer_team");
Route::get('sea_customer_team_del/{id}', 'SeaCustomerController@seaCustomerTeamDel')->name("crm.reports.sea_customer_team_del");
Route::get('sea_customer_team_add/{id}', 'SeaCustomerController@seaCustomerTeamAdd')->name("crm.reports.sea_customer_team_add");
Route::post('sea_customer_team_create', 'SeaCustomerController@seaCustomerTeamCreate')->name("crm.reports.sea_customer_team_create");
//网点合同
Route::get('service_contract', 'ServiceContractController@index')->name("crm.view.service_contract");
Route::post('service_contract_search', 'ServiceContractController@serviceContractSearch')->name("crm.reports.service_contract_search");
Route::get('service_contract_detail', 'ServiceContractController@serviceContractDetail')->name("crm.reports.service_contract_detail");
//详情页合同列表
Route::get('sea_customer_contract/{id}', 'SeaCustomerController@seaCustomerContract')->name("crm.reports.sea_customer_contract");

//租点合同
Route::get('lease_contract', 'LeaseContractController@index')->name("crm.view.lease_contract");
Route::post('lease_contract_search', 'LeaseContractController@leaseContractSearch')->name("crm.reports.lease_contract_search");
Route::get('lease_contract_detail', 'LeaseContractController@leaseContractDetail')->name("crm.reports.lease_contract_detail");


//工作台
Route::get('console', 'ConsoleController@index')->name('console.index');
Route::post('data/brief', 'ConsoleController@brief')->name('console.data.brief');
Route::get('plan/index', 'PlanController@planIndex')->name('plan.plan_index');
Route::post('performanceTrend', 'ConsoleController@performanceTrend')->name('console.performanceTrend');
Route::post('performanceRank', 'ConsoleController@performanceRank')->name('console.performanceRank');
//客户管理
Route::resource('cus', 'CustomerController')->except('show');
Route::get('cus/detail/{id}', 'CustomerController@detail')->name('cus.detail');
Route::get('cus/paginate', 'CustomerController@paginate')->name('cus.paginate');
Route::put('cus/mark/{id}', 'CustomerController@mark')->name('cus.mark');
Route::put('cus/top/{id}', 'CustomerController@top')->name('cus.top');
Route::get('cus/move', 'CustomerController@moveView')->name('cus.move.view');
Route::post('cus/move', 'CustomerController@move')->name('cus.move');
Route::get('cus/move_sea', 'CustomerController@moveSeaView')->name('cus.move.sea.view');
Route::post('cus/move_sea', 'CustomerController@moveSea')->name('cus.move.sea');
Route::get('cus/search', 'CustomerController@selfCus')->name('cus.search');
Route::get('cus/change', 'CustomerController@contactsAndTeams')->name('cus.change.search');
Route::get('cus/down', 'CustomerController@down')->name('cus.down');
//跟进记录管理
Route::resource('plan', 'PlanController')->except('show');
Route::get('plan/paginate', 'PlanController@paginate')->name('plan.paginate');
Route::post('plans', 'PlanController@plans')->name('plan.console');
//职位管理
Route::resource('position', 'PositionController')->except('show');
Route::post('positions', 'PositionController@show')->name('position.show');
//职员管理
Route::resource('staff', 'PositionStaffController')->except('show');
Route::get('staff/paginate', 'PositionStaffController@paginate')->name('staff.paginate');
//公海管理
Route::resource('sea', 'SeaController')->except('show');
Route::post('seas', 'SeaController@show')->name('sea.show');
//公海人员管理
Route::resource('sea-staff', 'SeaStaffController')->except('show');
Route::get('sea-staff/paginate', 'SeaStaffController@paginate')->name('sea-staff.paginate');
//CRM客户联系人管理
Route::resource('contact', 'ContactController')->except('show');
Route::get('contact/creates', 'ContactController@createByConsole')->name('contact.create.by.console');
Route::get('contact/paginate', 'ContactController@paginate')->name('contact.paginate');
Route::get('contact/paginate/{id}', 'ContactController@paginateByCus')->name('contact.paginate.cus');
//查询子区域
Route::post('agent/search/{id}', 'CustomerController@agent')->name('agent.search');
//数据字典
Route::resource('dict', 'DictController')->except('show');
Route::get('dict/paginate', 'DictController@paginate')->name('dict.paginate');
//图片上传
Route::post('upload', 'UploadController@uploadImage')->name('crm.upload');

Route::get('config/update', 'DictController@config');
