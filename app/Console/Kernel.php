<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands
        = [
            //
        ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('forms:fix')->withoutOverlapping()->everyMinute()->between('00:15', '05:00');
        $schedule->command('forms:transfer')->withoutOverlapping()->everyMinute()->between('00:15', '05:00');
        $schedule->command('companies:inspect')->monthlyOn(1, '6:00');
        $schedule->command("run:briefings")->monthlyOn(10, '10:00');
        $schedule->command('forms:fill-day-hash')
            ->withoutOverlapping()
            ->everyMinute()
            ->between('00:00', '05:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
