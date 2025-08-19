<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name_first' => 'User',
            'name_last' => 'Example',
            'email' => 'user@example.com',
            'created_at' => Carbon::now()->subDays(11),
        ]);

        User::factory()->create([
            'name_first' => 'User',
            'name_last' => 'Test',
            'email' => 'user@test.com',
        ]);
    }
}
