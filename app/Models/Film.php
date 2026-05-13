<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Film extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'synopsis',
        'duration',
        'rating',
        'actors',
        'director',
        'production',
        'status',
        'classification',
        'cover',
        'release_date',
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

    /**
     * Get the URL for the film cover.
     */
    public function getCoverUrlAttribute()
    {
        if ($this->cover && (str_starts_with($this->cover, 'http') || file_exists(public_path('storage/cover/' . $this->cover)))) {
            return str_starts_with($this->cover, 'http') ? $this->cover : asset('storage/cover/' . $this->cover);
        }
        
        return asset('storage/cover/default-cover.svg'); // Default template image
    }
}
