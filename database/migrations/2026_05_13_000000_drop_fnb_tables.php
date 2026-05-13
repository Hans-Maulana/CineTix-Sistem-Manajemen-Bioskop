<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FNB related tables if they exist
        Schema::dropIfExists('fnb_bookings');
        Schema::dropIfExists('bundling_fnb');
        Schema::dropIfExists('fnbs');
    }

    public function down(): void
    {
        // Recreate FNB tables if rollback
        Schema::create('fnbs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->decimal('current_price', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('fnb_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('fnb_id')->constrained('fnbs')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price_at_sale', 12, 2);
            $table->timestamps();
        });

        Schema::create('bundling_fnb', function (Blueprint $table) {
            $table->foreignId('bundling_id')->constrained('bundlings')->cascadeOnDelete();
            $table->foreignId('fnb_id')->constrained('fnbs')->cascadeOnDelete();
            $table->primary(['bundling_id', 'fnb_id']);
        });
    }
};
