<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateEventFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gene:EventFlow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成活跃事件日志';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成活跃事件日志任务开始!");
        dispatch(new \App\Jobs\GenerateEventFlow());
    }
}
