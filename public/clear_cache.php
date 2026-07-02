<?php
/**
 * Temporary cache clearing script
 * Upload this to public/ directory on your server
 * Access: https://client.ecofieldgroup.com/clear_cache.php
 * DELETE THIS FILE AFTER USE!
 */

// Change to parent directory
chdir(__DIR__ . '/..');

// Load Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');

echo "<h2>Clearing Laravel Cache...</h2>";

// Clear various caches
$commands = [
    'cache:clear',
    'route:clear',
    'config:clear',
    'view:clear',
];

foreach ($commands as $command) {
    echo "<p>Running: $command... ";
    $kernel->call($command);
    echo "DONE</p>";
}

echo "<h3 style='color:green'>All caches cleared successfully!</h3>";
echo "<p><strong>IMPORTANT:</strong> Delete this file (clear_cache.php) from your server now for security.</p>";
