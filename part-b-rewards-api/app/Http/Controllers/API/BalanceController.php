<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class BalanceController extends Controller
{
    public function show(int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $balance = $user->pointsLedger()
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

        return response()->json([
            'user_id' => $user->id,
            'balance' => (int) $balance
        ]);
    }
}
