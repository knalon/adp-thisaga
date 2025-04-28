<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarImage>
 */
class CarImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $imageIndex = 0;
        $imageIndex++;
        
        return [
            'car_id' => Car::factory(),
            'image_path' => "https://source.unsplash.com/random/800x600?car,vehicle&sig={$imageIndex}",
            'is_primary' => $this->faker->boolean(25), // 25% chance of being primary
        ];
    }

    /**
     * Indicate that the image is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Indicate that the image is not primary.
     */
    public function notPrimary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => false,
        ]);
    }
} 