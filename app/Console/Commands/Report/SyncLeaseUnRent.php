<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncLeaseUnRent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:LeaseUnRent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步租点续租统计';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new \App\Jobs\SyncLeaseUnRent());
    }
}
