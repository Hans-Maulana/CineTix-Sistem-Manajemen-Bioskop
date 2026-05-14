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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'promo_id')) {
                $table->foreignId('promo_id')->nullable()->after('user_id')->constrained('promos');
            }
            if (!Schema::hasColumn('bookings', 'booking_type')) {
                $table->string('booking_type')->default('ticket')->after('promo_id');
            }
            if (!Schema::hasColumn('bookings', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0)->after('booking_type');
            }
            if (!Schema::hasColumn('bookings', 'qr_redeem')) {
                $table->string('qr_redeem')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bookings', 'status_redeem')) {
                $table->enum('status_redeem', ['unredeemed', 'redeemed'])->default('unredeemed')->after('qr_redeem');
            }
            // Remove schedule_id if it's not used in this context (optional, but keep it for safety if exists)
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'countdown_seconds')) {
                $table->integer('countdown_seconds')->default(300)->after('status');
            }
            // Change method enum to allow qris and virtual_account
            $table->string('method')->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['promo_id', 'booking_type', 'total_amount', 'qr_redeem', 'status_redeem']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['countdown_seconds']);
        });
    }
};
