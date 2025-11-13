<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'url',
        'desired_price'
    ];

    protected function casts(): array
    {
        return [
            'desired_price' => 'double',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function price_histories() {
        return $this->hasMany(PriceHistory::class, 'product_id', 'id');
    }

}
