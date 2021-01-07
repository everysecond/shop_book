<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAppDownLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gene:AppDownLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成app下载统计日志';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成app下载统计日志任务开始!");
        dispatch(new \App\Jobs\GenerateAppDownLog());
    }
}
