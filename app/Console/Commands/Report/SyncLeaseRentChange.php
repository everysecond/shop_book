<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseRentChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseRentChange';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步租点换租统计';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步租点换租统计任务开始!");
        dispatch(new \App\Jobs\SyncLeaseRentChange());
    }
}
