<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fnb extends Model
{
    use HasFactory;

    protected $table = 'fnbs';

    protected $fillable = [
        'name',
        'category',       
        'current_price',
        'stock',
        'is_available',
    ];

    protected $casts = [
        'current_price' => 'decimal:2',
        'stock'         => 'integer',
        'is_available'  => 'boolean',
    ];



    public function fnbBookings()
    {
        return $this->hasMany(FnbBooking::class, 'fnb_id');
    }

    public function bundlings()
    {
        return $this->belongsToMany(Bundling::class, 'bundling_fnb', 'fnb_id', 'bundling_id');
    }



    public function updatePrice(float $newPrice): bool
    {
        $this->current_price = $newPrice;
        return $this->save();
    }

    public function isAvailable(int $qty = 1): bool
    {
        return $this->is_available && $this->stock >= $qty;
    }

    public function reduceStock(int $qty): void
    {
        $this->decrement('stock', $qty);
    }

    public function addStock(int $qty): void
    {
        $this->increment('stock', $qty);
    }

    public function createItem(array $data): bool
    {
        return (bool) self::create($data);
    }
}
