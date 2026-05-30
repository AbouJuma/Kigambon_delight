<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$nullProducts = \DB::table('products')->whereNull('code')->count();
$nullVariants = \DB::table('product_variants')->whereNull('code')->count();

dump("Null codes in products: " . $nullProducts);
dump("Null codes in variants: " . $nullVariants);
