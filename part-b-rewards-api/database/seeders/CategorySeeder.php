<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\CategoryLimit;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Fashion',
            'Home Appliances',
            'Grocery',
            'Accessories'
        ];

        foreach ($categories as $name) {

            $category = Category::create([
                'name' => $name
            ]);

            CategoryLimit::create([
                'category_id' => $category->id,
                'monthly_limit' => 2
            ]);
        }
    }
}
