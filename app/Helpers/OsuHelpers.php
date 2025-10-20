<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class OsuHelpers
{
    public static function computeRecordHash(array $score): string
    {
        $fields = [
            $score['user_id'],
            $score['beatmap']['id'],
            $score['ended_at'],
            $score['rank'],
            $score['accuracy'],
            $score['max_combo'],
            $score['classic_total_score'],
            $score['total_score'],
            $score['legacy_total_score'],
            json_encode($score['mods']),
        ];

        $hashInput = implode('|', array_map(fn($f) => (string) $f, $fields));

        return md5($hashInput, true);
    }
}
