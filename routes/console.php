<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fetch cada 2 horas en horario de actividad (7am - 11pm)
Schedule::command('fetch:reddit-posts')
    ->everyTwoHours()
    ->between('7:00', '23:00')
    ->withoutOverlapping()
    ->runInBackground();

// Fetch express cada 30 min solo keywords con proyectos activos
Schedule::command('fetch:reddit-posts')
    ->everyThirtyMinutes()
    ->between('9:00', '21:00')
    ->withoutOverlapping(10)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/reddit-fetcher.log'));
