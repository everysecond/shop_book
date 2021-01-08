<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncServiceStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ServiceStock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步租点网点库存';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("同步租点网点库存开始!");
        dispatch(new \App\Jobs\SyncServiceStock());
    }
}
