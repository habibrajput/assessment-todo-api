<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * Represents an application user. Implements JWTSubject so tymon/jwt-auth
 * can build tokens directly from this model.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $password
 * @property string|null $verification_code   Random token sent in the welcome e-mail
 * @property bool        $is_verified         True once the user clicks the link
 * @property \Carbon\Carbon|null $email_verified_at
 *
 * @package App\Models
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    // Mass-assignable fields
    protected $fillable = [
        'name',
        'email',
        'password',
        'verification_code',
        'is_verified',
    ];

    // Hidden from serialization
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',   // keep the code out of API responses
    ];

    // Casts
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified'       => 'boolean',
        'password'          => 'hashed',
    ];

    // Relationships

    /**
     * A user owns many to-do items.
     */
    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    // JWTSubject contract

    /**
     * Get the identifier that will be stored inside the JWT subject claim.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key-value array of custom claims to add to the JWT payload.
     * Keep this lean – tokens are sent with every request.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
