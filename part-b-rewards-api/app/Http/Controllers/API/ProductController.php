<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Get all available products.
     */
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->with('category:id,name')
            ->select([
                'id',
                'category_id',
                'name',
                'points_required',
            ])
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Products fetched successfully',
            'data' => $products,
        ]);
    }
}
