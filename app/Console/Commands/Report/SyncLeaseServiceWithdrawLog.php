<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseServiceWithdrawLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseServiceWithdrawLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成网点提现统计日志';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成网点提现统计日志任务开始!");
        dispatch(new \App\Jobs\SyncLeaseServiceWithdrawLog());
    }
}
