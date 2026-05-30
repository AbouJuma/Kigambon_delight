<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$missing = \DB::table('products')
    ->leftJoin('product_warehouse', function($join) {
        $join->on('products.id', '=', 'product_warehouse.product_id')
             ->where('product_warehouse.warehouse_id', '=', 1);
    })
    ->whereNull('product_warehouse.id')
    ->count();

dump("Missing in warehouse 1: " . $missing);
