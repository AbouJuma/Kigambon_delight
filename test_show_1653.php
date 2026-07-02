<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

app()->bind('Illuminate\Contracts\Auth\Access\Gate', function () {
    return new class {
        public function authorize() { return true; }
    };
});

$ctrl = app()->make('App\Http\Controllers\ProductsController');
try {
    $res = $ctrl->show_product_data(1653, null);
    echo "1653: " . json_encode($res->getData()) . "\n";
} catch (\Exception $e) {
    echo "ERROR 1653: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n";
}

try {
    $res = $ctrl->show_product_data(1655, null);
    echo "1655: " . json_encode($res->getData()) . "\n";
} catch (\Exception $e) {
    echo "ERROR 1655: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n";
}
