<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Stress_Data;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            "username" => "test2",
            "password" => "123456",
            'email' => 'test2@example.com',
        ]);

        Stress_Data::factory()->count(1000)->create();
    }
}
