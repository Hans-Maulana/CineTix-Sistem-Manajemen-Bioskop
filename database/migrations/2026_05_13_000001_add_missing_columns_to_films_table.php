<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('films', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('films', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('films', 'rating')) {
                $table->decimal('rating', 3, 1)->nullable()->after('duration');
            }
            if (!Schema::hasColumn('films', 'actors')) {
                $table->string('actors')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('films', 'director')) {
                $table->string('director')->nullable()->after('actors');
            }
            if (!Schema::hasColumn('films', 'production')) {
                $table->string('production')->nullable()->after('director');
            }
            if (!Schema::hasColumn('films', 'classification')) {
                $table->string('classification')->nullable()->after('status');
            }
            if (!Schema::hasColumn('films', 'cover')) {
                $table->string('cover')->nullable()->after('classification');
            }
        });
    }

    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'rating',
                'actors',
                'director',
                'production',
                'status',
                'classification',
                'cover',
            ]);
        });
    }
};
