<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal(
                'quantity',
                14,
                3
            )->default(0);

            $table->decimal(
                'minimum_stock',
                14,
                3
            )->default(0);

            $table->timestamps();

            $table->unique([
                'business_id',
                'branch_id',
                'product_id',
            ]);

            $table->index([
                'business_id',
                'branch_id',
            ]);

            $table->index([
                'business_id',
                'product_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
