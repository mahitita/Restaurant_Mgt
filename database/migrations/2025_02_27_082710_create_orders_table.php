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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('order_type', ['dine-in', 'takeout', 'delivery']);
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null'); // For dine-in orders
            $table->timestamp('pickup_time')->nullable(); // For takeout orders
            $table->string('delivery_address')->nullable(); // For delivery orders
            $table->enum('status', ['pending', 'preparing', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamps();
        });
    }




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
