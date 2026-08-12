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
        Schema::table('transactions', function (Blueprint $table) {
            // Make ride_id nullable. (For SQLite compatibility with Doctrine DBAL, it's safer to just alter or recreate if it fails, but Laravel 11 handles this fine if doctrine/dbal is installed)
            $table->unsignedBigInteger('ride_id')->nullable()->change();
            // Add type column
            $table->string('type')->default('ride_payment')->after('id');
        });

        Schema::table('rides', function (Blueprint $table) {
            $table->string('payment_method')->default('cash')->after('distance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->unsignedBigInteger('ride_id')->nullable(false)->change();
        });
    }
};
