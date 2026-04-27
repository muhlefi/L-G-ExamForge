<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$svg = app(App\Services\QuestionSvgRenderer::class)->render('Matematika', 'Aljabar', 'SMA', 'Tentukan hasil pada garis bilangan dari -3 + 5.');
$path = 'question-visuals/_smoke_test.svg';
Illuminate\Support\Facades\Storage::disk('public')->put($path, $svg);
echo Illuminate\Support\Facades\Storage::disk('public')->url($path);
