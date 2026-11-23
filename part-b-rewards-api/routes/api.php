<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RedemptionController;
use App\Http\Controllers\API\BalanceController;
use App\Http\Controllers\API\ReportController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/redemptions', [RedemptionController::class, 'store']);

Route::get('/users/{id}/balance', [BalanceController::class, 'show']);

Route::get('/reports/redemptions', [ReportController::class, 'index']);