<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderStatusRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;

class PurchaseOrderController extends Controller
{
    /**
     * Store a newly created purchase order in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\PurchaseOrderService  $purchaseOrderService
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseOrderRequest $request, PurchaseOrderService $purchaseOrderService)
    {
        $purchaseOrder = $purchaseOrderService->create(
            $request->validated()
        );

         return (new PurchaseOrderResource($purchaseOrder))
            ->additional([
                'message' => 'Purchase order created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified purchase order.
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'supplier',
            'items.product',
        ]);

        return new PurchaseOrderResource($purchaseOrder);
    }

    /**
     * Update the status of the specified purchase order.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(UpdatePurchaseOrderStatusRequest $request, PurchaseOrder $purchaseOrder, PurchaseOrderService $purchaseOrderService) {
        $purchaseOrder = $purchaseOrderService->updateStatus(
            $purchaseOrder,
            $request->validated('status')
        );

        return new PurchaseOrderResource($purchaseOrder);
    }
}
