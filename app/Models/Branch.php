<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'code',
        'email',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country_code',
        'is_main',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function businessUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessUser::class,
            'business_user_branch',
            'branch_id',
            'business_user_id'
        )->withTimestamps();
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(
            Inventory::class
        );
    }

    public function outgoingInventoryTransfers(): HasMany
    {
        return $this->hasMany(
            InventoryTransfer::class,
            'source_branch_id'
        );
    }

    public function incomingInventoryTransfers(): HasMany
    {
        return $this->hasMany(
            InventoryTransfer::class,
            'destination_branch_id'
        );
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(
            Purchase::class
        );
    }
}
