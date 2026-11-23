<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryTransfer extends Model
{
    protected $fillable = [
        'business_id',
        'source_branch_id',
        'destination_branch_id',
        'user_id',
        'status',
        'notes',
        'transferred_at',
    ];

    protected function casts(): array
    {
        return [
            'transferred_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(
            Business::class
        );
    }

    public function sourceBranch(): BelongsTo
    {
        return $this->belongsTo(
            Branch::class,
            'source_branch_id'
        );
    }

    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(
            Branch::class,
            'destination_branch_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            InventoryTransferItem::class
        );
    }
}
