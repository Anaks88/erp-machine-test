<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_purchase_order_with_valid_items(): void
    {
        $supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'phone' => '9876543210',
        ]);

        $product = Product::create([
            'sku' => 'TEST-001',
            'name' => 'Test Product',
            'unit_price' => 50,
            'current_stock' => 10,
        ]);

        $response = $this->postJson(
            '/api/v1/purchase-orders',
            [
                'supplier_id' => $supplier->id,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 10,
                        'unit_price' => 50,
                    ],
                ],
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath(
                'data.status',
                'DRAFT'
            )
            ->assertJsonPath(
                'data.total_amount',
                '500.00'
            );

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $supplier->id,
            'status' => 'DRAFT',
            'total_amount' => 500.00,
        ]);

        $this->assertDatabaseHas('purchase_order_items', [
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => 50,
            'subtotal' => 500,
        ]);
    }

    public function test_cannot_create_purchase_order_with_invalid_product(): void
    {
        $supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'phone' => '9876543210',
        ]);

        $response = $this->postJson(
            '/api/v1/purchase-orders',
            [
                'supplier_id' => $supplier->id,
                'items' => [
                    [
                        'product_id' => 99999,
                        'quantity' => 10,
                        'unit_price' => 50,
                    ],
                ],
            ]
        );

        $response->assertStatus(422);
    }

    public function test_receiving_purchase_order_increments_product_stock(): void
    {
        $supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'phone' => '9876543210',
        ]);

        $product = Product::create([
            'sku' => 'TEST-002',
            'name' => 'Test Product',
            'unit_price' => 50,
            'current_stock' => 10,
        ]);

        $createResponse = $this->postJson(
            '/api/v1/purchase-orders',
            [
                'supplier_id' => $supplier->id,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 5,
                        'unit_price' => 50,
                    ],
                ],
            ]
        );

        $poId = $createResponse->json('data.id');

        $this->patchJson(
            "/api/v1/purchase-orders/{$poId}/status",
            [
                'status' => 'APPROVED',
            ]
        )->assertSuccessful();

        $this->patchJson(
            "/api/v1/purchase-orders/{$poId}/status",
            [
                'status' => 'RECEIVED',
            ]
        )->assertSuccessful();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_stock' => 15,
        ]);
    }

    public function test_cannot_transition_from_received_to_draft(): void
    {
        $supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'phone' => '9876543210',
        ]);

        $product = Product::create([
            'sku' => 'TEST-003',
            'name' => 'Test Product',
            'unit_price' => 50,
            'current_stock' => 10,
        ]);

        $createResponse = $this->postJson(
            '/api/v1/purchase-orders',
            [
                'supplier_id' => $supplier->id,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 5,
                        'unit_price' => 50,
                    ],
                ],
            ]
        );

        $poId = $createResponse->json('data.id');

        $this->patchJson(
            "/api/v1/purchase-orders/{$poId}/status",
            [
                'status' => 'APPROVED',
            ]
        );

        $this->patchJson(
            "/api/v1/purchase-orders/{$poId}/status",
            [
                'status' => 'RECEIVED',
            ]
        );

        $response = $this->patchJson(
            "/api/v1/purchase-orders/{$poId}/status",
            [
                'status' => 'DRAFT',
            ]
        );

        $response->assertStatus(422);
    }
}