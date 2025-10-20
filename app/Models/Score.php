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
        'record_hash',
        'user_id',
        'beatmap_id',
        'ended_at',

        'pp',
        'accuracy',
        'classic_total_score',
        'total_score',
        'legacy_total_score',
        'max_combo',

        'rank',
        'mods',
        'is_perfect_combo',
        'passed',
        'has_replay',

        'great',
        'ok',
        'meh',
        'miss',
        'ignore_hit',
        'ignore_miss',
        'large_tick_hit',
        'slider_tail_hit',
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
