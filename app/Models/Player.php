<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Score;

class Player extends Model
{
    use HasFactory;

    protected $table = 'players';
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'username',
    ];

    public $timestamps = true;

    public function scores()
    {
        return $this->hasMany(Score::class, 'user_id', 'id');
    }
}
