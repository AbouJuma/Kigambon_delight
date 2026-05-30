<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$diff = \DB::table('products')
    ->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
    ->where('product_warehouse.warehouse_id', 1)
    ->where('products.is_variant', 0)
    ->select('products.name', 'products.id', 'product_warehouse.qte as pw_qte', 'product_warehouse.warehouse_id')
    ->whereRaw('product_warehouse.qte = 0')
    ->limit(5)
    ->get();

dump($diff);

$has_stock_in_products = \DB::table('products')
    ->whereIn('id', $diff->pluck('id'))
    ->get(['id', 'type']);

dump($has_stock_in_products);
