<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseProcessPeriod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseProcessPeriod';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步统计注册租赁周期';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步统计注册租赁周期的任务开始!");
        dispatch(new \App\Jobs\SyncLeaseProcessPeriod());
    }
}
