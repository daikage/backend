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
                        $table->renameColumn('driver_id', 'user_id');
                    } else {
                        $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
                    }
                }
                if (!Schema::hasColumn('driver_documents', 'license_path')) {
                    $table->string('license_path')->nullable();
                }
                if (!Schema::hasColumn('driver_documents', 'insurance_path')) {
                    $table->string('insurance_path')->nullable();
                }
                if (!Schema::hasColumn('driver_documents', 'vehicle_license_path')) {
                    $table->string('vehicle_license_path')->nullable();
                }
                if (!Schema::hasColumn('driver_documents', 'road_worthiness_path')) {
                    $table->string('road_worthiness_path')->nullable();
                }
                if (!Schema::hasColumn('driver_documents', 'hackney_permit_path')) {
                    $table->string('hackney_permit_path')->nullable();
                }
                if (!Schema::hasColumn('driver_documents', 'status')) {
                    $table->string('status')->default('pending');
                }
            });
        }
    }

    public function down(): void
    {
        // Not reversible safely
    }
};
