<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseServiceCancel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseServiceCancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步网点每日补货/退货/回收统计';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new \App\Jobs\SyncLeaseServiceCancel());
    }
}
