<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// مزامنة تلقائية كل 15 دقيقة
Schedule::command('woocommerce:sync --type=all')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// مزامنة الطلبات كل 5 دقائق (لأنها تتغير كثيراً)
Schedule::command('woocommerce:sync --type=orders')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();
