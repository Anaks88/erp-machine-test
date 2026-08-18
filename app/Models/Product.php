<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'unit_price',
        'current_stock',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'current_stock' => 'integer',
    ];

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
