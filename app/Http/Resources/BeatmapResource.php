<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeatmapResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $beatmapset = (new BeatmapsetResource($this->beatmapset))->toArray($request);
        $map_details = [
            'beatmap_id'        => $this->id,
            'difficulty_rating' => $this->difficulty_rating,
            'version'           => $this->version,
            'bpm'               => $this->bpm,
            'cs'                => $this->cs,
            'ar'                => $this->ar,
            'drain'             => $this->drain,
            'accuracy'          => $this->accuracy,
            'hit_length'        => $this->hit_length,
            'total_length'      => $this->total_length,
            'count_circles'     => $this->count_circles,
            'count_sliders'     => $this->count_sliders,
            'count_spinners'    => $this->count_spinners,
        ];

        return array_merge($beatmapset, $map_details);
    }
}
