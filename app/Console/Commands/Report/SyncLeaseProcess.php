<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseProcess';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步租点统计每个小时从登录到租赁的到达';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步统计每个小时从登录到租赁的到达的任务开始!");
        dispatch(new \App\Jobs\SyncLeaseProcess());
    }
}
