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
        Schema::table('driver_documents', function (Blueprint $table) {
            $table->string('vehicle_license_path')->nullable()->after('insurance_path');
            $table->string('road_worthiness_path')->nullable()->after('vehicle_license_path');
            $table->string('hackney_permit_path')->nullable()->after('road_worthiness_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_documents', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_license_path',
                'road_worthiness_path',
                'hackney_permit_path',
            ]);
        });
    }
};
