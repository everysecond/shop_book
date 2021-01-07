<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncCrmServiceUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:CrmServiceUser';

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
        dispatch(new \App\Jobs\SyncCrmServiceUser());
    }
}
