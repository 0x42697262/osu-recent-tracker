<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OsuApiToken extends Model
{
    protected $table = 'osu_api_tokens';

    protected $fillable = [
        'name',
        'access_token',
        'refresh_token',
        'expires_at',
        'token_type'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Encryption Mutators
    |--------------------------------------------------------------------------
    | Encrypt tokens before storing them, decrypt when reading them.
    */

    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = Crypt::encryptString($value);
    }

    public function getAccessTokenAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function setRefreshTokenAttribute($value)
    {
        $this->attributes['refresh_token'] = $value
            ? Crypt::encryptString($value)
            : null;
    }

    public function getRefreshTokenAttribute($value)
    {
        return $value
            ? Crypt::decryptString($value)
            : null;
    }

    public static function isValid(string $name = 'osu_api_v2'): bool
    {
        $token = static::where('name', $name)->first();

        if (! $token) {
            return false;
        }

        if (! $token->expires_at) {
            return false;
        }

        return now()->lt($token->expires_at);
    }
}
