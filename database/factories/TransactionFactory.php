<?php

namespace Database\Factories;

use App\Models\Bid;
use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $car = Car::factory()->create();
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        
        return [
            'car_id' => $car->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'bid_id' => null,
            'amount' => $this->faker->numberBetween(5000, 100000),
            'payment_method' => $this->faker->randomElement(['cash', 'bank_transfer', 'credit_card', 'debit_card']),
            'transaction_reference' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'notes' => $this->faker->paragraph(1),
        ];
    }

    /**
     * Indicate that the transaction is related to a bid.
     */
    public function withBid(): static
    {
        return $this->state(function (array $attributes) {
            $bid = Bid::factory()->create([
                'user_id' => $attributes['buyer_id'],
                'car_id' => $attributes['car_id'],
                'amount' => $attributes['amount'],
                'status' => 'accepted',
            ]);
            
            return [
                'bid_id' => $bid->id,
            ];
        });
    }

    /**
     * Indicate that the transaction is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the transaction is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the transaction has failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Indicate that the transaction was refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
        ]);
    }
} 