<?php

// database/seeders/WishlistSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all(); // جلب كل المستخدمين

        foreach ($users as $user) {
            // إنشاء Wishlist للمستخدم إذا لم يكن موجودًا
            $wishlist = Wishlist::firstOrCreate(['user_id' => $user->id]);

            // إضافة 3 منتجات عشوائية إلى الـ Wishlist
            $products = Product::inRandomOrder()->limit(3)->pluck('id')->toArray();
            $wishlist->products()->syncWithoutDetaching($products); // ربط المنتجات بالـ Wishlist
        }
    }
}
