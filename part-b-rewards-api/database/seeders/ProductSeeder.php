<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            'Electronics' => [
                ['name' => 'Bluetooth Speaker', 'points' => 500],
                ['name' => 'Wireless Mouse', 'points' => 300],
                ['name' => 'Headphones', 'points' => 800],
                ['name' => 'Power Bank', 'points' => 600],
            ],

            'Fashion' => [
                ['name' => 'T-Shirt', 'points' => 200],
                ['name' => 'Jeans', 'points' => 700],
                ['name' => 'Backpack', 'points' => 500],
                ['name' => 'Shoes', 'points' => 1000],
            ],

            'Home Appliances' => [
                ['name' => 'Electric Kettle', 'points' => 900],
                ['name' => 'Mixer', 'points' => 1500],
                ['name' => 'Iron Box', 'points' => 700],
                ['name' => 'Table Fan', 'points' => 1200],
            ],

            'Grocery' => [
                ['name' => 'Rice Bag', 'points' => 400],
                ['name' => 'Cooking Oil', 'points' => 300],
                ['name' => 'Coffee Powder', 'points' => 200],
                ['name' => 'Dry Fruits', 'points' => 600],
            ],

            'Accessories' => [
                ['name' => 'Watch', 'points' => 800],
                ['name' => 'Wallet', 'points' => 300],
                ['name' => 'Sunglasses', 'points' => 500],
                ['name' => 'Belt', 'points' => 200],
            ],
        ];

        foreach ($products as $categoryName => $items) {

            $category = Category::where('name', $categoryName)->firstOrFail();

            foreach ($items as $item) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'points_required' => $item['points'],
                ]);
            }
        }
    }
}
