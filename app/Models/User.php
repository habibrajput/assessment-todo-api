<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'verification_code',
        'is_verified',
        'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token', 'verification_code'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified'       => 'boolean',
        'password'          => 'hashed',
    ];

    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function generateVerificationCode(): string
    {
        $code = Str::random(64);
        $this->update(['verification_code' => $code, 'is_verified' => false]);
        return $code;
    }

    public function markAsVerified(): void
    {
        $this->update([
            'is_verified' => true,
            'verification_code' => null,
            'email_verified_at' => now(),
        ]);
    }

    public function isNotVerified(): bool
    {
        return !$this->is_verified;
    }

    public function toPublicArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'email' => $this->email];
    }
}
