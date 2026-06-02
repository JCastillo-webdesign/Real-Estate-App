<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 * 
 * Seeds the database with initial data.
 * Currently creates a test user with known credentials for development/testing.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Creates a test user account with email 'test@example.com'.
     * Useful for local development and automated testing.
     * 
     * @return void
     */
    public function run(): void
    {
        // Uncomment to seed 10 random users
        // User::factory(10)->create();

        // Create a single test user with known credentials
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
