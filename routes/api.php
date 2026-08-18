<?php

use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('products',[ProductController::class, 'index']);

    Route::post('purchase-orders',[PurchaseOrderController::class, 'store']);

    Route::get('purchase-orders/{purchaseOrder}',[PurchaseOrderController::class, 'show']);

    Route::patch('purchase-orders/{purchaseOrder}/status',[PurchaseOrderController::class, 'updateStatus']);
});