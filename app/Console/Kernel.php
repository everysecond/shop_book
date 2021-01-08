<?php

namespace App\Console;

use App\Console\Commands\GenerateAppDownLog;
use App\Console\Commands\GenerateBatteryLog;
use App\Console\Commands\GenerateBigViewCache;
use App\Console\Commands\GenerateEventFlow;
use App\Console\Commands\GenerateEventLog;
use App\Console\Commands\GenerateCusEventFlow;
use App\Console\Commands\GenerateCusEventLog;
use App\Console\Commands\GenerateIncomeLog;
use App\Console\Commands\GenerateRegisterLog;
use App\Console\Commands\GenerateServiceStockLog;
use App\Console\Commands\SyncLeaseServiceStockLog;
use App\Console\Commands\SyncServiceStock;
use App\Console\Commands\SyncServiceStockLog;
use App\Console\Commands\GenerateStartLog;
use App\Console\Commands\GenerateStartTerminalLog;
use App\Console\Commands\SyncCrmLeaseInflowUser;
use App\Console\Commands\SyncCrmLeaseReturnUser;
use App\Console\Commands\SyncCrmLeaseUser;
use App\Console\Commands\SyncCrmSeaUser;
use App\Console\Commands\SyncCrmService;
use App\Console\Commands\SyncCrmServiceNoRentUser;
use App\Console\Commands\SyncCrmServiceReturnRentUser;
use App\Console\Commands\SyncCrmServiceReturnUser;
use App\Console\Commands\SyncCrmServiceUser;
use App\Console\Commands\SyncCrmUser;
use App\Console\Commands\SyncDailyContractInfo;
use App\Console\Commands\SyncLeaseAdvanceRenewal;
use App\Console\Commands\SyncLeaseContract;
use App\Console\Commands\SyncLeaseLossUserNum;
use App\Console\Commands\SyncLeasePayment;
use App\Console\Commands\SyncLeaseProcess;
use App\Console\Commands\SyncLeaseProcessRegister;
use App\Console\Commands\SyncLeaseProcessChannel;
use App\Console\Commands\SyncLeaseProcessPeriod;
use App\Console\Commands\SyncLeaseServiceCancel;
use App\Console\Commands\SyncLeaseServiceStatistic;
use App\Console\Commands\SyncLeaseRenewal;
use App\Console\Commands\SyncLeaseRentRebate;
use App\Console\Commands\SyncLeaseService;
use App\Console\Commands\SyncLeaseServiceBalanceLog;
use App\Console\Commands\SyncLeaseServiceRetire;
use App\Console\Commands\SyncLeaseServiceStock;
use App\Console\Commands\SyncLeaseServiceSupplie;
use App\Console\Commands\SyncLeaseServiceWithdraw;
use App\Console\Commands\SyncLeaseServiceWithdrawLog;
use App\Console\Commands\SyncLeaseServiceWithdrawRate;
use App\Console\Commands\SyncLeaseUnRent;
use App\Console\Commands\SyncLeaseUser;
use App\Console\Commands\SyncLeaseRentChange;
use App\Console\Commands\SyncLeaseInsurance;
use App\Console\Commands\SyncLeaseRenewalReport;
use App\Console\Commands\SyncLeaseRentRebateReport;
use App\Console\Commands\SyncLeaseServiceExchange;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SyncLeaseUser::class,
        SyncLeaseContract::class,
        SyncLeasePayment::class,
        SyncLeaseService::class,
        SyncLeaseServiceBalanceLog::class,
        SyncDailyContractInfo::class,
        SyncCrmUser::class,
        SyncServiceStockLog::class,
        SyncServiceStock::class,
//        SyncLeaseServiceStockLog::class,
        GenerateServiceStockLog::class,
        GenerateRegisterLog::class,
        GenerateStartLog::class,
        GenerateEventFlow::class,
        GenerateEventLog::class,
        GenerateCusEventFlow::class,
        GenerateCusEventLog::class,
        GenerateStartTerminalLog::class,
        GenerateBatteryLog::class,
        GenerateIncomeLog::class,
        GenerateBigViewCache::class,
        GenerateAppDownLog::class,

        SyncLeaseRenewal::class,
        SyncLeaseProcess::class,
        SyncLeaseProcessPeriod::class,
        SyncLeaseProcessRegister::class,
        SyncLeaseProcessChannel::class,
        SyncLeaseRentRebate::class,
        SyncLeaseRentChange::class,
        SyncLeaseInsurance::class,
        SyncLeaseRenewalReport::class,
        SyncLeaseRentRebateReport::class,
        SyncLeaseServiceExchange::class,
        SyncLeaseServiceRetire::class,
        SyncLeaseServiceWithdraw::class,
        SyncLeaseServiceWithdrawLog::class,
        SyncLeaseServiceCancel::class,
        SyncLeaseServiceWithdrawRate::class,
        SyncLeaseServiceSupplie::class,

        SyncCrmLeaseUser::class,
        SyncCrmLeaseInflowUser::class,
        SyncCrmService::class,
        SyncCrmSeaUser::class,
        SyncCrmLeaseReturnUser::class,
        SyncCrmServiceUser::class,
        SyncCrmServiceNoRentUser::class,
        SyncCrmServiceReturnUser::class,
        SyncCrmServiceReturnRentUser::class,
        SyncLeaseUnRent::class,
        SyncLeaseAdvanceRenewal::class,
        SyncLeaseLossUserNum::class

    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sync:LeaseUser')->hourlyAt(1)->withoutOverlapping();
        $schedule->command('sync:LeaseContract')->hourlyAt(2)->withoutOverlapping();
        $schedule->command('sync:LeasePayment')->hourlyAt(3)->withoutOverlapping();
        $schedule->command('sync:LeaseService')->hourly()->withoutOverlapping();
        $schedule->command('sync:LeaseServiceBalanceLog')->hourlyAt(3)->withoutOverlapping();
        $schedule->command('sync:DailyContractInfo')->hourlyAt(5)->withoutOverlapping();
        $schedule->command('sync:CrmUser')->hourly()->withoutOverlapping();
        $schedule->command('sync:ServiceStockLog')->hourly()->withoutOverlapping();
        $schedule->command('sync:ServiceStock')->hourly()->withoutOverlapping();
