<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Klantbehoud draait op de klok: eerst de beheer-digest, daarna de klantmails.
// Vereist één cronregel op de server: * * * * * php artisan schedule:run
Schedule::command('mails:digest')->dailyAt('08:00');
Schedule::command('mails:lifecycle')->dailyAt('10:00');

// De blog-machine: maandag de onderwerpenlijst aanvullen, elke ochtend één
// concept schrijven. De review-wachtrij-cap is de rem: zit die vol, dan
// slaat de schrijver over tot er in het beheer beoordeeld is.
Schedule::command('blog:plan')->weeklyOn(1, '07:00');
Schedule::command('blog:schrijf')->dailyAt('07:30');
