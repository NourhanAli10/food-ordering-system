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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->dateTime('start_date');
            $table->dateTime('expire_date');
            $table->enum('type', ['fixed', 'percentage', 'buy_x_get_y', 'combo']);
            $table->decimal('value', 8, 2)->nullable();
            $table->decimal('min_order_amount', 8, 2)->nullable();
            $table->integer('priority')->default(1);
            $table->integer('buy_quantity')->nullable();         
            $table->integer('free_quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
