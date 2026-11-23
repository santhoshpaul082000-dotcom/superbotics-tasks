<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Redemption;
use App\Models\PointsLedger;
use Carbon\Carbon;

class RedemptionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all()->groupBy('category_id');

        foreach ($users as $user) {

            // Two redemptions from each of five categories
            foreach ($products as $categoryProducts) {

                for ($i = 0; $i < 2; $i++) {

                    $product = $categoryProducts->random();

                    // Random date within the last 6 months
                    $date = Carbon::now()
                        ->subDays(rand(1, 180));

                    $redemption = Redemption::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'category_id' => $product->category_id,
                        'points_spent' => $product->points_required,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    // Record spent points
                    PointsLedger::create([
                        'user_id' => $user->id,
                        'redemption_id' => $redemption->id,
                        'type' => 'spend',
                        'points' => $product->points_required,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }
            }
        }
    }
}
