<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Resources\PlayerResource;
use App\Models\Player;
use App\Models\Score;

class PlayerController extends Controller
{
    public function index(): JsonResponse
    {
        $players = Player::all();
        return response()->json($players);
    }

    public function history(int $user_id): JsonResponse
    {
        $player = Player::with(['scores' => function($q){
            $q->orderByDesc('ended_at')->limit(100);
        }])->find($user_id);
        $username = $player->username;

        if (!$player)
        {
            return response()->json([
                'error' => 'Player not tracked.',
            ], 404);
        }

        $scores = Score::where('user_id', $user_id)
                    ->orderByDesc('ended_at')
                    ->limit(100)
                    ->get();

        return (new PlayerResource($player))->response();
    }
}
