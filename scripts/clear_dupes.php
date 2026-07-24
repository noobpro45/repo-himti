<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dupes = \App\Models\RiwayatUnduhan::select('id_user', 'id_media')
    ->groupBy('id_user', 'id_media')
    ->havingRaw('COUNT(*) > 1')
    ->get();

foreach ($dupes as $dupe) {
    $ids = \App\Models\RiwayatUnduhan::where('id_user', $dupe->id_user)
        ->where('id_media', $dupe->id_media)
        ->orderBy('created_at', 'desc')
        ->pluck('id')
        ->slice(1);
    
    \App\Models\RiwayatUnduhan::whereIn('id', $ids)->delete();
}

echo "Duplicates removed.\n";
