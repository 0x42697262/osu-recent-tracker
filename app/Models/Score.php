<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $table = 'scores';
    protected $primaryKey = 'id';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'beatmap_id',
        'record_hash',
        'score_id',
        'accuracy',
        'max_combo',
        'mods',
        'passed',
        'perfect',
        'pp',
        'rank',
        'score',
        'count_100',
        'count_300',
        'count_50',
        'count_geki',
        'count_katu',
        'count_miss',
        'submission_date',
    ];

    public $timestamp = true;

    protected $casts = [
        'mods' => 'array',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'user_id', 'id');
    }

    public function beatmap()
    {
        return $this->belongsTo(Beatmap::class, 'beatmap_id', 'id');
    }
}
