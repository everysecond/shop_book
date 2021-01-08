<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseServiceWithdraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseServiceWithdraw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步网点提现';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步网点提现开始!");
        dispatch(new \App\Jobs\SyncLeaseServiceWithdraw());
    }
}
