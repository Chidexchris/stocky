<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LoginLog;
use App\Models\User;

$nonSuperLogs = LoginLog::whereHas('user', function($q) {
    $q->whereDoesntHave('roles', function($rq) {
        $rq->where('name', 'Super Admin');
    });
})->get();

$totalNonSuper = $nonSuperLogs->count();
$noBusNonSuper = 0;
$noStoreNonSuper = 0;

foreach($nonSuperLogs as $log) {
    if (is_null($log->business_id)) $noBusNonSuper++;
    if (is_null($log->store_id)) $noStoreNonSuper++;
}

echo "Non-Super Admin Logs: $totalNonSuper\n";
echo "Missing Business ID: $noBusNonSuper\n";
echo "Missing Store ID: $noStoreNonSuper\n";

if ($noBusNonSuper > 0) {
    echo "\nSample non-super logs with missing scoping:\n";
    foreach($nonSuperLogs as $log) {
        if (is_null($log->business_id)) {
            echo " - ID: {$log->id}, User: {$log->user->email}, Event: {$log->event_type}, User Bus: " . ($log->user->business_id ?? 'N/A') . "\n";
        }
    }
}
