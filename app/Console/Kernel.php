<?php

namespace App\Console;

use App\Console\Commands\ScheduleMessaging;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Notifications\DatabaseNotification;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (app_type() == 'saas') {
            $schedule->command(\App\SuperAdmin\Commands\UpdateLicenseExpiry::class)->daily();
            $schedule->command(\App\SuperAdmin\Commands\NotifyLicenseExpiryPre::class)->daily();
        }

        $schedule->command(ScheduleMessaging::class)->everyTwoMinutes();
        
        $schedule->call(function () {
            $deleted = DatabaseNotification::where(function($q) {
                    // notificaciones leídas hace más de 1 hora (para test)
                    $q->whereNotNull('read_at')
                    ->where('read_at', '<', Carbon::now()->subHour());
                })
                ->orWhere(function($q) {
                    // notificaciones NO leídas con created_at > 7 días
                    $q->whereNull('read_at')
                    ->where('created_at', '<', Carbon::now()->subDays(7));
                })
                ->delete();
        })
        ->hourly()
        ->appendOutputTo(storage_path('logs/cron-scheduler.log'));

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
