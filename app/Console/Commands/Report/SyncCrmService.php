<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncCrmService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:CrmService';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步CRM服务点表';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步CRM服务点表任务开始!");
        dispatch(new \App\Jobs\SyncCrmService());
    }
}
