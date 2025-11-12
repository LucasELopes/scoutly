<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()
                ->whereNotIn('id', Subscription::pluck('user_id'))
                ->first()
                ?->id ?? User::factory(), // cria um novo user se todos já tiverem subscription
            'plan' => $this->faker->randomElement(Subscription::getPlans()),
            'active_until' => now()->addMonth(),
        ];
    }
}
