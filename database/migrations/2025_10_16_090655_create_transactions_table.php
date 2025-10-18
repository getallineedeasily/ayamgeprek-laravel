<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** 
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id');
            $table->foreignId('user_id')->constrained(table: 'users', column: 'id');
            $table->text('address');
            $table->foreignId('food_id')->constrained(table: 'foods', column: 'id');
            $table->unsignedInteger('price');
            $table->tinyInteger('quantity');
            $table->unsignedInteger('total');
            $table->text('payment_proof')->nullable();
            $table->enum('status', ['pending payment', 'waiting confirmation', 'confirmed', 'delivered', 'cancelled']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
