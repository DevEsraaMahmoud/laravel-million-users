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
        // Check if column already exists (it's already in the create_notifications_table migration)
        if (!Schema::hasColumn('user_activity_notifications', 'read_at')) {
            Schema::table('user_activity_notifications', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('read');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_activity_notifications', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }
};
