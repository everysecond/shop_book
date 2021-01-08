<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseServiceBalanceLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseServiceBalanceLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步网点余额变动表';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步网点余额变动表任务开始!");
        dispatch(new \App\Jobs\SyncLeaseServiceBalanceLog());
    }
}
