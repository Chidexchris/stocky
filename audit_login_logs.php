<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LoginLog;

$total = LoginLog::count();
$noBusiness = LoginLog::whereNull('business_id')->count();
$noStore = LoginLog::whereNull('store_id')->count();

echo "Total Logs: $total\n";
echo "Logs with NULL Business ID: $noBusiness\n";
echo "Logs with NULL Store ID: $noStore\n";

if ($noBusiness > 0) {
    echo "\nSample logs with NULL business:\n";
    $samples = LoginLog::whereNull('business_id')->with('user')->take(5)->get();
    foreach($samples as $s) {
        echo " - ID: {$s->id}, User: " . ($s->user->email ?? 'N/A') . " (User Bus: " . ($s->user->business_id ?? 'N/A') . "), Event: {$s->event_type}\n";
    }
}
