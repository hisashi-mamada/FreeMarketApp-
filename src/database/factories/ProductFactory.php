<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(1000, 10000),
            'description' => $this->faker->sentence(),
            'image_url' => 'storage/images/sample.jpg',
            'condition' => '良好',
            'category_id' => '1,2',
            'brand' => $this->faker->company(),
            'user_id' => null,
            'is_sold' => false,
        ];
    }
}
