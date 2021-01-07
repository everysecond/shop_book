<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseProcessChannel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseProcessChannel';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步统计租点渠道从登录到租赁的到达';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步统计租点每天通过渠道到租赁的到达的任务开始!");
        dispatch(new \App\Jobs\SyncLeaseProcessChannel());
    }
}
