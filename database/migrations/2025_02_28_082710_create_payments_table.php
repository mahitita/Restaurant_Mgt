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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Make order_id nullable since payment might happen before an order is finalized
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            // Add reservation_id as an optional link if payment is tied to a reservation
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->onDelete('set null');
            $table->string('payment_method', 50);
            $table->decimal('amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->boolean('deposit_refunded')->default(false);
            $table->timestamp('paid_at')->nullable(); // Already nullable, good for pre-payments
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
