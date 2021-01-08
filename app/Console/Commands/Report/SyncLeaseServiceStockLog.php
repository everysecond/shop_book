<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseServiceStockLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseServiceStockLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步网点库存日志';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new \App\Jobs\SyncLeaseServiceStockLog());
    }
}
