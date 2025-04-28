<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bid>
 */
class BidFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'car_id' => Car::factory(),
            'amount' => $this->faker->numberBetween(1000, 100000),
            'message' => $this->faker->paragraph(1),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected', 'expired']),
            'expiry_date' => $this->faker->dateTimeBetween('+1 week', '+4 weeks'),
        ];
    }

    /**
     * Indicate that the bid is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the bid is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    /**
     * Indicate that the bid is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Indicate that the bid is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expiry_date' => $this->faker->dateTimeBetween('-4 weeks', '-1 day'),
        ]);
    }
} 