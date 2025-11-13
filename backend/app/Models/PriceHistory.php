<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    /** @use HasFactory<\Database\Factories\PriceHistoryFactory> */
    use HasFactory, HasUuids;

    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'price',
        'checked_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

}
