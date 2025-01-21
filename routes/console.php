<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('recipes:fetch')->daily();
Schedule::command('recipes:scrape')->everyMinute();
Schedule::command('recipes:process')->everyMinute();
