<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Modules\Kood\Models\Site;
use Modules\Kood\Models\UserBalanceRecord;
use Modules\Lease\Models\BlAppDown;
use Modules\Manage\Models\Report\KdUserBalanceLog;
use Modules\Manage\Models\Report\LeaseAppDownLog;

class GenerateKdUserBalanceLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //最多运行5次
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $maxDate = KdUserBalanceLog::query()->max("date");
            $begin = $end = date("Y-m-d");
            if (!$maxDate) {
                $firstLog = UserBalanceRecord::query()->min("created_at");
                $begin = $firstLog ? date("Y-m-d", $firstLog) : $begin;
            } else {
                $begin = $maxDate;
                KdUserBalanceLog::query()->whereBetween("date", [$begin, $end])->delete();
            }
            $sitesArr = array();
            $sites = Site::sites(false);
            foreach ($sites as $id => $site) {
                $sitesArr[$id] = [
                    'name'  => $site,
                    'value' => 0
                ];
            }
            $dateRange = getDateRange($begin, $end);
            $result = array();
            foreach ($dateRange as $date) {
                $data = UserBalanceRecord::query()
                    ->selectRaw("site_id,"
                        . "SUBSTRING_INDEX(GROUP_CONCAT(balance ORDER BY created_at desc ),',',1) as value")
                    ->where('created_at', "<=", strtotime($date) + 86400)
                    ->groupBy(['user_id'])->get()->toArray();
                $result[$date] = $sitesArr;
                foreach ($data as $datum) {
                    $siteId = $datum['site_id'];
                    Arr::set($result, "$date.$siteId.value", Arr::get($result, "$date.$siteId.value") + $datum['value']);
                }
                unset($data);
            }

            $log = array();
            foreach ($result as $date => $value) {
                if (count($log) > 100) {
                    KdUserBalanceLog::query()->insert($log);
                    $log = array();
                }
                $total = 0;
                foreach ($value as $item) {
                    $total += $item['value'];
                }
                $log[] = [
                    'date'  => $date,
                    'json'  => json_encode($value),
                    'total' => $total
                ];
            }
            KdUserBalanceLog::query()->insert($log);
            systemLog("生成快点商家余额变动统计日志表成功");
        } catch (\Exception $e) {
            \Log::error("生成快点商家余额变动统计日志表失败: {$e->getMessage()}");
            systemLog("生成快点商家余额变动统计日志表失败: 详情请见laravel-log");
        }
    }

    /**
     * @param \Exception $exception
     */
    public function failed(\Exception $exception)
    {
        \Log::error("生成快点商家余额变动统计日志表失败：{$exception->getMessage()}");
        systemLog("生成快点商家余额变动统计日志表失败: 详情请见laravel-log");
    }
}
