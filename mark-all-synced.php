<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Usage: php mark-all-synced.php "2025-07-05" (mark records created before this date as synced)
$date = $argv[1] ?? null;

if (!$date) {
    echo "Usage: php mark-all-synced.php \"YYYY-MM-DD\"\n";
    echo "Example: php mark-all-synced.php \"2025-07-05\"\n";
    echo "This marks all records created before the given date as synced.\n";
    exit(1);
}

$cutoffDate = \Carbon\Carbon::parse($date)->endOfDay();

echo "Marking records created before {$date} as synced...\n\n";

$tables = [
    'warehouses', 'categories', 'brands', 'units', 'clients', 'providers',
    'products', 'product_warehouse', 'purchases', 'purchase_details',
    'sales', 'sale_details', 'payment_sales', 'payment_sale_returns',
    'expenses', 'accounts', 'deposits', 'adjustments'
];

foreach ($tables as $table) {
    if (!\Schema::hasTable($table) || !\Schema::hasColumn($table, 'synced_to_online')) {
        echo "Skipping {$table} (no sync column)\n";
        continue;
    }

    if (!\Schema::hasColumn($table, 'created_at')) {
        echo "Skipping {$table} (no created_at column)\n";
        continue;
    }

    $count = DB::table($table)
        ->where('synced_to_online', false)
        ->where('created_at', '<=', $cutoffDate)
        ->count();
    
    if ($count > 0) {
        DB::table($table)
            ->where('synced_to_online', false)
            ->where('created_at', '<=', $cutoffDate)
            ->update([
                'synced_to_online' => true,
                'synced_at' => now()
            ]);
        echo "✓ {$table}: marked {$count} records as synced (before {$date})\n";
    } else {
        echo "✓ {$table}: no records to mark (before {$date})\n";
    }
}

echo "\nDone! Now only records created after {$date} will sync.\n";
