<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateKdUserBalanceLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gene:KdUserBalanceLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成快点商家余额变动统计日志表';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成快点商家余额变动统计日志表任务开始!");
        dispatch(new \App\Jobs\GenerateKdUserBalanceLog());
    }
}
