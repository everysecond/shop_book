<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseContract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步租点合约订单';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步租赁合约任务开始!");
        dispatch(new \App\Jobs\SyncLeaseContract());
    }
}
