<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => 'Product - ' . uniqid(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'quantity' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->text(200),
            'sku' => $this->faker->unique()->lexify('???????'),
            'image' => $this->faker->imageUrl,
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id,
        ];
    }
}
