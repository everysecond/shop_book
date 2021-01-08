<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseServiceSupplie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseServiceSupplie';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步网点每日库存补货表的统计';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new \App\Jobs\SyncLeaseServiceSupplie());
    }
}
