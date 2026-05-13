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
        Schema::table('films', function (Blueprint $table) {
            $table->text('synopsis')->nullable()->after('description');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->string('status')->default('on schedule')->after('ticket_price'); // on schedule, complete, canceled
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn('synopsis');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
