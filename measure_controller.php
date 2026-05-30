<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = new \Illuminate\Http\Request([
    'stock' => '0',
    'product_service' => '0'
]);
$id = 1;

$start = microtime(true);
$controller = new \App\Http\Controllers\ProductsController();
$response = $controller->Products_by_Warehouse($request, $id);

dump("Time: " . (microtime(true) - $start) . " seconds");
