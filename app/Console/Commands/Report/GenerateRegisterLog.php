<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateRegisterLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gene:RegisterLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成注册统计日志';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成注册统计日志任务开始!");
        dispatch(new \App\Jobs\GenerateRegisterTodayLog());
        dispatch(new \App\Jobs\GenerateRegisterTotalLog());
    }
}
