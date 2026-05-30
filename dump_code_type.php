<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$item = \DB::table('products')->first();
dump(gettype($item->code));
dump($item->code);
