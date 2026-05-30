<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$start = microtime(true);
$product_warehouse_data = \App\Models\product_warehouse::with('warehouse', 'product', 'productVariant')
    ->where('warehouse_id', 1)
    ->where('deleted_at', '=', null)
    ->get();

dump("Count: " . count($product_warehouse_data));
dump("Time: " . (microtime(true) - $start) . " seconds");
