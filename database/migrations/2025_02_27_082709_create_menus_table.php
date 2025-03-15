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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('prep_time')->default(15)->nullable();
            $table->integer('stock_quantity')->default(100)->nullable();
            $table->decimal('cost', 8, 2)->default(0)->nullable();
            $table->foreignId('inventory_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('available')->default(true);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
