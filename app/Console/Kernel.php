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
    protected $commands = [
        Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //$schedule->command('inspire')
        //         ->hourly();

        //$schedule->call('App\Modules\Product\Controllers\ProductDetailController@migrateFileProductVisit')
        //        ->everyMinute();

        $schedule->call('App\Modules\Account\Controllers\AccountController@benkaStampActivation')
                ->daily();
        $schedule->call('App\Modules\Account\Controllers\AccountController@benkaStampEmailNotif')
                ->dailyAt('08:00');;
    }
}
