<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('reference')
                ->nullable();

            $table->string('status', 30)
                ->default('completed');

            $table->decimal(
                'total',
                14,
                2
            )->default(0);

            $table->text('notes')
                ->nullable();

            $table->timestamp('purchased_at');

            $table->timestamps();

            $table->index([
                'business_id',
                'branch_id',
            ]);

            $table->index([
                'business_id',
                'supplier_id',
            ]);

            $table->index([
                'business_id',
                'purchased_at',
            ]);

            $table->index([
                'business_id',
                'reference',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
