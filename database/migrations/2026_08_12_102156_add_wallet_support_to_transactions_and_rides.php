<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // IMPORTANT: This migration is intentionally a no-op.
    //
    // The wallet support (ridable ride_id on `transactions` + the `type` column,
    // and `payment_method` on `rides`) was already applied by the earlier
    // migration `2026_08_12_100000_add_wallet_support_to_transactions_and_rides`.
    //
    // This file is kept (under its original timestamp) so that databases which
    // already recorded this migration as run do not diverge. It deliberately no
    // longer references a non-existent `transactions_and_rides` table.

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Nothing to do — see note above.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to do — the changes are owned by the 2026_08_12_100000 migration.
    }
};
