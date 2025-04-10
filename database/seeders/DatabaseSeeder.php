<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(CartSeeder::class);
        $this->call(CartProductSeeder::class);
        $this->call(WishlistSeeder::class);
        $this->call(OrderSeeder::class);
    }
}
