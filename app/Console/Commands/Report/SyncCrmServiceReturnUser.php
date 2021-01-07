<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncCrmServiceReturnUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:CrmServiceReturnUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步Crm 租点C端用户';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步Crm 租点C端用户任务开始!");
        dispatch(new \App\Jobs\SyncCrmServiceReturnUser());
    }
}
