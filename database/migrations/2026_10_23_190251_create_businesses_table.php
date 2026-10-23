<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('legal_name')->nullable();

            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();

            $table->string('status', 20)
                ->default('active')
                ->index();

            $table->string('timezone')
                ->default('America/Mexico_City');

            $table->string('currency', 3)
                ->default('MXN');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
