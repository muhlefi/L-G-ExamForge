<?php

namespace App\Providers;

use App\Models\Batch;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $batches = collect();

            try {
                if (Schema::hasTable('batches')) {
                    $batches = Batch::query()
                        ->latest()
                        ->get(['id', 'subject', 'topic', 'created_at']);
                }
            } catch (Throwable) {
                // Ignore errors when the app is booting before migrations/DB are ready.
            }

            $currentBatch = request()->route('batch');

            $view->with([
                'batches' => $batches,
                'currentBatch' => $currentBatch instanceof Batch ? $currentBatch : null,
            ]);
        });
    }
}
