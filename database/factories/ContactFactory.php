<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        fake()->locale = 'es_ES';

        return [
            'name' => ucfirst(fake()->unique()->words(3, true)),
            'email' => fake()->unique()->safeEmail(),
            'number_phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'avatar' => fake()->imageUrl(500, 500, 'people', true),
            'user_id' => User::factory(),
        ];
    }
}
