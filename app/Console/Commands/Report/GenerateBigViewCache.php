<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateBigViewCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gene:BigViewCache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成数据大屏缓存';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        systemLog("生成数据大屏缓存任务开始!");
        dispatch(new \App\Jobs\GenerateBigViewCache());
    }
}
