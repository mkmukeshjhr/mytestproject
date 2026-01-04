<?php

namespace App\Console;

use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\HoroscopeController;
use App\Http\Controllers\Admin\SendScheduledNotificationsController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected function schedule(Schedule $schedule): void
    {

        // COMMAND: Puja Reminders (Every Minute)
        $schedule->command('puja:send-reminders')
            ->everyMinute()
            ->name('puja_send_reminders')
            ->withoutOverlapping();

        // 🗓️ CALL: Daily Horoscope Generation (12:10 AM)
        $schedule->call(function () {
            $controller = new HoroscopeController();
            $controller->generateDailyHorscope();
        })->dailyAt('00:10')
            ->name('horoscope_generate_daily')
            ->withoutOverlapping();

        // CALL: Weekly Horoscope Generation (Monday, 12:30 AM)
        // NOTE: This task and the one below are identical in timing. Ensure this is intentional.
        $schedule->call(function () {
            $controller = new HoroscopeController();
            $controller->generateWeeklyHorscope();
        })->weeklyOn(1, '00:30')
            ->name('horoscope_generate_weekly')
            ->withoutOverlapping();

        // 📅 CALL: Yearly Horoscope Generation (Monday, 12:30 AM)
        // NOTE: This runs at the same time as weekly horoscope generation.
        $schedule->call(function () {
            $controller = new HoroscopeController();
            $controller->generateYearlyHorscope();
        })->weeklyOn(1, '00:30')
            ->name('horoscope_generate_yearly')
            ->withoutOverlapping();


        // COMMAND: Scheduled Notifications (Every Minute)
        $schedule->command('notifications:send-scheduled')
            ->everyMinute()
            ->name('notifications_send_scheduled')
            ->withoutOverlapping();

        // COMMAND: Live Schedule Reminders (Every Minute)
        $schedule->command('live-schedule:reminder')
            ->everyMinute()
            ->name('live_schedule_reminder')
            ->withoutOverlapping();

        // COMMAND: Call/Chat Deletion (Every Five Minutes)
        $schedule->command('call-chat:delete')
            ->everyFiveMinutes()
            ->name('call_chat_delete')
            ->withoutOverlapping();

        // COMMAND: Reset Astro Free/Paid Status (Daily)
        $schedule->command('reset:astro-free-paid')
            ->daily()
            ->name('reset_astro_free_paid')
            ->withoutOverlapping();

        // CALL: Internal Webhook Trigger (Every Minute)
        $schedule->call(function () {
            $url = url('/sendnotificationjob');

            try {
                $response = Http::get($url);

                if ($response->successful()) {
                    Log::info('Successfully triggered internal job endpoint.');
                } else {
                    Log::error('Internal job endpoint failed.', ['status' => $response->status()]);
                }
            } catch (\Exception $e) {
                Log::critical('Network error calling internal job endpoint: ' . $e->getMessage());
            }
        })->everyMinute()
            ->name('internal_send_notification_webhook')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
