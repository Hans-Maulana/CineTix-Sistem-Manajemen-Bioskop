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
            $table->dropForeign(['refund_processed_by']);
            $table->dropColumn([
                'refund_status',
                'refund_reason',
                'refund_requested_at',
                'refund_processed_at',
                'refund_processed_by',
                'refund_rejection_reason',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('refund_status')->nullable()->after('status');
            $table->text('refund_reason')->nullable()->after('refund_status');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_amount');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_requested_at');
            $table->foreignId('refund_processed_by')->nullable()->after('refund_processed_at')->constrained('users')->nullOnDelete();
            $table->text('refund_rejection_reason')->nullable()->after('refund_processed_by');
        });
    }
};
