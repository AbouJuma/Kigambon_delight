<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = new \Illuminate\Http\Request([
    'stock' => '0',
    'product_service' => '0'
]);
$id = 1;

$controller = new \App\Http\Controllers\ProductsController();
$response = $controller->Products_by_Warehouse($request, $id);

$data = json_decode($response->getContent(), true);
dump("Total items: " . count($data));

$has_stock = 0;
foreach ($data as $item) {
    if ($item['qte_purchase'] > 0) {
        $has_stock++;
    }
}
dump("Items with stock > 0: " . $has_stock);

// Let's print one item that has qte_purchase > 0
foreach ($data as $item) {
    if ($item['qte_purchase'] > 0) {
        dump($item);
        break;
    }
}
