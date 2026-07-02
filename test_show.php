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
    // Try to get a product that was failing
    $res = $ctrl->show_product_data(1612, null);
    echo json_encode($res->getData());
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
