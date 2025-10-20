<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeatmapsetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'artist'         => $this->artist,
            'artist_unicode' => $this->artist_unicode,
            'title'          => $this->title,
            'title_unicode'  => $this->title_unicode,
            'creator'        => $this->creator,
        ];
    }
}
