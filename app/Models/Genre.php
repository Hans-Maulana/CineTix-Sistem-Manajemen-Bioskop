<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'genre_name',
    ];

    
    public function films()
    {
        return $this->belongsToMany(Film::class, 'genre_film', 'genre_id', 'film_id');
    }
}
