<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CartProductSeeder extends Seeder
{
    public function run()
    {
        Cart::all()->each(function ($cart) {
            $products = Product::inRandomOrder()->take(5)->get();

            foreach ($products as $product) {
                $cart->products()->attach($product->id, [
                    'quantity' => rand(1, 500)
                ]);
            }
        });
    }
}
