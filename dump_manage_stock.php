<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$counts = \DB::table('product_warehouse')->select('manage_stock', \DB::raw('count(*) as count'))->groupBy('manage_stock')->get();
dump($counts);
