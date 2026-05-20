<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PwdFactory extends Factory
{
    public function definition(): array
    {
        return [
            'last_name' => fake()->lastName(),

            'first_name' => fake()->firstName(),

            'middle_name' => fake()->optional()->lastName(),

            'suffix' => fake()->optional()->randomElement([
                'Jr.',
                'Sr.',
                'III',
                null
            ]),

            'date_of_birth' => fake()->date(),

            'sex' => fake()->randomElement([
                'Male',
                'Female'
            ]),

            // IMPORTANT:
            // These IDs must exist in database
            'civil_status_id' => rand(1, 4),

            'educational_attainment_id' => rand(1, 6),

            'mobile_no' => fake()->phoneNumber(),

            'email' => fake()->safeEmail(),
        ];
    }
}