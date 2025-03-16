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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('stock')->default(0); // Total available stock
            $table->integer('threshold')->default(10); // Alert level
            $table->string('unit')->default('g'); // Measurement unit (e.g., g, kg, ml)
            $table->integer('waste')->default(0); // Waste tracking
            $table->integer('min_stock')->default(10); // Minimum stock for forecasting
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
