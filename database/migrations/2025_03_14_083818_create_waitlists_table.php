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
        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('party_size')->unsigned()->check('party_size > 0');
            $table->timestamp('added_at')->useCurrent();
            $table->integer('estimated_wait_minutes')->nullable()->unsigned();
            $table->enum('status', ['waiting', 'seated', 'cancelled'])->default('waiting');
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waitlists');
    }
};
