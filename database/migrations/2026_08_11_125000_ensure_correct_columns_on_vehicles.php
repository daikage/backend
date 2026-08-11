<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {
                // The original migration created 'license_plate', but the controller expects 'plate_number'
                if (Schema::hasColumn('vehicles', 'license_plate') && !Schema::hasColumn('vehicles', 'plate_number')) {
                    $table->renameColumn('license_plate', 'plate_number');
                } elseif (!Schema::hasColumn('vehicles', 'plate_number')) {
                    $table->string('plate_number')->nullable()->unique();
                }

                // The controller expects 'ride_category_id' which was missing in the original migration
                if (!Schema::hasColumn('vehicles', 'ride_category_id')) {
                    // We don't add a strict constraint because some records might be broken, just the column
                    $table->unsignedBigInteger('ride_category_id')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Not reversible safely
    }
};
