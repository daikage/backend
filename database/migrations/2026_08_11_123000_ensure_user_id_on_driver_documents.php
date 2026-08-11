<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('driver_documents')) {
            Schema::table('driver_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('driver_documents', 'user_id')) {
                    if (Schema::hasColumn('driver_documents', 'driver_id')) {
                        // Rename driver_id to user_id
                        $table->renameColumn('driver_id', 'user_id');
                    } else {
                        // Just add user_id
                        $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
                    }
                }
            });
        }
    }

    public function down(): void
    {
        // Not reversible safely
    }
};
