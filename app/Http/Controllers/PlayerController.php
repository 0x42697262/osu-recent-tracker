<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Resources\PlayerResource;
use App\Models\Player;

class PlayerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $offset = (int) $request->query('offset', 0);

        $players = Player::offset($offset)
            ->limit(100)
            ->get();

        return PlayerResource::collection($players)->response();
    }
}
