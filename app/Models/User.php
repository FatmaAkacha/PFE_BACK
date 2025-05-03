<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'username', 'email', 'role_id'];

    public static function syncFromToken(array $kcUser)
    {
        return self::updateOrCreate(
            ['id' => $kcUser['sub']],
            [
                'username' => $kcUser['preferred_username'] ?? null,
                'email' => $kcUser['email'] ?? null,
            ]
        );
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}