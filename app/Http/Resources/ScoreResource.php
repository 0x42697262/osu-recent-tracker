<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ended_at'              => $this->ended_at,
            'pp'                    => $this->pp,
            'accuracy'              => $this->accuracy,
            'classic_total_score'   => $this->classic_total_score,
            'total_score'           => $this->total_score,
            'legacy_total_score'    => $this->legacy_total_score,
            'max_combo'             => $this->max_combo,
            'rank'                  => $this->rank,
            'mods'                  => $this->mods,
            'is_perfect_combo'      => (bool) $this->is_perfect_combo,
            'passed'                => (bool) $this->passed,
            'has_replay'            => (bool) $this->has_replay,
            'statistics'            => collect([
                'great'                 => $this->great,
                'ok'                    => $this->ok,
                'meh'                   => $this->meh,
                'miss'                  => $this->miss,
                'ignore_hit'            => $this->ignore_hit,
                'ignore_miss'           => $this->ignore_miss,
                'large_tick_hit'        => $this->large_tick_hit,
                'slider_tail_hit'       => $this->slider_tail_hit,
            ])->reject(fn ($value) => $value == 0)->all(),
            'maximum_statistics'    => collect([
                'legacy_combo_increase' => $this->legacy_combo_increase,
                'great'                 => $this->max_great,
                'ignore_hit'            => $this->max_ignore_hit,
                'large_tick_hit'        => $this->max_large_tick_hit,
                'slider_tail_hit'       => $this->max_slider_tail_hit,
            ])->reject(fn ($value) => $value == 0)->all(),
            'beatmap'               => BeatmapResource::make($this->whenLoaded('beatmap')),
        ];
    }
}
