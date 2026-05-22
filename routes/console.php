<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fetch base cada 2 horas (7am-11pm)
Schedule::command('fetch:reddit-posts')
    ->everyTwoHours()
    ->between('7:00', '23:00')
    ->withoutOverlapping()
    ->runInBackground();

// Fetch express cada 30 min + enrichment (9am-9pm)
Schedule::command('fetch:reddit-posts --enrich')
    ->everyThirtyMinutes()
    ->between('9:00', '21:00')
    ->withoutOverlapping(10)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/reddit-fetcher.log'));
