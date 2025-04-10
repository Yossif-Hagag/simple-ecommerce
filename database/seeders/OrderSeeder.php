<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Order::factory(10)->create()->each(function ($order) {
            OrderItem::factory(3)->create(['order_id' => $order->id]);
        });
    }
}
