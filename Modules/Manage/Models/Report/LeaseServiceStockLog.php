<?php

namespace Modules\Manage\Models\Report;

use Modules\Manage\Models\Model;

class LeaseServiceStockLog extends Model
{
    const LOG_TYPE_ARR = [
        '0'  => '新租抵押旧电池',
        '1'  => '新租电池出库',
        '2'  => '退租电池入库',
        '3'  => '换租退回电池入库',
        '4'  => '换租备用电池出库',
        '5'  => '网点废旧电池出库',
        '6'  => '网点未出售全新电池退回仓库',
        '7'  => '网点未出售备用电池退回仓库',
        '8'  => '网点出售后客户退回电池退回仓库',
        '9'  => '网点全新电池补货',
        '10' => '网点备用电池补货'
    ];
}
