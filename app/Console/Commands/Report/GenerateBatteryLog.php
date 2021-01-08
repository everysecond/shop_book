<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateBatteryLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gene:BatteryLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成租赁合约电池型号统计表';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成租赁合约电池型号统计表任务开始!");
        dispatch(new \App\Jobs\GenerateBatteryLog());
    }
}
