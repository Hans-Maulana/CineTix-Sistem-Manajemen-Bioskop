<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change method enum to include qris and virtual_account
        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('cash','transfer','ewallet','qris','virtual_account') NOT NULL");

        Schema::table('payments', function (Blueprint $table) {
            $table->string('va_number')->nullable()->after('method');
            $table->integer('countdown_seconds')->nullable()->after('va_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['va_number', 'countdown_seconds']);
        });

        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('cash','transfer','ewallet') NOT NULL");
    }
};
