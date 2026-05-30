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

$weird_items = [];
foreach ($data as $item) {
    if ($item['qte'] > 0 && $item['qte_purchase'] == 0) {
        $weird_items[] = $item;
    }
}
dump("Weird items: " . count($weird_items));
if (count($weird_items) > 0) {
    dump($weird_items[0]);
}
