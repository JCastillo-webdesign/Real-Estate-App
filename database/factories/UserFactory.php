<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
/**
 * UserFactory
 * 
 * Generates fake user data for testing and seeding.
 * Uses the Faker library to create realistic test data.
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     * Cached to avoid redundant hashing operations.
     * 
     * @var string|null
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     * 
     * Returns an array of attributes for a new User model instance.
     * Uses faker to generate realistic but random data.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),                                  // Random person name
            'email' => fake()->unique()->safeEmail(),                  // Unique safe email address
            'email_verified_at' => now(),                              // Current timestamp (verified)
            'password' => static::$password ??= Hash::make('password'), // Hashed default password
            'remember_token' => Str::random(10),                       // Random remember token
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     * 
     * Used to create test users with unverified email accounts.
     *
     * @return static
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
