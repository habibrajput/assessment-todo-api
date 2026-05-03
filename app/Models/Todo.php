<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (!empty($keyword)) {
            $query->where('title', 'like', '%' . $keyword . '%');
        }
        return $query;
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    public function toPublicArray(): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
