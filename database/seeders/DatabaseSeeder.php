<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            'email' => 'u@u.com',
        ]);
        User::factory()->create([
            'name' => 'o User',
            'email' => 'o@o.com',
        ]);
        User::factory()->create([
            'name' => 'a User',
            'email' => 'a@a.com',
        ]);
    }
}
