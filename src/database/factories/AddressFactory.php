<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'postal_code' => $this->faker->postcode,
            'prefecture' => '東京都',
            'city' => $this->faker->city,
            'block' => $this->faker->streetAddress,
            'building' => 'テストビル101',
        ];
    }
}
