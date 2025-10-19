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
            $score['id'],
            $score['accuracy'],
            $score['created_at'],
            $score['max_combo'],
            $score['rank'],
            $score['score'],
        ];

        $hashInput = implode('|', array_map(fn($f) => (string) $f, $fields));

        return md5($hashInput, true);
    }
}
