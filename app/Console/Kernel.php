<?php

namespace App\Console;

use App\Console\Commands\SendLessonsCreated;
use App\Console\Commands\SendLessonsUpdated;
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
        SendLessonsCreated::class,
        SendLessonsUpdated::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('send:lessonsCreated')
            ->everyThirtyMinutes()
            ->between('7:00', '19:00');

        $schedule->command('send:lessonsUpdated')
            ->everyFifteenMinutes()
            ->between('7:00', '19:00');

        $schedule->command('telescope:prune')
            ->mondays()
            ->wednesdays()
            ->fridays()
            ->sundays();
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
