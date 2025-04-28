<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\User;
use Illuminate\Database\Seeder;

class CarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!app()->environment('local')) {
            return;
        }

        // Get all users with role 'user'
        $users = User::role('user')->get();

        // Sample car data
        $carData = [
            [
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => 2020,
                'color' => 'Silver',
                'transmission' => 'Automatic',
                'price' => 25000,
                'description' => 'Well maintained Toyota Camry with low mileage. Excellent condition.',
                'mileage' => 15000,
                'fuel_type' => 'Petrol',
                'condition' => 'Used',
                'is_approved' => true,
                'is_active' => true,
            ],
            [
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2019,
                'color' => 'Blue',
                'transmission' => 'Automatic',
                'price' => 22000,
                'description' => 'Honda Civic in good condition. Regular service history.',
                'mileage' => 20000,
                'fuel_type' => 'Petrol',
                'condition' => 'Used',
                'is_approved' => true,
                'is_active' => true,
            ],
            [
                'make' => 'Ford',
                'model' => 'Mustang',
                'year' => 2018,
                'color' => 'Red',
                'transmission' => 'Manual',
                'price' => 35000,
                'description' => 'Classic Ford Mustang. Great performance and sound.',
                'mileage' => 25000,
                'fuel_type' => 'Petrol',
                'condition' => 'Used',
                'is_approved' => true,
                'is_active' => true,
            ],
            [
                'make' => 'BMW',
                'model' => '3 Series',
                'year' => 2021,
                'color' => 'Black',
                'transmission' => 'Automatic',
                'price' => 45000,
                'description' => 'Luxury BMW 3 Series. Packed with features and in excellent condition.',
                'mileage' => 10000,
                'fuel_type' => 'Petrol',
                'condition' => 'Used',
                'is_approved' => true,
                'is_active' => true,
            ],
            [
                'make' => 'Mercedes',
                'model' => 'C-Class',
                'year' => 2020,
                'color' => 'White',
                'transmission' => 'Automatic',
                'price' => 48000,
                'description' => 'Mercedes C-Class in pristine condition. Full service history.',
                'mileage' => 12000,
                'fuel_type' => 'Diesel',
                'condition' => 'Used',
                'is_approved' => true,
                'is_active' => true,
            ],
        ];

        // Create cars for users
        foreach ($users as $user) {
            $randomCars = array_rand($carData, min(2, count($carData)));
            
            if (!is_array($randomCars)) {
                $randomCars = [$randomCars];
            }
            
            foreach ($randomCars as $index) {
                $car = $user->cars()->create($carData[$index]);
                
                // Create dummy car images
                for ($i = 1; $i <= 3; $i++) {
                    $isPrimary = ($i === 1);
                    CarImage::create([
                        'car_id' => $car->id,
                        'image_path' => "https://source.unsplash.com/random/800x600?car,vehicle&sig={$car->id}-{$i}",
                        'is_primary' => $isPrimary,
                    ]);
                }
            }
        }
    }
} 