<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseProcessRegister extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseProcessRegister';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步统计租点每个小时从注册到租赁的到达';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步统计租点每个小时从注册到租赁的到达的任务开始!". date("Y-m-d H:i:s", strtotime("-1 hour"))."");
        dispatch(new \App\Jobs\SyncLeaseProcessRegister());
    }
}
