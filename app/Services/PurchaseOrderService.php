<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderService
{
    /**
     * Create a new purchase order with its items.
     *
     * @param array $data
     * @return PurchaseOrder
     * @throws \Throwable
     */
    public function create(array $data): PurchaseOrder
    {
        DB::beginTransaction();

        try {

            $poNumber = $this->generatePoNumber();

            $totalAmount = 0;

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $data['supplier_id'],
                'status' => 'DRAFT',
                'total_amount' => 0,
            ]);

            foreach ($data['items'] as $item) {

                $subtotal = $item['quantity'] * $item['unit_price'];

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $purchaseOrder->update([
                'total_amount' => $totalAmount,
            ]);

            DB::commit();

            return $purchaseOrder->load([
                'supplier',
                'items.product',
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Update the status of a purchase order.
     *
     * @param PurchaseOrder $purchaseOrder
     * @param string $newStatus
     * @return PurchaseOrder
     * @throws \Throwable
     */
    public function updateStatus(PurchaseOrder $purchaseOrder, string $newStatus): PurchaseOrder {

        return DB::transaction(function () use (
            $purchaseOrder,
            $newStatus
        ) {

            $this->validateStatusTransition(
                $purchaseOrder->status,
                $newStatus
            );

            if ($newStatus === 'RECEIVED') {

                $purchaseOrder->load('items');

                foreach ($purchaseOrder->items as $item) {

                    $product = Product::where(
                        'id',
                        $item->product_id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                    $product->increment(
                        'current_stock',
                        $item->quantity
                    );
                }
            }

            $purchaseOrder->update([
                'status' => $newStatus,
            ]);

            return $purchaseOrder->fresh([
                'supplier',
                'items.product',
            ]);
        });
    }

    /**
     * Validate the status transition of a purchase order.
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @throws ValidationException
     */
    private function validateStatusTransition(string $currentStatus, string $newStatus): void 
    {

        $validTransitions = [
            'DRAFT' => [
                'APPROVED',
                'CANCELLED',
            ],

            'APPROVED' => [
                'RECEIVED',
                'CANCELLED',
            ],

            'RECEIVED' => [],

            'CANCELLED' => [],
        ];

        if (
            ! in_array(
                $newStatus,
                $validTransitions[$currentStatus] ?? [],
                true
            )
        ) {
            throw ValidationException::withMessages([
                'status' => [
                    "Cannot change status from {$currentStatus} to {$newStatus}.",
                ],
            ]);
        }
    }

    /**
     * Generate a unique purchase order number.
     *
     * @return string
     */
    private function generatePoNumber(): string
    {
        $year = now()->format('Y');

        $lastOrder = PurchaseOrder::query()
            ->where('po_number', 'like', "PO-{$year}%")
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastOrder
            ? ((int) substr($lastOrder->po_number, -4)) + 1
            : 1;

        return 'PO-' . $year . str_pad(
            $nextNumber,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}