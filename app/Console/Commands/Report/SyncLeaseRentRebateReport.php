<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseRentRebateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseRentRebateReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步租点续租报表统计';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new \App\Jobs\SyncLeaseRentRebateReport());
    }
}
