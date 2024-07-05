<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific customers with given attributes
        Customer::factory()->create([
            'name' => 'Choco Bars Factory',
            'address' => '123 Main St',
            'pickup_day' => 1, // Monday
            'pickup_frequency' => 1, // Every week
            'contact_method' => 'email',
            'contact_email' => 'hromanchik1213+choco@gmail.com',
            'contact_phone' => '519-719-9053',
        ]);

        Customer::factory()->create([
            'name' => 'Banana Pudding Plantation',
            'address' => '456 Elm St',
            'pickup_day' => 3, // Wednesday
            'pickup_frequency' => 2, // Every other week
            'contact_method' => 'text',
            'contact_email' => 'hromanchik1213+banana@gmail.com',
            'contact_phone' => '519-719-9053',
        ]);
    }
}
