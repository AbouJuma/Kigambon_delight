<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * SyncToOnlineDatabase
 *
 * Pushes all unsynced local records to the online/shared-server database.
 *
 * Usage:
 *   php artisan db:sync-online              (uses default limit from .env)
 *   php artisan db:sync-online --limit=50   (override limit)
 *   php artisan db:sync-online --dry-run    (show counts only, no writes)
 *   php artisan db:sync-online --table=sales (sync one table only)
 *
 * To switch from TEST to PRODUCTION, only change .env — no code changes needed.
 */
class SyncToOnlineDatabase extends Command
{
    protected $signature = 'db:sync-online
                            {--limit=0       : Max records per table per run (0 = use DB_SYNC_LIMIT from .env)}
                            {--table=        : Sync a specific table only}
                            {--dry-run       : Show pending counts, do not write anything}
                            {--force         : Re-sync records even if already synced}';

    protected $description = 'Push unsynced local records to the online shared-server database';

    /**
     * Priority-ordered list of tables to sync.
     * Each entry: [ table, primary_key, order_by_column ]
     */
    protected array $syncTables = [
        ['table' => 'warehouses',            'pk' => 'id', 'order' => 'id'],
        ['table' => 'categories',            'pk' => 'id', 'order' => 'id'],
        ['table' => 'brands',                'pk' => 'id', 'order' => 'id'],
        ['table' => 'units',                 'pk' => 'id', 'order' => 'id'],
        ['table' => 'clients',               'pk' => 'id', 'order' => 'id'],
        ['table' => 'providers',             'pk' => 'id', 'order' => 'id'],
        ['table' => 'products',              'pk' => 'id', 'order' => 'id'],
        ['table' => 'product_warehouse',     'pk' => 'id', 'order' => 'id'],
        ['table' => 'purchases',             'pk' => 'id', 'order' => 'id'],
        ['table' => 'purchase_details',      'pk' => 'id', 'order' => 'id'],
        ['table' => 'sales',                 'pk' => 'id', 'order' => 'id'],
        ['table' => 'sale_details',          'pk' => 'id', 'order' => 'id'],
        ['table' => 'payment_sales',         'pk' => 'id', 'order' => 'id'],
        ['table' => 'payment_sale_returns',  'pk' => 'id', 'order' => 'id'],
        ['table' => 'expenses',              'pk' => 'id', 'order' => 'id'],
        ['table' => 'accounts',              'pk' => 'id', 'order' => 'id'],
        ['table' => 'deposits',              'pk' => 'id', 'order' => 'id'],
        ['table' => 'adjustments',           'pk' => 'id', 'order' => 'id'],
    ];

    protected int  $totalSynced  = 0;
    protected int  $totalFailed  = 0;
    protected int  $totalSkipped = 0;

