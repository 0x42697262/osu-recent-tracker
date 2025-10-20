<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\ScoreResource;
use App\Models\Player;
use App\Models\Score;

class ScoreController extends Controller
{
    public function history(Request $request, $user_id): JsonResponse
    {
        $validated = Validator::make(['user_id' => $user_id], [
            'user_id' => 'required|integer',
        ]);

        if ($validated->fails()) {
            return response()->json(['error' => 'user_id must be an integer'], 400);
        }

        $limit = min((int) $request->query('limit', 25), 100);
        $offset = (int) $request->query('offset', 0);
        $player = Player::find($user_id);

        if (!$player)
        {
            return response()->json(['error' => 'Player not tracked.'], 404);
        }

        $scores = Score::with('beatmap.beatmapset')
            ->where('user_id', $user_id)
            ->orderByDesc('ended_at')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'id'        => $player->id,
            'username'  => $player->username,
            'history'   => ScoreResource::collection($scores),
        ]);
    }
}
