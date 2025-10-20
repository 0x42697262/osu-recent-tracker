<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlayerController;
use App\Models\Player;

Route::prefix('v1')->group(function (){
    Route::get('players', [PlayerController::class, 'index']);
    Route::get('player/{user_id}', [PlayerController::class, 'history']);
});
