<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('source_branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            $table->foreignId('destination_branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('status', 30)
                ->default('completed');

            $table->text('notes')
                ->nullable();

            $table->timestamp('transferred_at');

            $table->timestamps();

            $table->index([
                'business_id',
                'source_branch_id',
            ]);

            $table->index([
                'business_id',
                'destination_branch_id',
            ]);

            $table->index([
                'business_id',
                'transferred_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'inventory_transfers'
        );
    }
};
