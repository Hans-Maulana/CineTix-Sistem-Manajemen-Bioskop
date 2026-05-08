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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // kode promo, misalnya DISKON50
            $table->string('description')->nullable();
            $table->enum('discount_type', ['percentage','fixed']);
            $table->decimal('discount_value', 8, 2);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
