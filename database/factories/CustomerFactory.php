<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'pickup_day' => $this->faker->numberBetween(1, 5), // Random day between Monday (1) and Friday (5)
            'pickup_frequency' => $this->faker->numberBetween(1, 4), // Random frequency between 1 and 4 weeks
            'contact_method' => $this->faker->randomElement(['email', 'text']),
            'contact_email' => $this->faker->safeEmail,
            'contact_phone' => $this->faker->phoneNumber,
        ];
    }
}
