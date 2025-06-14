<?php

namespace Database\Factories;

use App\Models\PromoCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromoCodeFactory extends Factory
{
    protected $model = PromoCode::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('???###')),
            'type' => $this->faker->randomElement(['fixed', 'percentage']),
            'discount' => $this->faker->randomFloat(2, 5, 100),
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'usage_limit' => $this->faker->numberBetween(0, 1000),
            'usage_per_user' => $this->faker->numberBetween(0, 10)
        ];
    }
} 