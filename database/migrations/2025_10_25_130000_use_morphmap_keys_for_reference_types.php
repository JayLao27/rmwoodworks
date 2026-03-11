<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only update if table exists
        if (Schema::hasTable('inventory_movements')) {
            DB::table('inventory_movements')
                ->where('reference_type', 'App\Models\Procurement\PurchaseOrder')
                ->update(['reference_type' => 'purchase_order']);
                
            DB::table('inventory_movements')
                ->where('reference_type', 'App\Models\WorkOrder')
                ->update(['reference_type' => 'work_order']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not meant to be reverted
    }
};
