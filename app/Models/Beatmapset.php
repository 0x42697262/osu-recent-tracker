<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beatmapset extends Model
{
    use HasFactory;

    protected $table = 'beatmapsets';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'artist',
        'artist_unicode',
        'creator',
        'title',
        'title_unicode',
        'user_id',
    ];

    public $timestamps = true;

    public function beatmaps()
    {
        return $this->hasMany(Beatmap::class, 'beatmapset_id', 'id');
    }

}