//        $schedule->command('sync:LeaseServiceStockLog')->hourly()->withoutOverlapping();
        $schedule->command('gene:ServiceStockLog')->dailyAt('00:03')->withoutOverlapping();
        $schedule->command('gene:RegisterLog')->hourlyAt(2)->withoutOverlapping();
        $schedule->command('gene:StartLog')->hourly()->withoutOverlapping();
        $schedule->command('gene:EventFlow')->hourlyAt(2)->withoutOverlapping();
        $schedule->command('gene:EventLog')->hourlyAt(5)->withoutOverlapping();
        $schedule->command('gene:CusEventFlow')->hourlyAt(2)->withoutOverlapping();
        $schedule->command('gene:CusEventLog')->hourlyAt(5)->withoutOverlapping();
        $schedule->command('gene:StartTerminalLog')->hourlyAt(1)->withoutOverlapping();
        $schedule->command('gene:BatteryLog')->dailyAt("1:00")->withoutOverlapping();
        $schedule->command('gene:IncomeLog')->hourlyAt(4)->withoutOverlapping();
        $schedule->command('gene:AppDownLog')->hourly()->withoutOverlapping();
        $schedule->command('gene:BigViewCache')->hourly()->withoutOverlapping();

        //每小时统计登录租赁的数据 --漏斗
        $schedule->command('sync:LeaseProcess')->hourly()->withoutOverlapping();
        //每小时统计从注册到租赁的数据 --漏斗
        $schedule->command('sync:LeaseProcessRegister')->hourly()->withoutOverlapping();
        //每天统计不同注册渠道的租赁数
        $schedule->command('sync:LeaseProcessChannel')->dailyAt('00:05')->withoutOverlapping();
        //每天维护30天的数据 从注册到租赁的发起周期
        $schedule->command('sync:LeaseProcessPeriod')->dailyAt('00:06')->withoutOverlapping();
        //每天同步网点每日补货/退货/回收统计的任务
        $schedule->command('sync:LeaseServiceCancel')->dailyAt('00:08')->withoutOverlapping();
        //每天同步网点每日补货/退货/回收统计的任务(等租点系统的定时任务运行后运行)
        $schedule->command('sync:LeaseServiceSupplie')->dailyAt('05:08')->withoutOverlapping();

        $schedule->command('sync:LeaseRenewal')->dailyAt('02:00')->withoutOverlapping();
        $schedule->command('sync:LeaseUnRent')->dailyAt('02:05')->withoutOverlapping();
        $schedule->command('sync:LeaseAdvanceRenewal')->dailyAt('02:32')->withoutOverlapping();

        $schedule->command('sync:LeaseRentRebate')->dailyAt('02:10')->withoutOverlapping();
        $schedule->command('sync:LeaseRentChange')->dailyAt('02:50')->withoutOverlapping();
        $schedule->command('sync:LeaseInsurance')->dailyAt('02:20')->withoutOverlapping();
        $schedule->command('sync:LeaseLossUserNum')->dailyAt('02:22')->withoutOverlapping();

        $schedule->command('sync:LeaseRenewalReport')->dailyAt('02:30')->withoutOverlapping();
        $schedule->command('sync:LeaseMonthRenewal')->dailyAt('02:32')->withoutOverlapping();
        $schedule->command('sync:LeaseRentRebateReport')->dailyAt('02:40')->withoutOverlapping();
        $schedule->command('sync:LeaseServiceExchange')->dailyAt('02:45')->withoutOverlapping();
        $schedule->command('sync:LeaseServiceRetire')->dailyAt('02:50')->withoutOverlapping();
        $schedule->command('sync:LeaseServiceWithdraw')->hourlyAt(8)->withoutOverlapping();
        $schedule->command('sync:LeaseServiceWithdrawRate')->dailyAt('00:15')->withoutOverlapping();
        $schedule->command('sync:LeaseServiceWithdrawLog')->hourlyAt(9)->withoutOverlapping();

        $schedule->command('sync:CrmService')->hourlyAt(3)->withoutOverlapping();
        $schedule->command('sync:CrmSeaUser')->dailyAt('02:55')->withoutOverlapping();
        $schedule->command('sync:CrmLeaseUser')->dailyAt('02:56')->withoutOverlapping();
        $schedule->command('sync:CrmLeaseInflowUser')->dailyAt('02:57')->withoutOverlapping();
        $schedule->command('sync:CrmLeaseReturnUser')->dailyAt('02:58')->withoutOverlapping();
        $schedule->command('sync:CrmServiceUser')->dailyAt('02:55')->withoutOverlapping();
        $schedule->command('sync:CrmServiceNoRentUser')->dailyAt('02:55')->withoutOverlapping();
        $schedule->command('sync:SyncCrmServiceReturnUser')->dailyAt('03:00')->withoutOverlapping();
        $schedule->command('sync:SyncCrmServiceReturnRentUser')->dailyAt('03:02')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
