<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = [
        'business_id',
        'branch_id',
        'product_id',
        'quantity',
        'minimum_stock',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'minimum_stock' => 'decimal:3',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(
            InventoryMovement::class
        );
    }
}
