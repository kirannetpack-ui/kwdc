<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add tracking fields to dispatch_orders
        if (Schema::hasTable('dispatch_orders')) {
            Schema::table('dispatch_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('dispatch_orders', 'current_latitude')) {
                    $table->decimal('current_latitude', 10, 8)->nullable();
                }
                if (!Schema::hasColumn('dispatch_orders', 'current_longitude')) {
                    $table->decimal('current_longitude', 11, 8)->nullable();
                }
                if (!Schema::hasColumn('dispatch_orders', 'last_location_update')) {
                    $table->timestamp('last_location_update')->nullable();
                }
                if (!Schema::hasColumn('dispatch_orders', 'status_level')) {
                    $table->integer('status_level')->default(1);
                }
            });
        }
        
        // Add status to warehouse_requests
        if (Schema::hasTable('warehouse_requests')) {
            Schema::table('warehouse_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('warehouse_requests', 'status')) {
                    $table->string('status')->default('pending');
                }
                if (!Schema::hasColumn('warehouse_requests', 'status_level')) {
                    $table->integer('status_level')->default(1);
                }
            });
        }
        
        // Add status to pickup_requests
        if (Schema::hasTable('pickup_requests')) {
            Schema::table('pickup_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('pickup_requests', 'status')) {
                    $table->string('status')->default('pending');
                }
                if (!Schema::hasColumn('pickup_requests', 'status_level')) {
                    $table->integer('status_level')->default(1);
                }
            });
        }
    }

    public function down()
    {
        // Remove tracking fields
        if (Schema::hasTable('dispatch_orders')) {
            Schema::table('dispatch_orders', function (Blueprint $table) {
                $table->dropColumn([
                    'current_latitude', 
                    'current_longitude', 
                    'last_location_update', 
                    'status_level'
                ]);
            });
        }
        
        if (Schema::hasTable('warehouse_requests')) {
            Schema::table('warehouse_requests', function (Blueprint $table) {
                $table->dropColumn(['status', 'status_level']);
            });
        }
        
        if (Schema::hasTable('pickup_requests')) {
            Schema::table('pickup_requests', function (Blueprint $table) {
                $table->dropColumn(['status', 'status_level']);
            });
        }
    }
};