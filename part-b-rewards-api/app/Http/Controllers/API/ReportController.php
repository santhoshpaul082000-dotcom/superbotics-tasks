<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Redemption;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $query = Redemption::with([
            'user',
            'product',
            'category'
        ]);

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Get redemption records
        $redemptions = $query->latest()->get();

        // Calculate totals using one SQL query
        $totals = Redemption::query()
            ->when($request->filled('from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from);
            })
            ->when($request->filled('to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to);
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(points_spent), 0) as total_points')
            ->first();

        return response()->json([
            'data' => $redemptions,
            'total_count' => (int) $totals->total_count,
            'total_points' => (int) $totals->total_points,
        ]);
    }
}
