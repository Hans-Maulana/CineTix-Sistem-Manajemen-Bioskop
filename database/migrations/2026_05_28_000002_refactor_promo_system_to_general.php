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
        // Modify promos table - ubah dari personal ke general promo system
        Schema::table('promos', function (Blueprint $table) {
            // Drop foreign keys properly
            if (Schema::hasColumn('promos', 'user_id')) {
                try {
                    DB::statement('ALTER TABLE promos DROP FOREIGN KEY promos_user_id_foreign');
                } catch (\Exception $e) {
                    // Already dropped
                }
            }
            
            if (Schema::hasColumn('promos', 'booking_id')) {
                try {
                    DB::statement('ALTER TABLE promos DROP FOREIGN KEY promos_booking_id_foreign');
                } catch (\Exception $e) {
                    // Already dropped
                }
            }
        });

        // Drop columns if they exist (SQLite needs table rebuild — skip drop on sqlite)
        if (Schema::hasColumns('promos', ['user_id', 'is_used', 'booking_id']) && Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('promos', function (Blueprint $table) {
                $table->dropColumn(['user_id', 'is_used', 'booking_id']);
            });
        }

        // Add new columns
        Schema::table('promos', function (Blueprint $table) {
            if (!Schema::hasColumn('promos', 'max_usage')) {
                $table->integer('max_usage')->nullable()->default(null)->comment('Total max usage untuk promo ini');
            }
            if (!Schema::hasColumn('promos', 'max_usage_per_customer')) {
                $table->integer('max_usage_per_customer')->default(1)->comment('Max usage per customer (default 1)');
            }
            if (!Schema::hasColumn('promos', 'usage_count')) {
                $table->integer('usage_count')->default(0)->comment('Total usage count saat ini');
            }
        });

        // Create promo usages tracking table
        if (!Schema::hasTable('promo_usages')) {
            Schema::create('promo_usages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promo_id')->constrained('promos')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
                $table->integer('usage_count')->default(1)->comment('Berapa kali user pakai promo ini');
                $table->timestamps();
                
                // Unique constraint: 1 user hanya bisa log 1x per promo
                $table->unique(['promo_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_usages');
        
        Schema::table('promos', function (Blueprint $table) {
            if (Schema::hasColumns('promos', ['max_usage', 'max_usage_per_customer', 'usage_count'])) {
                $table->dropColumn(['max_usage', 'max_usage_per_customer', 'usage_count']);
            }
        });
    }
};
