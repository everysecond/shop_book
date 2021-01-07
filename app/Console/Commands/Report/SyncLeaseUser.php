<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步租点用户';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步租赁用户任务开始!");
        dispatch(new \App\Jobs\SyncLeaseUser());
    }
}
