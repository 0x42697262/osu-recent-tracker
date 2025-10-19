<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beatmap extends Model
{
    use HasFactory;

    protected $table = 'beatmaps';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'beatmapset_id',
        'difficulty_rating',
        'user_id',
        'total_length',
        'version',
        'checksum',
        'bpm',
        'cs',
        'ar',
        'drain',
        'accuracy',
        'hit_length',
        'count_circles',
        'count_sliders',
        'count_spinners',
        'last_updated',
    ];

    public $timestamps = true;

    public function beatmapset()
    {
        return $this->belongsTo(Beatmapset::class, 'beatmapset_id', 'id');
    }
}
