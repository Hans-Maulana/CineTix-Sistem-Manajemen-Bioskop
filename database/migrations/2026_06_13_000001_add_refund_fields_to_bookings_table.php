<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('refund_status')->nullable()->after('status'); // requested, approved, rejected
            $table->text('refund_reason')->nullable()->after('refund_status');
            $table->decimal('refund_amount', 12, 2)->nullable()->after('refund_reason');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_amount');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_requested_at');
            $table->foreignId('refund_processed_by')->nullable()->after('refund_processed_at')->constrained('users')->nullOnDelete();
            $table->text('refund_rejection_reason')->nullable()->after('refund_processed_by');
        });

        // Extend the status enum to include 'refunded'
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','paid','cancelled','completed','refunded') DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['refund_processed_by']);
            $table->dropColumn([
                'refund_status',
                'refund_reason',
                'refund_amount',
                'refund_requested_at',
                'refund_processed_at',
                'refund_processed_by',
                'refund_rejection_reason',
            ]);
        });

        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','confirmed','paid','cancelled','completed') DEFAULT 'pending'");
    }
};
