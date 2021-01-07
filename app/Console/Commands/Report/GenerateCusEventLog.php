<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateCusEventLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gene:CusEventLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成C端活跃事件二次处理日志';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成C端活跃事件二次处理日志开始!");
        dispatch(new \App\Jobs\GenerateCusEventLog());
    }
}
