<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseService';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步租点服务点表';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步租点服务点表任务开始!");
        dispatch(new \App\Jobs\SyncLeaseService());
    }
}
