<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Todo
 *
 * Represents a single to-do item that belongs to one user.
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $title
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class Todo extends Model
{
    use HasFactory;

    // Mass-assignable fields
    protected $fillable = [
        'user_id',
        'title',
        'description',
    ];

    // Relationships

    /**
     * Each todo belongs to exactly one user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
