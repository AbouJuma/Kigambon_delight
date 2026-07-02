<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds synced_to_online tracking columns to all key tables.
 * - synced_to_online: false = needs push, true = already synced
 * - synced_at: timestamp of last successful push
 */
class AddSyncColumnsToKeyTables extends Migration
{
    /**
     * Tables that need sync tracking.
     * Add more table names here if needed.
     */
    protected array $tables = [
        'warehouses',
        'categories',
        'brands',
        'units',
        'sales',
        'sale_details',
        'payment_sales',
        'payment_sale_returns',
        'products',
        'product_warehouse',
        'clients',
        'providers',
        'purchases',
        'purchase_details',
        'expenses',
        'accounts',
        'deposits',
        'adjustments',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'synced_to_online')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->boolean('synced_to_online')->default(false)->index()->after('id');
                    $blueprint->timestamp('synced_at')->nullable()->after('synced_to_online');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'synced_to_online')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn(['synced_to_online', 'synced_at']);
                });
            }
        }
    }
}
