<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Hur Admin',
            'email' => 'hromanchik1213+admin@gmail.com',
        ]);

        User::factory()->create([
            'name' => 'Hur Driver',
            'email' => 'hromanchik1213+driver@gmail.com',
        ]);
    }
}
