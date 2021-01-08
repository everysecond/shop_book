<?php
//数据大屏
Route::get('actualData', 'BigViewController@actualData')->name("kood.data.actualData");
Route::get('inventory', 'BigViewController@inventory')->name("kood.data.inventory");
Route::get('stockArea/{type}', 'BigViewController@stockArea')->name("kood.data.stockArea");
Route::get('recycleRank', 'BigViewController@recycleRank')->name("kood.data.recycleRank");





