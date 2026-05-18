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
        // database/migrations/xxxx_xx_xx_create_films_table.php
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('duration'); // menit
            $table->date('release_date');
            $table->enum('status', ['now_playing', 'coming_soon', 'ended'])->default('coming_soon');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
