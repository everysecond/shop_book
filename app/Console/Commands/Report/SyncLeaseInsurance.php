<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseInsurance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseInsurance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步租点投保统计';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步租点投保统计任务开始!");
        dispatch(new \App\Jobs\SyncLeaseInsurance());
    }
}
