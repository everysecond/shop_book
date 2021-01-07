<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateServiceStockLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gene:ServiceStockLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成网点库存统计日志';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成网点库存统计日志开始!");
        dispatch(new \App\Jobs\GenerateServiceStockLog());
    }
}
