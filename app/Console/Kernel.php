<?php

namespace App\Console;

use App\Models\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

	/**
	 * Define the application's command schedule.
	 *
	 * @param \Illuminate\Console\Scheduling\Schedule $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$search = "end_time_turn";
		$times = Configuration::where('name', 'like', '%' . $search . '%')->get();
		$end_time_db_day = $times->where('name', $search.'_day' )->first()->value;
		$end_time_db_night = $times->where('name', $search.'_night')->first()->value;

		$schedule->command('send:notification')->everyMinute();
		$schedule->command('close:attendance-sheet')->dailyAt($end_time_db_day);
		$schedule->command('close:attendance-sheet')->dailyAt($end_time_db_night);

		// $schedule->command('inspire')->hourly();
//		$schedule->call(function () {
//			error_log('Some message here.');
//		})->cron('* * * * *');	;
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
