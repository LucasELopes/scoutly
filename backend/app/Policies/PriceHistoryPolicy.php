<?php

namespace App\Policies;

use App\Models\PriceHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PriceHistoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === User::USER_ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PriceHistory $priceHistory): bool
    {
        $product = Product::findOrFail($priceHistory->product_id);

        return $user->role === User::USER_ADMIN || $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PriceHistory $priceHistory): bool
    {
        $product = Product::findOrFail($priceHistory->product_id);

        return $user->role === User::USER_ADMIN || $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PriceHistory $priceHistory): bool
    {
        $product = Product::findOrFail($priceHistory->product_id);

        return $user->role === User::USER_ADMIN || $user->id === $product->user_id;
    }
}
