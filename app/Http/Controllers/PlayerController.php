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

    public function history(Request $request, int $user_id): JsonResponse
    {
        $limit = min((int) $request->query('limit', 25), 100);
        $offset = (int) $request->query('offset', 0);

        $player = Player::with([
            'scores' => function($q) use($limit, $offset) {
                $q->orderByDesc('ended_at')
                  ->offset($offset)
                  ->limit($limit)
                  ->with('beatmap.beatmapset');
            }
        ])->find($user_id);

        if (!$player)
        {
            return response()->json([
                'error' => 'Player not tracked.',
            ], 404);
        }

        return (new PlayerResource($player))->response();
    }
}
