<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$rows = App\Models\Question::whereNotNull('image_url')->orderByDesc('id')->take(10)->get(['id','image_url']);
foreach ($rows as $r) {
  $len = strlen($r->image_url);
  echo $r->id . "|" . $len . "|" . substr($r->image_url, -70) . PHP_EOL;
}
