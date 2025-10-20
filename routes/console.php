<?php

use Illuminate\Support\Facades\Schedule;

use App\Console\Commands\FetchOsuScores;


Schedule::command(FetchOsuScores::class)->withoutOverlapping()
                                        ->everyMinute();
