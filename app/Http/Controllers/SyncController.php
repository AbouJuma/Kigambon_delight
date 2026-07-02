<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class SyncController extends Controller
{
    /**
     * Trigger the DB sync Artisan command on demand.
     */
    public function triggerSync(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'pos_settings', Setting::class);

        $limit = $request->get('limit', 200);

        try {
            // Run the artisan command
            Artisan::call('db:sync-online', [
                '--limit' => $limit
            ]);

            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'status' => $this->fetchSyncStatusInfo(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the current status/pending counts for the sync.
     */
    public function getSyncStatus(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'pos_settings', Setting::class);

        return response()->json([
            'success' => true,
            'status' => $this->fetchSyncStatusInfo(),
        ]);
    }

    /**
     * Helper to get pending counts and latest sync log entries.
     */
    protected function fetchSyncStatusInfo(): array
    {
        $tables = [
            'warehouses', 'categories', 'brands', 'units', 'sales',
            'sale_details', 'payment_sales', 'payment_sale_returns', 'products',
            'product_warehouse', 'clients', 'providers', 'purchases',
            'purchase_details', 'expenses', 'accounts', 'deposits', 'adjustments'
        ];

        $pendingCounts = [];
        foreach ($tables as $table) {
            if (\Schema::hasTable($table) && \Schema::hasColumn($table, 'synced_to_online')) {
                $count = DB::table($table)->where('synced_to_online', false)->count();
                if ($count > 0) {
                    $pendingCounts[$table] = $count;
                }
            }
        }

        // Get latest 10 failed items from sync_log
        $failures = [];
        if (\Schema::hasTable('sync_log')) {
            $failures = DB::table('sync_log')
                ->where('status', 'failed')
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();
        }

        return [
            'online_database' => env('DB_DATABASE_ONLINE'),
            'online_host' => env('DB_HOST_ONLINE'),
            'sync_enabled' => env('DB_SYNC_ENABLED', true),
            'pending_tables_count' => count($pendingCounts),
            'total_pending_rows' => array_sum($pendingCounts),
            'pending_details' => $pendingCounts,
            'recent_failures' => $failures,
        ];
    }
}
