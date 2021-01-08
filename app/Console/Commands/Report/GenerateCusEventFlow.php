<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateCusEventFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gene:CusEventFlow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成C端活跃事件日志';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成C端活跃事件日志任务开始!");
        dispatch(new \App\Jobs\GenerateCusEventFlow());
    }
}
