<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Manage\Models\Report\LeaseContractDateInfo;

class SyncDailyContractInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:DailyContractInfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成每日租赁合约统计信息';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成每日租赁合约统计信息任务开始!");
        //新用户租赁
        dispatch(new \App\Jobs\GenerateNewUserDailyContractInfo());
        //老用户租赁
        dispatch(new \App\Jobs\GenerateOldUserDailyContractInfo());
    }
}
