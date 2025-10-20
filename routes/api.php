<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ScoreController;
use App\Models\Player;

Route::prefix('v1')->group(function (){
    Route::get('players', [PlayerController::class, 'index']);
    Route::get('history/{user_id}', [ScoreController::class, 'history']);
});
