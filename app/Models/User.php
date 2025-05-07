<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'username', 'email', 'password', 'role_id'];

    // Automatically hash the password
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

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

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}