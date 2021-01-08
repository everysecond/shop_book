<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//根据用户类型选择首页
Route::get('/', function () {
    return redirect(route('manage.index'));
});

Route::get('login', 'LoginController@login')->name("login");
Route::post('login', 'LoginController@loginSubmit')->name("login.submit");
Route::post('logout', 'LoginController@logout')->name("logout");
Route::get('unauthorized', 'LoginViewController@unauthorized')->name("unauthorized");
//验证是否登陆中间件
Route::group([
    'middleware' => ['auth:manage', 'permission'],
    'prefix'     => 'manage'
], function () {
    //后台首页
    Route::get('/', 'ManageController@index')->name('manage.index');
    Route::get('console', 'ManageController@console')->name('manage.console');
    //菜单管理
    Route::get('menus', 'MenuViewController@index')->name("index");
    Route::get('menus/add', 'MenuController@add')->name("add");
    Route::post('menus/add', 'MenuController@store')->name("add");
    Route::put('menus/status', 'MenuController@menuStatus')->name("status");
    Route::put('menus/edit', 'MenuController@menuUpdate')->name("edit");
    Route::put('menus/sort', 'MenuController@sortUpdate')->name("sort");
    Route::delete('menus/delete', 'MenuController@menuDelete')->name("delete");
    //菜单下操作管理
    Route::get('menus/action/{menuid}', 'MenuViewController@action')->name("action");
    Route::post('menus/actionAdd', 'MenuController@addAction')->name("addaction");
    Route::delete('menus/actionDel', 'MenuController@deleteAction')->name("deleteaction");

    //后台管理员管理
    Route::get('admins', 'AdminViewController@index')->name("index");
    Route::get('admins/add', 'AdminViewController@add')->name("add");
    Route::get('admins/edit/{id}', 'AdminViewController@edit')->name("edit");
    Route::get('admins/repass', 'AdminViewController@repass')->name("repass");
    Route::get('admins/profile', 'AdminViewController@profile')->name("profile");
    Route::post('admins/repass', 'AdminController@setpass')->name("repasss");
    Route::post('admins', 'AdminController@store')->name("index");
    Route::delete('admins/del', 'AdminController@adminDelete')->name("delete");
    Route::put('admins/edit/status', 'AdminController@adminStatus')->name("status");
    Route::put('admins/edit', 'AdminController@update')->name("update");
    Route::put('admins/upload', 'AdminController@uploadPortrait')->name("upload");

    //角色管理
    Route::get('roles', 'RoleViewController@index')->name("index");
    Route::get('roles/auth/{id}', 'RoleViewController@roleAuth')->name("auth");
    Route::post('roles/roles', 'RoleController@store')->name("index");
    Route::post('roles/add', 'RoleController@store')->name("index");
    Route::put('roles/update', 'RoleController@roleUpdate')->name("update");
    Route::delete('roles/delete', 'RoleController@roleDelete')->name("delete");
    Route::put('roles/editauth', 'RoleController@editRoleAuth')->name("editauth");

    //系统日志
    Route::get('logs', 'LogViewController@index')->name("index");
    Route::get('logs/lists', 'LogController@lists')->name("lists");

    //租点用户端报表统计
    Route::group(['prefix' => 'lease', 'namespace' => 'Lease'], function () {
        include_once base_path('modules/Manage/Routes/Lease/report.php');
    });

    //租点网点端报表统计
    Route::group(['prefix' => 'service', 'namespace' => 'Service'], function () {
        include_once base_path('modules/Manage/Routes/Service/report.php');
    });

    Route::group(['prefix' => 'crm', 'namespace' => 'Crm'], function () {
        include_once base_path('modules/Manage/Routes/Crm/crm.php');
    });

    Route::group(['prefix' => 'kood', 'namespace' => 'Kood'], function () {
        include_once base_path('modules/Manage/Routes/Kood/kood.php');
    });
});


Route::group(['middleware' => ['auth:manage', 'permission'], 'prefix' => 'reports'], function () {
    Route::get('/', 'ManageController@home')->name("reports.home");
});


Route::group(['middleware' => ['auth:manage', 'permission']], function () {
    //管理员
    Route::get('manager/permission', 'ManagerController@permission')->name('manager.permission');
    Route::put('manager/permission', 'ManagerController@putPermission')->name('manager.permission');
    Route::get('manager/paginate', 'ManagerController@paginate')->name('manager.paginate');
    Route::resource('manager', 'ManagerController')->except('show');
    //管理员角色
    Route::get('manager-role/permission', 'ManagerRoleController@permission')->name('manager-role.permission');
    Route::put('manager-role/permission', 'ManagerRoleController@putPermission')->name('manager-role.permission');
    Route::resource('manager-role', 'ManagerRoleController')->except('show');
    //管理员权限
    Route::resource('manager-permission', 'ManagerPermissionController')->except('show');
    //后台菜单
    Route::get('manage-menu/icon', 'ManageMenuController@icon')->name('manage-menu.icon');
    Route::put('manage-menu/change', 'ManageMenuController@change')->name('manage-menu.change');
    Route::put('manage-menu/change_status', 'ManageMenuController@changeStatus')->name('manage-menu.change.status');
    Route::resource('manage-menu', 'ManageMenuController')->except('show');
    Route::get('manage-menu/menus', 'ManageMenuController@menus')->name('manage-menu.menus');
});
