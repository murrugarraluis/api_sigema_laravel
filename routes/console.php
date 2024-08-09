<?php

use App\Models\Configuration;
use Illuminate\Support\Facades\Schedule;

if (app()->environment() !== 'testing') {
    $search = "end_time_turn";
    $times = Configuration::where('name', 'like', '%' . $search . '%')->get();
    $end_time_db_day = $times->where('name', $search . '_day')->first()->value;
    $end_time_db_night = $times->where('name', $search . '_night')->first()->value;

    Schedule::command('send:notification')->everyMinute();
    Schedule::command('close:attendance-sheet')->dailyAt($end_time_db_day);
    Schedule::command('close:attendance-sheet')->dailyAt($end_time_db_night);
}
