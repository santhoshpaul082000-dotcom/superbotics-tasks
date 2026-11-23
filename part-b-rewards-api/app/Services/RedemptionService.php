<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\Redemption;
use App\Models\PointsLedger;
use Illuminate\Support\Facades\DB;
use Exception;

class RedemptionService
{
    public function redeem(int $userId, int $productId)
    {
        return DB::transaction(function () use ($userId, $productId) {

            // 1. Lock user row to prevent simultaneous redemptions
            $user = User::where('id', $userId)
                ->lockForUpdate()
                ->first();

            if (!$user) {
                throw new Exception('User not found');
            }

            // 2. Check restricted user
            if ($user->profile()->value('user_type') === 'restricted') {
                throw new Exception('Restricted users cannot redeem products');
            }

            // 3. Find product
            $product = Product::find($productId);

            if (!$product) {
                throw new Exception('Product not found');
            }

            // 4. Check monthly category limit
            $monthlyLimit = $product->category
                ->categoryLimit
                ->monthly_limit ?? 2;

            $usedCount = Redemption::where('user_id', $userId)
                ->where('category_id', $product->category_id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            if ($usedCount >= $monthlyLimit) {
                throw new Exception('Monthly category redemption limit reached');
            }

            // 5. Calculate points balance
            $balance = PointsLedger::where('user_id', $userId)
                ->selectRaw("
                    COALESCE(
                        SUM(
                            CASE
                                WHEN type = 'earn' THEN points
                                WHEN type = 'spend' THEN -points
                                ELSE 0
                            END
                        ), 0
                    ) as balance
                ")
                ->value('balance');

            if ($balance < $product->points_required) {
                throw new Exception('Insufficient points');
            }

            // 6. Create redemption
            $redemption = Redemption::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'category_id' => $product->category_id,
                'points_spent' => $product->points_required,
            ]);

            // 7. Add spend entry to ledger
            PointsLedger::create([
                'user_id' => $userId,
                'redemption_id' => $redemption->id,
                'type' => 'spend',
                'points' => $product->points_required,
            ]);

            return $redemption;
        });
    }
}
