<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRedemptionRequest;
use App\Services\RedemptionService;
use Illuminate\Http\JsonResponse;

class RedemptionController extends Controller
{
    public function __construct(
        private RedemptionService $redemptionService
    ) {}

    public function store(StoreRedemptionRequest $request): JsonResponse
    {
        try {
            $redemption = $this->redemptionService->redeem(
                $request->user_id,
                $request->product_id
            );

            return response()->json([
                'message' => 'Product redeemed successfully',
                'data' => $redemption
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
