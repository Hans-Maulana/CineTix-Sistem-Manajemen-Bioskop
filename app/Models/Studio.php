<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Studio extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'name',
        'capacity',
        'seat_layout',
        'status', 
    ];

    protected $casts = [
        'capacity' => 'integer',
        'seat_layout' => 'array',
    ];


    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
