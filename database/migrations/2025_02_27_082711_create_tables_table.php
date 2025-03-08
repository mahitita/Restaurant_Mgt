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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number')->unique();
            $table->integer('seats');
            $table->integer('x_coordinate');
            $table->integer('y_coordinate');
            $table->integer('width')->default(100); // Default width for rectangular tables
            $table->integer('height')->default(60); // Default height for rectangular tables
            $table->enum('type', ['rectangle', 'round', 'oval', 'square' ])->default('rectangle'); // Table type
            $table->enum('status', ['available', 'reserved', 'occupied'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tables');
    }
};
