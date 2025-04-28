<?php

namespace Database\Factories;

use App\Models\Bid;
use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
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
            'bid_id' => null,
            'appointment_date' => $this->faker->dateTimeBetween('+1 day', '+2 weeks'),
            'notes' => $this->faker->paragraph(1),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'completed', 'cancelled']),
            'is_test_drive' => $this->faker->boolean(70), // 70% chance of being a test drive
            'is_purchase_appointment' => $this->faker->boolean(30), // 30% chance of being a purchase appointment
        ];
    }

    /**
     * Indicate that the appointment is related to a bid.
     */
    public function withBid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'bid_id' => Bid::factory()->create([
                    'user_id' => $attributes['user_id'],
                    'car_id' => $attributes['car_id'],
                ])->id,
                'is_purchase_appointment' => true,
            ];
        });
    }

    /**
     * Indicate that the appointment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the appointment is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the appointment is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Indicate that the appointment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'appointment_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the appointment is for a test drive.
     */
    public function testDrive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_test_drive' => true,
            'is_purchase_appointment' => false,
        ]);
    }

    /**
     * Indicate that the appointment is for a purchase.
     */
    public function purchase(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_test_drive' => false,
            'is_purchase_appointment' => true,
        ]);
    }
} 