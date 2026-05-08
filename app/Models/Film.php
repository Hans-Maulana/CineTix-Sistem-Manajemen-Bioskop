<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Film extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'rating',
        'actors',
        'director',
        'production',
        'status',
        'classification',
        'cover',
    ];

    protected $casts = [
        'duration' => 'integer',
        'rating'   => 'decimal:1',
    ];

    

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_film', 'film_id', 'genre_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
