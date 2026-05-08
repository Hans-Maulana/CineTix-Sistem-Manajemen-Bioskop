<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'film_id',
        'rating',   
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function film()
    {
        return $this->belongsTo(Film::class);
    }


    public function createReview(array $data): bool
    {
        return (bool) self::create($data);
    }

    public function updateReview(array $data): bool
    {
        return $this->update($data);
    }

    public function deleteReview(): bool
    {
        return (bool) $this->delete();
    }
}
