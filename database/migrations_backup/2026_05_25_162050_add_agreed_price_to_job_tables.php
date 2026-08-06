<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if pickup_requests table exists before altering
        if (Schema::hasTable('pickup_requests')) {
            Schema::table('pickup_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('pickup_requests', 'agreed_price')) {
                    $table->decimal('agreed_price', 10, 2)->nullable()->after('price');
                }
                if (!Schema::hasColumn('pickup_requests', 'total_amount')) {
                    $table->decimal('total_amount', 10, 2)->nullable()->after('agreed_price');
                }
            });
        }
        
        // Check if delivery_requests table exists
        if (Schema::hasTable('delivery_requests')) {
            Schema::table('delivery_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('delivery_requests', 'agreed_price')) {
                    $table->decimal('agreed_price', 10, 2)->nullable()->after('price');
                }
                if (!Schema::hasColumn('delivery_requests', 'total_amount')) {
                    $table->decimal('total_amount', 10, 2)->nullable()->after('agreed_price');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pickup_requests')) {
            Schema::table('pickup_requests', function (Blueprint $table) {
                $table->dropColumn(['agreed_price', 'total_amount']);
            });
        }
        
        if (Schema::hasTable('delivery_requests')) {
            Schema::table('delivery_requests', function (Blueprint $table) {
                $table->dropColumn(['agreed_price', 'total_amount']);
            });
        }
    }
};