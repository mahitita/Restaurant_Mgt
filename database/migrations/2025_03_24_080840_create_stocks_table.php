<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Chair, Table, Pan, Jug
            $table->integer('quantity')->default(0); // Quantity in stock
            $table->decimal('price', 8, 2)->default(0.00); // Price per unit
            $table->text('description')->nullable(); // Description of the item
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
