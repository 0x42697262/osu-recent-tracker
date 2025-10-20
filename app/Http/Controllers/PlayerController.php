<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Resources\PlayerResource;
use App\Models\Player;

class PlayerController extends Controller
{
    public function index(): JsonResponse
    {
        $players = Player::all();
        return response()->json($players);
    }
}
