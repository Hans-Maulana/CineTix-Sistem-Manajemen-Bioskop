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
        Schema::table('promos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->boolean('is_used')->default(false);
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('promos', 'user_id')) {
                try {
                    $table->dropForeignIdFor(\App\Models\User::class);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            }
            
            if (Schema::hasColumn('promos', 'booking_id')) {
                try {
                    $table->dropForeignIdFor(\App\Models\Booking::class);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            }
            
            // Drop columns
            $table->dropColumn(['user_id', 'is_used', 'booking_id']);
        });
    }
};
