<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('genre_name');
            $table->timestamps();
        });

        Schema::create('genre_film', function (Blueprint $table) {
            $table->foreignId('film_id')->constrained('films')->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete();
            $table->primary(['film_id', 'genre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genre_film');
        Schema::dropIfExists('genres');
    }
};
