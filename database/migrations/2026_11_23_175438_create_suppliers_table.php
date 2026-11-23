<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('legal_name')
                ->nullable();

            $table->string('tax_id', 50)
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->string('phone', 50)
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'business_id',
                'name',
            ]);

            $table->index([
                'business_id',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