    public function handle(): int
    {
        // ── 0. Check if sync is enabled ──────────────────────────────────
        if (! env('DB_SYNC_ENABLED', true)) {
            $this->warn('⏸  Sync is disabled (DB_SYNC_ENABLED=false). Exiting.');
            return Command::SUCCESS;
        }

        $isDryRun = $this->option('dry-run');
        $limit    = (int) ($this->option('limit') ?: env('DB_SYNC_LIMIT', 200));
        $onlyTable = $this->option('table');
        $forceSync = $this->option('force');

        $env = env('DB_DATABASE_ONLINE', '?');
        $host = env('DB_HOST_ONLINE', '?');

        $this->info('');
        $this->info('╔══════════════════════════════════════════════════╗');
        $this->info('║         KIGAMBONI DELIGHT — DB SYNC              ║');
        $this->info('╚══════════════════════════════════════════════════╝');
        $this->info("  Target DB : {$env} @ {$host}");
        $this->info("  Limit     : {$limit} rows / table");
        $this->info('  Mode      : ' . ($isDryRun ? '🔍 DRY RUN (no writes)' : '🚀 LIVE'));
        $this->info('');

        // ── 1. Test connectivity to online DB ────────────────────────────
        if (! $isDryRun) {
            try {
                DB::connection('mysql_online')->getPdo();
                $this->info('  ✅ Online DB connection OK');
            } catch (\Exception $e) {
                $this->error('  ❌ Cannot reach online DB: ' . $e->getMessage());
                Log::error('[DB SYNC] Connection failed: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        $this->info('');

        // ── 2. Filter tables if --table option provided ──────────────────
        $tables = $onlyTable
            ? array_filter($this->syncTables, fn($t) => $t['table'] === $onlyTable)
            : $this->syncTables;

        if (empty($tables)) {
            $this->error("  Table '{$onlyTable}' not found in sync list.");
            return Command::FAILURE;
        }

        // ── 3. Sync each table ───────────────────────────────────────────
        try {
            DB::connection('mysql_online')->statement('SET FOREIGN_KEY_CHECKS=0;');
        } catch (\Exception $e) {}

        foreach ($tables as $config) {
            $this->syncTable($config, $limit, $isDryRun, $forceSync);
        }

        try {
            DB::connection('mysql_online')->statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Exception $e) {}

        // ── 4. Summary ───────────────────────────────────────────────────
        $this->info('');
        $this->info('══════════════ SYNC SUMMARY ══════════════');
        $this->info("  ✅ Synced  : {$this->totalSynced}");
        $this->info("  ⏭  Skipped : {$this->totalSkipped} (no pending rows)");
        if ($this->totalFailed > 0) {
            $this->warn("  ❌ Failed  : {$this->totalFailed} (check sync_log table)");
        }
        $this->info('==========================================');
        $this->info('');

        return $this->totalFailed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Sync a single table's unsynced rows to the online DB.
     */
    protected function syncTable(array $config, int $limit, bool $isDryRun, bool $forceSync): void
    {
        $tableName = $config['table'];
        $pk        = $config['pk'];
        $orderBy   = $config['order'];

        // Check the table exists locally and has the sync column
        if (! \Schema::hasTable($tableName) || ! \Schema::hasColumn($tableName, 'synced_to_online')) {
            $this->line("  ⚪ {$tableName} — skipped (no sync column, run migrations first)");
            $this->totalSkipped++;
            return;
        }

        // Count pending rows
        $query = DB::table($tableName);
        if (! $forceSync) {
            $query->where('synced_to_online', false);
        }
        $pending = $query->count();

        if ($pending === 0) {
            $this->line("  ✓  {$tableName} — up to date (0 pending)");
            $this->totalSkipped++;
            return;
        }

        if ($isDryRun) {
            $this->warn("  🔍 {$tableName} — {$pending} rows pending (dry run, not written)");
            return;
        }

        $this->line("  🔄 {$tableName} — syncing {$pending} rows (limit: {$limit})...");

        $synced = 0;
        $failed = 0;

        // Fetch rows in batches
        $rows = DB::table($tableName)
            ->where('synced_to_online', false)
            ->orderBy($orderBy)
            ->limit($limit)
            ->get();

        foreach ($rows as $row) {
            $rowArray = (array) $row;
            $recordId = $rowArray[$pk];

            // Remove our tracking columns before inserting into online DB
            unset($rowArray['synced_to_online'], $rowArray['synced_at']);

            try {
                DB::connection('mysql_online')
                    ->table($tableName)
                    ->updateOrInsert([$pk => $recordId], $rowArray);

                // Mark as synced locally
                DB::table($tableName)
                    ->where($pk, $recordId)
                    ->update([
                        'synced_to_online' => true,
                        'synced_at'        => now(),
                    ]);

                // Clear any previous failure from sync_log
                DB::table('sync_log')
                    ->where('table_name', $tableName)
                    ->where('record_id', $recordId)
                    ->where('status', 'failed')
                    ->update(['status' => 'synced', 'synced_at' => now()]);

                $synced++;

            } catch (\Exception $e) {
                $failed++;
                $errMsg = $e->getMessage();

                // Log to sync_log for retry tracking
                DB::table('sync_log')->updateOrInsert(
                    ['table_name' => $tableName, 'record_id' => $recordId],
                    [
                        'action'        => 'insert',
                        'status'        => 'failed',
                        'error_message' => substr($errMsg, 0, 500),
                        'synced_at'     => null,
                        'updated_at'    => now(),
                        'created_at'    => now(),
                    ]
                );

                Log::warning("[DB SYNC] Failed {$tableName}#{$recordId}: {$errMsg}");
            }
        }

        $remaining = max(0, $pending - $synced);
        $status    = $failed > 0 ? "⚠" : "✅";
        $this->line("  {$status} {$tableName} — {$synced} synced, {$failed} failed" .
                    ($remaining > 0 ? ", {$remaining} still pending" : ''));

        $this->totalSynced += $synced;
        $this->totalFailed += $failed;
    }
}
