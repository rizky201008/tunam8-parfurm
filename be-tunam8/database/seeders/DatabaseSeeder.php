<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        // Address::factory(10)->create();
        // Category::factory(10)->create();
        // CartItem::factory(10)->create();
        Tag::factory(10)->create();
        // Product::factory(10)->create();
        // Transaction::factory(100)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
