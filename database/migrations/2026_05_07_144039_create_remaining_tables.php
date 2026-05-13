<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel types 
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['2D', '3D', '4D Experience', 'Premium']);
            $table->timestamps();
        });

        // Tambah kolom type_id ke studios (
        if (!Schema::hasColumn('studios', 'type_id')) {
            Schema::table('studios', function (Blueprint $table) {
                $table->foreignId('type_id')->nullable()->after('id')->constrained('types')->nullOnDelete();
            });
        }

        // Tabel Bundling (paket tiket)
        Schema::create('bundlings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });

        // Tabel Bundling Booking
        Schema::create('bundling_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('bundling_id')->constrained('bundlings')->cascadeOnDelete();
            $table->timestamps();
        });

        // Tabel Reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('film_id')->constrained('films')->cascadeOnDelete();
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('bundling_bookings');
        Schema::dropIfExists('bundlings');
        Schema::dropIfExists('types');
    }
};
