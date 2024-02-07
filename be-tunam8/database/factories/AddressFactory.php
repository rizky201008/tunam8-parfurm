<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $userCounter = 0;

    public function definition(): array
    {
        $this->userCounter++;

        return [
            'user_id' => $this->userCounter <= 10 ? $this->userCounter : random_int(1, 10),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'postal_code' => $this->faker->postcode,
            'city' => $this->faker->city,
            'province' => Arr::random(['Jawa Timur', 'Sumatra', 'Sulawesi', 'Kalimantan', 'Papua', 'Bali', 'Nusa Tenggara', 'Maluku', 'Jawa Barat', 'Jawa Tengah']),
        ];
    }
}
