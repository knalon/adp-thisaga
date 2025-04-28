<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $carMakes = ['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes', 'Audi', 'Volkswagen', 'Nissan', 'Hyundai', 'Kia'];
        $carModels = [
            'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Highlander', 'Tacoma'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Odyssey'],
            'Ford' => ['F-150', 'Mustang', 'Explorer', 'Escape', 'Edge'],
            'BMW' => ['3 Series', '5 Series', 'X3', 'X5', '7 Series'],
            'Mercedes' => ['C-Class', 'E-Class', 'GLC', 'GLE', 'S-Class'],
            'Audi' => ['A4', 'A6', 'Q5', 'Q7', 'A8'],
            'Volkswagen' => ['Golf', 'Passat', 'Tiguan', 'Atlas', 'Jetta'],
            'Nissan' => ['Altima', 'Maxima', 'Rogue', 'Pathfinder', 'Murano'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Palisade'],
            'Kia' => ['Forte', 'Optima', 'Sportage', 'Sorento', 'Telluride'],
        ];
        
        $colors = ['White', 'Black', 'Silver', 'Gray', 'Red', 'Blue', 'Green', 'Yellow', 'Brown', 'Gold'];
        $transmissions = ['Automatic', 'Manual', 'CVT', 'Dual-Clutch'];
        $fuelTypes = ['Petrol', 'Diesel', 'Hybrid', 'Electric'];
        $conditions = ['New', 'Used', 'Certified Pre-Owned'];
        
        $make = $this->faker->randomElement($carMakes);
        $model = $this->faker->randomElement($carModels[$make]);
        
        return [
            'user_id' => User::factory(),
            'make' => $make,
            'model' => $model,
            'year' => $this->faker->numberBetween(2010, 2024),
            'color' => $this->faker->randomElement($colors),
            'transmission' => $this->faker->randomElement($transmissions),
            'price' => $this->faker->numberBetween(5000, 100000),
            'description' => $this->faker->paragraph(3),
            'mileage' => $this->faker->numberBetween(100, 100000),
            'fuel_type' => $this->faker->randomElement($fuelTypes),
            'condition' => $this->faker->randomElement($conditions),
            'is_approved' => $this->faker->boolean(80), // 80% chance of being approved
            'is_active' => $this->faker->boolean(90),   // 90% chance of being active
            'is_sold' => $this->faker->boolean(20),     // 20% chance of being sold
        ];
    }

    /**
     * Indicate that the car is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }

    /**
     * Indicate that the car is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the car is sold.
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sold' => true,
        ]);
    }

    /**
     * Indicate that the car is not approved.
     */
    public function notApproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }

    /**
     * Indicate that the car is not active.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the car is not sold.
     */
    public function notSold(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sold' => false,
        ]);
    }
} 