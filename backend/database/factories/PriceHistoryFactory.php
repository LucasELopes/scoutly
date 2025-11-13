<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceHistory>
 */
class PriceHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::all()->random();

        return [
            'product_id' => $product->id,
            'price' => $product->desired_price,
            'checked_at' => now()
        ];
    }
}
