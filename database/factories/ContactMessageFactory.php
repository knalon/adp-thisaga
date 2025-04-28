<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subjects = [
            'General Inquiry',
            'Car Listing Question',
            'Technical Support',
            'Payment Issue',
            'Feedback',
            'Partnership Opportunity',
            'Job Application',
            'Complaint',
            'Other',
        ];
        
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'subject' => $this->faker->randomElement($subjects),
            'message' => $this->faker->paragraph(3),
            'is_read' => $this->faker->boolean(30), // 30% chance of being read
        ];
    }

    /**
     * Indicate that the message has been read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    /**
     * Indicate that the message has not been read.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }
} 