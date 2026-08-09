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
        Schema::table('ride_categories', function (Blueprint $table) {
            $table->string('service_type', 20)->default('single')->after('name');
        });

        Schema::table('rides', function (Blueprint $table) {
            $table->string('service_type', 20)->default('single')->after('ride_category_id');
            $table->json('service_meta')->nullable()->after('service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn(['service_type', 'service_meta']);
        });

        Schema::table('ride_categories', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });
    }
};
