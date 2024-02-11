<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total' => $this->faker->randomFloat(2, 1000, 100000),
            'products' => json_encode([
                [
                    'product_id' => random_int(1, 10),
                    'qty' => random_int(1, 10),
                ],
                [
                    'product_id' => random_int(1, 10),
                    'qty' => random_int(1, 10),
                ],
                [
                    'product_id' => random_int(1, 10),
                    'qty' => random_int(1, 10),
                ],
            ]),
            'user_id' => random_int(1, 10),
            'status' => $this->faker->randomElement(['unpaid', 'proccess', 'shipping', 'received', 'canceled']),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'postal_code' => $this->faker->postcode,
            'city' => $this->faker->city,
            'province' => $this->faker->randomElement(['Jawa Timur', 'Sumatra', 'Sulawesi', 'Kalimantan', 'Papua', 'Bali', 'Nusa Tenggara', 'Maluku', 'Jawa Barat', 'Jawa Tengah']),
            'tracking_number' => $this->faker->randomElement([null, $this->faker->word]),
        ];
    }
}
