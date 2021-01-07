<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeasePayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeasePayment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步租点新租订单';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步新租订单任务开始!");
        dispatch(new \App\Jobs\SyncLeasePayment());
    }
}
