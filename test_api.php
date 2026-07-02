<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = new \Illuminate\Http\Request();
$req->merge(['warehouse_id' => 2, 'stock' => 1, 'product_service' => 1]);
// We have to bypass auth
app()->bind('Illuminate\Contracts\Auth\Access\Gate', function () {
    return new class {
        public function authorize() { return true; }
    };
});

$ctrl = app()->make('App\Http\Controllers\PosController');
try {
    $res = $ctrl->GetProductsByParametre($req);
    echo json_encode($res->getData());
} catch (\Exception $e) {
    echo $e->getMessage();
}
