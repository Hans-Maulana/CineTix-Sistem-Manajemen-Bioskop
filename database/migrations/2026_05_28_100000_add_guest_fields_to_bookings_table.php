<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'guest_email')) {
                $table->string('guest_email')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('bookings', 'access_token')) {
                $table->string('access_token', 64)->nullable()->unique()->after('guest_email');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'guest_email')) {
                $table->dropColumn('guest_email');
            }
            if (Schema::hasColumn('bookings', 'access_token')) {
                $table->dropColumn('access_token');
            }
        });
    }
};
